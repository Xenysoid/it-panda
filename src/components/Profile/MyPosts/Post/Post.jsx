import React from "react";
import s from "./Post.module.css";

const Post = () => {
  return (
    
      <div className={s.item}>
        <img
          className={s.ava}
          src="https://www.nastol.com.ua/pic/201302/1024x1024/nastol.com.ua-40414.jpg"
        ></img>
        Кот Василий
      </div>
    
    
  );
};

export default Post;
