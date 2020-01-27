/**
 * External dependencies
 */
import React from "react";

/**
 * WordPress dependencies
 */
import { dispatch, select } from "@wordpress/data";
import { Panel, PanelBody, PanelRow, TextControl, Button } from "@wordpress/components";

const wordlift = window["wordlift"];

class SynonymsPanel extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      altLabels: []
    };
    this.addSynomym = this.addSynomym.bind(this);
    this.deleteSynomym = this.deleteSynomym.bind(this);
  }

  componentDidMount() {
    let altLabels = select("core/editor").getEditedPostAttribute("meta")["_wl_alt_label"];
    if (altLabels) {
      this.setState({
        altLabels
      });
    }
  }

  addSynomym() {
    // Add a new synonym only if the last is not empty
    if (this.state.altLabels.length === 0) {
      this.setState({
        altLabels: [...this.state.altLabels, ""]
      });
    } else if (this.state.altLabels[this.state.altLabels.length - 1].trim() !== "") {
      this.setState({
        altLabels: [...this.state.altLabels, ""]
      });
    }
  }

  deleteSynomym(indexToDelete) {
    let altLabels = this.state.altLabels.filter((item, index) => index !== indexToDelete);
    this.setState(
      {
        altLabels
      },
      () => dispatch("core/editor").editPost({ meta: { _wl_alt_label: this.state.altLabels } })
    );
  }

  changeSynomym(indexToSet, valueToSet) {
    let altLabels = [...this.state.altLabels];
    altLabels[indexToSet] = valueToSet;
    this.setState(
      {
        altLabels
      },
      () => dispatch("core/editor").editPost({ meta: { _wl_alt_label: this.state.altLabels } })
    );
  }

  render() {
    return wordlift.currentPostType === "entity" ? (
      <Panel>
        <PanelBody title="Synomyms" initialOpen={false}>
          {this.state.altLabels.map((item, index) => (
            <PanelRow>
              <TextControl onChange={value => this.changeSynomym(index, value)} value={item} />
              <Button isDefault onClick={() => this.deleteSynomym(index)}>
                Delete
              </Button>
            </PanelRow>
          ))}
          <PanelRow>
            <Button isDefault onClick={this.addSynomym}>
              Add Synomym
            </Button>
          </PanelRow>
        </PanelBody>
      </Panel>
    ) : null;
  }
}

export default SynonymsPanel;
