/**
 * External dependencies.
 */
import React from "react";
import {connect} from "react-redux";
import {__} from "@wordpress/i18n";
/**
 * Internal dependencies.
 */
import Video from "../video";
import {getAllVideos} from "../../actions";
import {WlContainer} from "../../../mappings/blocks/wl-container";
import {WlColumn} from "../../../mappings/blocks/wl-column";


const NoVideos = () => {
    return (
        <WlContainer fullWidth={true}>
            <WlColumn centerText={true} className={"wl-col--width-100"}>
                <p>{ __("No Videos found in Post", "wordlift") }</p>
            </WlColumn>
        </WlContainer>
    )
}

class VideoList extends React.Component {

    constructor(props) {
        super(props);
        this.props.dispatch(getAllVideos())
    }

    render() {

        if ( this.props.videos.length === 0 ) {
            return (<NoVideos />)
        }

        return (
            <React.Fragment>
                {this.props.videos && this.props.videos.map((video, index) => (
                    <Video video={video} videoIndex={index} key={video.id}/>
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