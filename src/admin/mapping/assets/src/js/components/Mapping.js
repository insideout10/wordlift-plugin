import React, { createContext } from 'react';
import './../../scss/mapping-app.scss';

const MappingConfigurationContext = createContext();

export class Mapping extends React.Component {
	constructor() {
		super();

		this.editControlHandler = this.editControlHandler.bind( this );
		this.addNewMappingHandler = this.addNewMappingHandler.bind( this );
		this.closeCurrentMappingHandler = this.closeCurrentMappingHandler.bind( this );
		this.propertyTextUpdateHandler = this.propertyTextUpdateHandler.bind( this );
		this.bulkSelectHandler = this.bulkSelectHandler.bind( this );
		this.mapRowCheckboxHandler = this.mapRowCheckboxHandler.bind( this );
		this.onPropertySelectChange = this.onPropertySelectChange.bind( this );

		this.state = {
			editControlHandler: this.editControlHandler,
			addNewMappingHandler: this.addNewMappingHandler,
			closeCurrentMappingHandler: this.closeCurrentMappingHandler,
			propertyTextUpdateHandler: this.propertyTextUpdateHandler,
			bulkSelectHandler: this.bulkSelectHandler,
			mapRowCheckboxHandler: this.mapRowCheckboxHandler,
			onPropertySelectChange: this.onPropertySelectChange,

			currentlyEditing: null,
			bulkSelect: false,

			defaultProperties: {
				checked: false,
				property: '',
				fieldType: {
					text: 'Text',
					acfCustomField: 'ACF Custom Field',
				},
				field: '',
				transform: {
					none: 'None',
					add: 'Add',
					subtract: 'subtract',
				},
			},

			savedProperties: [
				{
					property: 'telephone',
					fieldType: 'acfCustomField',
					field: 'Contact Form',
					transform: 'none',
				},
				{
					property: 'description',
					fieldType: 'text',
					field: 'Description',
					transform: 'add',
				},
			],
		};
	}

	editControlHandler( e, savedPropertyItemIndex ) {
		e.preventDefault();

		const editControlId = e.target.dataset;

		switch( Number( editControlId.controlId ) ) {
			case 0:
				this.setState( {
					currentlyEditing: savedPropertyItemIndex,
				} );
				break;

			case 1:
				this.setState( {
					savedProperties: this.state.savedProperties.reduce( ( accumulator, currentItem, currentIndex ) => ( currentIndex === savedPropertyItemIndex ? [ ...accumulator, currentItem, currentItem ] : [ ...accumulator, currentItem ] ), [] ),
				} );
				break;

			case 2:
				this.setState( {
					savedProperties: this.state.savedProperties.filter( ( item, index ) => index !== savedPropertyItemIndex )
				} );
				break;

			default:
				break;
		}
	}

	addNewMappingHandler() {
		this.setState( {
			savedProperties: [
				...this.state.savedProperties,
				Object.assign( {}, this.state.defaultProperties ),
			],
			currentlyEditing: this.state.savedProperties.length,
		} );

	}

	propertyTextUpdateHandler( e, savedPropertyItemIndex ) {
		const savedProperties = [ ...this.state.savedProperties ];
		savedProperties[ savedPropertyItemIndex ].property = e.target.value;

		this.setState( {
			savedProperties: savedProperties
		} )
	}

	closeCurrentMappingHandler( e ) {
		e.preventDefault();

		this.setState( {
			currentlyEditing: null,
		} )
	}

	bulkSelectHandler( e ) {
		const savedProperties = [ ...this.state.savedProperties ];

		this.setState( {
			savedProperties: savedProperties.map( ( item ) => {
				item.checked = e.target.checked;
				return item
			} ),
		} );
	}

	mapRowCheckboxHandler( e, savedPropertyItemIndex ) {
		const savedProperties = [ ...this.state.savedProperties ];

		savedProperties[ savedPropertyItemIndex ].checked = e.target.checked;

		this.setState( { savedProperties } );
	}

	onPropertySelectChange( e, savedPropertyItemIndex ) {
		const savedProperties = [ ...this.state.savedProperties ];
		const selectId = Number( e.target.dataset.selectId );
		const selectValue = e.target.value;

		switch( selectId ) {
			case 0:
				savedProperties[ savedPropertyItemIndex ].fieldType = selectValue;
				this.setState( { savedProperties } );
				break;

			case 1:
				savedProperties[ savedPropertyItemIndex ].field = selectValue;
				this.setState( { savedProperties } );
				break;

			case 2:
				savedProperties[ savedPropertyItemIndex ].transform = selectValue;
				this.setState( { savedProperties } );
				break;
		}
	}

