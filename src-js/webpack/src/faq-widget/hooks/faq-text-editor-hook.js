/**
 * FaqTextEditorHook Provides a abstract class for the hooks to implement
 * the methods.
 *
 * @since ???
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class FaqTextEditorHook {
    constructor() {
        this._plugin = this.initializePluginForTextEditor()
        this.listenForTextSelection()
    }
    /**
     * This should listen for the changes in the text editor selection and
     * emit the text.
     */
    listenForTextSelection() {
        this._throwFunctionNotImplementedError("listenForTextSelection()")
    }
    _throwFunctionNotImplementedError( functionName ) {
        throw new Error( functionName + ' should be implemented by the parent class ')
    }
}
export default FaqTextEditorHook