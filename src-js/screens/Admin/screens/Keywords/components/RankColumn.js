import React from 'react';
import { Icon } from 'antd';

const RankColumn = {
  title: 'Rank',
  key: 'rank',
  dataIndex: 'rank',
  render: (text, record) => {
    return (
      <span>
        <Icon type={'POSITIVE' === record.trend ? 'caret-up' : 'caret-down'} />
        {text}
      </span>
    );
  },
  sorter: (a, b) => a.rank - b.rank
};

export default RankColumn;
