/**
 * Components: Arrow Toggle component.
 *
 * @since 3.18.0
 */
/**
 * External dependencies
 */
import React, { Component } from 'react';
import { Table, Icon, Button, Popconfirm, Form } from 'antd';
import _ from 'lodash'

/**
 * Internal dependencies
 */
import Wrapper from './components/Wrapper';
import Link from './components/Link';
import Popup from './components/popup/Popup';

import './Keywords.css';
import 'antd/dist/antd.css';

/**
 * @inheritDoc
 */
class Keywords extends Component {

	constructor(props) {
		super(props);

		this.state = {
			showPopup: false,
			keywordValid: false,
		}

		this.columns = [{
			title: 'Keywords',
			dataIndex: 'keyword',
			key: 'keyword',
		}, {
			title: 'Rank',
			key: 'rank',
			dataIndex: 'rank',
			render: (text, record) => {
				return(
					<span>
						<Icon type={ 'POSITIVE' === record.trend ? 'caret-up' : 'caret-down' } />
						{text}
					</span>
				)
			},
			sorter: (a, b) => a.rank - b.rank,
		}, {
			title: 'Ranking page',
			dataIndex: 'urls',
			key: 'urls',
			render: (text, record) => {
				if ( record.urls ) {
					return (
						<span>
							{
								record.urls
									.map( link => <Link href={link} blank={true} key={_.uniqueId()} >{link}</Link> )
									.reduce( ( prev, curr ) => [ prev, ', ', curr ] )
							}
						</span>
					)
				}
			}
		}, {
			title: 'Volume',
			dataIndex: 'volume',
			key: 'volume',
			sorter: (a, b) => a.volume - b.volume,
		}, {
			title: 'operation',
			dataIndex: 'operation',
			render: (text, record) => {
				return (
					<Popconfirm title="Are you sure?" onConfirm={() => this.handleDelete(record.keyword)}>
						<Button>Delete</Button>
					</Popconfirm>
				);
			},
		}];
	}

	componentDidMount(){
		// To do: find a way to add this in a better way.
		var data = new FormData()
		data.append('action', 'load_keywords')

		fetch(
			this.props.dataRoute,
			{
				method: 'POST',
				body: data
			}
		)
		.then( res => res.json() )
		.then( response => this.setState({ 'dataSource': response.data }) );
	}

	handleDelete = (key) => {
		const dataSource = [...this.state.dataSource];
		this.setState({ dataSource: dataSource.filter(item => item.keyword !== key) });
	}

	handleAdd = (e) => {
		e.preventDefault();

		const dataSource = this.state.dataSource;
		const newKeyword = {
			keyword: this.refs.keyword.value,
			trend: 'NEUTRAL',
			rank: 0,
			volume: 0,
		};

		// Hide the popup.
		this.togglePopup()

		// To do: find a way to add this in a better way.
		var data = new FormData()
		data.append('action', 'add_keyword')
		data.append('keyword', this.refs.keyword.value)

		// To do: implement the add request
		fetch(
			this.props.dataRoute,
			{
				method: 'POST',
				body: data
			}
		)
		.then( res => res.json() )
		.then( response => this.setState({ dataSource: [...dataSource, newKeyword] }) );

		this.setState( { keywordValid: false } );
	}

	togglePopup = () => {
		this.setState({
			showPopup: ! this.state.showPopup
		});
	}

	isKeywordValid = (e) => {
		const re = /^[\w\d]+$/g;

		if ( e.target.value.length && re.test( e.target.value ) ) {
			this.setState( { keywordValid: true } );
		} else {
			this.setState( { keywordValid: false } );
		}
	}

	/**
	 * @inheritDoc
	 */
	render() {
		return (
			<Wrapper>
				<Button className="editable-add-btn" onClick={this.togglePopup.bind(this)}>Add</Button>
				<Table
					dataSource={this.state.dataSource}
					columns={this.columns}
					rowKey={record => record.keyword}
				/>

				{this.state.showPopup ?
					<Popup heading='Please enter the keyword name'>
						<Form onSubmit={this.handleAdd}>

							<input type="text" name="keyword" placeholder="Keyword" ref="keyword" onChange={this.isKeywordValid} />

							<br /><br />

							<Button type="primary" htmlType="submit" disabled={!this.state.keywordValid}>
								Submit
							</Button>
						</Form>
					</Popup>
					: null
				}
			</Wrapper>
		);
	}
}

export default Keywords;
