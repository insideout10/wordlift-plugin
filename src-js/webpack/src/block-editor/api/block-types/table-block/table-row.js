/**
 * Represents a single row of table.
 * @since 3.29.1
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
import TableColumn, {TABLE_COLUMN_DELIMITER} from "./table-column";

export const TABLE_ROW_DELIMITER = '<wl-table-row-delimiter/>'

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

    getAnalysisHtml() {
        const columns = this.columns.map((column)=> {
            return column.getAnalysisHtml();
        })
        return columns.join("") + TABLE_ROW_DELIMITER
    }


    getAttributeData() {
        const columns = [];
        this.columns.map((column) => {
            return column.getAttributeData()
        })
        return { "cells": columns };
    }



    /**
     *
     * @param html {String}
     */
    static createFromAnalysisHtml(html) {
        const tableRow = new TableRow(null);
        // Set the rows after parsing.
        tableRow.columns = html.split(TABLE_COLUMN_DELIMITER)
            .map((rowHtml) => {
                return TableColumn.createFromAnalysisHtml(rowHtml);
            });

        return tableRow;
    }

}