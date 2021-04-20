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
import {addNewThumbnail, removeThumbnail, thumbnailFieldChanged} from "../../actions";
import {ModalFieldLabel} from "../modal-field";


const ModalRepeaterTextFieldRemoveIcon = ({onRemoveListener}) => {
    return (<div onClick={onRemoveListener} className={"wl-modal__thumbnail_field__remove_button"}>
    </div>)
}


const ModalRepeaterTextField = (props) => {

    const {value, onRemoveListener, onChange} = props
    return (
        <WlContainer fullWidth={true} className={"wl-modal__thumbnail_field wl-container--center"}>
            <WlColumn className={"wl-col--width-90 wl-col--less-padding"}>
                <ModalInput value={value} onChange={onChange}/>
            </WlColumn>
            <WlColumn className={"wl-col--width-10 wl-col--less-padding"}>
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
                <ModalFieldLabel title={"THUMBNAIL URL"}
                                 description={__("A URL pointing to the video thumbnail image file.", "wordlift")}/>
                {thumbnails.length > 0 && thumbnails.map((thumbnail, thumbnailIndex) => {
                    return (<ModalRepeaterTextField
                        value={thumbnail}
                        identifier={"thumbnail_url"}
                        onRemoveListener={() => {
                            this.props.dispatch(
                                removeThumbnail({
                                    videoIndex,
                                    thumbnailIndex
                                }))
                        }}
                        onChange={
                            (key, value) => {
                                this.props.dispatch(
                                    thumbnailFieldChanged({
                                        value, thumbnailIndex
                                    })
                                )
                            }

                        }/>)
                })}

                <WlContainer fullWidth={true}>
                    <WlColumn className={"wl-col--width-75"}></WlColumn>
                    <WlColumn className={"wl-col--width-25"}>
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




