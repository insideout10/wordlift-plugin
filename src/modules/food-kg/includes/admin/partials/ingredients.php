<?php

use Wordlift\Modules\Food_Kg\Admin\Ingredients_List_Table;

?>
<div class="wrap">
	<h1><?php esc_attr_e( 'Ingredients', 'wordlift' ); ?></h1>
	<?php

	// Prepare Table of elements
	$ingredients_list_table = new Ingredients_List_Table();
	$ingredients_list_table->prepare_items();
	$ingredients_list_table->display();
	?>
</div>
