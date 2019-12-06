import React from "react"
export default class MappingListItemComponent extends
React.Component {
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
                        
                    </a>
                    <div class="row-actions">
                        <span class="edit">
                            <a>Edit</a>
                            | 
                        </span>
                        <span>
                            <a title="Duplicate this item">Duplicate</a> |
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