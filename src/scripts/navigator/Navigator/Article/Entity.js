import React from 'react';
import Radium from 'radium';
import color from 'color';

export default class Entity extends React.Component {
  render() {
    return (
      <a href={this.props.entityLink}
        style={[
          styles.base,
          styles[this.props.category]
        ]}>
        {this.props.entity}
      </a>
    );
  }
}

//styling for the Entity wrap
var styles = {
  base: {
    //sizeing and positioning the element
    maring: '0',
    width: '100%',
    display: 'block',
    boxSizing: 'border-box',
    //positioning the text
    lineHeight: '24px',
    padding: '0 8px',
    //styling the text
    fontSize: '14px',
    textDecoration: 'underline',
    fontFamily: 'Droid Seif',
    //coloring the header
    backgroundColor: 'transparent',
    color: '#747474',
  },
  //this is the styling for a plain header
  plain: {
    backgroundColor: '#747474',
    color: '#FFFFFF',
  },
  //here starts the styling for category colors
  what: {
    backgroundColor: '#2E92FF',
    color: '#FFFFFF',
  },
  who: {
    backgroundColor: '#BD10E0',
    color: '#FFFFFF',
  },
  where: {
    backgroundColor: '#7ED321',
    color: '#FFFFFF',
  },
  when: {
    backgroundColor: '#F7941D',
    color: '#FFFFFF',
  },
};
