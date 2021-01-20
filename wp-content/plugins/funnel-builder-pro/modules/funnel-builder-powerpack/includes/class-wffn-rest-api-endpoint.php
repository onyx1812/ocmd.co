<?php

class WFFN_REST_API_EndPoint extends WFFN_REST_Controller {

	private static $ins = null;

	/**
	 * WFFN_Funnel_Contacts constructor.
	 */
	public function __construct() {

		add_action( 'rest_api_init', [ $this, 'register_endpoint' ], 12 );
	}

	/**
	 * @return WFFN_Funnel_Contacts|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	public function register_endpoint() {
		register_rest_route( 'woofunnels-analytics', '/funnels' . '/(?P<id>[\d]+)/stats/', array(
			array(
				'args'                => $this->get_stats_collection(),
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_stats' ),
				'permission_callback' => array( $this, 'get_permission' ),
			),
		) );

		register_rest_route( 'woofunnels-analytics', '/funnels' . '/(?P<id>[\d]+)/steps/', array(
			array(
				'args'                => $this->get_stats_collection_for_steps(),
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_steps' ),
				'permission_callback' => array( $this, 'get_permission' ),
			),
		) );


	}

	/**
	 * @return bool
	 */
	public function get_permission() {
		return true;

		return current_user_can( 'manage_options' );
	}

	public function get_stats( $request ) {
		$response = array();

		$response['totals'] = $this->prepare_item_for_response( $request );

		$response['intervals'] = $this->prepare_item_for_response( $request, 'interval' );


		return rest_ensure_response( $response );
	}

	public function prepare_item_for_response( $request, $is_interval = '' ) {

		$start_date  = ( isset( $request['after'] ) && '' !== $request['after'] ) ? $request['after'] : self::default_date( WEEK_IN_SECONDS )->format( self::$sql_datetime_format );
		$end_date    = ( isset( $request['before'] ) && '' !== $request['before'] ) ? $request['before'] : self::default_date()->format( self::$sql_datetime_format );
		$int_request = ( isset( $request['interval'] ) && '' !== $request['interval'] ) ? $request['interval'] : 'week';
		$funnel_id   = ( isset( $request['id'] ) && '' !== $request['id'] ) ? intval( $request['id'] ) : 0;

		$get_total_visits  = $this->get_unique_visits( $funnel_id, $start_date, $end_date, $is_interval, $int_request );
		if ( is_array( $get_total_visits ) && isset( $get_total_visits['db_error'] ) ) {
			return $get_total_visits;
		}

		$get_total_orders  = $this->get_total_orders( $funnel_id, $start_date, $end_date, $is_interval, $int_request );
		if ( is_array( $get_total_orders ) && isset( $get_total_orders['db_error'] ) ) {
			return $get_total_orders;
		}

		$get_total_revenue = $this->get_total_revenue( $funnel_id, $start_date, $end_date, $is_interval, $int_request );
		if ( is_array( $get_total_revenue ) && isset( $get_total_revenue['db_error'] ) ) {
			return $get_total_revenue;
		}

		$result            = [];
		$intervals         = array();
		if ( ! empty( $is_interval ) ) {
			$intervals_all = $this->intervals_between( $start_date, $end_date, $int_request );
			foreach ( $intervals_all as $all_interval ) {
				$interval   = $all_interval['time_interval'];
				$start_date = $all_interval['start_date'];
				$end_date   = $all_interval['end_date'];

				$get_total_visit = $this->maybe_interval_exists( $get_total_visits, 'time_interval', $interval );
				$get_total_order = $this->maybe_interval_exists( $get_total_orders, 'time_interval', $interval );

				$total_revenue_aero = $this->maybe_interval_exists( $get_total_revenue['aero'], 'time_interval', $interval );

				$total_revenue_aero = is_array( $total_revenue_aero ) ? $total_revenue_aero[0]['sum_aero'] : 0;

				$total_revenue_bump = $this->maybe_interval_exists( $get_total_revenue['bump'], 'time_interval', $interval );
				$total_revenue_bump = is_array( $total_revenue_bump ) ? $total_revenue_bump[0]['sum_bump'] : 0;

				$total_revenue_upsells = $this->maybe_interval_exists( $get_total_revenue['upsell'], 'time_interval', $interval );
				$total_revenue_upsells = is_array( $total_revenue_upsells ) ? $total_revenue_upsells[0]['sum_upsells'] : 0;

				$get_total_visit             = is_array( $get_total_visit ) ? $get_total_visit[0]['unique_views'] : 0;
				$get_total_order             = is_array( $get_total_order ) ? $get_total_order[0]['total_orders'] : 0;
				$intervals['interval']       = $interval;
				$intervals['start_date']     = $start_date;
				$intervals['date_start_gmt'] = $this->convert_local_datetime_to_gmt( $start_date )->format( self::$sql_datetime_format );
				$intervals['end_date']       = $end_date;
				$intervals['date_end_gmt']   = $this->convert_local_datetime_to_gmt( $end_date )->format( self::$sql_datetime_format );

				$intervals['subtotals'] = array(
					'unique_visits'     => $get_total_visit,
					'total_orders'      => $get_total_order,
					'total_revenue'     => $total_revenue_aero + $total_revenue_bump + $total_revenue_upsells,
					'revenue_per_visit' => ( absint( $get_total_visit ) !== 0 ) ? ( $total_revenue_aero + $total_revenue_bump + $total_revenue_upsells ) / $get_total_visit : 0,
					'segments'          => array(),
				);

				$result[] = $intervals;

			}

		} else {

			$unique_visits = $get_total_visits[0]['unique_views'];
			$total_orders  = $get_total_orders[0]['total_orders'];
			if ( count( $get_total_revenue['aero'] ) > 0 ) {
				$total_revenue = $get_total_revenue['aero'][0]['sum_aero'];
			}
			if ( count( $get_total_revenue['bump'] ) > 0 ) {
				$total_revenue += $get_total_revenue['bump'][0]['sum_bump'];
			}
			if ( count( $get_total_revenue['upsell'] ) > 0 ) {
				$total_revenue += $get_total_revenue['upsell'][0]['sum_upsells'];
			}


			if ( ! is_null( $total_revenue ) && ! is_null( $unique_visits ) ) {
				$revenue_per_visit = $total_revenue / $unique_visits;
			} else {
				$revenue_per_visit = 0;
			}


			$result = [
				'unique_visits'     => is_null( $unique_visits ) ? 0 : $unique_visits,
				'total_orders'      => is_null( $total_orders ) ? 0 : $total_orders,
				'total_revenue'     => is_null( $total_revenue ) ? 0 : $total_revenue,
				'revenue_per_visit' => $revenue_per_visit,
			];
		}

		return $result;

	}

