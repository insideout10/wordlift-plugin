/**
 * External dependencies.
 */
import React from "react";
/**
 * Internal dependencies.
 */
import "./index.scss"

export class WlLoadingAnimation extends React.Component {
	render() {
		return (
			<React.Fragment>
				<div className={`wl-spinner wl-spinner--running`}>
					<svg
						transform-origin="10 10"
						className="wl-spinner__shape wl-spinner__shape--circle"
					>
						<circle
							cx="10"
							cy="10"
							r="6"
							className="wl-spinner__shape__path"
						></circle>
					</svg>
					<svg
						transform-origin="10 10"
						className="wl-spinner__shape wl-spinner__shape--rect"
					>
						<rect
							x="4"
							y="4"
							width="12"
							height="12"
							className="wl-spinner__shape__path"
						></rect>
					</svg>
					<svg
						transform-origin="10 10"
						className="wl-spinner__shape wl-spinner__shape--hexagon"
					>
						<polygon
							points="3,10 6.5,4 13.4,4 16.9,10 13.4,16 6.5,16"
							className="wl-spinner__shape__path"
						></polygon>
					</svg>
				</div>
			</React.Fragment>
		);
	}
}
