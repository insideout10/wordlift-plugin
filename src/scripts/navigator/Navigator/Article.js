import React from 'react';
import Radium from 'radium';
import color from 'color';

import Entity from './Article/Entity';
import Preview from './Article/Preview';
import Lead from './Article/Lead';
import ReadMore from './Article/ReadMore';
import Thumb from './Article/Thumb';

export default class Article extends React.Component {
  constructor() {
    super();
  }
  render() {
    return (
      <div
        style={[
          styles.base,
        ]}>
        <Entity
          entity={this.props.entity}
          entityLink={this.props.entityLink}
          category={this.props.category} />
        <Thumb
          image={this.props.image}
          link={this.props.link} />
        <Lead
          title={this.props.title}
          link={this.props.link} />
        <Preview
          excerpt={this.props.excerpt}
          link={this.props.link} />
        <ReadMore />
      </div>
    );
  }
}

//styling for the Navigator wrap
var styles = {
  base: {
    display: 'inline-block',
    maxWidth: '182px',
    minWidth: '168px',
    margin: '8px 4px',
  }
};
