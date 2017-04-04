import React from 'react';
import Radium from 'radium';
import color from 'color';

import Article from './Navigator/Article';

export default class Navigator extends React.Component {
  render() {
    return (
    <div
      style={[
        styles.base,
        styles[this.props.kind]
      ]}>
      <Article
        link={"http://www.google.com"}
        category={"who"}
        entity={"Entita di test"}
        image={"http://www.fillmurray.com/182/120"}
        title={"Accordo per un cessate il fuoco in Siria"}
        excerpt={"Le grandi potenze coinvolte nella guerra in Sirvia, riunite a Monaco di Baviera, hanno raggiunto un accordo per una tregua, che dovrebbe …"}
        />
      <Article
        link={"http://www.google.com"}
        category={"plain"}
        entity={"Entita di test"}
        image={"http://www.fillmurray.com/182/120"}
        title={"Accordo per un cessate il fuoco in Siria"}
        excerpt={"Le grandi potenze coinvolte nella guerra in Sirvia, riunite a Monaco di Baviera, hanno raggiunto un accordo per una tregua, che dovrebbe …"}
        />
      <Article
        link={"http://www.google.com"}
        category={"what"}
        entity={"Entita di test what"}
        image={"http://www.fillmurray.com/182/120"}
        title={"Accordo per un cessate il fuoco in Siria"}
        excerpt={"Le grandi potenze coinvolte nella guerra in Sirvia, riunite a Monaco di Baviera, hanno raggiunto un accordo per una tregua, che dovrebbe …"}
        />
      <Article
        link={"http://www.google.com"}
        entity={"Entita di test"}
        image={"http://www.fillmurray.com/182/120"}
        title={"Accordo per un cessate il fuoco in Siria"}
        excerpt={"Le grandi potenze coinvolte nella guerra in Sirvia, riunite a Monaco di Baviera, hanno raggiunto un accordo per una tregua, che dovrebbe …"}
        />
    </div>
    );
  }
}

//styling for the Navigator wrap
var styles = {
  base: {
    display: 'table',
    position: 'relative',
    margin: '1em auto'
  }
}
