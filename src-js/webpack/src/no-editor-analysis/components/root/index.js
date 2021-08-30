import React from "react";
import {WlLoadingAnimation} from "../../../vocabulary/components/wl-loading-animation";
import App from "../../../Edit/components/App";
import {connect} from "react-redux";

class Root extends React.Component {
    render()
    {
        return (
            <React.Fragment>
                {this.props.analysisRunning && <WlLoadingAnimation/>}
                {!this.props.analysisRunning && <App/>}
            </React.Fragment>
        )
    }

}

export default connect(state => ({
    analysisRunning: state.analysisRunning
}))(Root);
