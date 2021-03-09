import ListBlock from "./list-block";
import TextBlock from "./text-block";

export default class BlockFactory {

    static getBlock(block, dispatch, start = 0, end = -1) {
        if ( "core/paragraph" === block.name || "core/freeform" === block.name ) {
            return new TextBlock(block, dispatch, start, end)
        }
        else if ( "core/list" ) {
            console.log("building list block for ")
            console.log(block)
            return new ListBlock(block, dispatch, start, end)
        }
    }


}