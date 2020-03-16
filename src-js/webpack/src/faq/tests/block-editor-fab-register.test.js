import BlockEditorFabButtonRegister, {FAB_WRAPPER_ID} from "../hooks/block-editor/block-editor-fab-button-register";

it("when the floating action button is registered, should be able to find it in the DOM", () => {
  const handler = new BlockEditorFabButtonRegister();
  handler.registerFabButton();
  expect(document.getElementById(FAB_WRAPPER_ID)).not.toEqual(null);
});
