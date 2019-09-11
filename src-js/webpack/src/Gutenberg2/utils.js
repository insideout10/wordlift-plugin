{
  /**
   *
   * @param accumulator
   * @param {{name, innerBlocks}[]} blocks
   * @returns {*}
   */
  function collectBlocks(accumulator, blocks) {
    blocks.forEach(block => {
      if ("core/paragraph" === block.name || "core/freeform" === block.name) {
        accumulator.push(block);
      }

      collectBlocks(accumulator, block.innerBlocks);
    });

    return accumulator;
  }

  /**
   *
   * @param {{getBlocks()}} store
   * @param {{updateBlock()}} dispatch
   * @param replacement
   * @returns {boolean}
   */
  function replaceFirst(store, dispatch, id, replacement) {
    const blocks = collectBlocks([], store.getBlocks());
    for (let i = 0; i < blocks.length; i++) {
      const content = blocks[0].attributes.content;
      const regexp = new RegExp(`<span id="${id}" class="(.*?)">`, "i");
      const newContent = content.replace(regexp, (match, p1) => `<span id="${id}" class="${replacement}">`);

      if (content === newContent) continue;

      dispatch.updateBlock(blocks[0].clientId, {
        attributes: { content: newContent }
      });

      return true;
    }

    return false;
  }

  function replaceClass(store, dispatch, id, callback) {
    const blocks = collectBlocks([], store.getBlocks());
    for (let i = 0; i < blocks.length; i++) {
      const content = blocks[0].attributes.content;
      const regexp = new RegExp(`<span\\s+id="${id}"\\s+class="(.*?)"`, "i");
      const newContent = content.replace(regexp, callback);

      if (content === newContent) continue;

      dispatch.updateBlock(blocks[0].clientId, {
        attributes: { content: newContent }
      });

      return true;
    }

    return false;
  }

  function addClass(store, dispatch, id, ...classNames) {
    return replaceClass(store, dispatch, id, (match, attr) => {
      const cls = attr.split(/\s+/);
      for (let i = 0; i < classNames.length; i++) if (-1 === cls.indexOf(classNames[i])) cls.push(classNames[i]);

      return `<span id="${id}" class="${cls.join(" ")}"`;
    });
  }

  function removeClass(store, dispatch, id, ...classNames) {
    return replaceClass(store, dispatch, id, (match, attr) => {
      const cls = attr.split(/\s+/).filter(c => -1 === classNames.indexOf(c));

      return `<span id="${id}" class="${cls.join(" ")}"`;
    });
  }

  removeClass(
    wp.data.select("core/editor"),
    wp.data.dispatch("core/editor"),
    "urn:urn:enhancement-a67a3714",
    "disambiguated"
  );
}
export default {
  collectBlocks,
  replaceFirst
};
