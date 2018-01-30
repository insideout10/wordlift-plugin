import React from 'react';
import Link from './Link';

import _ from 'lodash';

const UrlsColumn = {
  title: 'Ranking page',
  dataIndex: 'urls',
  key: 'urls',
  render: (text, record) => {
    if (record.urls) {
      return (
        <span>
          {record.urls
            .map(link => (
              <Link href={link} blank={true} key={_.uniqueId()}>
                {link}
              </Link>
            ))
            .reduce((prev, curr) => [prev, ', ', curr])}
        </span>
      );
    }
  }
};

export default UrlsColumn;
