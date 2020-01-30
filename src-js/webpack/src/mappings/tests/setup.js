/**
 * The file is used to provide globals for running tests in the mappings module.
 * NOTE: all the settings here are mocked, the mocked settings are provided to components.
 *
 */
import MockHttpServer from "./MockHttpServer";

const mappingsConfig = {
  rest_url: "https://wordlift.localhost/index.php?rest_route=/wordlift/v1/mappings",
  wl_mapping_nonce: "b23118674e",
  wl_edit_mapping_nonce: "f4ec1e5ee6"
};

global.wlMappingsConfig = mappingsConfig;
global.MockHttpServer = new MockHttpServer();
global.fetch = () => {
  const response = global.MockHttpServer.dequeueResponse()
  if (response  != null) {
    return new Promise((resolve, reject) => {
      resolve({
        ok: true,
        status:200,
        json: () => {
          return response
        },
      });
    });
  }
  else {
    return new Promise().reject()
  }
};
export const editMappingsConfig = {
  rest_url: "https://wordlift.localhost/index.php?rest_route=/wordlift/v1/mappings",
  wl_edit_mapping_rest_nonce: "b23118674e",
  wl_edit_mapping_id: "11",
  wl_add_mapping_text: "Add Mapping",
  wl_edit_mapping_text: "Edit Mapping",
  wl_edit_mapping_no_item: "Unable to find the mapping item",
  page: "wl_edit_mapping",
  wl_transform_function_options: [
    {
      label: "URL to Entity",
      value: "url_to_entity"
    },
    {
      label: "Taxonomy to Terms",
      value: "taxonomy_to_terms"
    },
    {
      label: "HowToStep Transform function",
      value: "how_to_step_transform_function"
    },
    {
      label: "HowToSupply Transform function",
      value: "how_to_supply_transform_function"
    },
    {
      label: "HowToTool Transform function",
      value: "how_to_tool_transform_function"
    },
    {
      label: "HowTo Total Time Transform function",
      value: "how_to_total_time_transform_function"
    }
  ],
  wl_field_type_options: [
    {
      label: "Fixed Text",
      value: "text"
    },
    {
      label: "Custom Field",
      value: "custom_field"
    },
    {
      label: "ACF",
      value: "acf"
    }
  ],
  wl_field_name_options: [
    {
      field_type: "text",
      value: "",
      label: "Fixed Text"
    },
    {
      field_type: "custom_field",
      value: "",
      label: "Custom Field"
    },
    {
      field_type: "acf",
      label: "ACF",
      value: [
        {
          group_name: "How To - Required fields",
          group_options: [
            {
              label: "Name",
              value: "field_5e16df2e60987"
            },
            {
              label: "Step",
              value: "field_5e09d8a36d4fd"
            }
          ]
        },
        {
          group_name: "HowTo - Recommended fields",
          group_options: [
            {
              label: "Description",
              value: "field_5e16df2e60988"
            },
            {
              label: "Total Time",
              value: "field_5e16df2e60990"
            },
            {
              label: "Estimated Cost",
              value: "field_5e16df2e60989"
            },
            {
              label: "HowToSupply",
              value: "field_5e16cb76dd915"
            },
            {
              label: "HowToTool",
              value: "field_5e16decc6097f"
            }
          ]
        }
      ]
    }
  ],
  wl_logic_field_options: [
    {
      label: "is equal to",
      value: "==="
    },
    {
      label: "is not equal to",
      value: "!=="
    }
  ],
  wl_rule_field_one_options: [
    {
      label: "Categories",
      value: "category",
      isTermsFetchedForTaxonomy: false
    },
    {
      label: "Tags",
      value: "post_tag",
      isTermsFetchedForTaxonomy: false
    },
    {
      label: "Format",
      value: "post_format",
      isTermsFetchedForTaxonomy: false
    },
    {
      label: "Topics",
      value: "wl_topic",
      isTermsFetchedForTaxonomy: false
    },
    {
      label: "Entity Types",
      value: "wl_entity_type",
      isTermsFetchedForTaxonomy: false
    },
    {
      label: "Post type",
      value: "post_type",
      isTermsFetchedForTaxonomy: true
    }
  ],
  wl_rule_field_two_options: [
    {
      label: "Posts",
      value: "post",
      taxonomy: "post_type"
    },
    {
      label: "Pages",
      value: "page",
      taxonomy: "post_type"
    },
    {
      label: "Media",
      value: "attachment",
      taxonomy: "post_type"
    },
    {
      label: "Revisions",
      value: "revision",
      taxonomy: "post_type"
    },
    {
      label: "Navigation Menu Items",
      value: "nav_menu_item",
      taxonomy: "post_type"
    },
    {
      label: "Custom CSS",
      value: "custom_css",
      taxonomy: "post_type"
    },
    {
      label: "Changesets",
      value: "customize_changeset",
      taxonomy: "post_type"
    },
    {
      label: "oEmbed Responses",
      value: "oembed_cache",
      taxonomy: "post_type"
    },
    {
      label: "User Requests",
      value: "user_request",
      taxonomy: "post_type"
    },
    {
      label: "Blocks",
      value: "wp_block",
      taxonomy: "post_type"
    },
    {
      label: "Field Groups",
      value: "acf-field-group",
      taxonomy: "post_type"
    },
    {
      label: "Fields",
      value: "acf-field",
      taxonomy: "post_type"
    },
    {
      label: "Vocabulary",
      value: "entity",
      taxonomy: "post_type"
    }
  ]
};
