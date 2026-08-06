import { setWordPressTaxonomySelection } from "../index.schema-class-tree";

describe("setWordPressTaxonomySelection", () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <ul id="wl_entity_typechecklist">
        <li><input id="in-wl_entity_type-123-2" value="123" type="checkbox"></li>
      </ul>
      <ul id="wl_entity_typechecklist-pop">
        <li><input id="in-popular-wl_entity_type-123" value="123" type="checkbox"></li>
      </ul>
      <input id="unrelated-123" value="123" type="checkbox">
    `;
  });

  it("updates matching taxonomy inputs independently of their generated IDs", () => {
    setWordPressTaxonomySelection(123, true);

    expect(document.getElementById("in-wl_entity_type-123-2").checked).toBe(true);
    expect(document.getElementById("in-popular-wl_entity_type-123").checked).toBe(true);
    expect(document.getElementById("unrelated-123").checked).toBe(false);

    setWordPressTaxonomySelection(123, false);

    expect(document.getElementById("in-wl_entity_type-123-2").checked).toBe(false);
    expect(document.getElementById("in-popular-wl_entity_type-123").checked).toBe(false);
  });
});
