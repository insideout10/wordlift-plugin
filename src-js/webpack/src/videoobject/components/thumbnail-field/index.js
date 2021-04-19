/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
import React from "react";
import {__} from "@wordpress/i18n";

/**
 * Internal dependencies
 */
import {WlContainer} from "../../../mappings/blocks/wl-container";
import "./index.scss"
import {WlColumn} from "../../../mappings/blocks/wl-column";
import {ModalInput} from "../modal-input";
import {connect} from "react-redux";
import WlActionButton from "../../../faq/components/wl-action-button";
import {addNewThumbnail, removeThumbnail} from "../../actions";


const ModalRepeaterTextFieldRemoveIcon = ({onRemoveListener}) => {
    return (<div onClick={onRemoveListener} className={"wl-modal__thumbnail_field__remove_button"}>
    </div>)
}


const ModalRepeaterTextField = (props) => {

    const {defaultValue, onRemoveListener, onFieldChangeListener, index} = props
    return (
        <WlContainer fullWidth={true} className={"wl-modal__thumbnail_field"}>
            <WlColumn className={"wl-col--width-90"}>
                <ModalInput defaultValue={defaultValue}/>
            </WlColumn>
            <WlColumn className={"wl-col--width-10"}>
                <ModalRepeaterTextFieldRemoveIcon onRemoveListener={onRemoveListener}/>
            </WlColumn>
        </WlContainer>
    )

}


/**
 * @param props
 * @constructor
 */

class ThumbnailField extends React.Component {


    render() {
        const {thumbnails, videoIndex} = this.props
        return (
            <WlContainer rowLayout={true} fullWidth={true}>
                <p>{__("THUMBNAIL URL", "wordlift")}</p>
                {thumbnails.length > 0 && thumbnails.map((thumbnail, thumbnailIndex) => {
                    return (<ModalRepeaterTextField defaultValue={thumbnail} onRemoveListener={() => {
                        this.props.dispatch(
                            removeThumbnail({
                                videoIndex,
                                thumbnailIndex
                            }))
                    }}/>)
                })}

                <WlContainer fullWidth={true}>
                    <WlColumn className={"wl-col--width-80"}></WlColumn>
                    <WlColumn className={"wl-col-width-20"}>
                        <WlActionButton className={"wl-action-button--primary"}
                                        text={__("Add new", "wordlift")}
                                        onClickHandler={() => this.props.dispatch(addNewThumbnail({videoIndex}))}/>
                    </WlColumn>
                </WlContainer>
            </WlContainer>
        )
    }


}


export default connect()(ThumbnailField);




