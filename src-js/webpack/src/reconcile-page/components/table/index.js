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

import {ASC, DESC} from "../../store";
import {sortByPostCountAsc, sortByPostCountDesc, sortByTermNameAsc, sortByTermNameDesc} from "../../actions";


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
                    {__('Sort By:', 'wordlift')} &nbsp;
                    {__('Name', 'wordlift')}
                    <Sort sortAscHandler={() => this.props.dispatch(sortByTermNameAsc())}
                          sortDescHandler={() => this.props.dispatch(sortByTermNameDesc())}/>&nbsp;
                    {__('Post Count', 'wordlift')}
                    <Sort sortAscHandler={() => this.props.dispatch(sortByPostCountAsc())}
                          sortDescHandler={() => this.props.dispatch(sortByPostCountDesc())}/>
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