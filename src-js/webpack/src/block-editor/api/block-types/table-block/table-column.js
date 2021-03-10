/**
 * Represents a single column of table
 * @since 3.29.1
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
const TABLE_COLUMN_DELIMITER = '<wl-table-column-delimiter/>'
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
        return this.data.content + TABLE_COLUMN_DELIMITER;
    }

}