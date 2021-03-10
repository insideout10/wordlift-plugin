import TableSection from "../api/block-types/table-block/table-section";

test("when a table section given, should create html content separated by delimiter", () => {

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
    const tableSection = new TableSection(mockTableSectionData);
    expect(tableSection.rows.length).toEqual(2)

})