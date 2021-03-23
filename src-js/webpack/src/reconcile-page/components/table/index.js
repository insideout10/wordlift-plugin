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


class Table extends React.Component {

    constructor(props) {
        super(props);
        this.postCountSort = this.postCountSort.apply(this)
    }

    postCountSort() {
        this.props.dispatch(sortByPostCount())
    }

    render() {
        return (<table className="wp-list-table widefat fixed striped table-view-list tags">
            <thead>
            <tr>
                <th scope="col" id="name" className="manage-column column-name column-primary desc"
                    style={{"width": "50%"}}>
                    <a href={"#"}><span>Tag Content</span> <Sort onClick={this.postCountSort}/></a>
                </th>
                <th scope="col" id="description" className="manage-column column-description desc" style={{"width": "50%"}}>
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



export default connect()(Table);