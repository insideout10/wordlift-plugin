import Table from "../api/block-types/table-block/table";
import {TABLE_SECTION_DELIMITER} from "../api/block-types/table-block/table-section";
import {TABLE_COLUMN_DELIMITER} from "../api/block-types/table-block/table-column";
import {TABLE_ROW_DELIMITER} from "../api/block-types/table-block/table-row";

const mockTableData = {
    "hasFixedLayout": false,
    "caption": "",
    "head": [
        {
            "cells": [
                {
                    "content": "h1",
                    "tag": "th"
                },
                {
                    "content": "h2",
                    "tag": "th"
                }
            ]
        }
    ],
    "body": [
        {
            "cells": [
                {
                    "content": "r1c1",
                    "tag": "td"
                },
                {
                    "content": "r1c2",
                    "tag": "td"
                }
            ]
        },

    ],
    "foot": [
        {
            "cells": [
                {
                    "content": "f1",
                    "tag": "td"
                },
                {
                    "content": "f2",
                    "tag": "td"
                }
            ]
        }
    ]
}

test("when given mock table table data, should be able to modify the table data correctly", () => {
    const table = new Table(mockTableData.head, mockTableData.body, mockTableData.foot)
    const html = "h1" + TABLE_COLUMN_DELIMITER + "h2" + TABLE_COLUMN_DELIMITER + TABLE_ROW_DELIMITER + TABLE_SECTION_DELIMITER
        + "r1c1" + TABLE_COLUMN_DELIMITER + "r1c2" + TABLE_COLUMN_DELIMITER + TABLE_ROW_DELIMITER + TABLE_SECTION_DELIMITER
        + "f1" + TABLE_COLUMN_DELIMITER + "f2" + TABLE_COLUMN_DELIMITER + TABLE_ROW_DELIMITER + TABLE_SECTION_DELIMITER;
    expect(table.getAnalysisHtml()).toEqual(html)
})

test("when given mock table table data with modified html, should return correct attribute", () => {
    const table = new Table(mockTableData.head, mockTableData.body, mockTableData.foot)
    const html = "<span>h1</span>" + TABLE_COLUMN_DELIMITER + "h2" + TABLE_COLUMN_DELIMITER + TABLE_ROW_DELIMITER + TABLE_SECTION_DELIMITER
        + "<span>r1c1</span>" + TABLE_COLUMN_DELIMITER + "r1c2" + TABLE_COLUMN_DELIMITER + TABLE_ROW_DELIMITER + TABLE_SECTION_DELIMITER
        + "<span>f1</span>" + TABLE_COLUMN_DELIMITER + "f2" + TABLE_COLUMN_DELIMITER + TABLE_ROW_DELIMITER + TABLE_SECTION_DELIMITER;


    const expectedData = {
        "head": [
            {
                "cells": [
                    {
                        "content": "<span>h1</span>",
                        "tag": "th"
                    },
                    {
                        "content": "h2",
                        "tag": "th"
                    }
                ]
            }
        ],
        "body": [
            {
                "cells": [
                    {
                        "content": "<span>r1c1</span>",
                        "tag": "td"
                    },
                    {
                        "content": "r1c2",
                        "tag": "td"
                    }
                ]
            },

        ],
        "foot": [
            {
                "cells": [
                    {
                        "content": "<span>f1</span>",
                        "tag": "td"
                    },
                    {
                        "content": "f2",
                        "tag": "td"
                    }
                ]
            }
        ]
    }
    table.updateFromAnalysisHtml(html)
    expect(table.getAttributeData()).toEqual(expectedData)
})