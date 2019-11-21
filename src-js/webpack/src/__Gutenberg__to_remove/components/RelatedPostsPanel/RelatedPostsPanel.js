/* globals wp, wlSettings, wordlift */
/**
 * External dependencies
 */
import React from "react";

/**
 * Internal dependencies
 */
import Post from "./Post";
import Spinner from "../Spinner";
import RelatedPostsService from "../../services/RelatedPostsService";

/*
 * Packages via WordPress global
 */
const { Panel, PanelBody, PanelRow } = wp.components;

class RelatedPostsPanel extends React.Component {
  constructor(props) {
    super(props);
  }

  componentDidMount() {
    let relatedPostsService = new RelatedPostsService();
    this.props.dispatch(relatedPostsService.getPosts());
  }

  render() {
    return (
      <Panel>
        <PanelBody title="Related posts" initialOpen={false}>
          {!this.props.relatedPosts && <Spinner />}
          {this.props.relatedPosts && this.props.relatedPosts.length === 0 ? (
            <PanelRow>No results found</PanelRow>
          ) : (
            this.props.relatedPosts.map(item => (
              <PanelRow key={item.ID}>
                <Post {...item} />
              </PanelRow>
            ))
          )}
        </PanelBody>
      </Panel>
    );
  }
}

export default RelatedPostsPanel;
