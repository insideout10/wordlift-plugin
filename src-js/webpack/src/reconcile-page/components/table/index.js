/**
 * External dependencies
 */
import React from "react";
/**
 * Internal dependencies
 */
import "./index.scss"


export const Table = function ({children}) {
    return (<table className="wp-list-table widefat fixed striped table-view-list tags">
        <thead>
        <tr>
            <th scope="col" id="name" className="manage-column column-name column-primary desc"
                style={{"width": "50%"}}>
                <span>Tag Content</span>
            </th>
            <th scope="col" id="description" className="manage-column column-description desc" style={{"width": "50%"}}>
                <span>Entity Matches</span>
            </th>
        </tr>
        </thead>
        <tbody>
        {children}
        </tbody>
    </table>)
}



