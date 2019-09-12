/**
 *
 * @param accumulator
 * @param {{name, innerBlocks}[]} blocks
 * @param callback
 * @returns {*}
 */
export function collectBlocks(accumulator, blocks, callback = x => x) {
  blocks.forEach(block => {
    if ("core/paragraph" === block.name || "core/freeform" === block.name) {
      accumulator.push(callback(block));
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
    const content = blocks[i].attributes.content;
    const regexp = new RegExp(`<span id="${id}" class="(.*?)">`, "i");
    const newContent = content.replace(regexp, (match, p1) => `<span id="${id}" class="${replacement}">`);

    if (content === newContent) continue;

    dispatch.updateBlock(blocks[i].clientId, {
      attributes: { content: newContent }
    });

    return true;
  }

  return false;
}

function replaceClass(store, dispatch, id, callback) {
  const blocks = collectBlocks([], store.getBlocks());
  for (let i = 0; i < blocks.length; i++) {
    const content = blocks[i].attributes.content;
    const regexp = new RegExp(`<span\\s+id="${id}"\\s+class="(.*?)"`, "i");
    const newContent = content.replace(regexp, callback);

    if (content === newContent) continue;

    dispatch.updateBlock(blocks[i].clientId, {
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

/**
 * Set an annotation as selected (disambiguated).
 *
 * Adds the `disambiguated` and `wl-{type}` classes.
 *
 * @param {BlockOps[]} blocks
 * @param dispatch
 * @param {string} annotationId
 * @param {string} type
 * @param {string} itemId
 * @returns {boolean}
 */
export function switchOn(blocks, dispatch, annotationId, type, itemId) {
  for (let i = 0; i < blocks.length; i++) {
    const content = blocks[i].content;
    const regexp = new RegExp(`<span\\s+id="${annotationId}"\\s+class="(.*?)">`, "i");
    const newContent = content.replace(regexp, (match, classAttr) => {
      const clsToAdd = ["disambiguated", `wl-${type.replace(/\s+/, "-")}`];
      const cls = mergeArray(classAttr.split(/\s+/), clsToAdd).join(" ");

      return `<span id="${annotationId}" class="${cls}" itemid="${itemId}">`;
    });

    if (content === newContent) continue;

    blocks[i].content = newContent;

    return true;
  }

  return false;
}

/**
 * Set an annotation as unselected.
 *
 * @param store
 * @param dispatch
 * @param id
 * @param type
 * @returns {boolean}
 */
function switchOff(store, dispatch, id, type) {
  return removeClass(store, dispatch, id, "disambiguated", `wl-${type.replace(/\s+/, "-")}`);
}

export function mergeArray(a1, a2) {
  const newArray = a1.splice(0);

  for (let i = 0; i < a2.length; i++) {
    if (-1 === a1.indexOf(a2[i])) newArray.push(a2[i]);
  }

  return newArray;
}
