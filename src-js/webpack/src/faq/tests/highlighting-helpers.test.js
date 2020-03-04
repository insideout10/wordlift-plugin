import { createTinymceHighlightHTML } from "../hooks/block-editor/helpers";
import { FAQ_ANSWER_TAG_NAME, FAQ_QUESTION_TAG_NAME } from "../hooks/custom-faq-elements";

it("when the plain text is given to the tinymce highlighting helper, should return the text with our highlighting tag", () => {
  const text = `
        foo text
        foo bar
    `;
  const expectedText = `
        <wl-faq-answer>
            foo text
            foo bar
        </wl-faq-answer>
    `;
  // Compare it ignoring spaces.
  expect(
    createTinymceHighlightHTML(text, FAQ_ANSWER_TAG_NAME)
      .trim()
      .replace(/\s/g, "")
  ).toEqual(expectedText.trim().replace(/\s/g, ""));
});

it("when the html with tags are given then apply the tags inside it", () => {

})