import TableRow from "../api/block-types/table-block/table-row";

const mockRowData = {
    "cells": [
        {
            "content": "<span id=\"urn:local-annotation-520881\" class=\"textannotation disambiguated\" itemid=\"http://data.wordlift.io/wl040/entity/my_entity\">My entity</span>",
            "tag": "td"
        },
        {
            "content": "",
            "tag": "td"
        }
    ]
}

test("when a table section given, should create correct columns", () => {
    const tableSection = new TableRow(mockRowData);
    expect(tableSection.columns.length).toEqual(2)
})