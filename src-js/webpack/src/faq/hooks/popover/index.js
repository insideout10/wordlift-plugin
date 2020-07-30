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
import {Button, createPopover, Popover} from "@wordlift/design";
import "./index.scss"

/**
 * This class renders the popover element in the
 * block | classic editor.
 */

const POPOVER_ELEMENT_ID = "wl-faq-popover-element";

export default class PopoverElement {
  /**
   *
   * @param textEditorHook {FaqTextEditorHook}
   */
  constructor(textEditorHook) {
    // upon construction, add a element with id to the body.
    this.el = document.createElement("div");
    this.el.id = POPOVER_ELEMENT_ID;

    document.body.appendChild(this.el);
    this.hook = textEditorHook;

    on(SELECTION_CHANGED, ({ value, onChange }) => {
      const selection = this.hook.getSelection();
      if (selection.rangeCount === 0) {
        return false;
      }
      const rectangle = selection.getRangeAt(0).getBoundingClientRect();

      ReactDOM.render(
        <React.Fragment>
          <Popover {...rectangle} position={"right"}>
            <Button size={"mini"}>Add Question</Button>
          </Popover>
        </React.Fragment>,
        document.getElementById(POPOVER_ELEMENT_ID)
      );
      console.log("html element");
      console.log(document.getElementById(POPOVER_ELEMENT_ID));
    });
  }

  show() {}

  hide() {}
}
