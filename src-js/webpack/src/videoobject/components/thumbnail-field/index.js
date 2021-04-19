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


const ModalRepeaterTextFieldRemoveIcon = ({onRemoveListener}) => {
    return (<button onClick={onRemoveListener}>
        -
    </button>)
}


const ModalRepeaterTextField = (props) => {

    const {onRemoveListener, onFieldChangeListener, index} = props
    return (
        <WlContainer fullWidth={true}>
            <WlColumn>
                <ModalInput defaultValue={""}/>
            </WlColumn>
            <WlColumn>
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
        const {thumbnails} = this.props
        return (
            <WlContainer rowLayout={true}>
                <p>{__("THUMBNAIL URL", "wordlift")}</p>
                {thumbnails.length > 0 && thumbnails.map((thumbnail) => {
                    return (<ModalRepeaterTextField defaultValue={thumbnail}/>)
                })}
                <WlActionButton className={"wl-action-button--primary"}
                                text={__("Add new", "wordlift")}/>
            </WlContainer>
        )
    }


}


export default connect()(ThumbnailField);




