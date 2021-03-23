/**
 * External dependencies
 */
import React from "react";
/**
 * Internal dependencies
 */
import "./index.scss"
import {Sort} from "../sort";
import {sortByPostCount} from "../../actions";
import {connect} from "react-redux";
import {ASC, DESC} from "../../store";


class Table extends React.Component {

    constructor(props) {
        super(props);
        this.postCountSort = this.postCountSort.bind(this)
    }

    postCountSort() {
        this.props.dispatch(sortByPostCount({sort: this.props.sortByPostCount === ASC ? DESC : ASC}))
    }

    render() {
        return (<table className="wp-list-table widefat fixed striped table-view-list tags">
            <thead>
            <tr>
                <th scope="col" id="name" className="manage-column column-name column-primary desc"
                    style={{"width": "50%"}}>
                    <a href={"#"} onClick={this.postCountSort}><span>Tag Content</span> <Sort
                        isAscending={this.props.sortByPostCount === ASC}/></a>
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
    sortByPostCount: state.sortByPostCount
}))(Table);