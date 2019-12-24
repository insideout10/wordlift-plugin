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

class PropertyListItemComponent extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        return (
            <div className="wl-property-list-item wl-container">
                <div className="wl-col">
                    <a className="row-title wl-property-list-item-title">
                        {this.props.propertyText}
                    </a>
                    <div className="row-actions">
                        <span className="edit wl-mappings-link">
                        <a onClick={()=> this.props.switchState(this.props.propertyIndex)}>
                            Edit
                        </a>
                        | 
                        </span>
                        <span className="wl-mappings-link">
                            <a title="Duplicate this item">Duplicate</a> |
                        </span>
                        <span className="trash wl-mappings-link">
                            <a>Trash</a>
                        </span>
                    </div>
                </div>
            </div>
        )
    }
}

PropertyListItemComponent.propTypes = {
    propertyText: PropTypes.string
}

export default PropertyListItemComponent