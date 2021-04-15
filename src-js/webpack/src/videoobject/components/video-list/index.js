/**
 * External dependencies.
 */
import React from "react";
import {connect} from "react-redux";

/**
 * Internal dependencies.
 */
import Video from "../video";
import {getAllVideos} from "../../actions";

class VideoList extends React.Component {

    constructor(props) {
        super(props);
        this.props.dispatch(getAllVideos())
    }

    render() {
        return (
            <React.Fragment>
                {this.props.videos && this.props.videos.map((video, index) => (
                    <Video video={video}/>
                ))}
            </React.Fragment>)
    }


}

const mapStateToProps = (state) => {
    return {videos: state.videos}
}

export default connect(
    mapStateToProps
)(VideoList);