<?php
/**
 * This file defines an interface for the Rule Validators.
 *
 * The interface defines just a couple of functions: the label which should be displayed in UI and the is_valid
 * function which is used to validate a post.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Mappings\Validators
 */

namespace Wordlift\Mappings\Validators;

/**
 * Interface Rule_Validator
 *
 * @package Wordlift\Mappings\Validators
 */
interface Rule_Validator {

	const IS_EQUAL_TO     = '===';
	const IS_NOT_EQUAL_TO = '!==';

	/**
	 * Get the validator label.
	 *
	 * @return string The validator label.
	 */
	public function get_label();

	/**
	 * Test whether a post passes a validation.
	 *
	 * @param int    $identifier The post id or term id.
	 * @param string $operator The operator.
	 * @param string $operand_1 The first operand.
	 * @param string $operand_2 The second operand.
	 * @param string $type The type is either post or term.
	 *
	 * @return bool Whether the post passes or not the validation.
	 */
	public function is_valid( $identifier, $operator, $operand_1, $operand_2, $type );

}
