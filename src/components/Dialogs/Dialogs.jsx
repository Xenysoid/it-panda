import React from "react";
import s from './Dialogs.module.css';

const Dialogs = (props) => {
    return (
        <div className={s.dialogs}>
            <div className={s.dialogs__items}>
                <div className={s.dialog + ' ' + s.active}>
                    Andrey
                </div>
                <div className={s.dialog}>
                    Max
                </div>
                <div className={s.dialog}>
                    Sveta
                </div>
                <div className={s.dialog}>
                    Viktor
                </div>
                <div className={s.dialog}>
                    Alex
                </div>
                <div className={s.dialog}>
                    John
                </div>
            </div>
            <div className={s.messages}>
                <div className="message">Hi</div>
                <div className="message">How are you?</div>
                <div className="message">Yo</div>
                <div className="message">I'm fun</div>
                <div className="message">Goodbye</div>
                <div className="message">Bye</div>
            </div>
        </div>
    )
}

export default Dialogs;