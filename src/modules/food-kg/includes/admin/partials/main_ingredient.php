<?php

use Wordlift\Modules\Food_Kg\Admin\Main_Ingredient_List_Table;

?>


<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Main Ingredients', 'wordlift' ); ?></h1>
	<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=wl_download_ingredients_data&_wpnonce=' . wp_create_nonce( 'wl-dl-ingredients-data-nonce' ) ) ); ?>" class="page-title-action"><?php esc_html_e( 'Download Ingredients Data', 'wordlift' ); ?></a>
	<hr class="wp-header-end">
	<?php

	// Prepare Table of elements
	$main_ingredient_list_table = new Main_Ingredient_List_Table();
	$main_ingredient_list_table->prepare_items();
	$main_ingredient_list_table->display();
	?>
</div>
