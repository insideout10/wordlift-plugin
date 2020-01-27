/**
 * MappingHeaderTitle : it displays the mapping header table title with sort functionality.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import {connect} from "react-redux";

/**
 * Internal dependencies.
 */
import {MAPPING_LIST_SORT_TITLE_CHANGED_ACTION} from "../../actions/actions";

/**
 *
 * @param props Object passed from { @link MappingComponent }
 * @returns MappingTableTitleSort Instance
 */
class _MappingHeaderTitle  extends React.Component {
    constructor(props) {
        super(props);
        this.sortMappingItemsByTitle = this.sortMappingItemsByTitle.bind(this)
    }
    sortMappingItemsByTitle() {
        this.props.dispatch( MAPPING_LIST_SORT_TITLE_CHANGED_ACTION )
    }
    render() {
        return (
            <th>
                <a
                    className="row-title wl-mappings-link"
                    onClick={() => {
                        this.sortMappingItemsByTitle()
                    }}
                >
                    Title
                    <span className={"dashicons " + this.props.titleIcon}> </span>
                </a>
            </th>
        )
    }
}
export const MappingHeaderTitle = connect( (state) => ({
    titleIcon: state.titleIcon
}))(_MappingHeaderTitle);