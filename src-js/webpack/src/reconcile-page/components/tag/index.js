/**
 * External dependencies
 */
import React from "react";
import Entity from "../entity";
import {connect} from "react-redux";
import { __ } from "@wordpress/i18n";

/**
 * Internal dependencies
 */
import "./index.scss"
import {acceptEntity, markTagAsNoMatch, undo} from "../../actions";

class Tag extends React.Component {

    constructor(props) {
        super(props);

    }

    showActionButtons() {
        return (<div>
            <input type="button" className="button button-primary" value="Accept"
                   onClick={() => {
                       this.props.dispatch(acceptEntity({
                           tagIndex: this.props.tagIndex
                       }))
                   }}/>
            <span className="space-right-1"></span>
            <input type="button" className="button button-secondary" value="Reject"
                   onClick={() => {
                       this.props.dispatch(markTagAsNoMatch({
                           tagIndex: this.props.tagIndex
                       }))
                   }}/>
        </div>)
    }

    render() {

        if (this.props.isHidden) {
            return <React.Fragment/>
        }

        if (this.props.isUndo) {
            return this.renderUndoMode();
        }

        return (
            <React.Fragment>
                <tr>
                    {this.getTagNameColumn()}
                    <td style={{width: "70%"}}>
                        {this.props.entities && this.props.entities.map((entity, index) => (
                            <Entity {...entity} tagIndex={this.props.tagIndex} entityIndex={index} key={entity.entityId}/>
                        ))}
                        {this.showActionButtons()}
                    </td>
                </tr>

            </React.Fragment>
        )
    }


    getTagNameColumn() {
        return <td style={{width: "30%"}}>
            <p className="tag-title" style={{"fontSize": "18px"}}><b>{this.props.tagName}</b>
                <a href={this.props.tagLink} target="_blank"><span
                    className="dashicons dashicons-external"></span></a>
            </p>
            <p>{this.props.tagDescription}</p>
            <small>{__("Posts : ", 'wordlift-plugin')}{this.props.tagPostCount}</small>
        </td>;
    }

    renderUndoMode() {
        return (
            <tr>
                {this.getTagNameColumn()}
                <td>
                    <button className="button button-secondary"
                            onClick={() => {
                                this.props.dispatch(undo({
                                    tagIndex: this.props.tagIndex
                                }))
                            }}> Undo
                    </button>
                </td>
            </tr>
        )
    }
}



const mapStateToItemProps = (_, initialProps) => (state) => {
    return {...state.tags[initialProps.tagIndex]}
}
export default connect(
    mapStateToItemProps
)(Tag)
