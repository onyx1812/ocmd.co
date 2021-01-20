<?php
defined( 'ABSPATH' ) || exit;

final class WFOB_Add_New_Position {

	private $data = [
		'position_id'   => '',
		'hook'          => '',
		'position_name' => '',
		'hook_priority' => ''
	];
	private $position_id = '';
	private $hook = '';
	private $position_name = '';
	private $hook_priority = 21;

	public function __construct( $data ) {
		if ( ! empty( $data ) ) {
			$this->data = array_merge( $this->data, $data );
			if ( false === $this->validate() ) {
				return;
			}
			add_filter( 'wfob_bump_positions', [ $this, 'add_new_place' ] );
			add_action( 'wfob_position_not_found', [ $this, 'attach_new_place' ], 10, 2 );
			add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'add_to_fragments' ], 100 );
		}

	}


	private function validate() {
		if ( empty( $this->data['position_id'] ) ) {
			return false;
		}
		if ( empty( $this->data['hook'] ) ) {
			return false;
		}
		if ( empty( $this->data['position_name'] ) ) {
			return false;
		}
		if ( ! is_numeric( $this->data['hook_priority'] ) ) {
			return false;
		}
		foreach ( $this->data as $key => $val ) {
			$this->{$key} = trim( $val );
		}
	}

	public function add_new_place( $positions ) {

		$positions[ $this->position_id ] = [
			'name'     => $this->position_name,
			'hook'     => $this->hook,
			'priority' => $this->hook_priority,
			'id'       => $this->position_id
		];

		return $positions;
	}

	public function attach_new_place( $position_id, $position ) {
		$hook = $position['hook'];
		if ( $position_id == $this->position_id ) {
			add_action( $hook, [ $this, 'print_position' ], $this->hook_priority );
		}
	}

	public function print_position() {

		printf( "<div class='wfob_bump_wrapper {$this->position_id}'></div>" );
	}

	public function add_to_fragments( $fragments ) {
		return WFOB_Core()->public->get_bump_html( $fragments, $this->position_id );
	}

}
