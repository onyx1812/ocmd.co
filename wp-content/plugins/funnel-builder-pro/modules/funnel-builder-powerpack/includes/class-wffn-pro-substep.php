<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * This class will be extended by all all single substep(like ordebump) to register different substeps
 * Class WFFN_Pro_Substep
 */
abstract class WFFN_Pro_Substep extends WFFN_Pro_Step_Base {

	public $slug = '';
	public $id;

	/**
	 * WFFN_Substep constructor.
	 *
	 * @param int $id
	 */
	public function __construct( $id = 0 ) {
		$this->id = $id;
	}



	public function get_substep_data( $substep ) {
		return [];
	}

	/**
	 * @param $control_id
	 *
	 * @return array
	 */
	public function maybe_get_ab_variants( $control_id ) {
		return [];
	}
}
