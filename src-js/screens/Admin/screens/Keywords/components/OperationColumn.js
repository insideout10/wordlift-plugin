import React from 'react';
import { Button, Popconfirm } from 'antd';

function OperationColumn(onConfirm) {
  return {
    title: 'operation',
    dataIndex: 'operation',
    render: (text, record) => {
      return (
        <Popconfirm
          title="Are you sure?"
          onConfirm={() => onConfirm(record.keyword)}
        >
          <Button>Delete</Button>
        </Popconfirm>
      );
    }
  };
}

export default OperationColumn;
