import WordLiftIcon from "../../../../src/images/svg/wl-logo-icon.svg";

const { Fragment } = wp.element;
const { Panel, PanelBody, PanelRow } = wp.components;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
const { registerPlugin } = wp.plugins;

const PLUGIN_NAMESPACE = "wordlift";

const PanelContentClassification = () => (
    <Panel>
        <PanelBody
            title="Content classification"
            initialOpen={ false }
        >
            <PanelRow>
                Content classification Inputs and Labels
            </PanelRow>
        </PanelBody>
    </Panel>
);

const PanelArticleMetadata = () => (
    <Panel>
        <PanelBody
            title="Article metadata"
            initialOpen={ false }
        >
            <PanelRow>
                Article metadata Inputs and Labels
            </PanelRow>
        </PanelBody>
    </Panel>
);

const PanelSuggestedImages = () => (
    <Panel>
        <PanelBody
            title="Suggested images"
            initialOpen={ false }
        >
            <PanelRow>
                Suggested images Inputs and Labels
            </PanelRow>
        </PanelBody>
    </Panel>
);

const PanelRelatedPosts = () => (
    <Panel>
        <PanelBody
            title="Related posts"
            initialOpen={ false }
        >
            <PanelRow>
                Related posts Inputs and Labels
            </PanelRow>
        </PanelBody>
    </Panel>
);

const WordLiftSidebar = () => (
    <Fragment>
        <PluginSidebarMoreMenuItem
            target="wordlift-sidebar"
            icon={ <WordLiftIcon /> }
        >
            WordLift
        </PluginSidebarMoreMenuItem>
        <PluginSidebar
            name="wordlift-sidebar"
            title="WordLift"
        >
            <PanelContentClassification />
            <PanelArticleMetadata />
            <PanelSuggestedImages />
            <PanelRelatedPosts />
        </PluginSidebar>
    </Fragment>
);

registerPlugin( PLUGIN_NAMESPACE, {
    render: WordLiftSidebar,
    icon: <WordLiftIcon />
} );