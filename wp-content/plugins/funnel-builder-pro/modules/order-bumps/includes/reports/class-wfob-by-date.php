<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Upstroke Admin Report - Upsells by date
 *
 * Find the upsells accepted between given dates
 *
 * @package        WooCommerce Upstroke
 * @subpackage    WC_Report_bumps_By_Date
 * @category    Class
 */
class WC_Report_wfob_by_date extends WC_Admin_Report {

	/**
	 * Chart colors.
	 *
	 * @var array
	 */
	public $chart_colours = array();

	private $average_gross_bump_revenue = 0;
	/**
	 * The report data.
	 *
	 * @var stdClass
	 */
	private $report_data = [ 'accepted' => [], 'rejected' => [], 'conversion' => [], 'revenue' => [], 'average_revenue' => 0 ];

	/**
	 * Get report data.
	 *
	 * @return stdClass
	 */
	public function get_report_data() {

		return $this->query_report_data();
	}

	private function add_default_property( $first_key, $date ) {
		if ( ! property_exists( $this->report_data->{$first_key}, $date ) ) {
			$this->report_data->{$first_key}->{$date}        = new stdClass();
			$this->report_data->{$first_key}->{$date}->date  = $date;
			$this->report_data->{$first_key}->{$date}->count = 0;
		}

	}

	/**
	 * Get all data needed for this report and store in the class.
	 */
	private function query_report_data() {


		global $wpdb;

		$start_date = date( 'Y-m-d', $this->start_date );

		$time           = strtotime( '23 hours 59 minutes', $this->end_date );
		$this->end_date = $time;
		$end_date       = date( 'Y-m-d H:i:s', $this->end_date );
		$where          = "where `date` >= '{$start_date}' AND `date` <='{$end_date}'";
		$sql            = "select * from {$wpdb->wfob_stats} {$where}";
		$results        = $wpdb->get_results( $sql, ARRAY_A );

		$this->report_data                  = new stdClass();
		$this->report_data->bump_views      = 0;
		$this->report_data->conversion_rate = 0;
		$this->report_data->no_accepted     = 0;
		$this->report_data->no_rejected     = 0;
		$this->report_data->total_revenue   = 0;
		$this->report_data->daily_views     = new stdClass();
		$this->report_data->accepted        = new stdClass();
		$this->report_data->rejected        = new stdClass();
		$this->report_data->conversion      = new stdClass();
		$this->report_data->revenue         = new stdClass();
		$this->report_data->average_revenue = 0;


		$static_data = [
			'no_accepted'     => 0,
			'no_rejected'     => 0,
			'conversion_rate' => 0,
			'total_revenue'   => 0,
		];

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {

				$date = $result['date'];


				$this->add_default_property( 'daily_views', $date );
				$this->add_default_property( 'accepted', $date );
				$this->add_default_property( 'rejected', $date );
				$this->add_default_property( 'revenue', $date );
				$this->add_default_property( 'conversion', $date );

				$this->report_data->daily_views->{$date}->count += 1;

				if ( '1' == $result['converted'] ) {
					$this->report_data->accepted->{$date}->count += 1;
					$this->report_data->no_accepted              += 1;
				} else {
					$this->report_data->rejected->{$date}->count += 1;
					$this->report_data->no_rejected              += 1;
				}

				if ( $result['total'] > 0 ) {
					$this->report_data->total_revenue           += $result['total'];
					$this->report_data->revenue->{$date}->count += $result['total'];
				}


				$accepted_bumps = $this->report_data->accepted->{$date}->count;
				$rejected_bumps = $this->report_data->rejected->{$date}->count;

				$total_bump_per_data = ( $accepted_bumps + $rejected_bumps );
				if ( $total_bump_per_data > 0 ) {
					$this->report_data->conversion->{$date}->count = ( $accepted_bumps / $total_bump_per_data ) * 100;
				}
			}
			$this->report_data->bump_views      = count( $results );
			$this->report_data->conversion_rate = ( $this->report_data->no_accepted / $this->report_data->bump_views ) * 100;
			$this->report_data->conversion_rate = number_format( $this->report_data->conversion_rate, 2 );
		}


		$this->report_data = apply_filters( 'woocommerce_admin_order_bump_report_data', $this->report_data );

