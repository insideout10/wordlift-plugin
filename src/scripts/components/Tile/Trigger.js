/**
 * External dependencies
 */
import React from 'react';
import styled from 'styled-components';

const TriggerWrap = styled.div`
	display: ${ props => 0 < props.entity.occurrences.length ? 'block' : 'none' };
	transition: opacity 150ms ease;
	opacity: ${ props => 0 < props.entity.occurrences.length ? 1 : 0 }
	position: absolute;
	right: 0;
	top: 0;
	bottom: 0;
	box-sizing: border-box;
	width: 16px;
	height: 32px;
	padding: 8px 4px;
	background-color: #F1F1F1;
`;

const Arrow = styled.div`
	display: block;
	width: 8px;
	height: 8px;
	border-top: 8px solid transparent;
	border-bottom: 8px solid transparent;
	border-left: 8px solid #7D7D7D;
	border-radius: 16px;
	transition: transform 150ms ease;
	transform: rotate( ${ props => props.tile.isOpen ? 180 : 0 }deg );
	&:hover {
		border-left-color: #FCCD34;
	} 
`;

export default class Trigger extends React.PureComponent {

	constructor() {
		super();

		this.open = this.open.bind( this );
	}

	open( e ) {
		this.props.open( e, this.props.index );
	}

	render() {
		return (
			<TriggerWrap
				entity={ this.props.entity }
				tile={ this.props.tile }
				onClick={ this.open } >
				<Arrow tile={ this.props.tile } />
			</TriggerWrap>
		);
	}
}
