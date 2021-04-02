/**
 * External dependencies
 */
import React from "react";
/**
 * Internal dependencies
 */
import "./index.scss"
import {getAnalysisStats, restartAnalysis, startBackgroundAnalysis, stopBackgroundAnalysis} from "./api";
import {ProgressBar} from "../progress-bar";


export default class AnalysisProgressBar extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            stats: {
                index: 0,
                count: 0
            },
            isRequestInProgress: false
        }
        this.buttonClickListener = this.buttonClickListener.bind(this)
        this.restartClickListener = this.restartClickListener.bind(this)
        // Start updating progress bar
        this.getStats()
        // Update progress bar every 5 seconds.
        this.interval = setInterval(() => this.getStats(), 5000);
    }

    componentWillUnmount() {
        clearInterval(this.interval)
    }

    getStats() {
        this.setState({isRequestInProgress: true})
        getAnalysisStats(this.props.apiConfig)
            .then((data) => {
                this.setState({
                    stats: data,
                    isRequestInProgress: false
                })
            })
    }

    buttonClickListener() {
        this.setState({isRequestInProgress: true})
        if (this.isAnalysisRunning(this.state.stats)) {
            stopBackgroundAnalysis(this.props.apiConfig)
                .then(() => {
                    this.updateAnalysisState('stopped');
                })
        } else {
            startBackgroundAnalysis(this.props.apiConfig)
                .then(() => {
                    this.updateAnalysisState('started');
                })
        }
    }

    updateAnalysisState(analysisState) {
        this.setState((prevState) => ({
            ...prevState,
            stats: {
                ...prevState.stats,
                state: analysisState
            }
        }))
    }

    render() {
        const stats = this.state.stats
        let progress = this.calcProgress(stats);
        if ( progress > 100 ) {
            progress = 100
        }
        return (
            <div className={"wl_cmkg_analysis_progress_bar_container"}>
                <div style={{width: "90%"}}>
                    <h3>Analysis background task ({stats.index + "/" + stats.count})</h3>
                    <br/>
                    <ProgressBar progress={progress}/>
                </div>
                <div style={{width: "10%"}}>
                   <span style={{cursor: "pointer", fontSize: "30px", marginTop: "10px"}}
                         className={this.getIconName(stats)}
                         onClick={this.buttonClickListener}/>
                    <span style={{cursor: "pointer", fontSize: "30px", marginTop: "10px", marginLeft: "20px"}}
                          className={"dashicons dashicons-image-rotate"}
                          title={"Restart Analysis"}
                          onClick={this.restartClickListener}/>
                </div>
            </div>
        )
    }

    getIconName(stats) {
        // check if we need to disable them.
        // if (stats.count === 0) {
        //     iconName += " wl_cmkg_icon--disabled"
        // }
        return this.isAnalysisRunning(stats)
            ? 'dashicons dashicons-controls-pause' : 'dashicons dashicons-controls-play'
    }

    isAnalysisRunning(stats) {
        return stats.state === "started";
    }

    calcProgress(stats) {
        if (stats.count === 0) {
            return 0;
        }
        if (stats.index === 0) {
            return 0;
        }
        return (stats.index / stats.count) * 100
    }

    restartClickListener() {
        const result = confirm("Restarting analysis will remove the previous results, are you sure you want to proceed ? ")
        if (result === true) {
            // send the restart request.
            restartAnalysis(this.props.apiConfig).then(() => {
                // update stats after restart
                this.getStats()
            });
        }
    }
}