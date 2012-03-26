<?php

/**
 * log4php
 */
require_once('lib/externals/log4php/Logger.php');

// Tell log4php to use our configuration file.
// temporary change the current directory in order to give a well-known context to the log4php configuration.
$current_directory = getcwd();
chdir(__DIR__);
Logger::configure(__DIR__.'/private/config/log4php.xml');
// return to the initial working directory.
chdir($current_directory);

// register a global logger.
$logger = Logger::getLogger('global');


/**
 * schema.org inclusions
 */
require_once('lib/domain/schema/Thing.php');
require_once('lib/domain/schema/CreativeWork.php');
require_once('lib/domain/schema/GeoCoordinates.php');
require_once('lib/domain/schema/Organization.php');
require_once('lib/domain/schema/Other.php');
require_once('lib/domain/schema/Place.php');
require_once('lib/domain/schema/Person.php');
require_once('lib/domain/schema/Product.php');

/**
 * domain classes
 */
require_once('lib/domain/Entity.php');
require_once('lib/domain/JobModel.php');
require_once('lib/domain/Property.php');
require_once('lib/domain/TextJobRequest.php');
require_once('lib/domain/Type.php');

/**
 * views classes
 */
require_once('lib/views/MapView.php');
require_once('lib/views/admin/EntityMetaBox.php');
require_once('lib/views/PostTileView.php');
require_once('lib/views/EntityTileView.php');
require_once('lib/views/EntityPostView.php');
require_once('lib/views/AllEntitiesView.php');
require_once('lib/views/PostsListView.php');
require_once('lib/views/PostListView.php');
require_once('lib/views/EntitiesMapView.php');

/**
 * services classes
 */
require_once('lib/services/EnhancerJobService.php');
require_once('lib/services/JobService.php');
require_once('lib/services/EntitiesBoxService.php');
require_once('lib/services/SlugService.php');
require_once('lib/services/EntityRankingService.php');
require_once('lib/services/EntityService.php');
require_once('lib/services/FormBuilderService.php');
require_once('lib/services/PostHelper.php');
require_once('lib/services/PropertyService.php');
require_once('lib/services/TermService.php');
require_once('lib/services/TypeService.php');
require_once('lib/services/WordLift.php');
require_once('lib/services/WordLiftSetup.php');


?>