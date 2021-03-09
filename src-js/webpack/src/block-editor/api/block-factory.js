import ListBlock from "./list-block";
import TextBlock from "./text-block";

export default class BlockFactory {

    static getBlock(block, dispatch, start = 0, blockSeparatorLength = -1) {
        if ("core/paragraph" === block.name || "core/freeform" === block.name) {
            return new TextBlock(block, dispatch, start, blockSeparatorLength)
        } else if ("core/list") {
            return new ListBlock(block, dispatch, start, blockSeparatorLength)
        }
    }


}