/**
 * External dependencies
 */
import React from "react";
/**
 * Internal dependencies
 */
import "./index.scss"
import {Sort} from "../sort";
import {sortByPostCount, sortByTermName} from "../../actions";
import {connect} from "react-redux";
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
                    <a href={"#"} onClick={this.termNameSort}>Sort by Name<Sort
                        isAscending={this.props.sortByTermName === ASC}/></a>
                </th>
                <th>
                    <a href={"#"} onClick={this.postCountSort}>Sort by post count<Sort
                        isAscending={this.props.sortByPostCount === ASC}/></a>
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