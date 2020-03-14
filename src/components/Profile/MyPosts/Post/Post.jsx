import React from "react";
import s from "./Post.module.css";

const Post = (props) => {
  return (
    <div className={s.item}>
      <img src="https://www.nastol.com.ua/pic/201302/1024x1024/nastol.com.ua-40414.jpg"></img>
      { props.message }
      <div>
        
      ❤ {props.likesCount}
      </div>
    </div>
  );
};

export default Post;
