/**
 * External dependencies
 */
import React from "react";

/**
 * WordPress dependencies
 */
import { withSelect, withDispatch } from "@wordpress/data";
import { compose } from "@wordpress/compose";
import { Panel, PanelBody, PanelRow, TextControl, Button, IconButton } from "@wordpress/components";

const wordlift = window["wordlift"];

let SynonymsPanel = props =>
  wordlift.currentPostType === "entity" ? (
    <Panel>
      <PanelBody title="Synonyms" intialOpen={false}>
        {props.altLabels.map((altLabel, altLabelN, altLabels) => (
          <PanelRow>
            <TextControl
              value={altLabel}
              onChange={value => {
                let altLabelsModified = [...altLabels];
                altLabelsModified[altLabelN] = value;
                props.onMetaFieldChange(altLabelsModified);
              }}
            />
            <IconButton
              icon="trash"
              label="Delete"
              onClick={() => {
                let altLabelsModified = altLabels.filter((item, index) => index !== altLabelN);
                props.onMetaFieldChange(altLabelsModified);
              }}
            />
          </PanelRow>
        ))}
        <PanelRow>
          <Button
            isDefault
            onClick={() => {
              let altLabelsModified = [...props.altLabels];
              altLabelsModified.push("");
              props.onMetaFieldChange(altLabelsModified);
            }}
          >
            Add
          </Button>
        </PanelRow>
      </PanelBody>
    </Panel>
  ) : null;

export default compose(
  withSelect(select => {
    return {
      altLabels: select("core/editor").getEditedPostAttribute("meta")["_wl_alt_label"]
    };
  }),
  withDispatch(dispatch => {
    return {
      onMetaFieldChange: value => {
        dispatch("core/editor").editPost({ meta: { _wl_alt_label: value } });
      }
    };
  })
)(SynonymsPanel);
