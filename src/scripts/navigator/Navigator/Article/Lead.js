import React from 'react';
import Radium from 'radium';
import color from 'color';

export default class Lead extends React.Component {
  render() {
    return (
      <a href={this.props.link}
        style={[
          styles.base,
          styles[this.props.category]
        ]}>
        {this.props.title}
      </a>
    );
  }
}

//styling for the Entity wrap
var styles = {
  base: {
    //text job
    fontFamily: 'Droid Serif',
    fontSize: '12px',
    fontWeight: '800',
    lineHeight: '16px',
    textDecoration: 'none',
    //colouring
    color: '#000000',
    //sizing the container
    display: 'block',
    width: '100%',
    height: '48x',
    //resetting and fixing the standard margin
    margin: '0',
    marginTop: '8px',
  },
};
