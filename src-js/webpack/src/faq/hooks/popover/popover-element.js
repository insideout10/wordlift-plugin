/**
 * External dependencies.
 */
import { on } from "backbone";
import React from "react";
import ReactDOM from "react-dom";
/**
 * Internal dependencies.
 */
import { SELECTION_CHANGED } from "../../../common/constants";
import { Button, Popover } from "@wordlift/design";

/**
 * This class renders the popover element in the
 * block | classic editor.
 */

const POPOVER_ELEMENT_ID = "wl-faq-popover-element";

export default class PopoverElement {
  constructor() {
    // upon construction, add a element with id to the body.
    this.el = document.createElement("div");
    this.el.id = POPOVER_ELEMENT_ID;
    this.el.style.position = 'fixed';
    document.body.appendChild(this.el);

    on(SELECTION_CHANGED, ({ value, onChange }) => {
      const selection = window.getSelection();
      let range;
      try {
        // Get the range from window.
        range = selection.getRangeAt(0);

      }
      catch (e) {
        console.log("error caught")
        console.log(e)
        return false;
      }
      const rectangle = range.getBoundingClientRect();

      ReactDOM.render(
        <React.Fragment>
          <Popover {...rectangle}>
            <Button size={"mini"}>Add Question</Button>
          </Popover>
        </React.Fragment>,
        document.getElementById(POPOVER_ELEMENT_ID)
      );
      console.log("html element")
      console.log( document.getElementById(POPOVER_ELEMENT_ID))
    });
  }

  show() {}

  hide() {}
}
