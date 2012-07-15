<?php

/**
 * Provides access to the WordPress categories.
 */
class CategoryService {
	
	/**
	 * Returns a hierarchical view of the categories.
	 */
	public function getCategoriesAsHierarchy() {
		$args = array(
				'hide_empty' => 0
		);
		
		$rootCategory = null;
		$categories = array();
		
		$result = get_categories($args);
		
		foreach ($result as $category) {
			$categories[$category->cat_ID] = array(
						'name' => $category->name,
						'slug' => $category->slug,
						'category_parent' => $category->category_parent,
						'childrenCategories' => array()
					);
		}
		
		foreach ($categories as &$category) {

			if ('0' === $category['category_parent'] && 'uncategorized' != $category['slug']) {
				$rootCategory = &$category;
				continue;
			}
			
			if (null != $categories[$category['category_parent']]) {
				$categories[$category['category_parent']]['childrenCategories'][] = &$category;
			}
		}
		
		return $rootCategory;
	}
	
	public function getPath(&$categories, $separator = '\\', $skipHome = true) {
		foreach ($categories as &$category) {
			# skip if skipHome has been set and this is the home category.
			if ('0' === $category->category_parent)
				continue;
			
			# add up to the path.
			$path .= $category->name . ' ' . $separator . ' ';
		}
		return $path;
	}
	
}

?>