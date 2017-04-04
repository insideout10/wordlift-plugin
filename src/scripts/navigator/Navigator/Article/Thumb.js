import React from 'react';
import Radium from 'radium';
import color from 'color';

export default class Thumb extends React.Component {
  render() {
    return (
      <a href=""
        style={[
          styles.base,
          styles[this.props.category]
        ]}>
          <img src={this.props.image} alt=""/>
      </a>
    );
  }
}

//styling for the Image wrap
var styles = {
  base: {
    //sizeing and positioning the element
    maring: '0',
    width: '100%',
    height: '120px',
    display: 'block',
  },
};
