import React from "react";
import s from "./MyPosts.module.css";
import Post from "./Post/Post";


const MyPosts = props => {
    return (
        <div className={s.postsBlock}>
            <h3>My Posts</h3>
            <div>
                <div><textarea name="" id="" cols="35" rows="5"></textarea></div>
                <div>
                    <button>Add post</button>
                </div>
                <div className={s.posts}>
                <Post message="Hi, how are you?" likesCount='5'/>

                <Post message="It's my first post" likesCount='20'/>
                </div>
            </div>
        </div>
    );
};

export default MyPosts;
