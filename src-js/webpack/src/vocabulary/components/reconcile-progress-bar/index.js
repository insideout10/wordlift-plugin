/**
 * @since 1.3.0
 * This progressbar shows how many tags are processed / remaining tags in reconcile screen.
 */

/**
 * External dependencies
 */
import React from "react";
/**
 * Internal dependencies
 */
import "./index.scss";
import { getReconcileProgress } from "./api";
import { ProgressBar } from "../progress-bar";

export default class ReconcileProgressBar extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      stats: {
        completed: 0,
        total: 0
      }
    };
    // Start updating progress bar
    this.getStats();
    // Update progress bar every 5 seconds.
    this.interval = setInterval(() => this.getStats(), 5000);
  }

  componentWillUnmount() {
    clearInterval(this.interval);
  }

  getStats() {
    getReconcileProgress().then(data => {
      this.setState({
        stats: data
      });
    });
  }

  render() {
    const stats = this.state.stats;
    const progress = this.calcProgress(stats);
    return (
      <div className={"wl_cmkg_analysis_progress_bar_container"}>
        <div style={{ width: "100%" }}>
          <h3>Reconcile Progress ({stats.completed + "/" + stats.total})</h3>
          <ProgressBar progress={progress} />
        </div>
      </div>
    );
  }

  calcProgress(stats) {
    if (stats.total === 0) {
      return 0;
    }
    if (stats.completed === 0) {
      return 0;
    }
    return (stats.completed / stats.total) * 100;
  }
}
