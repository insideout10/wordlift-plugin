import HighlightHelper from "../hooks/helpers/highlight-helper";
import CustomFaqElementsRegistry from "../hooks/custom-faq-elements";

it("when given html, should apply inline highlighting to all text nodes", () => {
  const html = `<p>this is a <b>simple</b> html test</p>`;
  const expectedHTML = `<p><wl-faq-answer class="123">this is a </wl-faq-answer><b><wl-faq-answer class="123">simple</wl-faq-answer></b><wl-faq-answer class="123"> html test</wl-faq-answer></p>`;
  const highlightedHTML = HighlightHelper.highlightHTML(html, "wl-faq-answer", "123");
  expect(expectedHTML).toEqual(highlightedHTML);
});

it("when given html to remove inline highlighting, should remove the tags correctly", () => {
  const expectedHTML = `<p>this is a <b>simple</b> html test</p>`;
  const highlightedHTML = `<p><wl-faq-answer class="123">this is a </wl-faq-answer><b><wl-faq-answer class="123">simple</wl-faq-answer></b><wl-faq-answer class="123"> html test</wl-faq-answer></p>`;
  const result = HighlightHelper.removeHighlightingTagsByClassName(highlightedHTML, "wl-faq-answer", "123");
  expect(expectedHTML).toEqual(result);
})