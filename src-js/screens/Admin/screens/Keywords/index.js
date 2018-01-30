import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { applyMiddleware, combineReducers, createStore } from 'redux';
import createSagaMiddleware from 'redux-saga';

import KeywordTableContainer from './containers/KeywordTableContainer';
import {
  loadKeywordsRequest,
  dataReducer,
  popupReducer,
  keywordReducer
} from './actions';
import saga from './actions/sagas';

// create the saga middleware
const sagaMiddleware = createSagaMiddleware();

const store = createStore(
  combineReducers({
    data: dataReducer,
    showPopup: popupReducer,
    keyword: keywordReducer
  }),
  applyMiddleware(sagaMiddleware)
);

// then run the saga
sagaMiddleware.run(saga);

ReactDOM.render(
  <Provider store={store}>
    <KeywordTableContainer />
  </Provider>,
  document.getElementById('wl-keywords-table')
);

store.dispatch(loadKeywordsRequest());
