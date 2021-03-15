import TableSection from "../api/block-types/table-block/table-section";

const mockTableSectionData = [
    {
        "cells": [
            {
                "content": "foo1",
                "tag": "td"
            },
            {
                "content": "bar1",
                "tag": "td"
            }
        ]
    },
    {
        "cells": [
            {
                "content": "foo2",
                "tag": "td"
            },
            {
                "content": "bar2",
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
    expect(html).toEqual("foo1<wl-table-column-delimiter/>bar1<wl-table-column-delimiter/><wl-table-row-delimiter/>" +
        "foo2<wl-table-column-delimiter/>bar2<wl-table-column-delimiter/><wl-table-row-delimiter/>" +
        "<wl-table-section-delimiter/>")
})


test("when a table section html given should create the original object", () => {
    const tableSection = new TableSection(mockTableSectionData);

    const expectedData = [
        {
            "cells": [
                {
                    "content": "foo3",
                    "tag": "td"
                },
                {
                    "content": "bar3",
                    "tag": "td"
                }
            ]
        },
        {
            "cells": [
                {
                    "content": "foo4",
                    "tag": "td"
                },
                {
                    "content": "bar4",
                    "tag": "td"
                }
            ]
        }
    ]

    tableSection.updateFromAnalysisHtml("foo3<wl-table-column-delimiter/>bar3<wl-table-column-delimiter/><wl-table-row-delimiter/>" +
        "foo4<wl-table-column-delimiter/>bar4<wl-table-column-delimiter/><wl-table-row-delimiter/>")

    expect(tableSection.getAttributeData()).toEqual(expectedData);
})