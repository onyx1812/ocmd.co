<?php
/**
 * Handles CSS output for background fields.
 *
 * @package     WFOCUKirki
 * @subpackage  Controls
 * @copyright   Copyright (c) 2017, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       3.0.0
 */

/**
 * Output overrides.
 */
class WFOCUKirki_Output_Field_Background extends WFOCUKirki_Output {

	/**
	 * Processes a single item from the `output` array.
	 *
	 * @access protected
	 * @param array $output The `output` item.
	 * @param array $value  The field's value.
	 */
	protected function process_output( $output, $value ) {

		$output = wp_parse_args(
			$output,
			array(
				'media_query' => 'global',
				'element'     => 'body',
				'prefix'      => '',
				'suffix'      => '',
			)
		);

		foreach ( array( 'background-image', 'background-color', 'background-repeat', 'background-position', 'background-size', 'background-attachment' ) as $property ) {

			// See https://github.com/aristath/kirki/issues/1808.
			if ( 'background-color' === $property && ( ! isset( $value['background-image'] ) || empty( $value['background-image'] ) ) ) {
				$this->styles[ $output['media_query'] ][ $output['element'] ]['background'] = $output['prefix'] . $this->process_property_value( $property, $value[ $property ] ) . $output['suffix'];
			}

			if ( isset( $value[ $property ] ) && ! empty( $value[ $property ] ) ) {
				$this->styles[ $output['media_query'] ][ $output['element'] ][ $property ] = $output['prefix'] . $this->process_property_value( $property, $value[ $property ] ) . $output['suffix'];
			}
		}
	}
}