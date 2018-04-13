<?php
/**
 * Installs: Install Version 3.18.3.
 *
 * @since      3.18.3
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_3_18_3} interface.
 *
 * @since      3.18.3
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_3_18_3 extends Wordlift_Install {
	/**
	 * @inheritdoc
	 */
	protected static $version = '3.18.3';

	/**
	 * @inheritdoc
	 */
	public function install() {
		$this->set_article_term_to_posts();
	}

	/**
	 * Set default article term to posts
	 * that exists in `wl_relation_instances` table.
	 *
	 * @since 3.18.3
	 *
	 * @return mixed false on failure or if the `article` doesn't exists
					 int on success.
	 */
	public function set_article_term_to_posts() {
		// Load the global $wpdb;
		global $wpdb;

		// Get the article term.
		$term = get_term_by(
			'slug',
			'article',
			Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME
		);

		// Bail if term doesn't exist.
		if ( empty( $term ) ) {
			return false;
		}

		// Set `article` term to all posts that exists in
		// `wl_relation_instances` table and don't have `article` term set.
		$result = $wpdb->query(
			$wpdb->prepare(
				"
				INSERT INTO $wpdb->term_relationships ( object_id, term_taxonomy_id, term_order )
					SELECT DISTINCT p.ID, %1\$d, 0
					FROM $wpdb->posts AS p
					INNER JOIN {$wpdb->prefix}wl_relation_instances AS ri 
						ON p.ID = ri.subject_id
					WHERE p.post_status = 'publish'
					AND p.post_type = '%2\$s'
					AND (
						p.ID NOT IN (
							SELECT object_id
							FROM $wpdb->term_relationships
							WHERE term_taxonomy_id IN (%1\$d)
						)
					)
				",
				$term->term_id,
				'post',
				$term->term_id
			)
		);

		// Return the result of the query.
		return $result;
	}

}
