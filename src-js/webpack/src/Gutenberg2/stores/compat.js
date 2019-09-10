const defaultType = "thing";

export default function parseAnalysisResponse(configuration, data) {
  var annotation,
    annotationId,
    ea,
    em,
    entity,
    i,
    id,
    index,
    j,
    len,
    len1,
    localEntity,
    local_confidence,
    ref,
    ref1,
    ref2,
    ref3,
    ref4,
    ref5,
    ref6,
    ref7,
    ref8,
    ref9;
  // TMP ... Should be done on WLS side
  //      unless data.topics?
  //        data.topics = []
  if (data.topics != null) {
    data.topics = data.topics.map(function(topic) {
      topic.id = topic.uri;
      topic.occurrences = [];
      topic.mainType = defaultType;
      return topic;
    });
  }
  ref = configuration.entities;
  for (id in ref) {
    localEntity = ref[id];
    data.entities[id] = localEntity;
  }
  ref1 = data.entities;
  for (id in ref1) {
    entity = ref1[id];
    // Remove the current entity from the proposed entities.

    // See https://github.com/insideout10/wordlift-plugin/issues/437
    // See https://github.com/insideout10/wordlift-plugin/issues/345
    if (configuration.currentPostUri === id) {
      delete data.entities[id];
      continue;
    }
    if (!entity.sameAs) {
      entity.sameAs = [];
      if ((ref2 = configuration.entities[id]) != null) {
        ref2.sameAs = [];
      }
    }
    entity.id = id;
    entity.occurrences = [];
    entity.annotations = {};
  }
  ref5 = data.annotations;
  // See #550: the confidence is set by the server.
  // entity.confidence = 1
  for (id in ref5) {
    annotation = ref5[id];
    annotation.id = id;
    annotation.entities = {};
    // Filter out annotations that don't have a corresponding entity. The entities list might be filtered, in order
    // to remove the local entity.
    data.annotations[id].entityMatches = (function() {
      var i, len, ref6, results;
      ref6 = annotation.entityMatches;
      results = [];
      for (i = 0, len = ref6.length; i < len; i++) {
        ea = ref6[i];
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
    ref6 = data.annotations[id].entityMatches;
    for (index = i = 0, len = ref6.length; i < len; index = ++i) {
      ea = ref6[index];
      if (!data.entities[ea.entityId].label) {
        data.entities[ea.entityId].label = annotation.text;
      }
      data.entities[ea.entityId].annotations[id] = annotation;
      data.annotations[id].entities[ea.entityId] = data.entities[ea.entityId];
    }
  }
  ref7 = data.entities;
  // TODO move this calculation on the server
  for (id in ref7) {
    entity = ref7[id];
    ref8 = data.annotations;
    for (annotationId in ref8) {
      annotation = ref8[annotationId];
      local_confidence = 1;
      ref9 = annotation.entityMatches;
      for (j = 0, len1 = ref9.length; j < len1; j++) {
        em = ref9[j];
        if (em.entityId != null && em.entityId === id) {
          local_confidence = em.confidence;
        }
      }
      entity.confidence = entity.confidence * local_confidence;
    }
  }
  return data;
}
