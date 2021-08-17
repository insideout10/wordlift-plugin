/**
 * External dependencies
 */
import {call, put, takeEvery, takeLatest} from "redux-saga/effects";


/**
 * Internal dependencies
 */
import {receiveAnalysisResults} from "../../Edit/actions";
import {
    ANNOTATION,
    SET_CURRENT_ENTITY,
    TOGGLE_ENTITY,
    TOGGLE_LINK
} from "../../Edit/constants/ActionTypes";
import {requestAnalysis} from "./actions";
import parseAnalysisResponse from "./compat";
import {EDITOR_STORE} from "../../common/constants";
import EditorOps from "../api/editor-ops";
import {addEntityRequest, addEntitySuccess} from "../../Edit/components/AddEntity/actions";
import {doAction} from "@wordpress/hooks";
import {createEntityRequest} from "../../common/containers/create-entity-form/actions";
import {relatedPostsRequest, relatedPostsSuccess} from "../../common/containers/related-posts/actions";
import getRelatedPosts from "../../common/api/get-related-posts";
import AnalysisServiceFactory from "../analysis/analysis-service-factory";


function* handleRequestAnalysis() {
    const editorOps = new EditorOps(EDITOR_STORE);

    const settings = global["wlSettings"];
    const canCreateEntities =
        "undefined" !== typeof settings["can_create_entities"] && "yes" === settings["can_create_entities"];
    const _wlMetaBoxSettings = global["_wlMetaBoxSettings"].settings;
    const request = editorOps.buildAnalysisRequest(
        settings["can_create_entities"]["language"],
        [_wlMetaBoxSettings["currentPostUri"]],
        canCreateEntities
    );

    const response = yield call(global["wp"].ajax.post, "wl_analyze", {
        _wpnonce: settings["analysis"]["_wpnonce"],
        data: JSON.stringify(request),
        postId: wp.data.select("core/editor").getCurrentPostId()
    });


    const analysisService = AnalysisServiceFactory.getAnalysisService()
    analysisService.embedAnalysis(editorOps, response);

    const parsed = parseAnalysisResponse(response);

    yield put(receiveAnalysisResults(parsed));
}

/**
 * Broadcast the `wordlift.addEntitySuccess` action in order to have the AddEntity local store capture it.
 */
function* handleAddEntitySuccess() {
    yield call(doAction, "wordlift.addEntitySuccess");
}

/**
 * Handles the action when the entity edit link is clicked in the Classification Box.
 *
 * Within the Block Editor we open a new window to the WordPress edit post screen.
 *
 * @since 3.23.0
 * @param Object entity The entity object.
 */
function* handleSetCurrentEntity({entity}) {
    const url = `${window["wp"].ajax.settings.url}?action=wordlift_redirect&uri=${encodeURIComponent(entity.id)}&to=edit`;
    window.open(url, "_blank");
}

/**
 * Handle the Create Entity Request, which is supposed to open a form in the sidebar.
 */
function* handleCreateEntityRequest() {
    // Call the WP hook to close the entity select (see ../../Edit/components/AddEntity/index.js).
    doAction("unstable_wordlift.closeEntitySelect");
}

/**
 * Handles the request to load the related posts.
 */
function* handleRelatedPostsRequest() {
    const posts = yield call(getRelatedPosts);

    yield put(relatedPostsSuccess(posts));
}

export default function* saga() {
    const analysisService = AnalysisServiceFactory.getAnalysisService()
    yield takeLatest(requestAnalysis, handleRequestAnalysis);
    yield takeEvery(TOGGLE_ENTITY, analysisService.toggleEntity);
    yield takeEvery(TOGGLE_LINK, analysisService.toggleLink);
    yield takeLatest(ANNOTATION, analysisService.toggleAnnotation);
    yield takeEvery(addEntityRequest, analysisService.handleAddEntityRequest);
    yield takeEvery(addEntitySuccess, handleAddEntitySuccess);
    yield takeEvery(SET_CURRENT_ENTITY, handleSetCurrentEntity);
    yield takeEvery(createEntityRequest, handleCreateEntityRequest);
    yield takeEvery(relatedPostsRequest, handleRelatedPostsRequest);
}