	public function get_unique_visits( $funnel_id, $start_date, $end_date, $is_interval, $int_request ) {

		global $wpdb;
		$table          = $wpdb->prefix . 'wfco_report_views';
		$date_col       = "date";
		$interval_query = '';
		$group_by       = '';

		if ( 'interval' === $is_interval ) {
			$get_interval   = $this->get_interval_format_query( $int_request, $date_col );
			$interval_query = $get_interval['interval_query'];
			$interval_group = $get_interval['interval_group'];
			$group_by       = "GROUP BY " . $interval_group;

		}

		$unique_views = $wpdb->get_results( "SELECT SUM(no_of_sessions) as unique_views" . $interval_query . "  FROM `" . $table . "` WHERE 1=1 AND `" . $date_col . "` >= '" . $start_date . "' AND `" . $date_col . "` < '" . $end_date . "' AND `type` = 7 AND object_id = $funnel_id " . $group_by . " ORDER BY id ASC", ARRAY_A ); //phpcs:ignore
		if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
            $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
            if ( true === $db_error['db_error'] ) {
                return $db_error;
            }
        }


		return $unique_views;
	}

	public function get_total_orders( $funnel_id, $start_date, $end_date, $is_interval, $int_request ) {
		global $wpdb;
		$table          = $wpdb->prefix . 'wfacp_stats';
		$date_col       = "date";
		$interval_query = '';
		$group_by       = '';
		$limit          = '';
		$total_orders   = [];

		if ( 'interval' === $is_interval ) {
			$get_interval   = $this->get_interval_format_query( $int_request, $date_col );
			$interval_query = $get_interval['interval_query'];
			$interval_group = $get_interval['interval_group'];
			$group_by       = "GROUP BY " . $interval_group;

		}
		if ( version_compare( WFACP_VERSION, '2.0.7', '>' ) ) {
			$total_orders = $wpdb->get_results( "SELECT  COUNT(ID) as total_orders " . $interval_query . "  FROM `" . $table . "` WHERE 1=1 AND `" . $date_col . "` >= '" . $start_date . "' AND `" . $date_col . "` < '" . $end_date . "' AND fid = $funnel_id " . $group_by . " ORDER BY id ASC $limit", ARRAY_A );
            if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                if ( true === $db_error['db_error'] ) {
                    return $db_error;
                }
            }
		}

		return $total_orders;
	}

	public function get_total_revenue( $funnel_id, $start_date, $end_date, $is_interval, $int_request ) {

		/**
		 * get aero revenue
		 */ global $wpdb;
		$table                 = $wpdb->prefix . 'wfacp_stats';
		$date_col              = "date";
		$interval_query        = '';
		$group_by              = '';
		$total_revenue_aero    = [];
		$total_revenue_bump    = [];
		$total_revenue_upsells = [];

		if ( 'interval' === $is_interval ) {
			$get_interval   = $this->get_interval_format_query( $int_request, $date_col );
			$interval_query = $get_interval['interval_query'];

			$interval_group = $get_interval['interval_group'];
			$group_by       = "GROUP BY " . $interval_group;

		}
		if ( version_compare( WFACP_VERSION, '2.0.7', '>' ) ) {
			$total_revenue_aero = $wpdb->get_results( "SELECT SUM(total_revenue) as sum_aero " . $interval_query . "  FROM `" . $table . "` WHERE 1=1 AND `" . $date_col . "` >= '" . $start_date . "' AND `" . $date_col . "` < '" . $end_date . "' AND fid = $funnel_id " . $group_by . " ORDER BY id ASC", ARRAY_A );
            if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                if ( true === $db_error['db_error'] ) {
                    return $db_error;
                }
            }
		}
		/**
		 * get bump revenue
		 */
		$table          = $wpdb->prefix . 'wfob_stats';
		$date_col       = "date";
		$interval_query = '';
		$group_by       = '';


		if ( 'interval' === $is_interval ) {
			$get_interval   = $this->get_interval_format_query( $int_request, $date_col );
			$interval_query = $get_interval['interval_query'];
			$interval_group = $get_interval['interval_group'];
			$group_by       = "GROUP BY " . $interval_group;

		}
		if ( class_exists( 'WFOB_Core' ) && version_compare( WFOB_VERSION, '1.8.2', '>' ) ) {
			$total_revenue_bump = $wpdb->get_results( "SELECT SUM(total) as sum_bump " . $interval_query . "  FROM `" . $table . "` WHERE 1=1 AND `" . $date_col . "` >= '" . $start_date . "' AND `" . $date_col . "` < '" . $end_date . "' AND fid = $funnel_id " . $group_by . " ORDER BY id ASC", ARRAY_A );
            if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                if ( true === $db_error['db_error'] ) {
                    return $db_error;
                }
            }
		}
		/**
		 * get upsells revenue
		 */
		$table          = $wpdb->prefix . 'wfocu_session';
		$date_col       = "timestamp";
		$interval_query = '';
		$group_by       = '';


		if ( 'interval' === $is_interval ) {
			$get_interval   = $this->get_interval_format_query( $int_request, $date_col );
			$interval_query = $get_interval['interval_query'];
			$interval_group = $get_interval['interval_group'];
			$group_by       = "GROUP BY " . $interval_group;

		}
		if ( class_exists( 'WFOCU_Core' ) && version_compare( WFOCU_VERSION, '2.2.0', '>=' ) ) {
			$total_revenue_upsells = $wpdb->get_results( "SELECT SUM(total) as sum_upsells" . $interval_query . "  FROM `" . $table . "` WHERE 1=1 AND `" . $date_col . "` >= '" . $start_date . "' AND `" . $date_col . "` < '" . $end_date . "' AND fid = $funnel_id " . $group_by . " ORDER BY id ASC", ARRAY_A );
            if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                if ( true === $db_error['db_error'] ) {
                    return $db_error;
                }
            }
		}

		return array( 'aero' => $total_revenue_aero, 'bump' => $total_revenue_bump, 'upsell' => $total_revenue_upsells );
	}

	public function get_stats_collection() {
		$params = array();

		$params['after']  = array(
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
			'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'woofunnels-upstroke-one-click-upsell' ),
		);
		$params['before'] = array(
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
			'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'woofunnels-upstroke-one-click-upsell' ),
		);

		$params['interval'] = array(
			'type'              => 'string',
			'default'           => 'week',
			'validate_callback' => 'rest_validate_request_arg',
			'description'       => __( 'Time interval to use for buckets in the returned data.', 'woofunnels-upstroke-one-click-upsell' ),
			'enum'              => array(
				'hour',
				'day',
				'week',
				'month',
				'quarter',
				'year',
			),
		);

		return apply_filters( 'wfocu_rest_funnels_stats_collection', $params );
	}

	public function get_stats_collection_for_steps() {
		$params = array();

		$params['after']  = array(
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
			'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'woofunnels-upstroke-one-click-upsell' ),
		);
		$params['before'] = array(
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
			'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'woofunnels-upstroke-one-click-upsell' ),
		);

		return apply_filters( 'wfocu_rest_funnels_stats_collection', $params );
	}

	public function get_steps( $request ) {
		$funnel_id  = (int) $request['id'];//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$start_date = ( isset( $request['after'] ) && '' !== $request['after'] ) ? $request['after'] : self::default_date( WEEK_IN_SECONDS )->format( self::$sql_datetime_format );
		$end_date   = ( isset( $request['before'] ) && '' !== $request['before'] ) ? $request['before'] : self::default_date()->format( self::$sql_datetime_format );

		$funnel = new WFFN_Funnel( $funnel_id );

		$landing_results          = $this->get_all_landing( $funnel, $funnel_id, $start_date, $end_date );
		$optin_results            = $this->get_all_optins( $funnel, $funnel_id, $start_date, $end_date );
		$aero_results             = $this->get_all_checkouts( $funnel, $funnel_id, $start_date, $end_date );
		$get_all_upsell_records   = $this->get_all_upsells( $funnel, $funnel_id, $start_date, $end_date );
		$get_all_thankyou_records = $this->get_all_thankyou( $funnel, $funnel_id, $start_date, $end_date );
		$get_op_thankyou_records  = $this->get_all_op_thankyou( $funnel, $funnel_id, $start_date, $end_date );

		/*
		if ( class_exists( 'WFOB_Core' ) && version_compare( WFOB_VERSION, '1.8,1', '>' ) ) {
			$bump_sql = "SELECT bump.bid as 'object_id',SUM(bump.total) as 'total_revenue',p.post_title as 'object_name', bump.date as 'date','bump' as 'type' FROM " . $wpdb->prefix . 'wfob_stats' . " AS bump LEFT JOIN " . $wpdb->prefix . 'posts' . " as p ON bump.bid  = p.id WHERE bump.fid=$funnel_id GROUP by bump.bid asc";

			$get_all_bump_records = $wpdb->get_results( $bump_sql );


		}*/


		return rest_ensure_response( array(
			'records' => array_merge( $landing_results, $optin_results, $aero_results, $get_all_upsell_records, $get_all_thankyou_records, $get_op_thankyou_records )
		) );
	}

	public function get_all_checkouts( $funnel, $funnel_id, $start_date, $end_date ) {
		global $wpdb;
		$data = [];
		if ( version_compare( WFACP_VERSION, '2.0.7', '<' ) || ( class_exists( 'WFOB_Core' ) && version_compare( WFOB_VERSION, '1.8,1', '<=' ) ) ) {
			return $data;
		}

		$aero_sql                 = "SELECT aero.wfacp_id as 'object_id', p.post_title as 'object_name',SUM(aero.total_revenue) as 'total_revenue',COUNT(aero.ID) as cn,aero.date as 'date', 'checkout' as 'type' FROM " . $wpdb->prefix . 'wfacp_stats' . " AS aero LEFT JOIN " . $wpdb->prefix . 'posts' . " as p ON aero.wfacp_id  = p.id WHERE aero.fid=$funnel_id AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "' GROUP by aero.wfacp_id ASC";
		$bump_sql                 = "SELECT bump.bid as 'object_id',COUNT(CASE WHEN converted = 1 THEN 1 END) AS `converted`, p.post_title as 'object_name',SUM(bump.total) as 'total_revenue',COUNT(bump.ID) as viewed, 'bump' as 'type' FROM " . $wpdb->prefix . 'wfob_stats' . " AS bump LEFT JOIN " . $wpdb->prefix . 'posts' . " as p ON bump.bid  = p.id WHERE bump.fid =$funnel_id AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "' GROUP by bump.bid ASC";
		$get_all_checkout_records = $wpdb->get_results( $aero_sql, ARRAY_A );
        if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
            $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
            if ( true === $db_error['db_error'] ) {
                return $db_error;
            }
        }

		if ( $this->should_query_bump() ) {
			$get_all_bump_records = $wpdb->get_results( $bump_sql, ARRAY_A );
            if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                if ( true === $db_error['db_error'] ) {
                    return $db_error;
                }
            }
		}

		if ( $funnel instanceof WFFN_Funnel && 0 < $funnel->get_id() ) {
			$get_steps = $funnel->get_steps();
			$get_steps = $this->maybe_add_ab_variants( $get_steps );
			foreach ( $get_steps as $step ) {
				if ( $step['type'] === 'wc_checkout' ) {

					$data[ $step['id'] ] = array(
						'type'            => 'checkout',
						'object_id'       => $step['id'],
						'object_name'     => get_the_title( $step['id'] ),
						'revenue'         => 0,
						'conversions'     => 0,
						'views'           => 0,
						'conversion_rate' => 0,
					);
					foreach ( $get_all_checkout_records as $key => $records_aero ) {

						if ( $records_aero['object_id'] !== $step['id'] ) {
							continue;
						}
						$aero_id         = $records_aero['object_id'];
						$is_variant      = get_post_meta( $aero_id, '_bwf_ab_variation_of', true );
						$case            = ! empty( $is_variant ) ? 'CASE WHEN type = 12 THEN `no_of_sessions` END' : 'CASE WHEN type = 4 THEN `no_of_sessions` END';
						$get_views_query = " SELECT SUM( " . $case . " ) as unique_views FROM " . $wpdb->prefix . 'wfco_report_views' . "  WHERE object_id=$aero_id AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "' GROUP by object_id ASC";
						$get_views       = $wpdb->get_results( $get_views_query, ARRAY_A );
                        if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                            $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                            if ( true === $db_error['db_error'] ) {
                                return $db_error;
                            }
                        }
						$data[ $aero_id ] = array(
							'type'            => 'checkout',
							'object_id'       => $aero_id,
							'object_name'     => $records_aero['object_name'],
							'revenue'         => $records_aero['total_revenue'],
							'conversions'     => intval( $records_aero['cn'] ),
							'views'           => intval( $get_views[0]['unique_views'] ),
							'conversion_rate' => $this->get_percentage( $get_views[0]['unique_views'], $records_aero['cn'] ),
						);
						unset( $get_all_checkout_records[ $key ] );

					}

					if ( $this->should_query_bump() ) {
						$get_step = WFFN_Core()->steps->get_integration_object( $step['type'] );
						if ( ! $get_step instanceof WFFN_Step ) {
							continue;
						}
						$substeps = $get_step->get_substeps( $funnel_id, $step['id'], array( 'wc_order_bump' ) );
						$substeps = $this->maybe_add_ab_substep_variants( $substeps );

						foreach ( $substeps as $subtype => $substep_ids ) {

							if ( 'wc_order_bump' === $subtype && is_array( $substep_ids ) && count( $substep_ids ) ) {
								$get_substep = WFFN_Core()->substeps->get_integration_object( $subtype );
								if ( ! $get_substep instanceof WFFN_Substep ) {
									break;
								}
								foreach ( $substep_ids as $substep_id ) {
									$data[ $substep_id ] = array(
										'type'            => 'bump',
										'object_id'       => $substep_id,
										'object_name'     => get_the_title( $substep_id ),
										'revenue'         => 0,
										'conversions'     => 0,
										'views'           => 0,
										'conversion_rate' => 0,
									);

									foreach ( $get_all_bump_records as $bmp_key => $records_bump ) {

										if ( absint( $records_bump['object_id'] ) !== absint( $substep_id ) ) {
											continue;
										}
										$data[ $substep_id ] = array(
											'type'            => 'bump',
											'object_id'       => $substep_id,
											'object_name'     => $records_bump['object_name'],
											'revenue'         => $records_bump['total_revenue'],
											'conversions'     => intval( $records_bump['converted'] ),
											'views'           => intval( $records_bump['viewed'] ),
											'conversion_rate' => $this->get_percentage( $records_bump['viewed'], $records_bump['converted'] ),
										);
										unset( $get_all_bump_records[ $bmp_key ] );
									}
								}
							}
						}
					}
				}
			}
		}

		foreach ( $get_all_checkout_records as $record ) {

			$data[ $record['object_id'] ] = array(
				'type'            => 'checkout',
				'object_id'       => $record['object_id'],
				'object_name'     => $record['object_name'],
				'revenue'         => $record['total_revenue'],
				'conversions'     => intval( $record['cn'] ),
				'views'           => 0,
				'conversion_rate' => 0,
			);

		}

		if ( is_array( $data ) && count( $data ) > 0 ) {
			foreach ( $data as $aero => &$data_aero ) {
				if( 'checkout' === $data_aero['type']) {
					$aero_id         = $aero;
					$is_variant      = get_post_meta( $aero_id, '_bwf_ab_variation_of', true );
					$case            = ! empty( $is_variant ) ? 'CASE WHEN type = 12 THEN `no_of_sessions` END' : 'CASE WHEN type = 4 THEN `no_of_sessions` END';
					$get_views_query = " SELECT SUM( " . $case . ") as unique_views FROM " . $wpdb->prefix . 'wfco_report_views' . "  WHERE object_id=$aero_id AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "' GROUP by object_id ASC";
					$get_views       = $wpdb->get_results( $get_views_query, ARRAY_A );
                    if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                        $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                        if ( true === $db_error['db_error'] ) {
                            return $db_error;
                        }
                    }
					$data_aero['views']           = intval( $get_views[0]['unique_views'] );
					$data_aero['conversion_rate'] = $this->get_percentage( $get_views[0]['unique_views'], $data_aero['conversions'] );
				}
			}
		}

		if ( $this->should_query_bump() ) {
			foreach ( $get_all_bump_records as $record ) {
				$data[ $record['object_id'] ] = array(
					'type'            => 'bump',
					'object_id'       => $record['object_id'],
					'object_name'     => $record['object_name'],
					'revenue'         => $record['total_revenue'],
					'conversions'     => intval( $record['converted'] ),
					'views'           => intval( $record['viewed'] ),
					'conversion_rate' => $this->get_percentage( $record['viewed'], $record['converted'] ),
				);

			}
		}

		return $data;
	}


	public function get_all_optins( $funnel, $funnel_id, $start_date, $end_date ) {
		global $wpdb;
		$data = [];

		if ( ! class_exists( 'WFOPP_Core' ) ) {
			return $data;
		}
		if ( $funnel instanceof WFFN_Funnel && 0 < $funnel->get_id() ) {
			$get_steps = $funnel->get_steps();
			$get_steps = $this->maybe_add_ab_variants( $get_steps );
			foreach ( $get_steps as $step ) {
				if ( $step['type'] === 'optin' ) {
					$data[ $step['id'] ] = array(
						'type'            => 'optin',
						'object_id'       => $step['id'],
						'object_name'     => get_the_title( $step['id'] ),
						'revenue'         => null,
						'conversions'     => 0,
						'views'           => 0,
						'conversion_rate' => 0,
					);
				}
			}
		}

		$optin_sql = "SELECT optin.step_id as 'object_id', p.post_title as 'object_name','0' as 'total_revenue',COUNT(optin.id) as cn FROM " . $wpdb->prefix . 'bwf_optin_entries' . " AS optin LEFT JOIN " . $wpdb->prefix . 'posts' . " as p ON optin.step_id  = p.id WHERE optin.funnel_id=$funnel_id AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "' GROUP by optin.step_id ASC";

		$get_all_optin_records = $wpdb->get_results( $optin_sql, ARRAY_A );
        if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
            $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
            if ( true === $db_error['db_error'] ) {
                return $db_error;
            }
        }
		foreach ( $get_all_optin_records as $record ) {
			$data[ $record['object_id'] ] = array(
				'type'            => 'optin',
				'object_id'       => $record['object_id'],
				'object_name'     => $record['object_name'],
				'revenue'         => null,
				'conversions'     => intval( $record['cn'] ),
				'views'           => 0,
				'conversion_rate' => 0,
			);
		}

		if ( is_array( $data ) && count( $data ) > 0 ) {
			foreach ( $data as $optin => &$data_optin ) {
				$optin_id        = $optin;
				$is_variant      = get_post_meta( $optin_id, '_bwf_ab_variation_of', true );
				$case            = ! empty( $is_variant ) ? 'CASE WHEN type = 16 THEN `no_of_sessions` END' : '`no_of_sessions`';
				$get_views_query = " SELECT SUM( " . $case . ") AS unique_views FROM " . $wpdb->prefix . 'wfco_report_views' . "  WHERE object_id=$optin_id AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "' GROUP by object_id ASC";
				$get_views       = $wpdb->get_results( $get_views_query, ARRAY_A );
                if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                    $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                    if ( true === $db_error['db_error'] ) {
                        return $db_error;
                    }
                }
				$data_optin['views']           = intval( $get_views[0]['unique_views'] );
				$data_optin['conversion_rate'] = $this->get_percentage( $get_views[0]['unique_views'], $data_optin['conversions'] );
			}
		}


		return $data;
	}

	public function get_all_landing( $funnel, $funnel_id, $start_date, $end_date ) {
		global $wpdb;
		$data = [];
		if ( $funnel instanceof WFFN_Funnel && 0 < $funnel->get_id() ) {
			$get_steps = $funnel->get_steps();
			$get_steps = $this->maybe_add_ab_variants( $get_steps );
			foreach ( $get_steps as $step ) {
				if ( $step['type'] === 'landing' ) {
					$landing_id = $step['id'];
					$is_variant = get_post_meta( $landing_id, '_bwf_ab_variation_of', true );
					if ( ! empty( $is_variant ) && $is_variant > 0 ) {
						$get_query = " SELECT SUM(CASE WHEN type = 13 THEN `no_of_sessions` END) AS `viewed` ,SUM(CASE WHEN type = 14 THEN `no_of_sessions` END) AS `converted` FROM  " . $wpdb->prefix . 'wfco_report_views' . "  WHERE object_id=$landing_id AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";

					} else {
						$get_query = " SELECT SUM(CASE WHEN type = 2 THEN `no_of_sessions` END) AS `viewed` ,SUM(CASE WHEN type = 3 THEN `no_of_sessions` END) AS `converted` FROM  " . $wpdb->prefix . 'wfco_report_views' . "  WHERE object_id=$landing_id AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";

					}
					$get_data = $wpdb->get_results( $get_query, ARRAY_A );
                    if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                        $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                        if ( true === $db_error['db_error'] ) {
                            return $db_error;
                        }
                    }

					$data[ $step['id'] ] = array(
						'type'            => 'landing',
						'object_id'       => $landing_id,
						'object_name'     => get_the_title( $landing_id ),
						'revenue'         => null,
						'conversions'     => is_null( $get_data[0]['converted'] ) ? 0 : intval( $get_data[0]['converted'] ),
						'views'           => is_null( $get_data[0]['viewed'] ) ? 0 : intval( $get_data[0]['viewed'] ),
						'conversion_rate' => $this->get_percentage( $get_data[0]['viewed'], $get_data[0]['converted'] ),
					);
				}
			}
		}

		return $data;
	}

	public function get_all_thankyou( $funnel, $funnel_id, $start_date, $end_date ) {
		global $wpdb;
		$data = [];
		if ( $funnel instanceof WFFN_Funnel && 0 < $funnel->get_id() ) {
			$get_steps = $funnel->get_steps();
			$get_steps = $this->maybe_add_ab_variants( $get_steps );
			foreach ( $get_steps as $step ) {
				if ( $step['type'] === 'wc_thankyou' ) {
					$landing_id          = $step['id'];
					$is_variant          = get_post_meta( $landing_id, '_bwf_ab_variation_of', true );
					$type                = ( ! empty( $is_variant ) && $is_variant > 0 ) ? 15 : 5;
					$get_query           = " SELECT SUM(CASE WHEN type = " . $type . " THEN `no_of_sessions` END) AS `viewed`  FROM  " . $wpdb->prefix . 'wfco_report_views' . "  WHERE object_id=$landing_id AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
					$get_data            = $wpdb->get_results( $get_query, ARRAY_A );
                    if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                        $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                        if ( true === $db_error['db_error'] ) {
                            return $db_error;
                        }
                    }
					$data[ $step['id'] ] = array(
						'type'            => 'thankyou',
						'object_id'       => $landing_id,
						'object_name'     => get_the_title( $landing_id ),
						'revenue'         => null,
						'conversions'     => null,
						'views'           => is_null( $get_data[0]['viewed'] ) ? 0 : intval( $get_data[0]['viewed'] ),
						'conversion_rate' => null,
					);
				}
			}
		}


		return $data;
	}

	public function get_all_upsells( $funnel, $funnel_id, $start_date, $end_date ) {
		global $wpdb;
		$data = [];
		if ( class_exists( 'WFOCU_Core' ) && version_compare( WFOCU_VERSION, '2.2.0', '>=' ) ) {

			if ( $funnel instanceof WFFN_Funnel && 0 < $funnel->get_id() ) {
				$get_steps = $funnel->get_steps();
				$get_steps = $this->maybe_add_ab_variants( $get_steps );
				foreach ( $get_steps as $step ) {
					if ( $step['type'] === 'wc_upsells' ) {

						$data[ $step['id'] ] = array(
							'type'            => 'upsell',
							'object_id'       => $step['id'],
							'object_name'     => get_the_title( $step['id'] ),
							'revenue'         => 0,
							'conversions'     => 0,
							'views'           => 0,
							'conversion_rate' => 0,
							'is_variant'      => ( isset( $step['is_variant'] ) ) ? true : false,
						);
					}
				}
			}
			$get_all_upsell_funnels_by_query = "SELECT DISTINCT(event.object_id),p.post_title as 'name' FROM " . $wpdb->prefix . 'wfocu_event' . " as event LEFT JOIN " . $wpdb->prefix . 'wfocu_session' . " as session ON event.sess_id = session.id LEFT JOIN " . $wpdb->prefix . 'posts' . " as p ON event.object_id  = p.id WHERE (event.action_type_id = 1) AND session.fid=$funnel_id AND event.timestamp BETWEEN '" . $start_date . "' AND '" . $end_date . "' order by event.object_id asc";

			$get_all_upsell_records = $wpdb->get_results( $get_all_upsell_funnels_by_query, ARRAY_A );
            if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                if ( true === $db_error['db_error'] ) {
                    return $db_error;
                }
            }

			if ( is_array( $get_all_upsell_records ) && count( $get_all_upsell_records ) > 0 ) {
				foreach ( $get_all_upsell_records as $records ) {
					$data[ $records['object_id'] ] = array(
						'type'            => 'upsell',
						'object_id'       => $records['object_id'],
						'object_name'     => $records['name'],
						'revenue'         => 0,
						'conversions'     => 0,
						'views'           => 0,
						'conversion_rate' => 0,
						'is_variant'      => isset( $data[ $records['object_id'] ] ) && isset( $data[ $records['object_id'] ]['is_variant'] ) ? $data[ $records['object_id'] ]['is_variant'] : false,
					);
				}
			}

			if ( is_array( $data ) && count( $data ) > 0 ) {
				foreach ( array_keys( $data ) as $id ) {

					$data[ $id ]['offers'] = [];

					$steps = get_post_meta( $id, '_funnel_steps', true );

					if ( is_array( $steps ) && count( $steps ) > 0 ) {
						foreach ( $steps as $step ) {
							$data[ $id ]['offers'][ $step['id'] ] = array(
								'object_id'       => $step['id'],
								'object_name'     => get_the_title( $step['id'] ),
								'revenue'         => 0,
								'conversions'     => 0,
								'views'           => 0,
								'conversion_rate' => 0,
							);
						}
					}

					$get_the_upsell_query = "SELECT COUNT(CASE WHEN action_type_id = 4 THEN 1 END) AS `converted`, COUNT(CASE WHEN action_type_id = 2 THEN 1 END) AS `viewed`, object_id  as 'offer', action_type_id,SUM(value) as revenue FROM " . $wpdb->prefix . 'wfocu_event' . "  as events INNER JOIN " . $wpdb->prefix . 'wfocu_event_meta' . " AS events_meta__funnel_id ON ( events.ID = events_meta__funnel_id.event_id ) 
			                        AND ( ( events_meta__funnel_id.meta_key   = '_funnel_id' AND events_meta__funnel_id.meta_value = $id )) AND (events.action_type_id = '2' OR events.action_type_id = '4' ) AND events.timestamp BETWEEN '" . $start_date . "' AND '" . $end_date . "'  GROUP BY events.object_id";

					$query_res = $wpdb->get_results( $get_the_upsell_query, ARRAY_A );
                    if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                        $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                        if ( true === $db_error['db_error'] ) {
                            return $db_error;
                        }
                    }

					if ( is_array( $query_res ) && count( $query_res ) > 0 ) {
						foreach ( $query_res as $offer_data ) {
							$data[ $id ]['offers'][ $offer_data['offer'] ] = array(
								'object_id'       => $offer_data['offer'],
								'object_name'     => get_the_title( $offer_data['offer'] ),
								'revenue'         => isset( $offer_data['revenue'] ) ? $offer_data['revenue'] : 0,
								'conversions'     => is_null( $offer_data['converted'] ) ? 0 : intval( $offer_data['converted'] ),
								'views'           => is_null( $offer_data['viewed'] ) ? 0 : intval( $offer_data['viewed'] ),
								'conversion_rate' => $this->get_percentage( $offer_data['viewed'], $offer_data['converted'] ),
							);
						}

					}

				}
			}

		}

		return $data;
	}

	public function get_all_op_thankyou( $funnel, $funnel_id, $start_date, $end_date ) {
		global $wpdb;
		$data = [];
		if ( $funnel instanceof WFFN_Funnel && 0 < $funnel->get_id() ) {
			$get_steps = $funnel->get_steps();
			$get_steps = $this->maybe_add_ab_variants( $get_steps );
			foreach ( $get_steps as $step ) {
				if ( $step['type'] === 'optin_ty' ) {
					$optin_ty_id         = $step['id'];
					$is_variant          = get_post_meta( $optin_ty_id, '_bwf_ab_variation_of', true );
					$type                = ( ! empty( $is_variant ) && $is_variant > 0 ) ? 17 : 10;
					$get_query           = "  SELECT SUM(CASE WHEN type = ". $type ." THEN `no_of_sessions` END) AS `viewed` FROM " . $wpdb->prefix . 'wfco_report_views' . "  WHERE object_id=$optin_ty_id AND date BETWEEN '" . $start_date . "' AND '" . $end_date . "' GROUP by object_id ASC";
					$get_data            = $wpdb->get_results( $get_query, ARRAY_A );
                    if(method_exists('WFFN_Common','maybe_wpdb_error' )) {
                        $db_error = WFFN_Common::maybe_wpdb_error( $wpdb );
                        if ( true === $db_error['db_error'] ) {
                            return $db_error;
                        }
                    }
					$data[ $step['id'] ] = array(
						'type'            => 'optin_ty',
						'object_id'       => $optin_ty_id,
						'object_name'     => get_the_title( $optin_ty_id ),
						'revenue'         => null,
						'conversions'     => null,
						'views'           => is_null( $get_data[0]['viewed'] ) ? 0 : intval( $get_data[0]['viewed'] ),
						'conversion_rate' => null,
					);
				}
			}
		}

		return $data;
	}

	/**
	 * @param $steps
	 *
	 * @return mixed
	 */
	public function maybe_add_ab_variants( $steps ) {
		$temp_steps = [];
		foreach ( $steps as $step ) {
			$step_type = $step['type'];
			$step_id   = $step['id'];
			$get_step  = WFFN_Pro_Core()->steps->get_integration_object( $step_type );
			if ( ! $get_step instanceof WFFN_Pro_Step ) {
				continue;
			}
			$temp_steps[] = $step;
			$variant_ids  = $get_step->maybe_get_ab_variants( $step_id );
			if ( is_array( $variant_ids ) && count( $variant_ids ) > 0 ) {
				foreach ( $variant_ids as $variant_id ) {
					$varinat_step = array( 'type' => $step_type, 'is_variant' => 'yes', 'id' => (string) $variant_id, 'substeps' => [] );
					$temp_steps[] = $varinat_step;
				}
			}
		}

		return $temp_steps;
	}

	public function maybe_add_ab_substep_variants( $substeps ) {
		$temp_substeps = [];
		foreach ( $substeps as $subtype => $substep_ids ) {
			if ( empty( $subtype ) ) {
				continue;
			}
			$get_substep = WFFN_Pro_Core()->substeps->get_integration_object( $subtype );
			if ( ! $get_substep instanceof WFFN_Pro_Substep ) {
				continue;
			}
			foreach ( $substep_ids as $substep_id ) {
				$temp_substeps[ $subtype ][] = $substep_id;
				$variant_ids                 = $get_substep->maybe_get_ab_variants( $substep_id );
				foreach ( $variant_ids as $variant_id ) {
					$temp_substeps[ $subtype ][] = $variant_id;
				}
			}
		}

		return $temp_substeps;
	}

	/**
	 * Get percentage of a given number against a total
	 *
	 * @param float|int $total total number of occurrences
	 * @param float|int $number the number to get percentage against
	 *
	 * @return float|int
	 */
	function get_percentage( $total, $number ) {
		if ( $total > 0 ) {
			return round( $number / ( $total / 100 ), 2 );
		} else {
			return 0;
		}
	}

	function should_query_bump() {

		if ( class_exists( 'WFOB_Core' ) && version_compare( WFOB_VERSION, '1.8,1', '>' ) ) {
			return true;
		}

		return false;
	}


}

WFFN_REST_API_EndPoint::get_instance();