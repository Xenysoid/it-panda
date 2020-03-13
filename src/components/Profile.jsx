import React from "react";
import s from './Profile.module.css'

const Profile = () => {
  return(
    <div className={s.content}>
    <div>
      
      <img
        className={s.ava}
        src="https://www.nastol.com.ua/pic/201302/1024x1024/nastol.com.ua-40414.jpg"
      ></img>
      Кот Василий
  
    </div>
    <div>
      <img src="https://avatars.mds.yandex.net/get-pdb/907231/09738fd8-6068-4d90-902f-c3c2733bfa6d/s1200?webp=false"></img>
    </div>
    <div className={s.posts}>
      My posts
      <div className={s.item}>New post1</div>
      <div className={s.item}>New post2</div>
      <div className={s.item}>New post3</div>
    </div>
  </div>
  )
};


export default Profile