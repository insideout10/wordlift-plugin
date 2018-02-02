import React from 'react';
import { Icon } from 'antd';

const RankColumn = {
  title: 'Rank',
  key: 'rank',
  dataIndex: 'rank',
  render: (text, record) => {
    return (
      <span>
        {'stable' === record.trend ? (
          <span>=</span>
        ) : (
          <Icon
            type={'positive' === record.trend ? 'caret-up' : 'caret-down'}
          />
        )}
        {text}
      </span>
    );
  },
  sorter: (a, b) => a.rank - b.rank
};

export default RankColumn;
