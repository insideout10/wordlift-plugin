import React, { createContext } from 'react';

const MappingConfigurationContext = createContext();

export class Mapping extends React.Component {
	constructor() {
		super();

		this.editControlHandler = this.editControlHandler.bind( this );
		this.addNewMappingHandler = this.addNewMappingHandler.bind( this );

		this.state = {
			editControlHandler: this.editControlHandler,
			addNewMappingHandler: this.addNewMappingHandler,

			currentlyEditing: null,

			defaultProperties: {
				property: '',
				fieldType: '',
				field: '',
				transform: '',
			},

			savedProperties: [
				{
					property: 'telephone',
					fieldType: 'custom-field',
					field: 'Contact Form',
					transform: 'none',
				},
				{
					property: 'description',
					fieldType: 'text',
					field: 'Description',
					transform: 'none',
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
				} )
				break;

			default:
				break;
		}
	}

	addNewMappingHandler() {
		let savedProperties = this.state.savedProperties;

		this.setState( {
			savedProperties: [
				...this.state.savedProperties,
				this.state.defaultProperties,
			],
			currentlyEditing: savedProperties.length,
		} );
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
		{ ( { savedProperties, addNewMappingHandler } ) => (
			<>
			{ savedProperties.map( ( savedPropertyItem, savedPropertyItemIndex ) => (
				<div className="wl-mapping-unit" key={ savedPropertyItemIndex }>
					<MappingRow mappingRowData={ savedPropertyItem } savedPropertyItemIndex={ savedPropertyItemIndex } />
				</div>
			) ) }
			<button type="button" onClick={ addNewMappingHandler }>Add Mapping</button>
			</>
		) }
	</MappingConfigurationContext.Consumer>
);

const MappingRow = ( { mappingRowData, savedPropertyItemIndex } ) => (
	<MappingConfigurationContext.Consumer>
		{ ( { editControlHandler, currentlyEditing } ) => (
			<div className="wl-mapping-unit">
				<div className="wl-mapping-checkbox">
					<input type="checkbox" />
				</div>

				<div className="wl-mapping-edit-panel">
					<div className="wl-mapping-title">{ mappingRowData.property }</div>

					{ currentlyEditing === savedPropertyItemIndex && ( <div className="wl-mapping-expanded-controls">
						<div className="wl-mapping-property-control">
							<label>Property</label>
							<input defaultValue={ mappingRowData.property } />
						</div>

						<div className="wl-mapping-property-control">
							<label>Field</label>
							<input defaultValue={ mappingRowData.field } />
						</div>
					</div> ) }

					<div className="wl-mapping-edit-controls">
						<a onClick={ ( e ) => editControlHandler( e, savedPropertyItemIndex ) } data-control-id={ 0 } href="#">Edit</a>
						<a onClick={ ( e ) => editControlHandler( e, savedPropertyItemIndex ) } data-control-id={ 1 } href="#">Duplicate</a>
						<a onClick={ ( e ) => editControlHandler( e, savedPropertyItemIndex ) } data-control-id={ 2 } href="#">Deletion</a>
					</div>
				</div>
			</div>
		) }
	</MappingConfigurationContext.Consumer>
);

