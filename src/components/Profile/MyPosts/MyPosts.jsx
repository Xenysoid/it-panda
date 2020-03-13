import React from "react";
import s from "./MyPosts.module.css";
import Post from "./Post/Post";
import Like from "./Post/Like";

const MyPosts = props => {
  return (
    <div className={s.posts}>
      <div>
        <textarea name="" id="" cols="15" rows="5"></textarea>
        <button>Add post</button>
        <Post message="Hi, how are you?" />
        <Like like="15" />
        <Post message="It's my first post" />
        <Like like="30" />
      </div>
    </div>
  );
};

export default MyPosts;
