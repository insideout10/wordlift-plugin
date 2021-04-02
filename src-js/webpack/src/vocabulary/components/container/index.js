/**
 * External dependencies
 */
import React from "react";
import {connect} from "react-redux";
import TagList from "../tag-list";
import {WlLoadingAnimation} from "../wl-loading-animation";


class Container extends React.Component {

    render() {
        return  (<React.Fragment>
            <TagList />
            {this.props.isRequestInProgress && <tr>
                <td colSpan={"90%"}>
                    <WlLoadingAnimation/>
                </td>
            </tr>}
        </React.Fragment>)
    }

}



export default connect(state => ({
    isRequestInProgress: state.isRequestInProgress
}))(Container);