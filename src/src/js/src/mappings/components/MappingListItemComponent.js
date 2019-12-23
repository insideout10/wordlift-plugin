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
import { connect } from 'react-redux'
import PropTypes from 'prop-types';

class MappingListItemComponent extends React.Component {

    constructor(props) {
        super(props)
    }
    constructEditMappingLink = ()=> {
        return '?page=wl_edit_mapping' 
        + '&_wl_edit_mapping_nonce=' 
        + this.props.nonce
        + '&wl_edit_mapping_id='
        + this.props.mappingId
    }
    /**
     * Return the options for the trash category.
     */
    returnOptionsForTrashCategory() {
        return <React.Fragment>
            <span className="edit">
                <a>
                    Move back to active
                </a>
                | 
            </span>
            <span className="trash">
                <a>
                    Delete Permanently
                </a> |
            </span>
        </React.Fragment>
    }
    /**
     * Return the template for the active category.
     */
    returnOptionsForActiveCategory() {
        return <React.Fragment>
            <span class="edit">
                <a href={this.constructEditMappingLink()}>
                Edit
                </a>
                | 
            </span>
            <span>
                <a title="Duplicate this item">
                Duplicate
                </a> |
            </span>
            <span className="trash">
                <a>Trash</a>
            </span>
        </React.Fragment>
    }
    /**
     * Render the options based on the mapping list item category.
     * @param {String} category Category which the mapping items belong to 
     */
    renderOptionsBasedOnItemCategory( category ) {
        switch ( category ) {
            case 'active':
                return this.returnOptionsForActiveCategory()
            case 'trash':
                return this.returnOptionsForTrashCategory()
        }
    }
    render() {
        return  ( 
            <tr>
                <td class="wl-check-column">
                    <input type="checkbox" checked={this.props.isSelected }/>
                </td>
                <td>
                    <a class="row-title wl-mappings-list-item-title">
                        { this.props.mappingData.mapping_title }
                    </a>
                    <div class="row-actions">
                        {
                            this.renderOptionsBasedOnItemCategory( this.props.mappingData.mapping_status )
                        }
                    </div>
                </td>
            </tr>
        )
    }
}

MappingListItemComponent.propTypes = {
    nonce: PropTypes.string,
    mappingData: PropTypes.object
}
export default connect()(MappingListItemComponent)
