/**
 * Internal dependencies
 */
import * as types from '../constants/ActionTypes';

const editor = function (state = {selection: ''}, action) {
    switch (action.type) {
        case types.EDITOR_SELECTION_CHANGED:
            // Update the selection.
            return Object.assign({}, state, {selection: action.selection});
        default:
            return state;
    }
};

export default editor;