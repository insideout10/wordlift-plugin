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


    syncData( entities ) {

        console.log("trying to sync data for " + this.id)
        const el = document.getElementById( this.id);
        console.log(entities)
        if ( ! el ) {
            return;
        }

        // Reset the state.
        el.innerHTML = "";

        entities.forEach(this.addSingleEntityHtml)

    }


    addSingleEntityHtml(value, key) {
        console.log(value)
        return "";
    }
}