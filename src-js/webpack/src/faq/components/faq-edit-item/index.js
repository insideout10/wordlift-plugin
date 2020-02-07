/**
 * FaqEditItem for the faq item.
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * external dependencies
 */
import React from "react";
import {connect} from "react-redux"

/**
 * Internal dependencies.
 */
import FaqEditButtonGroup from "../faq-edit-button-group";
import {WlContainer} from "../../../mappings/blocks/wl-container";
import {WlColumn} from "../../../mappings/blocks/wl-column";

class FaqEditItem extends React.Component{
    render() {
        return (
            <React.Fragment>
                <b>{this.props.title}</b>
                <br/><br/>
                <WlContainer>
                    <WlColumn className={"wl-col--width-100 wl-col--less-padding"}>
                        <textarea cols={25} rows={3} value={this.props.value}/>
                    </WlColumn>
                </WlContainer>
                <FaqEditButtonGroup />
            </React.Fragment>
        )
    }
}

export default connect()(FaqEditItem)