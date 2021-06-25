/**
 * External dependencies
 */
import React from "react";

/**
 * WordPress dependencies
 */
import { withSelect, withDispatch } from "@wordpress/data";
import { compose } from "@wordpress/compose";
import { __ } from "@wordpress/i18n";
import { Panel, PanelBody, PanelRow, TextControl, Button, IconButton } from "@wordpress/components";

const settings = global["_wlMetaBoxSettings"].settings;

let SynonymsPanel = props =>
  settings.isEntity ? (
    <Panel>
      <PanelBody title={__("Synonyms", "wordlift")} intialOpen={false}>
        {props.altLabels.map((altLabel, altLabelN, altLabels) => (
          <PanelRow key={altLabelN}>
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
        <PanelRow key={-1}>
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
    const meta = select("core/editor").getEditedPostAttribute("_wl_alt_label");
    if (undefined !== meta ) {
      return { altLabels: meta };
    }
    return { altLabels: [] };
  }),
  withDispatch(dispatch => {
    return {
      onMetaFieldChange: value => {
        dispatch("core/editor").editPost({ _wl_alt_label: value });
      }
    };
  })
)(SynonymsPanel);
