<?php
/**
 * Override field methods
 *
 * @package     WFOCUKirki
 * @subpackage  Controls
 * @copyright   Copyright (c) 2017, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       3.0.0
 */

/**
 * Field overrides.
 */
class WFOCUKirki_Field_FontAwesome extends WFOCUKirki_Field {

	/**
	 * Sets the control type.
	 *
	 * @access protected
	 */
	protected function set_type() {

		$this->type = 'wfocukirki-fontawesome';

	}

	/**
	 * Sets the $sanitize_callback
	 *
	 * @access protected
	 */
	protected function set_sanitize_callback() {

		// If a custom sanitize_callback has been defined,
		// then we don't need to proceed any further.
		if ( ! empty( $this->sanitize_callback ) ) {
			return;
		}
		$this->sanitize_callback = 'esc_attr';

	}
}
