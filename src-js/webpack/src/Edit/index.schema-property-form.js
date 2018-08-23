/**
 * Load the Schema Properties Form.
 *
 * @since 3.20.0
 */
import React from "react";
import ReactDOM from "react-dom";
import Form from "wordlift-for-schemaorg/src/Schema/Properties/Form";
import SelectionListener from "wordlift-for-schemaorg/src/Schema/Properties/SelectionListener";

import uuid from "./uuid";

/**
 * Add the SchemaClassTree.
 *
 * @since 3.20.0
 */
window.addEventListener("load", () => {
  const element = document.getElementById("wl-schema-properties-form");

  if (null === element) {
    return;
  }
  // Set a reference to the WordLift's settings stored in the window instance.
  const settings = window["wlSettings"] || {};

  const data = settings["properties"];

  const Reader = property => data[property.name];

  const PropertyDecorator = Component => props => (
    <Component values={Reader(props.property)} {...props} />
  );

  const PropertyInstanceDecorator = Component => {
    const uniqueId = uuid();
    return props => (
      <React.Fragment>
        <input
          type="hidden"
          name={`_wl_prop[${props.property.name}][${uniqueId}][type]`}
          defaultValue={props.type}
        />
        <input
          type="hidden"
          name={`_wl_prop[${props.property.name}][${uniqueId}][language]`}
          defaultValue={props.language || ""}
        />
        <Component
          name={`_wl_prop[${props.property.name}][${uniqueId}][value]`}
          defaultValue={props.value}
          {...props}
        />
      </React.Fragment>
    );
  };

  const FetchLoader = selected => {
    return wp.ajax
      .post("wl_schemaorg_property", {
        class: selected.toArray(),
        _wpnonce: settings["wl_schemaorg_property_nonce"]
      })
      .then(json => json["schemaProperties"])
      .then(properties =>
        // Sort alphabetically.
        properties.sort((a, b) => {
          const nameA = a.name.toUpperCase();
          const nameB = b.name.toUpperCase();

          if (nameA < nameB) return -1;

          if (nameA > nameB) return 1;

          return 0;
        })
      );
  };

  ReactDOM.render(
    <Form
      selected={settings["entity_types"]}
      loader={FetchLoader}
      selectionListener={SelectionListener}
      propertyDecorator={PropertyDecorator}
      propertyInstanceDecorator={PropertyInstanceDecorator}
    />,
    element
  );
});
