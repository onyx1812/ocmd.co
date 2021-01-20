<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WFFN_Funnels_Listing_Table extends WP_List_Table {

	public $per_page = 4;
	public $data;
	public $meta_data;
	public $date_format;

	/**
	 * Constructor.
	 * @since  1.0.0
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array(
			'singular' => 'Funnel',
			'plural'   => 'Funnels',
			'ajax'     => false,
		) );

		$this->data        = array();
		$this->date_format = WFFN_Core()->admin->get_date_format();
		$this->per_page    = WFFN_Core()->admin->posts_per_page();
		// Make sure this file is loaded, so we have access to plugins_api(), etc.
		require_once( ABSPATH . '/wp-admin/includes/plugin-install.php' );

		parent::__construct( $args );

	}

	public static function render_trigger_nav() {
		return '';
		$sql_query     = 'SELECT {table_name}.id as funnel_id FROM {table_name} WHERE 1=1 ORDER BY {table_name}.id DESC';
		$funnel_ids    = WFFN_Core()->get_dB()->get_results( $sql_query );
		$funnel_status = array( 'all' => __( 'All', 'funnel-builder' ) );

		$html        = '<ul class="subsubsub subsubsub_wffn">';
		$html_inside = array();

		foreach ( $funnel_status as $status ) {
			$status        .= ( count( $funnel_ids ) > 0 ) ? " (" . count( $funnel_ids ) . ")" : " (0)";
			$need_class    = 'current';
			$url           = "";
			$html_inside[] = sprintf( '<li><a href="%s" class="%s">%s</a> </li>', esc_url( $url ), $need_class, $status );
		}
		if ( is_array( $html_inside ) && count( $html_inside ) > 0 ) {
			$html .= implode( '', $html_inside );
		}
		$html .= '</ul>';

		echo wp_kses_post( $html );
	}

	/**
	 * Text to display if no funnels are present.
	 * @return  void
	 * @since  1.0.0
	 */
	public function no_items() {
		echo esc_html__( 'No funnels available.', 'funnel-builder' );
	}

	public function column_title( $item ) {

		$edit_link     = $item['row_actions']['edit']['link'];
		$column_string = '<div><strong>';

		$column_string .= '<a href="' . esc_url( $edit_link ) . '" class="row-title">' . $item['title'] . '</a>';
		if('yes' === WFFN_Core()->get_dB()->get_meta($item['id'],'_is_global')) {
			$global_label =  __('Global', 'funnel-builder');
            $column_string .= ' — <span class="post-state">'.$global_label.'</span>';
		}
		$column_string .= '</strong>';
		$column_string .= $item['desc'];

		$column_string .= "<div style='clear:both'></div></div>";

		$column_string .= '<div class=\'row-actions\'>';

		$item_last     = array_keys( $item['row_actions'] );
		$item_last_key = end( $item_last );
		foreach ( $item['row_actions'] as $k => $action ) {
			$column_string .= '<span class="' . $action['action'] . '">';
			if ( ! empty( $action['link'] ) ) {
				if ( ! isset( $action['_blank'] ) ) {
					$column_string .= '<a href="' . $action['link'] . '" ' . $action['attrs'] . ' >';
				} else {
					$column_string .= '<a target="_blank" href="' . $action['link'] . '" ' . $action['attrs'] . ' >';
				}
			}

			$column_string .= $action['text'];
			if ( ! empty( $action['link'] ) ) {
				$column_string .= '</a>';
			}

			if ( $k !== $item_last_key ) {
				$column_string .= ' | ';
			}
			$column_string .= '</span>';
		}
		$column_string .= '</div>';

		return ( $column_string );
	}

	public function column_desc( $item ) {
		return $item['desc'];
	}

	public function column_date_added( $item ) {
		$date = gmdate( 'Y/m/d H:i:s', strtotime( $item['date_added'] ) );

		return $date;
	}

	/**
	 * Prepare an array of items to be listed.
	 * @return array Prepared items.
	 * @since  1.0.0
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$total_items = $this->data['found_posts'];

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $this->per_page, //WE have to determine how many items to show on a page
		) );
		$this->items = $this->data['items'];
	}

	/**
	 * Retrieve an array of columns for the list table.
	 * @return array Key => Value pairs.
	 * @since  1.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'title'      => __( 'Name', 'funnel-builder' ),
			'date_added' => __( 'Date', 'funnel-builder' ),
		);

		return $columns;
	}

	public function get_table_classes() {
		$get_default_classes = parent::get_table_classes();
		array_push( $get_default_classes, 'wffn-instance-table' );

		return $get_default_classes;
	}

	public function single_row( $item ) {
		$tr_class = 'wffn_funnels';
		echo '<tr class="' . esc_attr( $tr_class ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Displays the search box.
	 *
	 * @param string $text The 'submit' button label.
	 * @param string $input_id ID attribute value for the search input field.
	 *
	 * @since 3.1.0
	 *
	 */
	public function search_box( $text = '', $input_id = 'wffn' ) {
		$input_id = $input_id . '-search-input'; ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label>
            <input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>"/>
			<?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
        </p>
		<?php
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @param string $which
	 *
	 * @since 3.1.0
	 *
	 */
	protected function display_tablenav( $which ) {

		?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

			<?php if ( $this->has_items() ) : ?>
                <div class="alignleft actions bulkactions">
					<?php $this->bulk_actions( $which ); ?>
                </div>
			<?php
			endif;
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>

            <br class="clear"/>
        </div>
		<?php
	}

	protected function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="funnels[]" value="%s" />', $item['id'] );
	}

	function get_bulk_actions() {
		$actions = array(
			'bulk_delete' => 'Delete'
		);

		return $actions;
	}

}
