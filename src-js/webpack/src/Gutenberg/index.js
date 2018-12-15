import PluginIcon from "../../../../src/images/svg/wl-logo-icon.svg";

const { Fragment } = wp.element;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
const { registerPlugin } = wp.plugins;

const Component = () => (
    <Fragment>
        <PluginSidebarMoreMenuItem
            target="wl-plugin-sidebar"
        >
            WordLift
        </PluginSidebarMoreMenuItem>
        <PluginSidebar
            name="wl-plugin-sidebar"
            title="WordLift"
        >
            Content of the WordLift sidebar
        </PluginSidebar>
    </Fragment>
);

registerPlugin( 'wl-plugin', {
    icon: <PluginIcon />,
    render: Component,
} );