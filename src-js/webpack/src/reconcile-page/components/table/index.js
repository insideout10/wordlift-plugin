/**
 * External dependencies
 */
import React from "react";
import {__} from "@wordpress/i18n";
import {connect} from "react-redux";

/**
 * Internal dependencies
 */
import "./index.scss"
import {Sort} from "../sort";
import {sortByPostCount, sortByTermName} from "../../actions";
import {ASC, DESC} from "../../store";


class Table extends React.Component {

    constructor(props) {
        super(props);
        this.postCountSort = this.postCountSort.bind(this)
        this.termNameSort = this.termNameSort.bind(this)
    }

    postCountSort() {
        this.props.dispatch(sortByPostCount({sort: this.props.sortByPostCount === ASC ? DESC : ASC}))
    }

    termNameSort() {
        this.props.dispatch(sortByTermName({sort: this.props.sortByTermName === ASC ? DESC : ASC}))
    }

    render() {
        return (<table className="wp-list-table widefat fixed striped table-view-list tags">
            <thead>
            <tr>
                <th>
                    <a href={"#"} onClick={this.termNameSort}>{__('Sort by name', 'wordlift')}<Sort
                        isAscending={this.props.sortByTermName === ASC}/></a> <a href={"#"} onClick={this.postCountSort}>{__('Sort by post count', 'wordlift')}<Sort
                    isAscending={this.props.sortByPostCount === ASC}/></a>
                </th>
                <th>

                </th>
            </tr>
            <tr>
                <th scope="col" id="name" className="manage-column column-name column-primary desc"
                    style={{"width": "50%"}}>
                    <span>Tag Content</span>
                </th>
                <th scope="col" id="description" className="manage-column column-description desc"
                    style={{"width": "50%"}}>
                    <span>Entity Matches</span>
                </th>
            </tr>
            </thead>
            <tbody>
            {this.props.children}
            </tbody>
        </table>)
    }
}


export default connect(state => ({
    sortByPostCount: state.sortByPostCount,
    sortByTermName: state.sortByTermName
}))(Table);