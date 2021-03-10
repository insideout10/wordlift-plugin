import {AbstractBlock} from "../abstract-block";
import TableSection from "./table-section";
import {TABLE_ROW_DELIMITER} from "./table-row";

/**
 * Table block represents the core/table
 * in the gutenberg editor.
 */
export default class TableBlock extends AbstractBlock {

    constructor(block, dispatch, start) {
        super(block, dispatch, start);
        this.sections = [
            new TableSection(block.attributes.head),
            new TableSection(block.attributes.body),
            new TableSection(block.attributes.foot)
        ]
        this._content = this.head.getAnalysisHtml() + this.body.getAnalysisHtml() + this.foot.getAnalysisHtml();
    }

    apply() {
        if (this._dirty) {

            // delimit and update the blocks.
            this.content.split(TABLE_ROW_DELIMITER)
                .map((section, index) => {
                    if (this.sections[index]) {
                        this.sections[index].updateFromAnalysisHtml(section)
                    }
                })


            // WP 5.0 returns undefined to this call.
            this._dispatch.updateBlockAttributes(this.clientId, {
                head: this.sections[0].getAttributeData(),
                body: this.sections[1].getAttributeData(),
                foot: this.sections[2].getAttributeData(),
            });
            this._dirty = false;
        }
    }
}
