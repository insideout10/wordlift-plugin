/**
 * @since 3.32.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * This class sync the entity state data to a dom element which is used for saving the entity data
 * of selected entities.
 */
export default class AnalysisStorage {


    constructor(id) {
        this.id = id
    }


    syncData(entities) {
        console.log(entities)
        console.log(" is received by sync data")
        const el = document.getElementById(this.id);

        if (!el) {
            return;
        }

        // Reset the state.
        el.innerHTML = "";

        let fieldHtml = "";
        for (const [key, value] of entities.entries()) {
            fieldHtml += this.generateHtmlForSingleEntity( value)
        }
        el.innerHTML = fieldHtml
    }




    generateForSingleField(entityId, fieldName, value, isArrayField = false) {

        const fieldHtmlName = isArrayField ?
            `wl_entities[${entityId}][${fieldName}][]`
            : `wl_entities[${entityId}][${fieldName}]`
        return `<input type='hidden' name="${fieldHtmlName}" value="${value}">`
    }

    generateForArrayField(entityId, fieldName, values) {
        if (!Array.isArray(values)) {
            return ''
        }
        let arrHtml = ""
        for (let value of values) {
            arrHtml += this.generateForSingleField(entityId, fieldName, value, true)
        }
        return arrHtml
    }

    generateHtmlForSingleEntity(entity) {
        let html = "";
        let entityId = entity.id;
        html += this.generateForSingleField(entityId, "uri", entityId)
        html += this.generateForSingleField(entityId, "label", entity.label)
        html += this.generateForSingleField(entityId, "description", entity.description)
        html += this.generateForSingleField(entityId, "main_type", "wl-" + entity.mainType)
        html += this.generateForArrayField(entityId, "type", entity.types)
        html += this.generateForArrayField(entityId, "image", entity.images)
        html += this.generateForArrayField(entityId, "sameas", entity.sameAs)
        html += this.generateForArrayField(entityId, "synonym", entity.synonyms)
        return html
    }
}