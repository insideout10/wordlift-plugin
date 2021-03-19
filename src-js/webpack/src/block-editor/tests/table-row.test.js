import TableRow from "../api/block-types/table-block/table-row";
import TableSection from "../api/block-types/table-block/table-section";

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

test("when a table row given, should create correct columns", () => {
    const tableSection = new TableRow(mockRowData);
    expect(tableSection.columns.length).toEqual(2)
})



test("when a table row given a null value should should create zero rows", () => {
    const tableSection = new TableRow(null);
    expect(tableSection.columns.length).toEqual(0)
})

test("when a table section given a invalid value should should create zero rows", () => {
    const tableSection = new TableRow(2);
    expect(tableSection.columns.length).toEqual(0)
})

test("when a table section given a invalid string should should create zero rows", () => {
    const tableSection = new TableRow("2");
    expect(tableSection.columns.length).toEqual(0)
})

test("when a table section given undefined should should create zero rows", () => {
    const tableSection = new TableRow(undefined );
    expect(tableSection.columns.length).toEqual(0)
})