/**
 * This file implements the v1 analysis.
 *
 * @package block-editor/analysis
 * @author Naveen Muthusamy <naveen@wordlft.io>
 * @since 3.32.5
 */

import AnalysisService from "./analysis-service";
import {Blocks} from "../../api/blocks";
import * as data from "@wordpress/data";
import {EDITOR_STORE} from "../../../common/constants";
import {makeEntityAnnotationsSelector, mergeArray} from "../../api/utils";
import {call, put, select} from "redux-saga/effects";
import {toggleLinkSuccess, updateOccurrencesForEntity} from "../../../Edit/actions";
import {
	getAnnotationFilter,
	getBlockEditorFormat,
	getClassificationBlock,
	getSelectedEntities
} from "../../stores/selectors";
import ClassicEditorBlockValidator from "../../stores/classic-editor-block-validator";
import {getSelection} from "../../../Edit/components/AddEntity";
import createEntity from "../../api/create-entity";
import {getMainType} from "../../../Edit/stores/sagas";
import ClassicEditorBlock from "../../api/classic-editor-block";
import {applyFormat} from "@wordpress/rich-text";
import {ADD_ENTITY} from "../../../Edit/constants/ActionTypes";
import {addEntitySuccess} from "../../../Edit/components/AddEntity/actions";


export default class V1AnalysisService extends AnalysisService {

	embedAnalysis(editorOps, response) {
		// Bail out if the response doesn't contain results.
		if ("undefined" === typeof response || "undefined" === typeof response.annotations) {
			return;
		}

		const annotations = Object.values( response.annotations ).sort(
			function (a1, a2) {
				if (a1.end > a2.end) {
					return -1;
				}
				if (a1.end < a2.end) {
					return 1;
				}

				return 0;
			}
		);

		annotations.forEach(
			annotation =>
			editorOps.insertAnnotation( annotation.annotationId, annotation.start, annotation.end )
		);

		editorOps.applyChanges();
	}


	* toggleEntity({entity}) {
		// Get the supported blocks.
		const blocks = Blocks.create(data.select(EDITOR_STORE).getBlocks(), data.dispatch(EDITOR_STORE));

		const mainType = entity.mainType || "thing";
		const onClassNames = ["disambiguated", `wl-${mainType.replace(/\s/, "-")}`];

		// Build a css selector to select all the annotations for the provided entity.
		const annotationSelector = makeEntityAnnotationsSelector(entity);

		// Collect the annotations that have been switch on/off.
		const occurrences = [];

		if (0 === entity.occurrences.length) {
			// Switch on.
			blocks.replace(
				new RegExp(`<span\\s+id="(${annotationSelector})"\\sclass="([^"]*)">`, "gi"),
				(match, annotationId, classNames) => {
					const newClassNames = mergeArray(classNames.split(/\s+/), onClassNames);
					occurrences.push(annotationId);
					return `<span id="${annotationId}" class="${newClassNames.join(" ")}" itemid="${entity.id}">`;
				}
			);
		} else {
			console.debug(`Looking for "<span\\s+id="(${annotationSelector})"\\sclass="([^"]*)"\\sitemid="[^"]*">"...`);
			// Switch off.
			blocks.replace(
				new RegExp(`<span\\s+id="(${annotationSelector})"\\sclass="([^"]*)"\\sitemid="[^"]*">`, "gi"),
				(match, annotationId, classNames) => {
					const newClassNames = classNames.split(/\s+/).filter(x => -1 === onClassNames.indexOf(x));
					return `<span id="${annotationId}" class="${newClassNames.join(" ")}">`;
				}
			);
		}

		yield put(updateOccurrencesForEntity(entity.id, occurrences));

		// Send the selected entities to the WordLift Classification box.
		data.dispatch(EDITOR_STORE).updateBlockAttributes(getClassificationBlock().clientId, {
			entities: yield select(getSelectedEntities)
		});

		// Apply the changes.
		blocks.apply();
	}

