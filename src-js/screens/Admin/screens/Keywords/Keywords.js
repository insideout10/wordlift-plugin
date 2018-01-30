/**
 * Components: Arrow Toggle component.
 *
 * @since 3.18.0
 */
/**
 * External dependencies
 */
import React, { Component } from 'react';
import { Table, Button, Form } from 'antd';

/**
 * Internal dependencies
 */
import Wrapper from './components/Wrapper';
import Popup from './components/popup/Popup';
import KeywordColumn from './components/KeywordColumn';
import RankColumn from './components/RankColumn';
import UrlsColumn from './components/UrlsColumn';
import VolumeColumn from './components/VolumeColumn';
import OperationColumn from './components/OperationColumn';

import './Keywords.css';
import 'antd/dist/antd.css';

/**
 * @inheritDoc
 */
class Keywords extends Component {
  constructor(props) {
    super( props );

    this.state = {
      showPopup: false,
      keywordValid: false
    };

  }

  componentDidMount() {
    // To do: find a way to add this in a better way.
    const data = new FormData();
    data.append( 'action', 'load_keywords' );

    fetch( this.props.dataRoute, {
      method: 'POST',
      body: data
    } )
      .then( res => res.json() )
      .then( response => this.setState( { dataSource: response.data } ) );
  }

  handleDelete = key => {
    const dataSource = [ ...this.state.dataSource ];
    this.setState( {
                     dataSource: dataSource.filter( item => item.keyword !== key )
                   } );
  };

  handleAdd = e => {
    e.preventDefault();

    const dataSource = this.state.dataSource;
    const newKeyword = {
      keyword: this.refs.keyword.value,
      trend: 'NEUTRAL',
      rank: 0,
      volume: 0
    };

    // Hide the popup.
    this.togglePopup();

    // To do: find a way to add this in a better way.
    var data = new FormData();
    data.append( 'action', 'add_keyword' );
    data.append( 'keyword', this.refs.keyword.value );

    // To do: implement the add request
    fetch( this.props.dataRoute, {
      method: 'POST',
      body: data
    } )
      .then( res => res.json() )
      .then( response =>
               this.setState( { dataSource: [ ...dataSource, newKeyword ] } )
      );

    this.setState( { keywordValid: false } );
  };

  togglePopup = () => {
    this.setState( {
                     showPopup: ! this.state.showPopup
                   } );
  };

  isKeywordValid = e => {
    const re = /^[\w\d]+$/g;

    if ( e.target.value.length && re.test( e.target.value ) ) {
      this.setState( { keywordValid: true } );
    } else {
      this.setState( { keywordValid: false } );
    }
  };

  /**
   * @inheritDoc
   */
  render() {
    return (
      <Wrapper>
        <Button
          className="editable-add-btn"
          onClick={this.togglePopup.bind( this )}
        >
          Add
        </Button>
        <Table
          dataSource={this.state.dataSource}
          columns={[
            KeywordColumn,
            RankColumn,
            UrlsColumn,
            VolumeColumn,
            OperationColumn
          ]}
          rowKey={record => record.keyword}
        />

        {this.state.showPopup ? (
          <Popup heading="Please enter the keyword name">
            <Form onSubmit={this.handleAdd}>
              <input
                type="text"
                name="keyword"
                placeholder="Keyword"
                ref="keyword"
                onChange={this.isKeywordValid}
              />

              <br />
              <br />

              <Button
                type="primary"
                htmlType="submit"
                disabled={! this.state.keywordValid}
              >
                Submit
              </Button>
            </Form>
          </Popup>
        ) : null}
      </Wrapper>
    );
  }
}

export default Keywords;
