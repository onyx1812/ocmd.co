<?php
//Updating contact and customer tables functions in background
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BWF_THRESHOLD_ORDERS', 0 ); //defining it more than 0 means you want the background to run only on "n" orders
define( 'BWF_ORDERS_PER_BATCH', 20 ); //defining it means how many orders to process per batch operation

/*** Updating customer tables ***/
if ( ! function_exists( 'bwf_create_update_contact_customer' ) ) {
	/**
	 *
	 * @return bool|string
	 */
	function bwf_create_update_contact_customer() {

		add_action( 'shutdown', [ WooFunnels_Dashboard::$classes['WooFunnels_DB_Updater'], 'capture_fatal_error' ] );
		/**
		 * get the offset and the threshold of max orders to process
		 */
		$offset = get_option( '_bwf_offset', 0 );

		$get_threshold_order = get_option( '_bwf_order_threshold', BWF_THRESHOLD_ORDERS );

		/**
		 * IF we do not find threshold, then query it
		 */

		add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'woofunnels_handle_indexed_orders', 10, 2 );
		if ( 0 === $get_threshold_order ) {
			$all_order_ids = wc_get_orders( array(
				'return'      => 'ids',
				'numberposts' => '-1',
				'post_type'   => 'shop_order',
				'status'      => wc_get_is_paid_statuses(),
			) );

			$get_threshold_order = count( $all_order_ids );
			update_option( '_bwf_order_threshold', $get_threshold_order );
		}

		/**************** PROCESS BATCH STARTS ************/
		$numberposts = ( ( $offset > 0 ) && ( ( $get_threshold_order / $offset ) < 2 ) && ( ( $get_threshold_order % $offset ) < BWF_ORDERS_PER_BATCH ) ) ? ( $get_threshold_order % $offset ) : BWF_ORDERS_PER_BATCH;
		// Get n orders which are not indexed yet
		$order_ids = wc_get_orders( array(
			'return'      => 'ids',
			'numberposts' => $numberposts,
			'post_type'   => 'shop_order',
			'offset'      => null,
			'orderby'     => 'ID',
			'order'       => 'DESC',
			'status'      => wc_get_is_paid_statuses(),
		) );
		wp_reset_query();

		/**
		 * IF offset reached the threshold or no unindexed orders found, its time to terminate the batch process.
		 */
		if ( $offset >= $get_threshold_order || count( $order_ids ) < 1 ) {
			WooFunnels_Dashboard::$classes['BWF_Logger']->log( 'Terminated on ' . $get_threshold_order, 'woofunnels_indexing' );
			remove_action( 'shutdown', [ WooFunnels_Dashboard::$classes['WooFunnels_DB_Updater'], 'capture_fatal_error' ] );

			return false;
		}

		/**
		 * @SuppressWarnings(PHPMD.DevelopmentCodeFragment)
		 */
		$retrieved_count = count( $order_ids );
		WooFunnels_Dashboard::$classes['BWF_Logger']->log( "These $retrieved_count orders are retrieved: " . implode( ',', $order_ids ), 'woofunnels_indexing' ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

		remove_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'woofunnels_handle_indexed_orders', 10, 2 );

		foreach ( $order_ids as $order_id ) {
			WooFunnels_Dashboard::$classes['WooFunnels_DB_Updater']->set_order_id_in_process( $order_id );
			bwf_create_update_contact( $order_id, array(), 0, true );

			$offset ++;
			update_option( '_bwf_offset', $offset );
		}

		wp_reset_query();
		/**************** PROCESS BATCH ENDS ************/

		WooFunnels_Dashboard::$classes['BWF_Logger']->log( "bwf_create_update_contact_customer function returned. Offset: $offset, Order Count: $get_threshold_order ", 'woofunnels_indexing' );
		remove_action( 'shutdown', [ WooFunnels_Dashboard::$classes['WooFunnels_DB_Updater'], 'capture_fatal_error' ] );

		return 'bwf_create_update_contact_customer';

	}
}

/**
 * Handle a custom '_woofunnel_cid' query var to get orders with the '_woofunnel_cid' meta.
 *
 * @param array $query - Args for WP_Query.
 * @param array $query_vars - Query vars from WC_Order_Query.
 *
 * @return array modified $query
 */
if ( ! function_exists( 'woofunnels_handle_indexed_orders' ) ) {
	function woofunnels_handle_indexed_orders( $query, $query_vars ) {
		if ( ! isset( $query_vars['_woofunnel_cid'] ) ) {
			$query['meta_query'][] = array(
				'key'      => '_woofunnel_cid',
				'compare'  => 'NOT EXISTS',
				'relation' => 'AND',
			);
		}

		return $query;
	}
}

if ( ! function_exists( 'woofunnels_order_query_filter' ) ) {
	function woofunnels_order_query_filter( $query, $query_vars ) {
		if ( isset( $query_vars['_woofunnel_order_cid'] ) ) {
			$query['meta_query'][] = array(
				'key'     => '_woofunnel_cid',
				'compare' => '=',
				'value'   => $query_vars['_woofunnel_order_cid']
			);
		}

		return $query;
	}
}

/*
 * CONTACTS DATABASE STARTS
 */
define( 'BWF_THRESHOLD_CONTACTS', 0 );
define( 'BWF_CONTACTS_PER_BATCH', 20 ); //defining it means how many orders to process per batch operation

