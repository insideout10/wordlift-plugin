# Constants
CONTEXT = '@context'
GRAPH = '@graph'
VALUE = '@value'

ANALYSIS_EVENT = 'analysisReceived'
CONFIGURATION_TYPES_EVENT = 'configurationTypesLoaded'

RDFS = 'http://www.w3.org/2000/01/rdf-schema#'
RDFS_LABEL = "#{RDFS}label"
RDFS_COMMENT = "#{RDFS}comment"

FREEBASE = 'freebase'
FREEBASE_COM = "http://rdf.#{FREEBASE}.com/"
FREEBASE_NS = "#{FREEBASE_COM}ns/"
FREEBASE_NS_DESCRIPTION = "#{FREEBASE_NS}common.topic.description"

SCHEMA_ORG = 'http://schema.org/'
SCHEMA_ORG_DESCRIPTION = "#{SCHEMA_ORG}description"

FISE_ONT = 'http://fise.iks-project.eu/ontology/'
FISE_ONT_ENTITY_ANNOTATION = "#{FISE_ONT}EntityAnnotation"
FISE_ONT_TEXT_ANNOTATION = "#{FISE_ONT}TextAnnotation"
FISE_ONT_CONFIDENCE = "#{FISE_ONT}confidence"

DCTERMS = 'http://purl.org/dc/terms/'

DBPEDIA = 'dbpedia'
DBPEDIA_ORG = "http://#{DBPEDIA}.org/"
DBPEDIA_ORG_REGEX = "http://(\\w{2}\\.)?#{DBPEDIA}.org/"

WORDLIFT = 'wordlift'

WGS84_POS = 'http://www.w3.org/2003/01/geo/wgs84_pos#'

# Define some constants for commonly used strings.
EDITOR_ID = 'content'
TEXT_ANNOTATION = 'textannotation'
CONTENT_IFRAME = '#content_ifr'
RUNNING_CLASS = 'running'
MCE_WORDLIFT = '.mce_wordlift, .mce-wordlift button'
CONTENT_EDITABLE = 'contenteditable'
TEXT_HTML_NODE_TYPE = 3

DEFAULT_ENTITY_ANNOTATION_CONFIDENCE_LEVEL = 1.0
