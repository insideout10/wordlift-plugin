<?php

use Wordlift\Modules\Food_Kg\Admin\Main_Ingredient_List_Table;

?>
<div class="wrap">
	<div class="wl-ingredients">
		<h1><?php esc_attr_e( 'Main Ingredients', 'wordlift' ); ?></h1>
		<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=wl_download_ingredients_data' ) ); ?>" class="wl-ingredients__btn-copy-table"><?php esc_html_e( 'Copy Table', 'wordlift' ); ?></a>
	</div>
	<?php

	// Prepare Table of elements
	$main_ingredient_list_table = new Main_Ingredient_List_Table();
	$main_ingredient_list_table->prepare_items();
	$main_ingredient_list_table->display();
	?>
</div>