	* toggleLink({entity}) {
		// Get the supported blocks.
		const blocks = Blocks.create(data.select(EDITOR_STORE).getBlocks(), data.dispatch(EDITOR_STORE));

		// Build a css selector to select all the annotations for the provided entity.
		const annotationSelector = makeEntityAnnotationsSelector(entity);

		const cssClasses = ["wl-link", "wl-no-link"];

		const link = !entity.link;

		blocks.replace(
			new RegExp(`<span\\s+id="(${annotationSelector})"\\sclass="([^"]*)"\\sitemid="([^"]*)">`, "gi"),
			(match, annotationId, classNames) => {
				// Remove existing `wl-link` / `wl-no-link` classes.
				const newClassNames = classNames.split(/\s+/).filter(x => -1 === cssClasses.indexOf(x));
				// Add the `wl-link` / `wl-no-link` class according to the desired outcome.
				newClassNames.push(link ? "wl-link" : "wl-no-link");
				return `<span id="${annotationId}" class="${newClassNames.join(" ")}" itemid="${entity.id}">`;
			}
		);

		// Apply the changes.
		blocks.apply();

		yield put(toggleLinkSuccess({id: entity.id, link}));
	}


	* toggleAnnotation({annotation}) {
		// Bail out if the annotation didn't change.
		const selectedAnnotation = yield select(getAnnotationFilter);
		if (annotation === selectedAnnotation) return null;

		// Get the supported blocks.
		const blocks = Blocks.create(data.select(EDITOR_STORE).getBlocks(), data.dispatch(EDITOR_STORE));

		blocks.replace(
			new RegExp(`<span\\s+id="([^"]+)"\\sclass="(textannotation(?:\\s[^"]*)?)"`, "gi"),
			(match, annotationId, classNames) => {
				// Get the class names removing any potential `selected` class.
				const newClassNames = classNames.split(/\s+/).filter(x => "selected" !== x);

				// Add the `selected` class if the annotation match.
				if (annotation === annotationId) newClassNames.push("selected");

				// Return the new span.
				return `<span id="${annotationId}" class="${newClassNames.join(" ")}"`;
			}
		);

		// Apply the changes.
		blocks.apply();
	}

	* handleAddEntityRequest({payload}) {
		// See https://developer.wordpress.org/block-editor/packages/packages-rich-text/#applyFormat
		const blockEditorFormat = yield select(getBlockEditorFormat);
		let value, onChange;
		let selectedBlock = wp.data.select("core/editor").getSelectedBlock();
		let isClassicEditorBlock = false;

		if (blockEditorFormat !== undefined) {
			onChange = blockEditorFormat.onChange;
			value = blockEditorFormat.value;
		}

		if (blockEditorFormat === undefined) {
			value = ClassicEditorBlockValidator.getValue(getSelection());
			if (value === false) {
				// This is not a valid classic block,return early.
				return false;
			}
			// mark it as classic editor block.
			isClassicEditorBlock = true;
		}

		const annotationId = "urn:local-annotation-" + Math.floor(Math.random() * 999999);

		// Create the entity if the `id` isn't defined.
		const id =
			payload.id ||
			(yield call(createEntity, {
				title: payload.label,
				// Set entity type to the created entity.
				wlEntityMainType: [payload.category],
				// Inherit the status from the edited post.
				status: wp.data.select('core/editor').getEditedPostAttribute('status'),
				// wp rest api uses content as the alias for description
				content: payload.description,
				excerpt: "",
			}))["wl:entity_url"];

		let entityToAdd = {
			id,
			...payload,
			annotations: {[annotationId]: {annotationId, start: value.start, end: value.end}},
			occurrences: [annotationId]
		};


		if ( entityToAdd.types ) {
			// Set the main type using the same function used by classic editor.
			entityToAdd.mainType = getMainType(entityToAdd.types)
		}
		else if (entityToAdd.category) {
			// When the user manually adds entity in the editor
			// set the mainType from category
			entityToAdd.mainType = entityToAdd.category
				.replace("http://schema.org/", "")
				.replace("https://schema.org/", "")
		}

		console.debug("Adding Entity", entityToAdd);
		const annotationAttributes = {id: annotationId, class: "disambiguated", itemid: entityToAdd.id};
		const format = {
			type: "wordlift/annotation",
			attributes: annotationAttributes
		};

		if (isClassicEditorBlock) {
			// classic editor block should be updated differently.
			const instance = new ClassicEditorBlock(selectedBlock.clientId, selectedBlock.attributes.content);
			instance.replaceWithAnnotation(getSelection(), annotationAttributes);
			instance.update();
		} else {
			// update the block
			yield call(onChange, applyFormat(value, format));
		}
		// update the state.
		yield put({type: ADD_ENTITY, payload: entityToAdd});

		// Send the selected entities to the WordLift Classification box.
		data.dispatch(EDITOR_STORE).updateBlockAttributes(getClassificationBlock().clientId, {
			entities: yield select(getSelectedEntities)
		});

		yield put(addEntitySuccess());
	}
}
