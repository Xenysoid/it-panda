import React from "react";
import s from './Dialogs.module.css';
import {NavLink} from "react-router-dom";


const DialogItem = (props) => {
    let path = "/dialogs/" + props.id;
    return (
        <div className={s.dialog + ' ' + s.active}>
            <NavLink to={path}>{props.name}</NavLink>
        </div>
    )
}

const Message = (props) => {
    return (
        <div className="message">{props.message}</div>
    )
}

const Dialogs = (props) => {

    let dialogsData = [
        {id: 1, name: 'Andrey'},
        {id: 2, name: 'Alex'},
        {id: 3, name: 'Maria'},
        {id: 4, name: 'John'},
        {id: 5, name: 'Jessi'},
        {id: 6, name: 'Diana'}
    ]

    return (
        <div className={s.dialogs}>
            <div className={s.dialogs__items}>
                <DialogItem name={dialogsData[0].name} id={dialogsData[0].id} />
                <DialogItem name={dialogsData[1].name} id={dialogsData[1].id} />
                <DialogItem name={dialogsData[2].name} id={dialogsData[2].id} />
                <DialogItem name={dialogsData[3].name} id={dialogsData[3].id} />
                <DialogItem name={dialogsData[4].name} id={dialogsData[4].id} />
                <DialogItem name={dialogsData[5].name} id={dialogsData[5].id} />
            </div>
            <div className={s.messages}>
                <Message message={"Hi"}/>
                <Message message={"How are you?"}/>
                <Message message={"Yo"}/>
                <Message message={"I'm fun"}/>
                <Message message={"Goodbye"}/>
                <Message message={"Bye"}/>
            </div>
        </div>
    )
}

export default Dialogs;