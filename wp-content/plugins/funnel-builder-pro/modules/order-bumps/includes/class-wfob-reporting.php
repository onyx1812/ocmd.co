<?php

class WFOB_Reporting {

	private static $ins = null;
	private $no_of_bump_used_order = [];
	private $no_of_bump_used_total = [];

	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init_db' ], 2 );
		add_action( 'admin_init', [ $this, 'create_table' ] );
		add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'woocommerce_create_order_line_item' ], 999999, 4 );
		add_filter( 'woocommerce_thankyou', [ $this, 'insert_custom_row_from_meta' ], 99, 2 );

		add_filter( 'woocommerce_admin_reports', [ $this, 'add_report_menu' ] );
		add_filter( 'wc_admin_reports_path', [ $this, 'initialize_bump_reports_path' ], 12, 3 );

		add_action( 'delete_post', [ $this, 'delete_report_for_order' ] );
		add_action( 'woocommerce_checkout_create_order', [ $this, 'update_used_bump_in_order_meta' ] );

		add_action( 'woocommerce_order_status_changed', array( $this, 'insert_row_for_ipn_based_gateways' ), 10, 3 );
	}

	public static function get_instance() {
		if ( is_null( self::$ins ) ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	public function init_db() {
		global $wpdb;
		$wpdb->wfob_stats = $wpdb->prefix . 'wfob_stats';
	}

	public function create_table() {
		/** create table in ver 1.0 */
		if ( false !== get_option( 'wfob_db_ver_3_0', false ) ) {
			return;
		}
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$creationSQL = "CREATE TABLE {$wpdb->prefix}wfob_stats (
 		  ID bigint(20) unsigned NOT NULL auto_increment,
 		  oid bigint(20) unsigned NOT NULL,
 		  bid bigint(20) unsigned NOT NULL,
 		  iid varchar(255) NOT NULL,
 		  converted tinyint(1) not null default 0,
 		  total varchar(255) not null default 0,
 		  date datetime NOT NULL, 	
 		  cid BIGINT(20) unsigned NOT NULL DEFAULT 0,
 		  fid BIGINT(20) unsigned NOT NULL DEFAULT 0, 
		  PRIMARY KEY  (ID),
		  KEY ID (ID),
		  KEY oid (oid),
		  KEY bid (bid),
		  KEY date (date)
		) $collate;";
		dbDelta( $creationSQL );

		update_option( 'wfob_db_ver_3_0', date( 'Y-m-d' ) );
	}


	/**
	 * @param $item WC_Order_Item
	 * @param $cart_item_key
	 * @param $values
	 * @param $order WC_Order
	 */
	public function woocommerce_create_order_line_item( $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['_wfob_product'] ) ) {
			$bump_id = absint( $values['_wfob_options']['_wfob_id'] );
			$item->add_meta_data( '_bump_purchase', 'yes' );
			$item->add_meta_data( '_wfob_id', $bump_id );
			if ( ! isset( $this->no_of_bump_used_total[ $bump_id ] ) ) {
				$this->no_of_bump_used_total[ $bump_id ] = 0;
			}
			$this->no_of_bump_used_total[ $bump_id ]   += BWF_Plugin_Compatibilities::get_fixed_currency_price_reverse( $item['subtotal'], BWF_WC_Compatibility::get_order_currency( $order ) );
			$this->no_of_bump_used_order[ $bump_id ][] = [ $values ];
		}
	}

	/**
	 * @param WC_Order $order
	 */
	public function update_used_bump_in_order_meta( $order ) {
		$bump_data = [];

		$show_bumps = WC()->session->get( 'wfob_no_of_bump_shown', [] );
		$funnel_id  = 0;
		if ( class_exists( 'WFFN_Core' ) ) {
			$funnel = WFFN_Core()->data->get_session_funnel();
			if ( $funnel instanceof WFFN_Funnel ) {
				$funnel_id = $funnel->get_id();
			}
		}

		$get_cid = empty( $get_cid ) ? 0 : $get_cid;

		foreach ( $show_bumps as $bump_id ) {
			$converted = isset( $this->no_of_bump_used_order[ $bump_id ] ) ? 1 : 0;
			$total     = 0;
			if ( $converted ) {
				$total = $this->no_of_bump_used_total[ $bump_id ];
			}
			$data                  = [
				'converted' => $converted,
				'bid'       => absint( $bump_id ),
				'total'     => $total,
				'iid'       => '{}',
				'fid'       => $funnel_id,
			];
			$bump_data[ $bump_id ] = $data;
		}
		WC()->session->set( 'wfob_no_of_bump_shown', [] );
		$order->update_meta_data( '_wfob_report_data', $bump_data );
	}

	/**
	 * hooked @woocommerce_thankyou
	 *
	 * @param $order_id
	 *
	 * @return bool|mixed
	 */
	public function insert_custom_row_from_meta( $order_id ) {
		global $wpdb;
		$order        = wc_get_order( $order_id );
		$order_status = $order->get_status();


        /**
         * If this is a renewal order then delete the meta if exists and return straight away
         */
        if ( $this->is_order_renewal( $order ) ) {
            delete_post_meta( $order_id, '_wfob_report_data' );
            delete_post_meta( $order_id, '_wfob_report_needs_normalization' );

            return false;
        }

        /**
         * if woocommerce thank you showed up and order status not paid, save meta to normalize status later
         */
		if ( did_action( 'woocommerce_thankyou' ) && ! in_array( $order_status, wc_get_is_paid_statuses(), true ) ) {
			$order->update_meta_data( '_wfob_report_needs_normalization', 'yes' );
			$order->save_meta_data();

			return false;
		}

		$bump_data = get_post_meta( $order_id, '_wfob_report_data', true );
		if ( ! is_array( $bump_data ) ) {
			return $bump_data;
		}

		$sql     = "select item.order_item_id as item_id ,meta.meta_value as bump_id from {$wpdb->prefix}woocommerce_order_items as item INNER JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as meta on item.order_item_id=meta.order_item_id and item.order_id='{$order_id}' and meta.meta_key='_wfob_id';";
		$results = $wpdb->get_results( $sql, ARRAY_A );

		$bump_items = [];
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$bump_id                  = absint( $result['bump_id'] );
				$item_id                  = $result['item_id'];
				$bump_items[ $bump_id ][] = absint( $item_id );
			}
		}

		$get_cid = get_post_meta( $order_id, '_woofunnel_cid', true );
		$get_cid = empty( $get_cid ) ? 0 : $get_cid;

		$date_created = $order->get_date_created();
		if ( ! empty( $date_created ) ) {

			$timezone = new DateTimeZone( wp_timezone_string() );
			$date_created->setTimezone( $timezone );
			$date_created = $date_created->format( 'Y-m-d H:i:s' );
		}

		$report_ids = [];
		foreach ( $bump_data as $id => $insert_data ) {
			if ( isset( $bump_items[ $id ] ) ) {
				$insert_data['iid'] = json_encode( $bump_items[ $id ] );
			}
			$insert_data['cid']  = $get_cid;
			$insert_data['fid']  = isset( $insert_data['fid'] ) ? $insert_data['fid'] : 0;
			$insert_data['oid']  = $order_id;
			$insert_data['date'] = empty( $date_created ) ? current_time( 'mysql' ) : $date_created;

			$report_id = $this->insert_data( $insert_data );

			if ( ! is_null( $report_id ) ) {
				$report_ids[] = $report_id;
			}
		}
		update_post_meta( $order_id, '_wfob_stats_ids', $report_ids );
		delete_post_meta( $order_id, '_wfob_report_data' );
		delete_post_meta( $order_id, '_wfob_report_needs_normalization' );
	}

	public function delete_report_for_order( $post_id ) {
		$get_post_type = get_post_type( $post_id );

		/**
		 * @todo keep eye on WC upgrades so that we can handle this when Orders are no longer be posts.
		 */
		if ( 'shop_order' === $get_post_type ) {
			global $wpdb;
			$wpdb->delete( $wpdb->wfob_stats, [ 'oid' => $post_id ], [ '%d' ] );
		}
	}

	private function insert_data( $data ) {
		global $wpdb;
		$status = $wpdb->insert( $wpdb->wfob_stats, $data, [ '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s' ] );
		if ( false !== $status ) {
			return $wpdb->insert_id;
		}

		return null;
	}

	private function update_data( $data, $where ) {
		global $wpdb;
		$status = $wpdb->update( $wpdb->wfob_stats, $data, $where, [ '%s' ], [ '%d', '%d' ] );
		if ( false !== $status ) {
			return true;
		}

		return null;
	}

	public function add_report_menu( $menu ) {
		$menu['wfob_bump'] = array(
			'title'   => __( 'Order Bumps', 'woofunnels-order-bump' ),
			'reports' => array(
				'wfob_by_date' => array(
					'title'       => __( 'Sales By Date', 'woofunnels-order-bump' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( 'WC_Admin_Reports', 'get_report' ),
				),

				'wfob_bumps' => array(
					'title'       => __( 'Sales By Bump', 'woofunnels-order-bump' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'get_report' ),
				),
			),
		);

		return $menu;
	}

	public function initialize_bump_reports_path( $reporting_path, $name, $class ) {
		if ( in_array( strtolower( $class ), [ 'wc_report_wfob_bumps', 'wc_report_wfob_by_date' ], true ) ) {
			$reporting_path = dirname( __FILE__ ) . '/reports/class-' . $name . '.php';
		}

		return $reporting_path;
	}

	public static function get_report() {
		include_once __DIR__ . '/reports/class-wfob-bumps.php';
		WC_Report_wfob_bumps::get_report();
	}

	/**
	 * hooked @ 'woocommerce_order_status_changed'
	 *
	 * @param $order_id
	 * @param $from
	 * @param $to
	 */
	public function insert_row_for_ipn_based_gateways( $order_id, $from, $to ) {
		$order          = wc_get_order( $order_id );
		$payment_method = $order->get_payment_method();

		$ipn_gateways = apply_filters( 'wfob_ipn_gateways_list', array( 'paypal', 'mollie_wc_gateway_ideal', 'mollie_wc_gateway_bancontact', 'mollie_wc_gateway_sofort' ) );
		if ( ! in_array( $payment_method, $ipn_gateways, true ) && 'yes' !== $order->get_meta( '_wfob_report_needs_normalization' ) ) {
			return false;
		}

		if ( $order_id > 0 && in_array( $from, array( 'pending', 'on-hold' ) ) && in_array( $to, wc_get_is_paid_statuses(), true ) ) {
			$this->insert_custom_row_from_meta( $order_id );
		}
	}

	public function is_order_renewal( $order ) {
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}
		$subscription_renewal = BWF_WC_Compatibility::get_order_data( $order, '_subscription_renewal' );

		return ! empty( $subscription_renewal );
	}
}

WFOB_Reporting::get_instance();
