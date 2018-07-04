/**
 * Containers: Ranking Panel.
 *
 * A {@link Panel} which is hidden when there's no selected node.
 *
 * @since 3.20.0
 */
import { connect } from "react-redux";
import Panel from "../../../components/Panel";

const mapStateToProps = state => ({
  display: null !== state.node ? "initial" : "none"
});

const RankingPanel = connect(mapStateToProps)(Panel);

export default RankingPanel;
