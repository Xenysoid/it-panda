<?php
/**
 * Таблица рекордов для игры «Вантузыч: Битва с засорами».
 * Положите этот файл рядом с index.html игры. Нужен PHP 7.0+ и право записи в папку data/.
 *
 *   GET  leaderboard.php                     -> {"top":[{"name":"...","score":123,"level":3,"win":true,"date":"2026-11-17"}, ...]}
 *   POST leaderboard.php {"action":"start"}  -> {"token":"..."}       (в начале каждой игры)
 *   POST leaderboard.php {"action":"submit","token":"...","name":"...","score":123,"level":3,"win":true}
 *
 * Защита от накруток базовая: одноразовый токен на игру, проверка правдоподобности
 * счёта по времени игры, ограничение частоты запросов с одного IP.
 */

const KEEP      = 10;        // сколько записей хранить
const SHOW      = 3;         // сколько отдавать по умолчанию
const MAX_SCORE = 200000;    // абсолютный потолок счёта
const MIN_GAME  = 20;        // минимум секунд игры для записи результата
const PER_SEC   = 250;       // не больше стольких очков за секунду игры (+ запас ниже)
const SLACK     = 6000;
const TOKEN_TTL = 3 * 3600;  // токен живёт 3 часа

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$dir = __DIR__ . '/data';
if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
if (!file_exists($dir . '/.htaccess')) { @file_put_contents($dir . '/.htaccess', "<IfModule mod_authz_core.c>\n  Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n  Deny from all\n</IfModule>\n"); }
$boardFile = $dir . '/leaderboard.json';
$tokenFile = $dir . '/tokens.json';

function out($data, $code = 200) { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }

/** Открыть JSON-файл под эксклюзивной блокировкой, вызвать $fn(&$data), сохранить. */
function withJson($file, callable $fn) {
    $fp = @fopen($file, 'c+');
    if (!$fp) out(['error' => 'storage'], 500);
    flock($fp, LOCK_EX);
    $raw = stream_get_contents($fp);
    $data = $raw ? json_decode($raw, true) : [];
    if (!is_array($data)) $data = [];
    $result = $fn($data);
    ftruncate($fp, 0); rewind($fp);
    fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));
    fflush($fp); flock($fp, LOCK_UN); fclose($fp);
    return $result;
}

function readBoard($file) {
    if (!file_exists($file)) return [];
    $d = json_decode((string)@file_get_contents($file), true);
    return is_array($d) ? $d : [];
}

function cleanName($s) {
    $s = trim(preg_replace('/[\x00-\x1F\x7F<>"\'`&\\\\]/u', '', (string)$s));
    $s = preg_replace('/\s+/u', ' ', $s);
    if (function_exists('mb_substr')) $s = mb_substr($s, 0, 16, 'UTF-8'); else $s = substr($s, 0, 16);
    return $s;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $n = max(1, min(KEEP, (int)($_GET['n'] ?? SHOW)));
    out(['top' => array_slice(readBoard($boardFile), 0, $n)]);
}

if ($method !== 'POST') out(['error' => 'method'], 405);

$in = json_decode(file_get_contents('php://input'), true);
if (!is_array($in)) out(['error' => 'bad json'], 400);
$ip = $_SERVER['REMOTE_ADDR'] ?? '0';
$now = time();

if (($in['action'] ?? '') === 'start') {
    $token = bin2hex(random_bytes(16));
    withJson($tokenFile, function (&$t) use ($token, $now, $ip) {
        foreach ($t as $k => $v) if ($now - ($v['t'] ?? 0) > TOKEN_TTL) unset($t[$k]);
        // не больше 30 активных токенов с одного IP
        $mine = array_filter($t, function ($v) use ($ip) { return ($v['ip'] ?? '') === $ip; });
        if (count($mine) >= 30) { uasort($t, function ($a, $b) { return $a['t'] - $b['t']; }); foreach ($t as $k => $v) if ($v['ip'] === $ip) { unset($t[$k]); break; } }
        $t[$token] = ['t' => $now, 'ip' => $ip];
    });
    out(['token' => $token]);
}

if (($in['action'] ?? '') === 'submit') {
    $token = (string)($in['token'] ?? '');
    $name  = cleanName($in['name'] ?? '');
    $score = (int)($in['score'] ?? 0);
    $level = max(1, min(3, (int)($in['level'] ?? 1)));
    $win   = !empty($in['win']);
    if ($name === '') out(['error' => 'name'], 400);
    if ($score <= 0 || $score > MAX_SCORE) out(['error' => 'score'], 400);

    // одноразовый токен + проверка времени игры
    $started = withJson($tokenFile, function (&$t) use ($token) {
        if (!isset($t[$token])) return null;
        $s = $t[$token]['t']; unset($t[$token]); return $s;
    });
    if ($started === null) out(['error' => 'token'], 403);
    $elapsed = $now - $started;
    if ($elapsed < MIN_GAME || $score > $elapsed * PER_SEC + SLACK) out(['error' => 'implausible'], 403);

    $board = withJson($boardFile, function (&$b) use ($name, $score, $level, $win) {
        // один игрок (имя) — одна строка, лучший результат
        $key = function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);
        $found = false;
        foreach ($b as &$row) {
            $rk = function_exists('mb_strtolower') ? mb_strtolower($row['name'], 'UTF-8') : strtolower($row['name']);
            if ($rk === $key) {
                $found = true;
                if ($score > $row['score']) $row = ['name' => $name, 'score' => $score, 'level' => $level, 'win' => $win, 'date' => date('Y-m-d')];
            }
        }
        unset($row);
        if (!$found) $b[] = ['name' => $name, 'score' => $score, 'level' => $level, 'win' => $win, 'date' => date('Y-m-d')];
        usort($b, function ($x, $y) { return $y['score'] - $x['score']; });
        $b = array_slice($b, 0, KEEP);
        return $b;
    });
    $place = 0;
    foreach ($board as $i => $row) if ($row['name'] === $name && $row['score'] === $score) { $place = $i + 1; break; }
    out(['ok' => true, 'place' => $place, 'top' => array_slice($board, 0, SHOW)]);
}

out(['error' => 'action'], 400);
