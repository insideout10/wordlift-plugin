import EditMappingStoreFilters from "../store/edit-mapping-store-filters";

it("when correct data is provided should convert it in to correct rule field one options", () => {
    const expectedServerData = [
        {
            api_source: "",
            label: "Taxonomy",
            value: "taxonomy"
        },
        {
            api_source: "",
            label: "Post Type",
            value: "post"
        }
    ];

    const result = EditMappingStoreFilters.filterRuleFieldOneData(expectedServerData);
    const filteredData = [
        {
            apiSource: "",
            label: "Taxonomy",
            value: "taxonomy"
        },
        {
            apiSource: "",
            label: "Post Type",
            value: "post"
        }
    ];
    expect(result).toEqual(filteredData);
});

it("when correct data is provided should convert it in to correct rule field two options", () => {
    const serverData = [
        {
            label: "Posts",
            parent_value: "post_type",
            value: "post"
        },
        {
            label: "Pages",
            parent_value: "post_type",
            value: "page"
        }
    ];

    const result = EditMappingStoreFilters.filterRuleFieldTwoData(serverData);

    const expectedData = [
        {
            label: "Posts",
            parentValue: "post_type",
            value: "post"
        },
        {
            label: "Pages",
            parentValue: "post_type",
            value: "page"
        }
    ];

    expect(result).toEqual(expectedData)

});
