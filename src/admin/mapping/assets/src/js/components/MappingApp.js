import React from 'react';

import { Rules } from './Rules';
export const MappingContext = React.createContext();

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

	andButtonHandler( event, setNumber ) {
		let and          = this.state.and;
		and[ setNumber ] = and[ setNumber ] + 1;

		this.setState( {
			and: and,
		} );
	}

	orButtonHandler() {
		this.setState( {
			and: [...this.state.and, 1 ]
		} )
	}

	render() {
		return (
			<MappingContext.Provider value={ this.state }>
				<Rules />
			</MappingContext.Provider>
		);
	}
}
