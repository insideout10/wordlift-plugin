/* globals wp, wordlift */
/**
 * External dependencies.
 */
import React from "react";

const { Button } = wp.components;

class CreateEntityForm extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      category: wordlift.types[0].slug,
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
    this.props.createEntity(this.state);
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
            {wordlift.types.map(item => (
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

export default CreateEntityForm;
