import { connect } from 'react-redux';

import KeywordTable from '../components/KeywordTable';
import {
  changeKeyword,
  createKeywordRequest,
  deleteKeywordRequest,
  togglePopup
} from '../actions';

const isKeywordValid = keyword => {
  const re = /^[\w\d]+$/g;

  return keyword.length && re.test(keyword);
};

const mapStateToProps = state => {
  return {
    showPopup: state.showPopup,
    keywordValid: isKeywordValid(state.keyword),
    keyword: state.keyword,
    data: state.data
  };
};

const mapDispatchToProps = dispatch => {
  return {
    onAddClick: () => dispatch(togglePopup()),
    onKeywordSubmit: keyword => dispatch(createKeywordRequest(keyword)),
    onKeywordChange: keyword => dispatch(changeKeyword(keyword)),
    onKeywordDelete: keyword => dispatch(deleteKeywordRequest(keyword))
  };
};

const KeywordTableContainer = connect(mapStateToProps, mapDispatchToProps)(
  KeywordTable
);

export default KeywordTableContainer;
