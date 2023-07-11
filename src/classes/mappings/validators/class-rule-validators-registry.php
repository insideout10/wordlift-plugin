<?php
/**
 * This file defines the Rule Validators Registry which holds the list of Validators installed in the system.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Mappings\Validators
 */

namespace Wordlift\Mappings\Validators;

use Exception;

/**
 * Class Rule_Validators_Registry
 *
 * @package Wordlift\Mappings\Validators
 */
class Rule_Validators_Registry {

	/**
	 * An array of {@link Rule_Validator}s.
	 *
	 * @var array An array of {@link Rule_Validator}s.
	 */
	private $rule_validators;

	/**
	 * Rule_Validators_Registry constructor.
	 *
	 * @param Rule_Validator $default The default rule validator.
	 *
	 * @throws Exception throws an exception if an invalid validator has been provided.
	 */
	public function __construct( $default ) {

		// Check that a valid validator has been provided.
		if ( ! ( $default instanceof Rule_Validator ) ) {
			throw new Exception( 'An invalid Rule_Validator was provided as default validator.' );
		}

		// Allow 3rd parties to register other validators.
		$this->rule_validators = apply_filters( 'wl_mappings_rule_validators', array( '__default__' => $default ) );

	}

	/**
	 * Get a rule validator by its key.
	 *
	 * @param string $key A key uniquely identifying a validator.
	 *
	 * @return Rule_Validator A {@link Rule_Validator} instance or the default one when not found.
	 */
	public function get_rule_validator( $key ) {

		return isset( $this->rule_validators[ $key ] )
			? $this->rule_validators[ $key ] : $this->rule_validators['__default__'];
	}

}
