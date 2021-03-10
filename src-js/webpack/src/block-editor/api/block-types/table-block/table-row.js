/**
 * Represents a single row of table.
 * @since 3.29.1
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
import TableColumn from "./table-column";

export default class TableRow {

    /**
     * @param rowData An array of table rows data
     */
    constructor(rowData) {
        this.columns = []

        if ( rowData && rowData.cells && Array.isArray(rowData.cells)) {
            this.columns = rowData.cells.map((column) => {
                return new TableColumn(column);
            })
        }
    }

}