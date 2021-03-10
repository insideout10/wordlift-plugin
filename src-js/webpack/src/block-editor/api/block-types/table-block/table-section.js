/**
 * Represents a single section of table.
 * @since 3.29.1
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */


import TableRow, {TABLE_ROW_DELIMITER} from "./table-row";

const TABLE_SECTION_DELIMITER = '<wl-table-section-delimiter/>'

export default class TableSection {

    /**
     * @param tableSectionData An array of table rows data
     */
    constructor(tableSectionData) {
        this._rows = []
        if ( tableSectionData && Array.isArray(tableSectionData) ) {
            this._rows = tableSectionData.map((row)=> {
                return new TableRow(row);
            })
        }
    }

    get rows() {
        return this._rows
    }

    set rows(rows) {
        this._rows = rows
    }
    getAnalysisHtml() {
        const rows = this.rows.map((row) => {
            return row.getAnalysisHtml()
        })
        return rows.join("")  + TABLE_SECTION_DELIMITER
    }


    /**
     *
     * @param html {String}
     */
    static createFromAnalysisHtml(html) {

        const tableSection = new TableSection(null);
        // Set the rows after parsing.
        tableSection.rows = html.split(TABLE_ROW_DELIMITER)
            .map((rowHtml) => {
                return TableRow.createFromAnalysisHtml(rowHtml);
            });

        return tableSection;

    }

    getAttributeData() {
        const sectionRows = [];
        this.rows.map((row)=> {
            sectionRows.push(row.getAttributeData())
        })
        return sectionRows;
    }


}