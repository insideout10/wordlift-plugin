export default function(data) {
  var annotation, ea, entity, i, id, index, len, ref, ref1, ref2;
  ref = data.entities;
  for (id in ref) {
    entity = ref[id];
    entity.id = id;
    if (entity.occurrences == null) {
      entity.occurrences = [];
    }

    if (entity.annotations == null) {
      entity.annotations = {};
    }
  }
  ref1 = data.annotations;
  for (id in ref1) {
    annotation = ref1[id];
    annotation.id = id;
    annotation.entities = {};
    // Filter out annotations that don't have a corresponding entity. The entities list might be filtered, in order
    // to remove the local entity.
    data.annotations[id].entityMatches = (function() {
      var i, len, ref2, results;
      ref2 = annotation.entityMatches;
      results = [];
      for (i = 0, len = ref2.length; i < len; i++) {
        ea = ref2[i];
        if (ea.entityId in data.entities) {
          results.push(ea);
        }
      }
      return results;
    })();
    // Remove the annotation if there's no entity matches left.

    // See https://github.com/insideout10/wordlift-plugin/issues/437
    // See https://github.com/insideout10/wordlift-plugin/issues/345
    if (0 === data.annotations[id].entityMatches.length) {
      delete data.annotations[id];
      continue;
    }
    ref2 = data.annotations[id].entityMatches;
    for (index = i = 0, len = ref2.length; i < len; index = ++i) {
      ea = ref2[index];
      if (!data.entities[ea.entityId].label) {
        data.entities[ea.entityId].label = annotation.text;
        // $log.debug(`Missing label retrieved from related annotation for entity ${ea.entityId}`);
      }
      if (data.entities[ea.entityId].annotations == null) {
        data.entities[ea.entityId].annotations = {};
      }
      data.entities[ea.entityId].annotations[id] = annotation;
      data.annotations[id].entities[ea.entityId] = data.entities[ea.entityId];
    }
  }
  return data;
}
