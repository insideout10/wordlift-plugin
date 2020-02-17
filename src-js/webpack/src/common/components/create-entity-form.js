/**
 * External dependencies
 */
import React from "react";

/**
 * Internal dependencies
 */
import { Button } from "@wordpress/components";

export default class CreateEntityForm extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      category: window["_wlEntityTypes"][0].slug,
      description: ""
    };
    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  handleChange(event) {
    const target = event.target;
    const value = target.type === "checkbox" ? target.checked : target.value;
    const name = target.name;

    this.setState({
      [name]: value
    });
  }

  handleSubmit(event) {
    this.props.onSubmit(this.state);
    event.preventDefault();
  }

  componentDidUpdate(prevProps) {
    if (this.props.value !== prevProps.value) {
      this.setState({
        label: this.props.value
      });
    }
  }

  render() {
    return this.props.showCreate ? (
      <form onSubmit={this.handleSubmit}>
        <div>
          Category
          <select name="category" style={{ width: "100%" }} value={this.state.category} onChange={this.handleChange}>
            {global["_wlEntityTypes"].map(item => (
              <option value={item.uri}>{item.label}</option>
            ))}
          </select>
        </div>
        <div>
          Description
          <textarea
            name="description"
            rows="5"
            style={{ width: "100%" }}
            value={this.state.description}
            onChange={this.handleChange}
          />
        </div>
        <div>
          <Button isPrimary type="submit" style={{ marginRight: "5px" }}>
            Create Entity
          </Button>
          <Button isDefault onClick={() => this.props.onCancel()}>
            Cancel
          </Button>
        </div>
      </form>
    ) : (
      ""
    );
  }
}
