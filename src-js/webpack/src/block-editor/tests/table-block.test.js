import Table from "../api/block-types/table-block/table";

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
        {
            "cells": [
                {
                    "content": "r2c1",
                    "tag": "td"
                },
                {
                    "content": "r2c2",
                    "tag": "td"
                }
            ]
        }
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

    const table  = new Table(mockTableData.head, mockTableData.body, mockTableData.foot)
    const html = TABLE_SECTION_DELIMITER
    expect(table.getAnalysisHtml()).equals()

})

