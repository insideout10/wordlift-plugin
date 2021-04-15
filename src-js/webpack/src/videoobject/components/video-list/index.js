/**
 * External dependencies.
 */
import React from "react";
import {connect} from "react-redux";

class VideoList extends React.Component {

    constructor(props) {
        super(props);
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

const mapStateToItemProps = (_, initialProps) => (state) => {
    return {videos: state.videos}
}

export default connect({
    mapStateToItemProps
})(VideoList);