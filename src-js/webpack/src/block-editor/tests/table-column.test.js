import TableColumn, {TABLE_COLUMN_DELIMITER} from "../api/block-types/table-block/table-column";

test("when a table column is given false should return empty for analysis html", () => {
    const tableColumn = new TableColumn({content: false});
    expect(tableColumn.getAnalysisHtml()).toEqual(TABLE_COLUMN_DELIMITER)
})

test("when a table column is given null should return empty for analysis html", () => {
    const tableColumn = new TableColumn({content: null});
    expect(tableColumn.getAnalysisHtml()).toEqual(TABLE_COLUMN_DELIMITER)
})

test("when a table column is given undefined should return empty for analysis html", () => {
    const tableColumn = new TableColumn({});
    expect(tableColumn.getAnalysisHtml()).toEqual(TABLE_COLUMN_DELIMITER)
})