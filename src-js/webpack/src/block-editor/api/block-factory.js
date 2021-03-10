/**
 * Factory to create different types of the blocks.
 */

import TextBlock from "./text-block";
import ListBlock from "./list-block";

export default  class BlockFactory {

    static getBlock( block, dispatch, start) {
        if ( "core/paragraph" === block.name || "core/freeform" === block.name ) {
            return new TextBlock(block, dispatch, start);
        }
        else if ( "core/list" === block.name ) {
            return new ListBlock(block, dispatch, start);
        }
    }

}