import React from 'react';
import Radium from 'radium';
import color from 'color';

export default class ReadMore extends React.Component {
  render() {
    return (
        <a
          style={[
            styles.base,
          ]}
          href={this.props.link}
        >
          Leggi l'articolo…
        </a>
    );
  }
}

//styling for the ReadMore btn
var styles = {
  base: {
    //size
    display: 'block',
    width: '100%',
    height: '16px',
    //resetting and fixing the standard margin
    margin: '0',
    marginTop: '16px',
    //colouring
    backgroundColor: '#D8D8D8',
    color: '#000000',
    //positioning the text
    textAlign: 'right',
    padding: '0 8px',
    boxSizing: 'border-box',
    //text job
    fontSize: '12px',
    fontFamily: 'Droid Seif',
  }
};
