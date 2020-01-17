/**
 * External dependencies
 */
import React from "react";

/**
 * WordPress dependencies
 */
import { Panel, PanelBody, PanelRow } from "@wordpress/components";

/**
 * Internal dependencies
 */
import Post from "./post";
import Spinner from "../spinner";

export default class RelatedPostsPanel extends React.Component {
  componentDidMount() {
    this.props.requestRelatedPosts();
  }

  render() {
    const { posts } = this.props;

    return (
      <Panel>
        <PanelBody title="Related posts" initialOpen={false}>
          {(!posts && <Spinner running={true} />) ||
            (posts.length > 0 &&
              posts.map(item => (
                <PanelRow key={item.ID}>
                  <Post {...item} />
                </PanelRow>
              ))) || <PanelRow>No results found</PanelRow>}
        </PanelBody>
      </Panel>
    );
  }
}
