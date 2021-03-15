/**
 * Factory to create different types of the blocks.
 */

import TextBlock from "./block-types/text-block";
import ListBlock from "./block-types/list-block";
import TableBlock from "./block-types/table-block/table-block";

export default  class BlockFactory {

    static getBlock( block, dispatch, start) {
        if ( "core/paragraph" === block.name || "core/freeform" === block.name ) {
            return new TextBlock(block, dispatch, start);
        }
        else if ( "core/list" === block.name ) {
            return new ListBlock(block, dispatch, start);
        }
        else if ( "core/table" === block.name ) {
            return new TableBlock(block, dispatch, start);
        }
    }

}