import React from "react";

const withProp = propName => Component =>
  class extends React.Component {
    constructor(props) {
      super(props);

      this.is = this.is.bind(this);
      this.toggle = this.toggle.bind(this);
      this.on = props[`on${this.capitalize(propName)}`] || (() => {});

      this.state = {
        [propName]: props[propName]
      };
    }
    componentDidUpdate(prevProps) {
      if (this.props[propName] !== prevProps[propName]) {
        this.setState({
          [propName]: this.props[propName]
        });
      }
    }
    is(item) {
      return -1 < this.state[propName].indexOf(item.id);
    }
    toggle(item) {
      this.setState(prevState => ({
        [propName]: this.is(item)
          ? prevState[propName].filter(value => item.id !== value)
          : prevState[propName].concat([item.id])
      }));

      // `!this.is(item)` because state hasn't yet changed here.
      this.on(item, !this.is(item));
    }
    capitalize(value) {
      return value.charAt(0).toUpperCase() + value.slice(1);
    }
    render() {
      const methods = {
        [`is${this.capitalize(propName)}`]: this.is,
        [`toggle${this.capitalize(propName)}`]: this.toggle
      };

      return <Component {...this.props} {...methods} />;
    }
  };

const withProps = (...propNames) => Component => {
  return propNames.reduce(
    (accumulator, current) => withProp(current)(accumulator),
    Component
  );
};

export default withProps;
