import React from 'react';
import { Button, Popconfirm } from 'antd';

const OperationColumn = {
  title: 'operation',
  dataIndex: 'operation',
  render: (text, record) => {
    return (
      <Popconfirm
        title="Are you sure?"
        onConfirm={() => this.handleDelete(record.keyword)}
      >
        <Button>Delete</Button>
      </Popconfirm>
    );
  }
};

export default OperationColumn;