		return $this->report_data;

	}

	/**
	 * Get the legend for the main chart sidebar.
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();
		$data   = $this->get_report_data();

		$legend[] = array(
			'title'            => sprintf( __( '%s Gross revenue in this period', 'woofunnels-order-bump' ), '<strong>' . wc_price( $data->total_revenue ) . '</strong>' ),
			'color'            => $this->chart_colours['revenue'],
			'placeholder'      => __( 'Additional revenue generated by order bump in this period.', 'woofunnels-order-bump' ),
			'highlight_series' => 3,
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s Total Orders', 'woofunnels-order-bump' ), '<strong>' . $data->bump_views . '</strong>' ),
			'placeholder'      => __( 'Total orders with bump offers.', 'woofunnels-order-bump' ),
			'color'            => $this->chart_colours['views'],
			'highlight_series' => 0,
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s Total bumps accepted in this period.', 'woofunnels-order-bump' ), '<strong>' . $data->no_accepted . '</strong>' ),
			'placeholder'      => __( 'Total bumps accepted.', 'woofunnels-order-bump' ),
			'color'            => $this->chart_colours['accepted'],
			'highlight_series' => 1,
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s Total bumps rejected in this period.', 'woofunnels-order-bump' ), '<strong>' . $data->no_rejected . '</strong>' ),
			'placeholder'      => __( 'Total bumps rejected.', 'woofunnels-order-bump' ),
			'color'            => $this->chart_colours['rejected'],
			'highlight_series' => 2,
		);


		$legend[] = array(
			'title'       => sprintf( __( '%s Conversion rates in this period', 'woofunnels-order-bump' ), '<strong>' . $data->conversion_rate . '%</strong>' ),
			'color'       => $this->chart_colours['conversion'],
			'placeholder' => __( 'Conversion rates.', 'woofunnels-order-bump' ),
		);

		return $legend;
	}

	/**
	 * Output the report.
	 */
	public function output_report() {
		$ranges = array(
			'year'       => __( 'Year', 'woocommerce' ),
			'last_month' => __( 'Last month', 'woocommerce' ),
			'month'      => __( 'This month', 'woocommerce' ),
			'7day'       => __( 'Last 7 days', 'woocommerce' ),
		);

		$this->chart_colours = array(
			'views'      => '#F6DDCC',
			'accepted'   => '#DBE0E2',
			'rejected'   => '#ABB2B9',
			'revenue'    => '#b9e6cc',
			'conversion' => '#422b35',
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->check_current_range_nonce( $current_range );
		$this->calculate_current_range( $current_range );

		include WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php';
	}

	/**
	 * Output an export link to export reports in csv file
	 */
	public function get_export_button() {
		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
		?>
        <a
                href="#"
                download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time( 'timestamp' ) ); ?>.csv"
                class="export_csv"
                data-export="chart"
                data-xaxes="<?php esc_attr_e( 'Date', 'woofunnels-order-bump' ); ?>"
                data-exclude_series=""
                data-groupby="<?php echo $this->chart_groupby; ?>"
        >
			<?php _e( 'Export CSV', 'woocommerce' ); ?>
        </a>
		<?php
	}

	/**
	 * Round our totals correctly.
	 *
	 * @param array|string $amount
	 *
	 * @return array|string
	 */
	private function round_chart_totals( $amount ) {
		if ( is_array( $amount ) ) {
			return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
		} else {
			return wc_format_decimal( $amount, wc_get_price_decimals() );
		}
	}

	/**
	 * Get the main chart.
	 */
	public function get_main_chart() {
		global $wp_locale;

		$views      = $this->prepare_chart_data( $this->report_data->daily_views, 'date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$accepted   = $this->prepare_chart_data( $this->report_data->accepted, 'date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$rejected   = $this->prepare_chart_data( $this->report_data->rejected, 'date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$conversion = $this->prepare_chart_data( $this->report_data->conversion, 'date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$revenue    = $this->prepare_chart_data( $this->report_data->revenue, 'date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );


		// Encode in json format
		$chart_data = array(
			'views'      => array_values( $views ),
			'accepted'   => array_values( $accepted ),
			'rejected'   => array_values( $rejected ),
			'conversion' => array_values( $conversion ),
			'revenue'    => array_values( $revenue ),
		);


		// 3rd party filtering of report data
		$chart_data = apply_filters( 'woocommerce_admin_order_bump_report_chart_data', $chart_data );

		?>
        <div class="chart-container">
            <div class="chart-placeholder main"></div>
        </div>
        <script type="text/javascript">

            var main_chart;

            jQuery(function () {
                var bumps_data = jQuery.parseJSON('<?php echo json_encode( $chart_data ); ?>');
                var drawGraph = function (highlight) {
                    var series = [
                        {
                            label: "<?php echo esc_js( __( 'Bumps Triggered', 'woofunnels-order-bump' ) ); ?>",
                            data: bumps_data.views,
                            color: '<?php echo $this->chart_colours['views']; ?>',
                            yaxis: 2,
                            bars: {
                                fillColor: '<?php echo $this->chart_colours['views']; ?>',
                                fill: true,
                                show: true,
                                lineWidth: 3,
                                order: 0,
                                barWidth: <?php echo $this->barwidth; ?>* 0.25,
                                align: 'center'
                            },
                            shadowSize: 0,
                            hoverable: true,
                        },
                        {
                            label: "<?php echo esc_js( __( 'Accepted', 'woofunnels-order-bump' ) ); ?>",
                            data: bumps_data.accepted,
                            color: '<?php echo $this->chart_colours['accepted']; ?>',
                            yaxis: 2,
                            bars: {
                                fillColor: '<?php echo $this->chart_colours['accepted']; ?>',
                                fill: true,
                                show: true,
                                lineWidth: 3,
                                order: 1,
                                barWidth: <?php echo $this->barwidth; ?>* 0.25,
                                align: 'center'
                            },
                            shadowSize: 0,
                            hoverable: true,
                        },
                        {
                            label: "<?php echo esc_js( __( 'Rejected', 'woofunnels-order-bump' ) ); ?>",
                            data: bumps_data.rejected,
                            color: '<?php echo $this->chart_colours['rejected']; ?>',
                            yaxis: 2,
                            bars: {
                                fillColor: '<?php echo $this->chart_colours['rejected']; ?>',
                                fill: true,
                                show: true,
                                lineWidth: 3,
                                order: 2,
                                barWidth: <?php echo $this->barwidth; ?>* 0.25,
                                align: 'center'
                            },
                            shadowSize: 0,
                            hoverable: true,
                        },
                        {
                            label: "<?php echo esc_js( __( 'Revenue', 'woofunnels-order-bump' ) ); ?>",
                            data: bumps_data.revenue,
                            yaxis: 1,
                            color: '<?php echo $this->chart_colours['revenue']; ?>',
                            points: {show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true},
                            lines: {show: true, lineWidth: 5, fill: false},
                            shadowSize: 0,
                        }
                    ];

                    if (highlight !== 'undefined' && series[highlight]) {
                        highlight_series = series[highlight];

                        highlight_series.color = '#9c5d90';

                        if (highlight_series.bars) {
                            highlight_series.bars.fillColor = '#9c5d90';
                        }

                        if (highlight_series.lines) {
                            highlight_series.lines.lineWidth = 5;
                        }
                    }

                    main_chart = jQuery.plot(
                        jQuery('.chart-placeholder.main'),
                        series,
                        {
                            legend: {
                                show: false
                            },
                            grid: {
                                color: '#aaa',
                                borderColor: 'transparent',
                                borderWidth: 0,
                                hoverable: true
                            },
                            xaxes: [{
                                color: '#aaa',
                                position: "bottom",
                                tickColor: 'transparent',
                                mode: "time",
                                timeformat: "<?php echo ( 'day' === $this->chart_groupby ) ? '%d %b' : '%b'; ?>",
                                monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ); ?>,
                                tickLength: 1,
                                minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
                                font: {
                                    color: "#aaa"
                                }
                            }],
                            yaxes: [
                                {
                                    min: 0,
                                    minTickSize: 1,
                                    tickDecimals: 0,
                                    color: '#d4d9dc',
                                    font: {color: "#aaa"}
                                },
                                {
                                    position: "right",
                                    min: 0,
                                    tickDecimals: 2,
                                    alignTicksWithAxis: 1,
                                    color: 'transparent',
                                    font: {color: "#aaa"}
                                }
                            ],
                        }
                    );

                    jQuery('.chart-placeholder').resize();
                }

                drawGraph();

                jQuery('.highlight_series').hover(
                    function () {
                        drawGraph(jQuery(this).data('series'));
                    },
                    function () {
                        drawGraph();
                    }
                );
            });
        </script>
		<?php
	}
}
