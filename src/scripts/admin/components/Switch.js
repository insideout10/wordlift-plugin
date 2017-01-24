/**
 * External dependencies
 */
import React from 'react';
import styled from 'styled-components';

const SwitchBg = styled.div`
	display: inline-block;
	position: relative;
	box-sizing: border-box;
	width: 24px;
	height: 16px;
	background: ${ props => props.link ? '#7ED321' : '#C7C7C7' };
	transition: background 200ms ease;
	border-radius: 10px;
`;

const SwtichBullet = styled.div`
	display: inline-block;
	position: absolute;
	top: 2px;
	left: ${ props => props.link ? 10 : 2 }px;
	transition: left 150ms ease;
	width: 12px;
	height: 12px;
	background: #FFFFFF;
	border-radius: 50%;
`;

export default function( props ) {
	return (
		<SwitchBg link={ props.link } >
			<SwtichBullet link={ props.link } />
		</SwitchBg>
	);
}
