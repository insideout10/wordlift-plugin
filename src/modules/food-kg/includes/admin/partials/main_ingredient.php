<?php

use Wordlift\Modules\Food_Kg\Admin\Main_Ingredient_List_Table;

?>
<div class="wrap">
	<h1><?php esc_attr_e( 'Main Ingredients', 'wordlift' ); ?></h1>
	<?php

	// Prepare Table of elements
	$main_ingredient_list_table = new Main_Ingredient_List_Table();
	$main_ingredient_list_table->prepare_items();
	$main_ingredient_list_table->display();
	?>
</div>
