import TableSection from "../api/block-types/table-block/table-section";

const mockTableSectionData = [
    {
        "cells": [
            {
                "content": "foo",
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
                "content": "bar",
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

test("when a table section given, should create correct analysis html", () => {
    const tableSection = new TableSection(mockTableSectionData);
    const html = tableSection.getAnalysisHtml();
    expect(html).toEqual("foo<wl-table-column-delimiter/>bar<wl-table-column-delimiter/><wl-table-row-delimiter/><wl-table-section-delimiter/>")
})