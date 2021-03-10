/**
 * Represents a single column of table
 * @since 3.29.1
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
export const TABLE_COLUMN_DELIMITER = '<wl-table-column-delimiter/>'
export default class TableColumn {

    /**
     * @param columnData A single column data
     */
    constructor(columnData) {
        this._data = columnData
    }

    get data() {
        return this._data;
    }

    getAnalysisHtml() {
        let content = "";
        if (this.data && this.data.content ) {
            content = this.data.content;
        }
        return content + TABLE_COLUMN_DELIMITER;
    }

    getAttributeData() {
        return {
            content: this.data.content
        }
    }

    /**
     *
     * @param html {String}
     */
    static createFromAnalysisHtml(html) {

        return new TableColumn({content: html} );
    }

}