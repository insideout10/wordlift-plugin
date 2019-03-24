import React from "react";
import { treemapSquarify } from "d3-hierarchy";
import TreemapAdapter from "./TreemapAdapter";
import TilingStrategy from "./TilingStrategy";
import RenderStrategy from "./RenderStrategy";
import "./Treemap.css";
import "./custom.css";

function scoreFn(data) {
  return undefined === data.score || null === data.score
    ? 0
    : Math.abs(data.score.value * 1000);
}

function bind(el, props) {
  const adapter = new TreemapAdapter(
    new TilingStrategy(
      props.minTileWidth,
      props.minTileHeight,
      treemapSquarify.ratio(1.61),
      scoreFn
    ),
    RenderStrategy(el, props.tileRenderCallback),
    scoreFn
  );

  adapter.load(props.url);
}

function Treemap(props) {
  return (
    <div
      ref={el => bind(el, props)}
      style={{
        width: `100%`,
        height: `${props.height}px`,
        position: "relative"
      }}
    >{props.children}</div>
  );
}

export default Treemap;
