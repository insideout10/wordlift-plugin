/**
 * PropertyListItemComponent : used to display a single
 * property item with the title property help text
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from 'react'
import PropTypes from 'prop-types';
import { connect } from 'react-redux'
import { PROPERTY_ITEM_CATEGORY_CHANGED_ACTION, PROPERTY_ITEM_CRUD_OPERATION_ACTION } from '../actions/actions';
import { TRASH_CATEGORY, ACTIVE_CATEGORY } from './CategoryComponent';

/** Constants to be supplied via actions, and also compared in 
 * the property reducers for making a CRUD Action on the property
 * list.
 */
export const DUPLICATE_PROPERTY = 'duplicate_property'
export const DELETE_PROPERTY_PERMANENT = 'delete_property_permanent'

class PropertyListItemComponent extends React.Component {
    constructor(props) {
        super(props)
    }
    /**
     * Return the options for the trash category.
     */
    returnOptionsForTrashCategory() {
        return <React.Fragment>
            <span className="edit wl-mappings-link">
                <a onClick={ 
                    () => { 
                        this.changeCategoryPropertyItem(
                            this.props.propData.property_id,
                            ACTIVE_CATEGORY
                        )
                    }
                }>
                    Restore
                </a>
                | 
            </span>
            <span className="trash wl-mappings-link">
                <a onClick={
                    ()=> { this.makeCrudOperationOnPropertyId(
                            this.props.propData.property_id,
                            DELETE_PROPERTY_PERMANENT
                    )}
                }>
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
            <span className="edit wl-mappings-link">
                <a onClick={()=> {
                        this.props.switchState(
                            this.props.propData.property_id
                        )}
                    }>
                    Edit
                </a>
                | 
            </span>
            <span className="wl-mappings-link">
                <a title="Duplicate this item"
                   onClick={ 
                       ()=> { this.makeCrudOperationOnPropertyId(
                           this.props.propData.property_id,
                           DUPLICATE_PROPERTY
                       ) 
                    }}
                >
                    Duplicate
                </a> |
            </span>
            <span className="trash wl-mappings-link">
                <a onClick={ 
                    () => {
                        this.changeCategoryPropertyItem(
                            this.props.propData.property_id,
                            TRASH_CATEGORY
                        )
                    }
                }>
                    Trash
                </a>
            </span>
        </React.Fragment>
    }

    /**
     * Render the options based on the mapping list item category.
     * @param {String} category Category which the mapping items belong to 
     */
    renderOptionsBasedOnItemCategory( category ) {
        switch ( category ) {
            case ACTIVE_CATEGORY:
                return this.returnOptionsForActiveCategory()
            case TRASH_CATEGORY:
                return this.returnOptionsForTrashCategory()
        }
    }
    changeCategoryPropertyItem = ( propertyId, category ) => {
        const action = PROPERTY_ITEM_CATEGORY_CHANGED_ACTION
        action.payload = {
            propertyId: propertyId,
            propertyCategory: category
        }
        this.props.dispatch( action )
    }

    makeCrudOperationOnPropertyId = ( propertyId, operationName ) => {
        const action = PROPERTY_ITEM_CRUD_OPERATION_ACTION
        action.payload = {
            propertyId: propertyId,
            operationName: operationName
        }
        console.log( action )
        this.props.dispatch( action )
    }

    render() {
        return (
            <div className="wl-property-list-item wl-container">
                <div className="wl-col">
                    <a className="row-title wl-property-list-item-title">
                        { this.props.propData.propertyHelpText }
                    </a>
                    <div className="row-actions">
                        { 
                            this.renderOptionsBasedOnItemCategory(
                                this.props.choosenCategory
                            )
                        }
                    </div>
                </div>
            </div>
        )
    }
}

PropertyListItemComponent.propTypes = {
    propertyText: PropTypes.string
}

export default connect( )( PropertyListItemComponent )