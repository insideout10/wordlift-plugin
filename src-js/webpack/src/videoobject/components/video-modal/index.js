import React from "react";
import {connect} from "react-redux";
import {WlModal} from "../../../common/components/wl-modal";

class VideoModal extends React.Component {

    render() {
        return (
            <WlModal shouldOpenModal={this.props.isModalOpened} >
                <p>foo</p>
            </WlModal>
        )
    }


}

const mapStateToProps = (state) => {
    return {isModalOpened: state.isModalOpened}
}

export default connect(
    mapStateToProps
)(VideoModal);