/**
 * Components: Arrow Toggle component.
 *
 * @since 3.18.0
 */
/**
 * External dependencies
 */
import React, { Component } from 'react';
import { Table, Icon } from 'antd';
/**
 * Internal dependencies
 */
import Wrapper from './components/Wrapper';
import Link from './components/Link';

import './Keywords.css';
import 'antd/dist/antd.css';

/**
 * @inheritDoc
 */
class Keywords extends Component {

	renderTrendIcon = ( text, record ) => (
		<span>
			<Icon type={ 'POSITIVE' === record.trend ? 'caret-up' : 'caret-down' } />
			{text}
		</span>
	)

	renderLinks = ( text, record ) => (
		<span>
			{
				// Map each entity to an `EntityTile`.
				record.urls
					.map( link => <Link href={link} blank={true} >{link}</Link> )
					.reduce( ( prev, curr ) => [ prev, ', ', curr ] )
			}
		</span>
	)

	tableColumns = [{
		title: 'Keywords',
		dataIndex: 'keyword',
		key: 'keyword',
	}, {
		title: 'Rank',
		key: 'rank',
		dataIndex: 'rank',
		render: this.renderTrendIcon,
	}, {
		title: 'Ranking page',
		dataIndex: 'urls',
		key: 'urls',
		render: this.renderLinks,
	}, {
		title: 'Volume',
		dataIndex: 'volume',
		key: 'volume',
	}];

	/**
	 * @inheritDoc
	 */
	render() {
		return (
			<Wrapper>
				<Table
					dataSource={this.props.data}
					columns={this.tableColumns}
				/>
			</Wrapper>
		);
	}
}

export default Keywords;
