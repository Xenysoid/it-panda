import React from "react";
import s from "./Like.module.css";

const Like = (props) => {
  return (
  <div className={s.like}>{props.like}</div>
  );
};

export default Like;
