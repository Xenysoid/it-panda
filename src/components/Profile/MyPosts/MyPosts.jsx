import React from "react";
import s from "./MyPosts.module.css";
import Post from "./Post/Post";

const MyPosts = () => {
  return (
    <div className={s.posts}>
      <div>
        <textarea name="" id="" cols="15" rows="5"></textarea>
        <button>Add post</button>
        <Post />
      </div>
    </div>
  );
};

export default MyPosts;
