/**
 * Load the Schema Properties Form.
 *
 * @since 3.20.0
 */
import React from "react";
import ReactDOM from "react-dom";
import { Form } from "@wordlift/wordlift-for-schemaorg";

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

  const PropertyInstanceDecorator = Component => props => {
    const uniqueId = uuid();
    const { property, propertyValue, ...pass } = props;
    return (
      <React.Fragment>
        <input
          type="hidden"
          name={`_wl_prop[${property.name}][${uniqueId}][type]`}
          defaultValue={propertyValue.type}
        />
        <input
          type="hidden"
          name={`_wl_prop[${property.name}][${uniqueId}][language]`}
          defaultValue={propertyValue.language}
        />
        <Component
          {...pass}
          property={property}
          propertyValue={propertyValue}
          name={`_wl_prop[${property.name}][${uniqueId}][value]`}
          value={propertyValue.value}
        />
      </React.Fragment>
    );
  };

  const FetchLoader = selected => {
    return window["wp"].ajax
      .post("wl_schemaorg_property", {
        term_id: selected,
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

  const syncWithSchemaClassTree = Component =>
    class extends React.Component {
      constructor(props) {
        super(props);

        this.state = {
          selected: Array.from(
            document.querySelectorAll(
              "#wl_entity_typechecklist input[type='checkbox']:checked"
            )
          ).map(item => parseInt(item.value))
        };
      }
      componentDidMount() {
        // Listen for messages, specifically whether the Schema.org class selection has changed.
        window.addEventListener(
          "message",
          ({ data, origin }) => {
            console.debug("message received", { data, origin });

            // Bail out if message isn't coming from our page.
            if (0 !== document.location.href.indexOf(origin)) return;

            if (
              undefined === data.type ||
              "syncWithWordPressTaxonomyMetabox.selected" !== data.type
            )
              return;

            this.setState({ selected: data.payload.selected });
          },
          false
        );
      }

      render() {
        const { selected, ...props } = this.props;
        return <Component {...props} selected={this.state.selected} />;
      }
    };

  const DecoratedForm = syncWithSchemaClassTree(Form);

  ReactDOM.render(
    <DecoratedForm
      loader={FetchLoader}
      propertyDecorator={PropertyDecorator}
      propertyInstanceDecorator={PropertyInstanceDecorator}
    />,
    element
  );
});
