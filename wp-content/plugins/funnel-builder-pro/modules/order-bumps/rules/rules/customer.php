<?php
defined( 'ABSPATH' ) || exit;


class wfob_Rule_Customer_User extends wfob_Rule_Base {

	public function __construct() {
		parent::__construct( 'customer_user' );
	}

	public function get_possible_rule_operators() {
		$operators = array(
			'in'    => __( 'is', 'woofunnels-order-bump' ),
			'notin' => __( 'is not', 'woofunnels-order-bump' ),
		);

		return $operators;
	}

	public function get_possible_rule_values() {
		return null;
	}

	public function get_condition_input_type() {
		return 'User_Select';
	}

	public function is_match( $rule_data ) {
		$user = wp_get_current_user();
		if ( is_wp_error( $user ) ) {
			return null;
		}
		$id = $user->ID;

		$result = in_array( $id, $rule_data['condition'] );
		$result = $rule_data['operator'] == 'in' ? $result : ! $result;

		return $this->return_is_match( $result, $rule_data );
	}
}

class wfob_Rule_Customer_Role extends wfob_Rule_Base {

	public function __construct() {
		parent::__construct( 'customer_role' );
	}

	public function get_possible_rule_operators() {
		$operators = array(
			'in'    => __( 'is', 'woofunnels-order-bump' ),
			'notin' => __( 'is not', 'woofunnels-order-bump' ),
		);

		return $operators;
	}

	public function get_possible_rule_values() {
		$result = array();

		$editable_roles = get_editable_roles();

		if ( $editable_roles ) {
			foreach ( $editable_roles as $role => $details ) {
				$name = translate_user_role( $details['name'] );

				$result[ $role ] = $name;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data ) {
		$user = wp_get_current_user();
		if ( is_wp_error( $user ) ) {
			return null;
		}
		$result = false;
		$id     = $user->ID;
		if ( $rule_data['condition'] && is_array( $rule_data['condition'] ) ) {
			foreach ( $rule_data['condition'] as $role ) {
				$result |= user_can( $id, $role );
			}
		}

		$result = $rule_data['operator'] == 'in' ? $result : ! $result;

		return $this->return_is_match( $result, $rule_data );
	}
}
