/**
 * External dependencies
 */
import React from 'react';
import styled from 'styled-components';

const PrimaryWrap = styled.div`
	display: block;
	position: absolute;
	left: ${ props => props.tile.isOpen ? '-248px' : 0 };
	top: 0;
	bottom: 0;
	box-sizing: border-box;
	width: 248px;
	height: 32px;
	transition: left 200ms ease;
`;

const Occurrences = styled.div`
	display: inline-block;
	position: relative;
	margin: 8px;
	width: 16px;
	height: 16px;
	border-radius: 2px;
	padding: 2px 0;
	text-align: center;
	font-weight: 600;
	font-size: 12px;
	color: #FFFFFF;
	letter-spacing: -0.21px;
	line-height: 12px;
	user-select: none;
	background-color: ${ props => 0 < props.entity.occurrences.length ? '#2E92FF' : '#c7c7c7' };
`;

const Entity = styled.div`
	display: inline-block;
	position: relative;
	box-sizing: border-box;
	max-width: 200px;
	height: 32px;
	line-height: 32px;
	font-weight: 600;
	font-size: 12px;
	user-select: none;
	color: ${ props => 0 < props.entity.occurrences.length ? '#2E92FF' : '#c7c7c7' };
`;

const Cloud = styled.i`
	display: block;
	position: absolute;
	right: 20px;
	top: 8px;
	font-size: 14px;
	line-height: 1;
	color: #CBCBCB;
	user-select: none;
	transition: opacity 150ms ease;
	opacity: ${ props => 0 < props.entity.occurrences.length ? 1 : 0 }
`;

export default function( props ) {
	return (
		<PrimaryWrap tile={ props.tile } >
			<Occurrences
				entity={ props.entity }
				tile={ props.tile } >
				{ 0 < props.entity.occurrences.length ? props.entity.occurrences.length : '+'}
			</Occurrences>

			<Entity
				entity={ props.entity }
				tile={ props.tile } >
				{ props.entity.label }
			</Entity>

			<Cloud
				entity={ props.entity }
				tile={ props.tile }
				className="fa fa-cloud" >
			</Cloud>
		</PrimaryWrap>
	);
}
