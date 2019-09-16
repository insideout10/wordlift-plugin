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

export function mergeArray(a1, a2) {
  const newArray = a1.splice(0);

  for (let i = 0; i < a2.length; i++) {
    if (-1 === a1.indexOf(a2[i])) newArray.push(a2[i]);
  }

  return newArray;
}

export function makeEntityAnnotationsSelector(entity) {
  return Object.values(entity.annotations)
    .map(annotation => annotation.annotationId)
    .join("|");
}
