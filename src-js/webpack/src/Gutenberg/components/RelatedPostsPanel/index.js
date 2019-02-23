/* globals wp, wlSettings, wordlift */
/**
 * External dependencies
 */
import React from "react";

import Post from "./Post";

/*
 * Packages via WordPress global
 */
const { Panel, PanelBody, PanelRow } = wp.components;

class RelatedPostsPanel extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      isLoaded: false,
      items: []
    };
  }

  componentDidMount() {
    wp.apiFetch({
      url: `${wlSettings["ajax_url"]}?action=wordlift_related_posts&post_id=${wlSettings["post_id"]}`,
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(Object.keys(wordlift.entities))
    }).then(result => {
      this.setState({
        isLoaded: true,
        items: result
      });
    });
  }

  render() {
    return (
      <Panel>
        <PanelBody title="Related posts" initialOpen={false}>
          {this.state.error && <PanelRow>Error: {this.state.message}</PanelRow>}
          {!this.state.isLoaded && <PanelRow>Loading...</PanelRow>}
          {this.state.items.map(item => (
            <PanelRow key={item.ID}>
              <Post {...item} />
            </PanelRow>
          ))}
        </PanelBody>
      </Panel>
    );
  }
}

export default RelatedPostsPanel;
