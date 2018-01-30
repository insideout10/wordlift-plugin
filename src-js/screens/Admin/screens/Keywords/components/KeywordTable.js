/**
 * Components: Arrow Toggle component.
 *
 * @since 3.18.0
 */
/**
 * External dependencies
 */
import React from 'react';
import { Table, Button, Form } from 'antd';

/**
 * Internal dependencies
 */
import Wrapper from './Wrapper';
import Popup from './popup/Popup';
import KeywordColumn from './KeywordColumn';
import RankColumn from './RankColumn';
import UrlsColumn from './UrlsColumn';
import VolumeColumn from './VolumeColumn';
import OperationColumn from './OperationColumn';

import './KeywordTable.css';
import 'antd/dist/antd.css';

/**
 * @inheritDoc
 */
  function KeywordTable(props) {
    return (
      <Wrapper>
        <Button className="editable-add-btn" onClick={props.onAddClick}>
          Add
        </Button>
        <Table
          dataSource={props.data}
          columns={[
            KeywordColumn,
            RankColumn,
            UrlsColumn,
            VolumeColumn,
            OperationColumn(props.onKeywordDelete)
          ]}
          rowKey={record => record.keyword}
        />

        {props.showPopup ? (
          <Popup heading="Please enter the keyword name">
            <Form
              onSubmit={e => {
                e.preventDefault();
                props.onKeywordSubmit(props.keyword);
              }}
            >
              <input
                type="text"
                name="keyword"
                placeholder="Keyword"
                value={props.keyword}
                onChange={e => props.onKeywordChange(e.target.value)}
              />

              <br />
              <br />

              <Button
                type="primary"
                htmlType="submit"
                disabled={!props.keywordValid}
              >
                Submit
              </Button>
            </Form>
          </Popup>
        ) : null}
      </Wrapper>
    );
  }

export default KeywordTable;
