/**
 * External dependencies
 */
import React from 'react';

import Lead from './Lead';

class Accordion extends React.Component {
    constructor(props) {
        super(props);
        this.state = {open: props.open};

        this.switch = this.switch.bind(this);
    }

    switch() {
        this.setState((prevState) => ({
            open: !prevState.open
        }));
    }

    render() {
        return (
            <div>
                <div className="wl-tab-lead" onClick={this.switch}>
                    <div className="wl-tab-lead-wrap">
                        <h1 className="wl-tab-lead-text">{this.props.label}</h1>
                        <Lead className="wl-tab-lead-text wl-tab-lead-btn" open={this.state.open} />
                    </div>
                </div>
                <div className="wl-tab-wrap" style={{display: this.state.open ? 'block' : 'none'}}>
                    {this.props.children}
                </div>
            </div>
        );
    }

}

export default Accordion;
