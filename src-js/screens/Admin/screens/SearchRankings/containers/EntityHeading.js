/**
 * Containers: Entity Heading.
 *
 * An `h2` element which displays the entity name.
 *
 * @since 3.20.0
 */
import Heading from "../../../components/Heading";
import { connect } from "react-redux";

const mapStateToProps = state => ({
  title: null !== state.node ? `Entity ${state.node.entity.label}` : "",
  visible: null !== state.node
});

const EntityHeading = connect(mapStateToProps)(Heading);

export default EntityHeading;