if ( ! function_exists( 'bwf_contacts_v1_0_init_db_setup' ) ) {
	function bwf_contacts_v1_0_init_db_setup() {

		$db_operations = WooFunnels_DB_Operations::get_instance();
		add_action( 'shutdown', [ WooFunnels_Dashboard::$classes['WooFunnels_DB_Updater'], 'capture_fatal_error_contacts' ] );
		/**
		 * get the offset and the threshold of max orders to process
		 */
		$offset = get_option( '_bwf_contacts_offset', 0 );

		$get_threshold_contacts = get_option( '_bwf_contacts_threshold', BWF_THRESHOLD_CONTACTS );


		if ( 0 === $get_threshold_contacts ) {


			$get_threshold_contacts = $db_operations->get_all_contacts_count();
			update_option( '_bwf_contacts_threshold', $get_threshold_contacts );
		}

		/**************** PROCESS BATCH STARTS ************/
		$numberposts = ( ( $offset > 0 ) && ( ( $get_threshold_contacts / $offset ) < 2 ) && ( ( $get_threshold_contacts % $offset ) < BWF_CONTACTS_PER_BATCH ) ) ? ( $get_threshold_contacts % $offset ) : BWF_CONTACTS_PER_BATCH;
		// Get n orders which are not indexed yet
		$contacts = $db_operations->get_contacts( [ 'contact_limit' => $offset . ',' . $numberposts ] );

		/**
		 * IF offset reached the threshold or no unindexed orders found, its time to terminate the batch process.
		 */
		if ( $offset >= $get_threshold_contacts || count( $contacts ) < 1 ) {
			WooFunnels_Dashboard::$classes['BWF_Logger']->log( 'Terminated on ' . $get_threshold_contacts, 'woofunnels_contacts_indexing' );
			remove_action( 'shutdown', [ WooFunnels_Dashboard::$classes['WooFunnels_DB_Updater'], 'capture_fatal_error_contacts' ] );

			return false;
		}

		/**
		 * @SuppressWarnings(PHPMD.DevelopmentCodeFragment)
		 */
		$retrieved_count = count( $contacts );
		WooFunnels_Dashboard::$classes['BWF_Logger']->log( "These $retrieved_count contacts are retrieved: " . implode( ',', wp_list_pluck( $contacts, 'id' ) ), 'woofunnels_contacts_indexing' ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r


		foreach ( $contacts as $contact ) {
			//do all the changes with the contact here..
			$bwf_contacts = BWF_Contacts::get_instance();
			$bwf_contact  = $bwf_contacts->get_contact_by( 'id', $contact->id );
			if ( $bwf_contact instanceof WooFunnels_Contact and $bwf_contact->get_id() > 0 ) {
				$country = $bwf_contact->get_meta( 'country', false );
				$state   = $bwf_contact->get_meta( 'state', false );
				if ( ! empty( $country ) ) {
					$bwf_contact->set_country( $country );

				}
				if ( !empty($country) && ! empty( $state ) ) {
					$bwf_contact->set_state(  bwf_get_states($country,$state) );

				}

				add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'woofunnels_order_query_filter', 10, 2 );

				$all_order_ids = wc_get_orders( array(
					'_woofunnel_order_cid' => $contact->id,
					'return'               => 'ids',
					'numberposts'          => '1',
					'post_type'            => 'shop_order',

				) );
				if ( is_array( $all_order_ids ) && count( $all_order_ids ) > 0 ) {

					$contact_order = wc_get_order( $all_order_ids[0] );

					if ( $contact_order instanceof WC_Order ) {

						$bwf_contact->set_type( 'customer' );
						$contact_no = $contact_order->get_billing_phone();
						$country    = $contact_order->get_billing_country();
						$state      = $contact_order->get_billing_state();
						$address_1  = $contact_order->get_billing_address_1();
						$address_2  = $contact_order->get_billing_address_2();
						$postcode   = $contact_order->get_billing_postcode();
						$company    = $contact_order->get_billing_company();


						if ( ! empty( $contact_no ) ) {
							$bwf_contact->set_contact_no( $contact_no );
						}

						if ( ! empty( $country ) ) {
							$bwf_contact->set_country( $country );

						}
						if ( !empty($country) && ! empty( $state ) ) {
							$bwf_contact->set_state(  bwf_get_states($country,$state) );

						}
						if ( ! empty( $address_1 ) ) {
							$bwf_contact->set_meta( 'address_1', $address_1 );
						}
						if ( ! empty( $address_2 ) ) {
							$bwf_contact->set_meta( 'address_2', $address_2 );
						}
						if ( ! empty( $postcode ) ) {
							$bwf_contact->set_meta( 'postcode', $postcode );
						}
						if ( ! empty( $company ) ) {
							$bwf_contact->set_meta( 'company', $company );
						}
					}

				}
				remove_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'woofunnels_order_query_filter', 10, 2 );

				$bwf_contact->save( true );
				$bwf_contact->delete_meta( 'country' );
				$bwf_contact->delete_meta( 'state' );
				$bwf_contact->save_meta();
			}
			$offset ++;
			update_option( '_bwf_contacts_offset', $offset );
		}

		/**************** PROCESS BATCH ENDS ************/

		WooFunnels_Dashboard::$classes['BWF_Logger']->log( "bwf_contacts_v1_0_init_db_setup function returned. Offset: $offset, Order Count: $get_threshold_contacts ", 'woofunnels_contacts_indexing' );
		remove_action( 'shutdown', [ WooFunnels_Dashboard::$classes['WooFunnels_DB_Updater'], 'capture_fatal_error_contacts' ] );

		return 'bwf_contacts_v1_0_init_db_setup';


	}
}