import { hierarchy as d3hierarchy, treemap } from "d3-hierarchy";

class TreemapAdapter {
  constructor(tilingStrategy, renderStrategy, scoreFn) {
    // console.debug("Creating new TreemapAdapter instance...");

    this.update = this.update.bind(this);

    // Set the tiling strategy.
    this.tilingStrategy = tilingStrategy;
    // Bind the update hierarchy to our function.
    this.tilingStrategy.update = this.update;

    this.renderStrategy = renderStrategy;
    this.scoreFn = scoreFn;
  }

  load(url) {

    fetch(url)
      .then(response => response.json())
      .then(json => json.data)
      .then(json => {
        this.treemap = treemap()
          .size([this.renderStrategy.width, this.renderStrategy.height])
          .tile(this.tilingStrategy.tile);

        this.update(
          d3hierarchy(json).sum(this.scoreFn)
        );
      })
      .catch(e => console.error("An error occurred", e));
  }

  update(hierarchy) {
    // Recalculate the tiles sizes.
    this.treemap(hierarchy);

    // Render the hierarchy.
    this.renderStrategy(hierarchy);
  }
}

export default TreemapAdapter;