	render() {
		return (
			<MappingConfigurationContext.Provider value={ this.state } >
				<MappingConfiguration />
			</MappingConfigurationContext.Provider>
		);
	}
}

const MappingConfiguration = () => (
	<MappingConfigurationContext.Consumer>
		{ ( { savedProperties, addNewMappingHandler, currentlyEditing, bulkSelectHandler } ) => (
			<div className="wl-mapping__container">
				<div className="wl-mapping__bulk-actions">
					<div className="wl-mapping__bulk-actions-checkbox">
						<input type="checkbox" onChange={ ( e ) => bulkSelectHandler( e ) }></input>
					</div>
					<div className="wl-mapping__property-headline">Properties</div>
				</div>
				<>
				{ savedProperties.map( ( savedPropertyItem, savedPropertyItemIndex ) => (
					<MappingRow key={ savedPropertyItemIndex } mappingRowData={ savedPropertyItem } savedPropertyItemIndex={ savedPropertyItemIndex } />
				) ) }
				<div className="wl-mapping__bulk-action-controls">
					<select>
						<option>Bulk Actions</option>
						<option value="delete">Delete</option>
					</select>
					<input name="wl-mapping-apply-bulk-filter" className="button button-primary" type="submit" value="Apply" />
				</div>
				{ null === currentlyEditing && <button className="button wl-mapping__add-mapping" type="button" onClick={ addNewMappingHandler }>Add Mapping</button> }
				</>
			</div>
		) }
	</MappingConfigurationContext.Consumer>
);

const MappingRow = ( { mappingRowData, savedPropertyItemIndex } ) => {
	return ( <MappingConfigurationContext.Consumer>
		{ ( { editControlHandler, currentlyEditing, defaultProperties, propertyTextUpdateHandler, closeCurrentMappingHandler, mapRowCheckboxHandler, onPropertySelectChange } ) => {
			const editControlStyle = ( currentlyEditing === savedPropertyItemIndex ) ? { display: 'block' } : { display: 'none' };

			return ( <div className="wl-mapping__unit">
				<div className="wl-mapping__checkbox">
					<input checked={ !! mappingRowData.checked } onChange={ ( e ) => mapRowCheckboxHandler( e, savedPropertyItemIndex ) } type="checkbox" />
				</div>

				<div className="wl-mapping__edit-panel">
					<div className="wl-mapping__title">{ mappingRowData.property }</div>

					<div className="wl-mapping__expanded-controls" style={ editControlStyle }>
						<div className="wl-mapping__property-control">
							<label>Property</label>
							<input type="text" required defaultValue={ mappingRowData.property } onChange={ ( e ) => propertyTextUpdateHandler( e, savedPropertyItemIndex ) } />
						</div>

						<div className="wl-mapping__property-control">
							<label>Field Type</label>
							<select data-select-id={ 0 } value={ mappingRowData.fieldType } onChange={ ( e ) => onPropertySelectChange( e, savedPropertyItemIndex ) }>
								{ Object.keys( defaultProperties.fieldType ).map( ( key, index ) => <option key={ index } value={ key }>{ defaultProperties.fieldType[ key ] }</option> ) }
							</select>
						</div>

						<div className="wl-mapping__property-control">
							<label>Field</label>
							<input data-select-id={ 1 } type="text" required value={ mappingRowData.field } onChange={ ( e ) => onPropertySelectChange( e, savedPropertyItemIndex ) } />
						</div>

						<div className="wl-mapping__property-control">
							<label>Transform</label>
							<select data-select-id={ 2 } value={ mappingRowData.transform } onChange={ ( e ) => onPropertySelectChange( e, savedPropertyItemIndex ) }>
								{ Object.keys( defaultProperties.transform ).map( ( key, index ) => <option key={ index } value={ key }>{ defaultProperties.transform[ key ] }</option> ) }
							</select>
						</div>
					</div>

					{ currentlyEditing !== savedPropertyItemIndex && <div className="wl-mapping__edit-controls">
						<a onClick={ ( e ) => editControlHandler( e, savedPropertyItemIndex ) } data-control-id={ 0 } href="#">Edit</a>
						<a onClick={ ( e ) => editControlHandler( e, savedPropertyItemIndex ) } data-control-id={ 1 } href="#">Duplicate</a>
						<a onClick={ ( e ) => editControlHandler( e, savedPropertyItemIndex ) } data-control-id={ 2 } href="#">Delete</a>
					</div> }

					{ currentlyEditing === savedPropertyItemIndex && <a href="#" className="wl-mapping__close-mapping" onClick={ ( e ) => { closeCurrentMappingHandler( e ) } }>Close</a> }
				</div>
			</div> ) } }
	</MappingConfigurationContext.Consumer> )
};
