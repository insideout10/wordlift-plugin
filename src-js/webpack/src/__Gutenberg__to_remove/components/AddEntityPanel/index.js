/* globals wp, wlSettings */
/**
 * External dependencies
 */
import React from "react";

/*
 * Internal dependencies.
 */
import Store2 from "../../stores/Store2";
import AddEntity from "./AddEntity";

/*
 * Packages via WordPress global
 */
const { Panel, PanelBody } = wp.components;

const canCreateEntities =
  "undefined" !== wlSettings["can_create_entities"] && "yes" === wlSettings["can_create_entities"];

const AddEntityPanel = () => (
  <Panel>
    <PanelBody initialOpen={true}>
      <AddEntity showCreate={canCreateEntities} store={Store2} />
    </PanelBody>
  </Panel>
);

export default AddEntityPanel;
