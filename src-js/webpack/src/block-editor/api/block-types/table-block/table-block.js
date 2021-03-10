import {AbstractBlock} from "../abstract-block";
import TableSection, {TABLE_SECTION_DELIMITER} from "./table-section";
import {TABLE_ROW_DELIMITER} from "./table-row";
import Table from "./table";

/**
 * Table block represents the core/table
 * in the gutenberg editor.
 */
export default class TableBlock extends AbstractBlock {

    constructor(block, dispatch, start) {
        super(block, dispatch, start);
        this.table = new Table(block.attributes.head, block.attributes.body, block.attributes.foot);
        this.content = this.table.getAnalysisHtml();

    }

    apply() {
        if (this._dirty) {
            // delimit and update the blocks.
            this.table.updateFromAnalysisHtml(this.content)
            // WP 5.0 returns undefined to this call.
            this._dispatch.updateBlockAttributes(this.clientId, this.table.getAttributeData());
            this._dirty = false;
        }
    }
}
