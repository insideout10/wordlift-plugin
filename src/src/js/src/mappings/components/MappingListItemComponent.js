/**
 * MappingListItemComponent : it displays the list of mapping items
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react"


export default class MappingListItemComponent extends React.Component {

    constructor(props) {
        super(props)
    }
    render() {
        return  ( 
            <tr>
                <td class="wl-check-column">
                    <input type="checkbox" />
                </td>
                <td>
                    <a class="row-title wl-mappings-list-item-title">
                        { this.props.title }
                    </a>
                    <div class="row-actions">
                        <span class="edit">
                            <a href="?page=wl_edit_mapping">
                                Edit
                            </a>
                            | 
                        </span>
                        <span>
                            <a title="Duplicate this item">
                                Duplicate
                            </a> |
                        </span>
                        <span class="trash">
                            <a>Trash</a>
                        </span>
                    </div>
                </td>
            </tr>
        )
    }
}