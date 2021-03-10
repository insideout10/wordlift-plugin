import TableSection from "../api/block-types/table-block/table-section";

const mockTableSectionData = [
    {
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
    },
    {
        "cells": [
            {
                "content": "<span id=\"urn:local-annotation-253356\" class=\"textannotation disambiguated\" itemid=\"http://data.wordlift.io/wl040/entity/foo_bar\">foo bar</span><br>test",
                "tag": "td"
            },
            {
                "content": "",
                "tag": "td"
            }
        ]
    }
]

test("when a table section given, should create correct rows", () => {
    const tableSection = new TableSection(mockTableSectionData);
    expect(tableSection.rows.length).toEqual(2)
})

test("when a table section given a null value should should create zero rows", () => {
    const tableSection = new TableSection(null);
    expect(tableSection.rows.length).toEqual(0)
})

test("when a table section given a invalid value should should create zero rows", () => {
    const tableSection = new TableSection(2);
    expect(tableSection.rows.length).toEqual(0)
})

test("when a table section given a invalid string should should create zero rows", () => {
    const tableSection = new TableSection("some invalid data");
    expect(tableSection.rows.length).toEqual(0)
})

test("when a table section given undefined should should create zero rows", () => {
    const tableSection = new TableSection(undefined);
    expect(tableSection.rows.length).toEqual(0)
})