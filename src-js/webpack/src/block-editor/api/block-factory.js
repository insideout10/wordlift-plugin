import ListBlock from "./list-block";

export default class BlockFactory {

    static getBlock(block, dispatch, start = 0, end = -1) {
        if ( "core/paragraph" === block.name || "core/freeform" === block.name ) {
            return new Block(block, dispatch, start, end)
        }
        else if ( "core/list" ) {
            return new ListBlock(block, dispatch, start, end)
        }
    }


}