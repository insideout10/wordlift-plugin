<?php

use Wordlift\Modules\Food_Kg\Admin\Ingredients_List_Table;

?>
<div class="wrap">
	<h1><?php esc_attr_e( 'Ingredients', 'wordlift' ); ?></h1>
	<?php

	// Prepare Table of elements
	$wp_list_table = new Ingredients_List_Table();
	$wp_list_table->prepare_items();
	$wp_list_table->display();
	?>
</div>
