/* global wp */

/**
 *
 * @param {{editor}} data
 * @returns {IterableIterator<{payload: *, type: string}>}
 */
function* requestAnalysis(data = { editor: "core/editor" }) {
  const { editorOps, request } = yield prepareAnalysis(data);
  const response = yield fetchAnalysis(request);
  yield embedAnalysis({ editorOps, response });

  return receiveAnalysis(response);
}

class ResponseOps {
  constructor(response) {
    this._response = response;
    this._entities = {};

    Object.keys(this._response.entities).map(k => {
      this._entities[k] = new EntityOps(this._response.entities[k]);
    });
  }

  get entities() {
    return this._entities;
  }
}

class EntityOps {
  constructor(entity) {
    this._entity = entity;
  }

  get id() {
    return this._entity.entityId;
  }
}

/**
 *
 * @param {{editor}} data
 * @returns {{payload: *, type: string}}
 */
function prepareAnalysis(data) {
  return {
    type: "PREPARE_ANALYSIS",
    payload: data
  };
}

function fetchAnalysis(data) {
  return {
    type: "FETCH_ANALYSIS",
    payload: data
  };
}

function embedAnalysis(data) {
  return {
    type: "EMBED_ANALYSIS",
    payload: data
  };
}

function receiveAnalysis(data) {
  return {
    type: "RECEIVE_ANALYSIS",
    payload: data
  };
}

const actions = {
  requestAnalysis,
  prepareAnalysis,
  fetchAnalysis,
  receiveAnalysis
};

export default actions;
