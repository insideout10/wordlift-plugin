<?php
/**
 * This file sets all the Recipe maker cpt posts to
 * the entity type `Recipe`.
 */

use Wordlift\External_Plugin_Hooks\Recipe_Maker\Recipe_Maker_Entity_Type_Procedure;

/**
 * @since 3.27.1
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Wordlift_Install_3_27_1 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.27.1';

	public function install() {
		$procedure = new Recipe_Maker_Entity_Type_Procedure();
		$procedure->run_procedure();
	}

}
