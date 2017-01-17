/**
 * External dependencies
 */
import React from 'react';
import styled from 'styled-components';

/**
 * Internal dependencies
 */
import Primary from './Tile/Primary';
import Secondary from './Tile/Secondary';
import Trigger from './Tile/Trigger';

const TileWrap = styled.div`
	display: block;
	position: relative;
	box-sizing: border-box;
	overflow: hidden;
	border-radius: 2px;
	margin: 8px auto;
	width: 248px;
	height: 32px;
	font-family: OpenSans;
	background-color: #f5f5f5;
	box-shadow: 0 4px 4px -3px rgba(0,0,0,.25), 0 8px 8px -6px rgba(0,0,0,.25);
	transition: all 150ms ease-out;
	${ props => props.tile.isSelected
	? '&:hover{transform: scale(1)}'
	: '&:hover{transform: scale(1.01)}' };
`;

export default class Tile extends React.PureComponent {

	constructor() {
		super();
		this.select = this.select.bind( this );
	}

	select( e ) {
		this.props.select( e, this.props.index );
	}

	render() {
		return (
			<TileWrap
				onClick={ this.select }
				tile={ this.props.tile } >

				<Primary tile={ this.props.tile } />

				<Secondary
					index={ this.props.index }
					tile={ this.props.tile }
					linker={ this.props.link }
				/>

				<Trigger
					index={ this.props.index }
					tile={ this.props.tile }
					open={ this.props.open }
				/>
			</TileWrap>
		);
	}
}

