import React from 'react';

import { Rules } from './Rules';
export const MappingContext = React.createContext();

import { Modal } from './Rules';

export class MappingApp extends React.Component {
	constructor() {
		super();

		this.andButtonHandler = this.andButtonHandler.bind( this );
		this.orButtonHandler = this.orButtonHandler.bind( this );

		this.state = {
			and: [1],
			or: [0],
			andButtonHandler: this.andButtonHandler,
			orButtonHandler: this.orButtonHandler,
		}
	}

	andButtonHandler() {

		let and = this.state.and[0] + 1;

		this.setState( {
			and: [ and ]
		} );
	}

	orButtonHandler() {

	}

	render() {
		return (
			<MappingContext.Provider value={ this.state }>
				<Rules />
			</MappingContext.Provider>
		);
	}
}
