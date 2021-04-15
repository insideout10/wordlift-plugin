/**
 * External dependencies
 */
import React from "react";
import {connect} from "react-redux";
/**
 * Internal dependencies
 */
import "./index.scss"
import {WlColumn} from "../../../mappings/blocks/wl-column";
import {WlContainer} from "../../../mappings/blocks/wl-container";
import WlActionButton from "../../../faq/components/wl-action-button";


const vimeoIcon = ({height, width}) => {
    return (
        <svg width={width} height={height} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img"
             aria-hidden="true"
             focusable="false">
            <g>
                <path
                    d="M22.396 7.164c-.093 2.026-1.507 4.8-4.245 8.32C15.323 19.16 12.93 21 10.97 21c-1.214 0-2.24-1.12-3.08-3.36-.56-2.052-1.118-4.105-1.68-6.158-.622-2.24-1.29-3.36-2.004-3.36-.156 0-.7.328-1.634.98l-.978-1.26c1.027-.903 2.04-1.806 3.037-2.71C6 3.95 7.03 3.328 7.716 3.265c1.62-.156 2.616.95 2.99 3.32.404 2.558.685 4.148.84 4.77.468 2.12.982 3.18 1.543 3.18.435 0 1.09-.687 1.963-2.064.872-1.376 1.34-2.422 1.402-3.142.125-1.187-.343-1.782-1.4-1.782-.5 0-1.013.115-1.542.34 1.023-3.35 2.977-4.976 5.862-4.883 2.14.063 3.148 1.45 3.024 4.16z"></path>
            </g>
        </svg>)
}

const youtubeIcon = ({height, width}) => {
    return (<span className="block-editor-block-icon has-colors"
                  style={{"color": "rgb(255, 0, 0)"}}>
        <svg width={width}
             height={height}
             viewBox="0 0 24 24"
             role="img"
             aria-hidden="true"
             focusable="false">
            <path
                d="M21.8 8s-.195-1.377-.795-1.984c-.76-.797-1.613-.8-2.004-.847-2.798-.203-6.996-.203-6.996-.203h-.01s-4.197 0-6.996.202c-.39.046-1.242.05-2.003.846C2.395 6.623 2.2 8 2.2 8S2 9.62 2 11.24v1.517c0 1.618.2 3.237.2 3.237s.195 1.378.795 1.985c.76.797 1.76.77 2.205.855 1.6.153 6.8.2 6.8.2s4.203-.005 7-.208c.392-.047 1.244-.05 2.005-.847.6-.607.795-1.985.795-1.985s.2-1.618.2-3.237v-1.517C22 9.62 21.8 8 21.8 8zM9.935 14.595v-5.62l5.403 2.82-5.403 2.8z"></path></svg></span>)
}

class Video extends React.Component {

    constructor(props) {
        super(props);
    }

    setIcon(video) {
        console.log(video)
        console.log(video.content_url)
        if (video.content_url.includes("youtube.com")) {
            return youtubeIcon({height: 48, width: 48})
        } else if (video.content_url.includes("vimeo.com")) {
            return vimeoIcon({height: 48, width: 48})
        }
        return youtubeIcon({height: 48, width: 48})
    }

    render() {
        const {video} = this.props
        return (
            <React.Fragment>
                <WlContainer>
                    <WlColumn>
                        {youtubeIcon({height: 24, width: 24})}
                    </WlColumn>
                    <WlColumn>
                        {video.name}
                    </WlColumn>
                    <WlColumn>
                        <WlActionButton text={"Edit"} className={"wl-action-button--primary"}/>
                    </WlColumn>
                </WlContainer>

            </React.Fragment>
        )
    }


}


export default connect()(Video);