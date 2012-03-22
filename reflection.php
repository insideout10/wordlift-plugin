<html>
	<head>
		<link rel='stylesheet' href='/wordlift/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=admin-bar,wp-admin&amp;ver=7f0753feec257518ac1fec83d5bced6a' type='text/css' media='all' />
		<style>

		</style>
	</head>
<body>

<div class="container">
<?php

require_once 'classes/services/PropertyService.php';
require_once 'classes/services/TypeService.php';
require_once 'classes/services/FormBuilderService.php';
require_once 'classes/model/Thing.php';
require_once 'classes/model/Person.php';

$field_prefix = 'io-wordlift-';

$types = TypeService::get_types();
FormBuilderService::build_type_selection($types,$field_prefix);

$type = TypeService::create('Person');

FormBuilderService::build_form_for_type($type,$field_prefix);

?>
</div>

</body>
</html>