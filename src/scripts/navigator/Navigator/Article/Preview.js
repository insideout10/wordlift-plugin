import React from 'react';
import Radium from 'radium';
import color from 'color';

export default class Preview extends React.Component {
  render() {
    return (
      <p
        style={[
          styles.base,
        ]}>
        {this.props.excerpt}
      </p>
    );
  }
}

//styling for the Entity wrap
var styles = {
  base: {
    //text job
    fontFamily: 'Droid Serif',
    fontSize: '10px',
    lineHeight: '12px',
    //colouring
    color: '#000000',
    //sizing the container
    width: '100%',
    height: '48x',
    //resetting and fixing the standard margin
    margin: '0',
    marginTop: '8px',
  }
};
