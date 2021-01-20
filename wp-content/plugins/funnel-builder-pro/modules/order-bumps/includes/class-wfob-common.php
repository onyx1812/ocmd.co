<?php

/**
 * Class WFOB_Common
 * Handles Common Functions For Admin as well as front end interface
 */
class WFOB_Common {

	public static $ins = null;
	public static $design_default_layout = 'layout_1';
	private static $wfob_id = 0;
	private static $design_model_data = [];
	private static $design_default_model_data = [];
	private static $product_data = [];

	public static function init() {

		/**
		 * Loading WooFunnels core
		 */
		add_action( 'plugins_loaded', [ __CLASS__, 'include_core' ], - 1 );

		/**
		 * Register Post Type
		 */
		add_action( 'init', array( __CLASS__, 'register_post_type' ), 100 );

		add_action( 'init', array( __CLASS__, 'register_post_status' ), 5 );

		add_filter( 'wfob_parse_shortcode', 'do_shortcode' );

		/**
		 * Allowing iframe tag in post context
		 */
		add_filter( 'wp_kses_allowed_html', array( __CLASS__, 'allow_iframe_tag_in_post' ), 10, 2 );
		add_action( 'wp_ajax_wfob_change_rule_type', array( __CLASS__, 'ajax_render_rule_choice' ) );

		add_filter( 'wcct_get_restricted_action', function ( $actions ) {
			$actions[] = 'wfob_add_product';
			$actions[] = 'wfob_remove_product';
			$actions[] = 'wfob_save_products';

			return $actions;
		} );
	}


	public static function include_core() {
		if ( isset( $_REQUEST['wfob_id'] ) && $_REQUEST['wfob_id'] > 0 ) {
			self::set_id( absint( $_REQUEST['wfob_id'] ) );
		}
		WooFunnel_Loader::include_core();
	}


	/**
	 * Get current Page id
	 * @return int
	 */
	public static function set_id( $wfob_id = 0 ) {
		if ( is_numeric( $wfob_id ) ) {
			self::$wfob_id = absint( $wfob_id );

		}
	}

	public static function get_instance() {
		if ( null == self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	public static function get_id() {
		return self::$wfob_id;
	}

	public static function get_wc_settings_tab_slug() {
		return 'wfob-bump';
	}

	public static function register_post_type() {

		register_post_type( self::get_bump_post_type_slug(), apply_filters( 'wfob_bump_post_type_args', array(
			'labels'              => array(
				'name'          => __( 'bump', 'woofunnels-order-bump' ),
				'singular_name' => __( 'Bump', 'woofunnels-order-bump' ),
				'add_new'       => __( 'Add Bump', 'woofunnels-order-bump' ),
				'add_new_item'  => __( 'Add New Bump', 'woofunnels-order-bump' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'capability_type'     => 'product',
			'map_meta_cap'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_menu'        => false,
			'hierarchical'        => false,
			'show_in_nav_menus'   => false,
			'rewrite'             => false,
			'query_var'           => true,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
		) ) );
	}

	public static function get_bump_post_type_slug() {
		return 'wfob_bump';
	}

	public static function is_load_admin_assets( $screen_type = 'single' ) {
		$screen = get_current_screen();
		if ( 'all' === $screen_type ) {
			if ( filter_input( INPUT_GET, 'page' ) == 'wfob' ) {

				return true;
			}
		} elseif ( 'listing' == $screen_type ) {

		} elseif ( 'all' === $screen_type || 'builder' == $screen_type ) {
			if ( filter_input( INPUT_GET, 'page' ) == 'wfob' && filter_input( INPUT_GET, 'wfob_id' ) > 0 ) {
				//&& filter_input( INPUT_GET, 'id' ) !== ''
				return true;
			}
		} elseif ( 'all' === $screen_type || 'settings' == $screen_type ) {
			if ( filter_input( INPUT_GET, 'page' ) == 'wfob' && filter_input( INPUT_GET, 'tab' ) == 'settings' ) {
				//&& filter_input( INPUT_GET, 'id' ) !== ''
				return true;
			}
		}

		return apply_filters( 'wfob_enqueue_scripts', false, $screen_type, $screen );
	}

	public static function array_flatten( $array ) {
		if ( ! is_array( $array ) ) {
			return false;
		}
		$result = iterator_to_array( new RecursiveIteratorIterator( new RecursiveArrayIterator( $array ) ), false );

		return $result;
	}

	public static function pr( $arr ) {
		echo '<pre>';
		print_r( $arr );
		echo '</pre>';
	}


	public static function get_product_id_hash( $bump_id, $offer_id, $product_id ) {
		if ( $bump_id == 0 || $offer_id == 0 || $product_id == 0 ) {
			return md5( time() );
		}

		$unique_multi_plier = $bump_id * $offer_id;
		$unique_key         = ( $unique_multi_plier * $product_id ) . time();
		$hash               = md5( $unique_key );

		return $hash;
	}

	public static function get_formatted_product_name( $product ) {
		$formatted_variation_list = self::get_variation_attribute( $product );

		$arguments = array();
		if ( ! empty( $formatted_variation_list ) && count( $formatted_variation_list ) > 0 ) {
			foreach ( $formatted_variation_list as $att => $att_val ) {
				if ( $att_val == '' ) {
					$att_val = __( 'any' );
				}
				$att         = strtolower( $att );
				$att_val     = strtolower( $att_val );
				$arguments[] = "$att: $att_val";
			}
		}

		return sprintf( '%s (#%d) %s', $product->get_title(), $product->get_id(), ( count( $arguments ) > 0 ) ? '(' . implode( ',', $arguments ) . ')' : '' );
	}

	public static function get_variation_attribute( $variation ) {
		if ( is_a( $variation, 'WC_Product_Variation' ) ) {
			$variation_attributes = $variation_attributes_basic = $variation->get_attributes();
			//          $variation_attributes       = array();
			//          foreach ( $variation_attributes_basic as $key => $value ) {
			//              $variation_attributes[ wc_attribute_label( $key, $variation ) ] = $value;
			//          }

		} else {
			$variation_attributes = array();
			if ( is_array( $variation ) ) {
				foreach ( $variation as $key => $value ) {
					$variation_attributes[ str_replace( 'attribute_', '', $key ) ] = $value;
				}
			}
		}

		return ( $variation_attributes );

	}

	public static function search_products( $term, $include_variations = false ) {
		global $wpdb;
		$like_term     = '%' . $wpdb->esc_like( $term ) . '%';
		$post_types    = $include_variations ? array( 'product', 'product_variation' ) : array( 'product' );
		$post_statuses = current_user_can( 'edit_private_products' ) ? array(
			'private',
			'publish',
		) : array( 'publish' );
		$type_join     = '';
		$type_where    = '';

		$product_ids = $wpdb->get_col(

			$wpdb->prepare( "SELECT DISTINCT posts.ID FROM {$wpdb->posts} posts
				LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
				$type_join
				WHERE (
					posts.post_title LIKE %s
					OR (
						postmeta.meta_key = '_sku' AND postmeta.meta_value LIKE %s
					)
				)
				AND posts.post_type IN ('" . implode( "','", $post_types ) . "')
				AND posts.post_status IN ('" . implode( "','", $post_statuses ) . "')
				$type_where
				ORDER BY posts.post_parent ASC, posts.post_title ASC", $like_term, $like_term ) );

		if ( is_numeric( $term ) ) {
			$post_id   = absint( $term );
			$post_type = get_post_type( $post_id );

			if ( 'product_variation' === $post_type && $include_variations ) {
				$product_ids[] = $post_id;
			} elseif ( 'product' === $post_type ) {
				$product_ids[] = $post_id;
			}

			$product_ids[] = wp_get_post_parent_id( $post_id );
		}

		return wp_parse_id_list( $product_ids );
	}


	public static function update_offer( $offer_id, $data, $bump_id = 0 ) {
		$data = apply_filters( 'update_offer', $data, $offer_id );
		update_post_meta( $offer_id, '_wfob_setting', $data );
		update_post_meta( $offer_id, '_wfob_bump_id', $bump_id );
	}

	public static function get_offer( $offer_id ) {
		$data = get_post_meta( $offer_id, '_wfob_setting', true );

		return apply_filters( 'get_offer', $data, $offer_id );
	}

	public static function update_bump_offers( $bump_id, $data ) {
		$data = apply_filters( 'update_bump_offers', $data, $bump_id );
		update_post_meta( $bump_id, '_bump_offers', $data );
	}

	public static function update_bump_steps( $bump_id, $data ) {
		$data = apply_filters( 'update_bump_steps', $data, $bump_id );
		update_post_meta( $bump_id, '_bump_steps', $data );
	}

	public static function update_bump_upsell_downsell( $bump_id, $data ) {
		$data = apply_filters( 'update_bump_upsell_downsell', $data, $bump_id );
		update_post_meta( $bump_id, '_bump_upsell_downsell', $data );
	}


	public static function get_bump() {
		$args = array(
			'post_type'   => self::get_bump_post_type_slug(),
			'post_status' => 'publish',
		);
		$loop = new WP_Query( $args );

		return $loop;
	}

	/**
	 * Slug-ify the class name and remove underscores and convert it to filename
	 * Helper function for the auto-loading
	 *
	 * @param $class_name
	 *
	 *
	 * @return mixed|string
	 * @see WFOB_Gateways::integration_autoload();
	 *
	 */
	public static function slugify_classname( $class_name ) {
		$classname = sanitize_title( $class_name );
		$classname = str_replace( '_', '-', $classname );

		return $classname;
	}

	/**
	 * Recursive Un-serialization based on   WP's is_serialized();
	 *
	 * @param $val
	 *
	 * @return mixed|string
	 * @see is_serialized()
	 */
	public static function unserialize_recursive( $val ) {
		//$pattern = "/.*\{(.*)\}/";
		if ( is_serialized( $val ) ) {
			$val = trim( $val );
			$ret = unserialize( $val );
			if ( is_array( $ret ) ) {
				foreach ( $ret as &$r ) {
					$r = self::unserialize_recursive( $r );
				}
			}

			return $ret;
		} elseif ( is_array( $val ) ) {
			foreach ( $val as &$r ) {
				$r = self::unserialize_recursive( $r );
			}

			return $val;
		} else {
			return $val;
		}

	}

	public static function maybe_parse_product_tags( $content = '', $key = '', $obj = '' ) {
		if ( empty( $content ) || empty( $key ) || empty( $obj ) ) {
			return;
		}
		$content = self::maybe_parse_merge_tags( $content );
		/** {{product_price}} */
		if ( strpos( $content, '{{product_price}}' ) !== false ) {
			$replace_str = self::maybe_parse_merge_tags( '{{product_offer_price key="' . $key . '"}}' );

			$content = str_replace( '{{product_price}}', $replace_str, $content );
		}

		return $content;
	}

	public static function maybe_parse_merge_tags( $content = '', $obj = false, $line_break = true ) {
		if ( empty( $content ) ) {
			return;
		}

		if ( true === $line_break ) {
			$content = nl2br( $content );
		}

		$content = WFOB_Static_Merge_Tags::maybe_parse_merge_tags( $content, $obj );
		$content = WFOB_Dynamic_Merge_Tags::maybe_parse_merge_tags( $content, $obj );
		$content = WFOB_ShortCode_Merge_Tags::maybe_parse_merge_tags( $content, $obj );

		$content = WFOB_Syntax_Merge_Tags::maybe_parse_merge_tags( $content, $obj );

		$content = apply_filters( 'wfob_parse_shortcode', $content );

		return $content;
	}

	public static function get_option( $field ) {

		return;
	}

	public static function get_variable_league_product_types() {
		return array(
			'variable',
			'variable-subscription',
		);
	}

	/**
	 * Get image source by image id or source
	 *
	 * @param type $value
	 * @param type $mode
	 *
	 * @return type
	 */
	public static function get_image_source( $value, $mode = 'full' ) {
		if ( is_numeric( $value ) ) {
			$image_data = wp_get_attachment_image_src( $value, $mode );
			if ( isset( $image_data[0] ) ) {
				return $image_data[0];
			}
		}

		return $value;
	}

	public static function maybe_convert_html_tag( $val ) {
		if ( false === is_string( $val ) ) {
			return $val;
		}
		$val = str_replace( '&lt;', '<', $val );
		$val = str_replace( '&gt;', '>', $val );

		return $val;

	}

	public static function get_post_table_data( $ids = false, $all = false ) {

		$args = array(
			'post_type'      => self::get_bump_post_type_slug(),
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => self::posts_per_page(),

		);
		$args = apply_filters( 'wfob_add_control_meta_query', $args );

		if ( isset( $_GET['paged'] ) && $_GET['paged'] > 0 ) {
			$args['paged'] = absint( $_GET['paged'] );
		}
		if ( isset( $_GET['order'] ) && '' !== $_GET['order'] && isset( $_GET['orderby'] ) ) {
			$args['orderby'] = $_GET['orderby'];
			$args['order']   = $_GET['order'];
		}
		if ( isset( $_REQUEST['s'] ) && '' !== $_REQUEST['s'] ) {
			$args['s'] = $_REQUEST['s'];
		}

		if ( isset( $_REQUEST['status'] ) && '' !== $_REQUEST['status'] ) {
			if ( $_REQUEST['status'] == 'active' ) {
				$args['post_status'] = 'publish';
			}
			if ( $_REQUEST['status'] == 'inactive' ) {
				$args['post_status'] = 'draft';
			}
		}

		$q = new WP_Query( $args );

		$found_posts = array(
			'found_posts' => $q->found_posts,
		);
		$items       = array();
		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {

				$q->the_post();
				global $post;

				$status   = get_post_status( get_the_ID() );
				$priority = $post->menu_order;

				$bump_url    = add_query_arg( array(
					'page'    => 'wfob',
					'section' => 'rules',
					'wfob_id' => get_the_ID(),
				), admin_url( 'admin.php' ) );
				$row_actions = array();

				$wfob_duplicate = add_query_arg( [
					'wfob_duplicate' => 'true',
					'wfob_id'        => $post->ID,
				], admin_url( 'admin.php?page=wfob' ) );

				$wfob_export = add_query_arg( [
					'action'   => 'wfob-export',
					'_wpnonce' => wp_create_nonce( 'wfob-export' ),
					'id'       => get_the_ID()
				], admin_url( 'admin.php?page=wfob' ) );

				$row_actions['edit'] = array(
					'action' => 'edit',
					'text'   => __( 'Edit', 'woofunnels-order-bump' ),
					'link'   => $bump_url,
					'attrs'  => '',
				);


				$row_actions['wfob_duplicate'] = array(
					'action' => 'wfob_duplicate',
					'class'  => 'wfob_duplicate_checkout_page',
					'text'   => __( 'Duplicate', 'woofunnels-order-bump' ),
					'link'   => $wfob_duplicate,
				);

				$row_actions['wfob_export'] = array(
					'action' => 'wfob_export',
					'class'  => 'wfob_export_order_bump',
					'text'   => __( 'Export', 'woofunnels-order-bump' ),
					'link'   => $wfob_export,
				);

				$row_actions['delete'] = array(
					'action' => 'delete',
					'class'  => 'wfob_delete_checkout_page',
					'text'   => __( 'Delete', 'woofunnels-order-bump' ),
					'link'   => get_delete_post_link( get_the_ID(), '', true ),
					'attrs'  => 'class="wfob_delete_checkout_page" data-id="' . get_the_ID() . '"',
				);

				$items[] = array(
					'id'           => get_the_ID(),
					'post_content' => get_the_content(),
					'status'       => $status,
					'row_actions'  => $row_actions,
					'priority'     => $priority,
				);

			}
		}

		$found_posts['items'] = $items;

		return $found_posts;
	}

	public static function posts_per_page() {
		return 20;
	}

	public static function string2hex( $string ) {
		$hex = '';
		for ( $i = 0; $i < strlen( $string ); $i ++ ) {
			$hex .= dechex( ord( $string[ $i ] ) );
		}

		return $hex;
	}

	/**
	 * @param $url
	 * @param string $type
	 *
	 * @return bool
	 */
	public static function get_video_id( $url, $type = 'youtube' ) {
		if ( empty( $url ) ) {
			return;
		}
		if ( 'youtube' == $type ) {
			preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match );
			if ( isset( $match[1] ) && ! empty( $match[1] ) ) {
				return $match[1];
			}

			return;
		} elseif ( 'vimeo' == $type ) {
			preg_match( '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $match );
			if ( isset( $match[3] ) && ! empty( $match[3] ) ) {
				return $match[3];
			}

			return;
		} elseif ( 'wistia' == $type ) {
			preg_match( '/https?:\/\/(.+)?(wistia\.com|wi\.st)\/(medias|embed)\/([a-zA-Z0-9]*).*/', $url, $match );
			if ( isset( $match[4] ) && ! empty( $match[4] ) ) {
				if ( $match[4] == 'iframe' ) {
					preg_match( '/https?:\/\/(.+)?(wistia\.com|wi\.st)\/(medias|embed)\/(iframe)\/([a-zA-Z0-9]*).*/', $url, $match );

					return ( isset( $match[5] ) ) ? $match[5] : '';
				} else {
					return $match[4];
				}
			}

			return;
		}

		return $url;

	}

	public static function get_order_status_settings() {
		$get_order_statuses = wc_get_order_statuses();

		$result = array_map( function ( $short, $long ) {
			return array(
				'id'   => $short,
				'name' => $long,
			);
		}, array_keys( $get_order_statuses ), $get_order_statuses );

		return $result;
	}

	public static function maybe_filter_boolean_strings( $options ) {
		$cloned_option = $options;
		foreach ( $options as $key => $value ) {

			if ( is_object( $options ) ) {

				if ( $value === 'true' || $value === true ) {

					$cloned_option->$key = true;
				}

				if ( $value === 'false' || $value === false ) {
					$cloned_option->$key = false;
				}
			} elseif ( is_array( $options ) ) {

				if ( $value === 'true' || $value === true ) {

					$cloned_option[ $key ] = true;
				}
				if ( $value === 'false' || $value === false ) {
					$cloned_option[ $key ] = false;
				}
			}
		}

		return $cloned_option;

	}

	public static function get_next_bump_priority() {
		$bump_max_priority = get_option( '_wfob_max_priority', 0, false );
		self::update_max_priority( $bump_max_priority + 1 );

		return $bump_max_priority + 1;
	}

	public static function update_max_priority( $current ) {
		$bump_max_priority = get_option( '_wfob_max_priority', 0 );

		if ( $current > $bump_max_priority ) {
			update_option( '_wfob_max_priority', $current );
		}
	}

	public static function register_post_status() {
		// acf-disabled
		register_post_status( WFOB_SLUG . '-disabled', array(
			'label'                     => __( 'Disabled', 'woofunnels-order-bump' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Disabled <span class="count">(%s)</span>', 'Disabled <span class="count">(%s)</span>', 'woofunnels-order-bump' ),
		) );
	}

	public static function tooltip( $tip, $allow_html = false ) {
		if ( $allow_html ) {
			$tip = wc_sanitize_tooltip( $tip );
		} else {
			$tip = esc_attr( $tip );
		}

		return '<span class="wfob-help-tip" data-tip="' . $tip . '"></span>';
	}


	public static function is_add_on_exist( $add_on = 'MultiProduct' ) {
		$status = false;
		if ( class_exists( 'WFOB_' . $add_on ) ) {
			$status = true;
		}

		return $status;
	}


	public static function get_date_format() {
		return get_option( 'date_format', '' ) . ' ' . get_option( 'time_format', '' );
	}

	public static function get_sidebar_menu() {
		$sidebar_menu = array(
			'20' => array(
				'icon' => 'dashicons dashicons-cart',
				'name' => __( 'Products', 'woofunnels-order-bump' ),
				'key'  => 'products',
			),
			'30' => array(
				'icon' => 'dashicons dashicons-art',
				'name' => __( 'Design', 'woofunnels-order-bump' ),
				'key'  => 'design',
			),
			'40' => array(
				'icon' => 'dashicons dashicons-admin-generic',
				'name' => __( 'Settings', 'woofunnels-order-bump' ),
				'key'  => 'settings',
			),

		);

		return apply_filters( 'wfob_builder_menu', $sidebar_menu );
	}

	public static function between( $needle, $that, $inthat ) {
		return self::before( $that, self::after( $needle, $inthat ) );
	}

	public static function before( $needle, $inthat ) {
		return substr( $inthat, 0, strpos( $inthat, $needle ) );
	}

	public static function after( $needle, $inthat ) {
		if ( ! is_bool( strpos( $inthat, $needle ) ) ) {
			return substr( $inthat, strpos( $inthat, $needle ) + strlen( $needle ) );
		}
	}

	public static function get_offer_base_url() {
		$offer_slug     = WFOB_Core()->data->get_option( 'offer_post_type_slug' );
		$offer_base_url = site_url( '/' . $offer_slug );

		return $offer_base_url;
	}

	public static function clean_ascii_characters( $content ) {

		if ( '' == $content ) {
			return $content;
		}

		$content = str_replace( '%', '_', $content );
		$content = str_replace( '!', '_', $content );
		$content = str_replace( '\"', '_', $content );
		$content = str_replace( '#', '_', $content );
		$content = str_replace( '$', '_', $content );
		$content = str_replace( '&', '_', $content );
		$content = str_replace( '(', '_', $content );
		$content = str_replace( ')', '_', $content );
		$content = str_replace( '(', '_', $content );
		$content = str_replace( '*', '_', $content );
		$content = str_replace( ',', '_', $content );
		$content = str_replace( '', '_', $content );
		$content = str_replace( '.', '_', $content );
		$content = str_replace( '/', '_', $content );

		return $content;
	}

	public static function allow_iframe_tag_in_post( $allowed, $context ) {

		if ( $context === 'post' ) {
			$allowed['script'] = array(
				'type'   => true,
				'src'    => true,
				'height' => true,
				'width'  => true,
			);
			$allowed['iframe'] = array(
				'src'                   => true,
				'width'                 => true,
				'height'                => true,
				'class'                 => true,
				'frameborder'           => true,
				'webkitAllowFullScreen' => true,
				'mozallowfullscreen'    => true,
				'allowFullScreen'       => true,
			);
			$allowed['style']  = true;
		}

		return $allowed;
	}

	/**
	 * remove unnecessary keys from single product array
	 */
	public static function remove_product_keys( $product ) {
		unset( $product['image'] );
		unset( $product['price'] );
		unset( $product['regular_price'] );
		unset( $product['sale_price'] );

		return $product;
	}

	public static function get_builder_localization() {
		$data           = [];
		$data['global'] = [
			'settings_success'    => __( 'Changes saved', 'woofunnels-order-bump' ),
			'form_has_changes'    => [
				'title'             => __( 'Changes have been made!', 'woofunnels-order-bump' ),
				'text'              => __( 'You need to save changes before generating preview.', 'woofunnels-order-bump' ),
				'confirmButtonText' => __( 'Yes, Save it!' ),
				'cancelText'        => __( 'Cancel', 'woofunnels-order-bump' ),
			],
			'remove_product'      => [
				'title'             => __( 'Want to remove this product from checkout?', 'woofunnels-order-bump' ),
				'text'              => __( "You won't be able to revert this!", 'woofunnels-order-bump' ),
				'confirmButtonText' => __( 'Yes, remove it!', 'woofunnels-order-bump' ),
			],
			'active'              => __( 'Active', 'woofunnels-order-bump' ),
			'inactive'            => __( 'Inactive', 'woofunnels-order-bump' ),
			'add_bump'            => [
				'heading'           => __( 'Page Title', 'woofunnels-order-bump' ),
				'checkout_url_slug' => __( 'Checkout Url', 'woofunnels-order-bump' ),
				'edit_order_bump'   => __( 'Edit OrderBump', 'woofunnels-order-bump' ),
				'new_bump'          => __( 'Add New OrderBump', 'woofunnels-order-bump' ),
			],
			'confirm_button_text' => __( 'Ok', 'woofunnels-order-bump' ),
			'cncel_button_text'   => __( 'Cancel', 'woofunnels-order-bump' ),
			'delete_wfob_page'    => __( 'Are you sure, you want to delete this permanently? This can`t be undone', 'woofunnels-order-bump' ),
			'data_saving'         => __( 'Data Saving...', 'woofunnels-order-bump' ),
		];

		$data['global_settings'] = [
			'custom_css_heading'           => __( 'Global Custom CSS', 'woofunnels-order-bump' ),
			'miscellaneous_heading'        => __( 'Miscellaneous', 'woofunnels-order-bump' ),
			'custom_css_label'             => __( 'Custom CSS Tweaks', 'woofunnels-order-bump' ),
			'custom_css_label_hint'        => __( 'Add your custom CSS to apply on all AeroCheckout pages', 'woofunnels-order-bump' ),
			'number_of_bump_print_heading' => __( 'Global Custom CSS', 'woofunnels-order-bump' ),
			'number_of_bump_print_label'   => __( 'Number of Bumps to be shown', 'woofunnels-order-bump' ),
			'number_of_bump_print_hint'    => __( 'Enter the limit of OrderBumps to be display on the checkout page', 'woofunnels-order-bump' ),
		];

		$data['settings'] = [
			'priority'                  => __( 'Set Priority', 'woofunnels-order-bump' ),
			'priority_hint'             => __( 'There may be a chance that OrderBump can be set up in a way that two OrderBump can trigger. In such cases, Prioirty is used to determine which OrderBump will display. Priority Number 1 is considered highest.', 'woofunnels-order-bump' ),
			'priority_label'            => __( 'Priority', 'woofunnels-order-bump' ),
			'custom_css_heading'        => __( 'Custom CSS', 'woofunnels-order-bump' ),
			'custom_css_label'          => __( 'Custom CSS Tweaks', 'woofunnels-order-bump' ),
			'order_bump_label'          => __( 'Position', 'woofunnels-order-bump' ),
			'order_bump_position_label' => __( 'Display Position', 'woofunnels-order-bump' ),
			'order_bump_position_hooks' => self::get_bump_position(),
			'other_bump_position_label' => __( 'Custom Position', 'woofunnels-order-bump' ),
			'other_bump_position_hint'  => __( 'You can reposition the OrderBump using these option', 'woofunnels-order-bump' ),

		];
		$data['design']   = [
			'group_heading'                      => __( 'Heading Bar', 'woofunnels-order-bump' ),
			'choose_layout_heading'              => __( 'Choose Layout', 'woofunnels-order-bump' ),
			'title_box'                          => __( 'Title', 'woofunnels-order-bump' ),
			'sub_title_box'                      => __( 'Sub Title', 'woofunnels-order-bump' ),
			'description_box'                    => __( 'Description', 'woofunnels-order-bump' ),
			'sub_description_box'                => __( 'Short Description', 'woofunnels-order-bump' ),
			'price_box'                          => __( 'Price', 'woofunnels-order-bump' ),
			'additional_settings'                => __( 'Box', 'woofunnels-order-bump' ),
			'header_enable_pointing_arrow'       => __( 'Arrow', 'woofunnels-order-bump' ),
			'price_color'                        => __( 'Regular price color', 'woofunnels-order-bump' ),
			'enable_price'                       => __( 'Enable Price', 'woofunnels-order-bump' ),
			'enable_price_side'                  => __( 'Display Price above Button', 'woofunnels-order-bump' ),
			'price_sale_color'                   => __( 'Sale price color', 'woofunnels-order-bump' ),
			'price_font_size'                    => __( 'Font Size', 'woofunnels-order-bump' ),
			'content_text_color'                 => __( 'Text Color', 'woofunnels-order-bump' ),
			'content_variation_link_color'       => __( 'Variation Link', 'woofunnels-order-bump' ),
			'content_variation_link_hover_color' => __( 'Variation Link Hover', 'woofunnels-order-bump' ),
			'featured_image_border_label'        => __( 'Product Image', 'woofunnels-order-bump' ),
			'border'                             => __( 'Border', 'woofunnels-order-bump' ),
			'padding'                            => __( 'Padding', 'woofunnels-order-bump' ),
			'background'                         => __( 'Background', 'woofunnels-order-bump' ),
			'hover_background'                   => __( 'Background hover', 'woofunnels-order-bump' ),
			'color'                              => __( 'Text', 'woofunnels-order-bump' ),
			'text_color'                         => __( 'Text', 'woofunnels-order-bump' ),
			'font_size'                          => __( 'Font Size', 'woofunnels-order-bump' ),
			'hover_color'                        => __( 'Text Hover', 'woofunnels-order-bump' ),
			'text_hover_color'                   => __( 'Text Hover', 'woofunnels-order-bump' ),
			'error_color'                        => __( 'Error', 'woofunnels-order-bump' ),
			'border_width'                       => __( 'width', 'woofunnels-order-bump' ),
			'border_color'                       => __( 'color', 'woofunnels-order-bump' ),
			'border_style'                       => __( 'style', 'woofunnels-order-bump' ),
			'bump_max_width'                     => __( 'Max Width (in px)', 'woofunnels-order-bump' ),
			'bump_max_width_hint'                => __( 'leave blank for full width', 'woofunnels-order-bump' ),
			'point_animation'                    => __( 'Arrow Animation', 'woofunnels-order-bump' ),
			'button'                             => __( 'Buttons', 'woofunnels-order-bump' ),
			'add_to_cart_button'                 => __( 'Add To Cart Button', 'woofunnels-order-bump' ),
			'added_to_cart_button'               => __( 'Added To Cart Button', 'woofunnels-order-bump' ),
			'remove_to_cart_button'              => __( 'Removed from Cart Button', 'woofunnels-order-bump' ),
			'default_read_more'                  => __( 'more', 'woofunnels-order-bump' ),

			'width' => __( 'Width', 'woofunnels-order-bump' ),

			'title'                  => __( 'Title', 'woofunnels-order-bump' ),
			'sub_title'              => __( 'Sub Title', 'woofunnels-order-bump' ),
			'title_hint'             => __( 'Use merge tag {{product_name}} to show product name dynamically. Useful in case if OrderBump is a variable product', 'woofunnels-order-bump' ),
			'feature_image'          => __( 'Product Image', 'woofunnels-order-bump' ),
			'description'            => __( 'Description', 'woofunnels-order-bump' ),
			'description_hint'       => __( "Use merge tag {{subscription_summary}} to show pricing details of subscription products <br> Use merge tag {{quantity_incrementer}} to allow increase quanity of orderBump products <br> Use merge tag {{variation_attribute_html}} to show selected variants of variable products", 'woofunnels-order-bump' ),
			'short_description'      => __( 'Short Description', 'woofunnels-order-bump' ),
			'read_more'              => __( 'Read More Text', 'woofunnels-order-bump' ),
			'short_description_hint' => __( "Use merge tag {{more}} to make Description collapsible", 'woofunnels-order-bump' ),
			'add_btn_text'           => __( 'Add Button', 'woofunnels-order-bump' ),
			'added_btn_text'         => __( 'Added Button', 'woofunnels-order-bump' ),
			'remove_btn_text'        => __( 'Remove Button', 'woofunnels-order-bump' ),
			'textOn'                 => __( 'Active', 'woofunnels-order-bump' ),
			'textOff'                => __( 'InActive', 'woofunnels-order-bump' ),
			'choose_option'          => sprintf( "<p><a href='#' class='wfob_qv-button var_product' qv-id='%d' qv-var-id='%d'>%s</a></p>", 0, 0, __( 'Choose an option', 'woocommerce' ) ),
			'layout_change'          => [
				'title'             => __( 'Are you sure want to change skin?', 'woofunnels-order-bump' ),
				'text'              => __( 'The current style settings would be changed as per new skin.', 'woofunnels-order-bump' ),
				'confirmButtonText' => __( 'Yes, change the skin!', 'woofunnels-order-bump' ),
			],
			'border_type'            => [
				[
					'id'   => 'solid',
					'name' => __( 'Solid', 'woofunnels-order-bump' ),
				],
				[
					'id'   => 'dotted',
					'name' => __( 'Dotted', 'woofunnels-order-bump' ),
				],
				[
					'id'   => 'dashed',
					'name' => __( 'Dashed', 'woofunnels-order-bump' ),
				],
			],
			'yes_no_drop_down'       => [
				[
					'id'   => '0',
					'name' => __( 'No', 'woofunnels-order-bump' ),
				],
				[
					'id'   => '1',
					'name' => __( 'Yes', 'woofunnels-order-bump' ),
				],
			],
			'layouts'                => [
				[
					'id'   => 'layout-1',
					'name' => __( 'Layout 1', 'woofunnels-order-bump' ),
				],
				[
					'id'   => 'layout-2',
					'name' => __( 'Layout 2', 'woofunnels-order-bump' ),
				],
			],
			'default_layout'         => 'layout-1',
		];

		return $data;
	}

	public static function get_bump_position( $without_key = false ) {

		$positions = [
			'woocommerce_checkout_order_review_above_order_summary'   => [
				'name'     => __( 'Above The Order Summary', 'woofunnels-order-bump' ),
				'hook'     => 'woocommerce_checkout_order_review',
				'priority' => 9,
				'id'       => 'woocommerce_checkout_order_review_above_order_summary',
			],
			'woocommerce_checkout_order_review_below_order_summary'   => [
				'name'     => __( 'Below The Order Summary', 'woofunnels-order-bump' ),
				'hook'     => 'woocommerce_checkout_order_review',
				'priority' => 11,
				'id'       => 'woocommerce_checkout_order_review_below_order_summary',
			],
			'woocommerce_checkout_order_review_above_payment_gateway' => [
				'name'     => __( 'Above The Payment Gateways', 'woofunnels-order-bump' ),
				'hook'     => 'woocommerce_checkout_order_review',
				'priority' => 19,
				'id'       => 'woocommerce_checkout_order_review_above_payment_gateway',
			],
			'woocommerce_checkout_order_review_below_payment_gateway' => [
				'name'     => __( 'Below The Payment Gateways', 'woofunnels-order-bump' ),
				'hook'     => 'wfob_below_payment_gateway',
				'priority' => 21,
				'id'       => 'woocommerce_checkout_order_review_below_payment_gateway',
			],

		];

		$positions = apply_filters( 'wfob_bump_positions', $positions );

		if ( false == $without_key ) {
			$positions = array_values( $positions );
		}

		return $positions;
	}

	/**
	 * save product against checkout page id
	 *
	 * @param $wfob_id
	 * @param $product
	 */
	public static function update_page_product( $wfob_id, $product ) {
		if ( empty( $product ) ) {
			$product = [];
		}
		update_post_meta( $wfob_id, '_wfob_selected_products', $product );
	}

	public static function get_bump_rules( $wfob_id, $rule_type = 'basic' ) {
		$data = get_post_meta( $wfob_id, '_wfob_rules', true );

		return apply_filters( 'get_bump_wfob_rules', ( isset( $data[ $rule_type ] ) ) ? $data[ $rule_type ] : array(), $wfob_id, $rule_type );
	}

	public static function get_timezone_difference() {
		$date_obj_utc = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		$diff         = timezone_offset_get( timezone_open( self::wc_timezone_string() ), $date_obj_utc );

		return $diff;
	}

	/**
	 * Function to get timezone string by checking WordPress timezone settings
	 * @return mixed|string|void
	 */
	public static function wc_timezone_string() {

		// if site timezone string exists, return it
		if ( $timezone = get_option( 'timezone_string' ) ) {
			return $timezone;
		}

		// get UTC offset, if it isn't set then return UTC
		if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) ) {
			return 'UTC';
		}

		// get timezone using offset manual
		return self::get_timezone_by_offset( $utc_offset );
	}

	public static function ajax_render_rule_choice( $options ) {

		$defaults = array(
			'group_id'  => 0,
			'rule_id'   => 0,
			'rule_type' => null,
			'condition' => null,
			'operator'  => null,
		);
		$is_ajax  = false;

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'wfob_change_rule_type' ) {
			$is_ajax = true;
		}
		if ( $is_ajax ) {
			$options = array_merge( $defaults, $_POST );
		} else {
			$options = array_merge( $defaults, $options );
		}
		$rule_object = self::woocommerce_wfob_rule_get_rule_object( $options['rule_type'] );

		if ( ! empty( $rule_object ) ) {
			$values               = $rule_object->get_possible_rule_values();
			$operators            = $rule_object->get_possible_rule_operators();
			$condition_input_type = $rule_object->get_condition_input_type();
			// create operators field
			$operator_args = array(
				'input'   => 'select',
				'name'    => 'wfob_rule[' . $options['rule_category'] . '][' . $options['group_id'] . '][' . $options['rule_id'] . '][operator]',
				'choices' => $operators,
			);

			echo '<td class="operator">';
			if ( ! empty( $operators ) ) {
				wfob_Input_Builder::create_input_field( $operator_args, $options['operator'] );
			} else {
				echo '<input type="hidden" name="' . $operator_args['name'] . '" value="==" />';
			}
			echo '</td>';
			// create values field
			$value_args = array(
				'input'   => $condition_input_type,
				'name'    => 'wfob_rule[' . $options['rule_category'] . '][' . $options['group_id'] . '][' . $options['rule_id'] . '][condition]',
				'choices' => $values,
			);
			echo '<td class="condition">';
			wfob_Input_Builder::create_input_field( $value_args, $options['condition'] );
			echo '</td>';
		}
		// ajax?
		if ( $is_ajax ) {
			die();
		}
	}

	public static function woocommerce_wfob_rule_get_rule_object( $rule_type ) {
		global $woocommerce_wfob_rule_rules;
		if ( isset( $woocommerce_wfob_rule_rules[ $rule_type ] ) ) {
			return $woocommerce_wfob_rule_rules[ $rule_type ];
		}
		$class = 'wfob_Rule_' . $rule_type;
		if ( class_exists( $class ) ) {
			$woocommerce_wfob_rule_rules[ $rule_type ] = new $class;

			return $woocommerce_wfob_rule_rules[ $rule_type ];
		} else {
			return null;
		}
	}

	public static function woocommerce_wfob_rule_get_input_object( $input_type ) {
		global $woocommerce_wfob_rule_inputs;
		if ( isset( $woocommerce_wfob_rule_inputs[ $input_type ] ) ) {
			return $woocommerce_wfob_rule_inputs[ $input_type ];
		}
		$class = 'wfob_Input_' . str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $input_type ) ) );
		if ( class_exists( $class ) ) {
			$woocommerce_wfob_rule_inputs[ $input_type ] = new $class;
		} else {
			$woocommerce_wfob_rule_inputs[ $input_type ] = apply_filters( 'woocommerce_wfob_rule_get_input_object', $input_type );
		}

		return $woocommerce_wfob_rule_inputs[ $input_type ];
	}

	public static function render_rule_choice_template( $options ) {
		// defaults
		$defaults = array(
			'group_id'  => 0,
			'rule_id'   => 0,
			'rule_type' => null,
			'condition' => null,
			'operator'  => null,
			'category'  => 'basic',
		);

		$options     = array_merge( $defaults, $options );
		$rule_object = self::woocommerce_wfob_rule_get_rule_object( $options['rule_type'] );
		if ( is_null( $rule_object ) ) {
			return;
		}
		$values               = $rule_object->get_possible_rule_values();
		$operators            = $rule_object->get_possible_rule_operators();
		$condition_input_type = $rule_object->get_condition_input_type();

		$operator_args = array(
			'input'   => 'select',
			'name'    => 'wfob_rule[' . $options['category'] . '][<%= groupId %>][<%= ruleId %>][operator]',
			'choices' => $operators,
		);
		echo '<td class="operator">';
		if ( ! empty( $operators ) ) {
			wfob_Input_Builder::create_input_field( $operator_args, $options['operator'] );
		} else {
			echo '<input type="hidden" name="' . $operator_args['name'] . '" value="==" />';
		}
		echo '</td>';
		// create values field
		$value_args = array(
			'input'   => $condition_input_type,
			'name'    => 'wfob_rule[basic][<%= groupId %>][<%= ruleId %>][condition]',
			'choices' => $values,
		);
		echo '<td class="condition">';
		wfob_Input_Builder::create_input_field( $value_args, $options['condition'] );
		echo '</td>';
	}

	public static function update_bump_rules( $wfob_id, $data = '' ) {

		update_post_meta( $wfob_id, '_wfob_rules', $data );
		update_post_meta( $wfob_id, '_wfob_is_rules_saved', 'yes' );
	}

	public static function update_bump_time( $bump_id ) {
		$my_post = array(
			'ID' => $bump_id,
		);

		wp_update_post( $my_post );
	}

	public static function update_design_data( $wfob_id, $data = [] ) {
		update_post_meta( $wfob_id, '_wfob_design_data', $data );
	}

	public static function update_setting_data( $wfob_id, $settings = [] ) {

		update_post_meta( $wfob_id, '_wfob_settings', $settings );
		if ( isset( $settings['priority'] ) ) {
			wp_update_post( array(
				'ID'         => $wfob_id,
				'menu_order' => $settings['priority'],
			) );
		}
	}

	public static function get_setting_data( $wfob_id ) {
		$data = get_post_meta( $wfob_id, '_wfob_settings', true );
		if ( empty( $data ) ) {
			$data = [
				'priority'                  => 0,
				'order_bump_position_hooks' => self::default_bump_position(),
			];
			if ( $wfob_id > 0 ) {
				$post = get_post( $wfob_id );
				if ( ! is_null( $post ) ) {
					$data['priority'] = $post->menu_order;
				}
			}
		}
		if ( ! isset( $data['order_bump_position_hooks'] ) ) {
			$data['order_bump_position_hooks'] = self::default_bump_position();
		}

		return $data;
	}

	public static function default_bump_position() {
		return 'woocommerce_checkout_order_review_below_payment_gateway';
	}

	public static function get_design_data( $wfob_id ) {
		$design_default_model_data = self::get_default_model_data();
		$design_model_data         = self::get_design_data_meta( $wfob_id );
		if ( ! is_array( $design_model_data ) || count( $design_model_data ) == 0 ) {
			$design_model_data           = $design_default_model_data[ self::$design_default_layout ];
			$design_model_data['layout'] = self::$design_default_layout;
		}
		$data['design_default_model_data'] = $design_default_model_data;
		$data['model']                     = $design_model_data;
		$data                              = self::get_design_product_fields( $wfob_id, $data );

		return $data;
	}

	public static function get_default_model_data() {
		self::$design_default_model_data['layout_1'] = array(
			'heading_background'                 => '#ffff99',
			'heading_hover_background'           => '#ffff56',
			'heading_box_padding'                => '10',
			'heading_color'                      => '#009900',
			'heading_hover_color'                => '#7AB061',
			'error_color'                        => '#E20707',
			'heading_font_size'                  => '16',
			'header_enable_pointing_arrow'       => 'true',
			'point_animation'                    => '1',
			'price_box_padding'                  => '10',
			'price_font_size'                    => '14',
			'price_background'                   => '#F2E363',
			'price_color'                        => '#333333',
			'enable_price'                       => '1',
			'price_sale_color'                   => '#333333',
			'content_box_padding'                => '10',
			'content_color'                      => '#2F2F2F',
			'content_variation_link_color'       => '#E20707',
			'content_variation_link_hover_color' => '#e15353',
			'content_font_size'                  => '13',
			'enable_featured_image_border'       => 'true',
			'featured_image_border_width'        => '1',
			'featured_image_border_style'        => 'solid',
			'featured_image_border_color'        => '#ECECEC',
			'enable_box_border'                  => 'true',
			'border_width'                       => '3',
			'border_style'                       => 'dashed',
			'border_color'                       => '#9e9e9e',
			'bump_max_width'                     => '',
			'box_background'                     => '#FFFFFF',
			'box_padding'                        => 1,

			'layout' => 'layout_1',

			'product_title'          => __( "<span style='color:#ff0000'>Yes!</span> Add ", 'woofunnels-order-bump' ) . '{{product_name}}' . __( ' to my order', 'woofunnels-order-bump' ),
			'product_featured_image' => true,
			'product_description'    => 'Lorem Ipsum Layout 1',

		);
		self::$design_default_model_data['layout_2'] = array(
			'heading_background'                 => '#1d96f3',
			'heading_hover_background'           => '#38a9ff',
			'heading_box_padding'                => '10',
			'heading_color'                      => '#ffffff',
			'heading_hover_color'                => '#ffffff',
			'heading_font_size'                  => '16',
			'header_enable_pointing_arrow'       => 'true',
			'point_animation'                    => '1',
			'price_box_padding'                  => '10',
			'price_font_size'                    => '14',
			'price_background'                   => '#117ed1',
			'price_color'                        => '#ffffff',
			'enable_price'                       => '1',
			'price_sale_color'                   => '#ffffff',
			'content_box_padding'                => '10',
			'content_color'                      => '#2F2F2F',
			'content_variation_link_color'       => '#e15353',
			'content_variation_link_hover_color' => '#E20707',
			'content_font_size'                  => '13',
			'enable_featured_image_border'       => 'true',
			'featured_image_border_width'        => '1',
			'featured_image_border_style'        => 'solid',
			'featured_image_border_color'        => '#ECECEC',
			'enable_box_border'                  => 'true',
			'border_width'                       => '1',
			'border_style'                       => 'solid',
			'border_color'                       => '#cccccc',
			'bump_max_width'                     => '',
			'box_background'                     => '#FFFFFF',
			'box_padding'                        => 0,

			'layout'      => 'layout_2',
			'error_color' => '#E20707',

			'product_title'          => __( "<span style='color:#ff0000'>Yes!</span> Add ", 'woofunnels-order-bump' ) . '{{product_name}}' . __( ' to my order', 'woofunnels-order-bump' ),
			'product_featured_image' => true,
			'product_description'    => 'Lorem Ipsum Layout 2',

		);
		self::$design_default_model_data['layout_3'] = array(
			'heading_font_size'   => '13',
			'heading_color'       => '#009900',
			'heading_hover_color' => '#009900',
			'heading_box_padding' => '0',

			'sub_heading_font_size'   => '17',
			'sub_heading_color'       => '#444444',
			'sub_heading_hover_color' => '#444444',

			'add_button_padding'           => '6',
			'add_button_width'             => '100',
			'add_button_font_size'         => '15',
			'add_button_enable_box_border' => 'true',
			'add_button_border_width'      => '1',
			'add_button_border_style'      => 'solid',
			'add_button_border_color'      => '#c4c4c4',

			'add_button_color'          => '#5f5d5d',
			'add_button_hover_color'    => '#5f5d5d',
			'add_button_bg_color'       => '#f2f2f2',
			'add_button_hover_bg_color' => '#efefef',

			'added_button_color'    => '#dcdcdc',
			'added_button_bg_color' => '#f7f7f7',

			'remove_button_color'    => '#ffffff',
			'remove_button_bg_color' => '#e43b2c',

			'enable_price'     => '0',
			'price_font_size'  => '14',
			'price_color'      => '#222222',
			'price_sale_color' => '#222222',

			'sub_content_font_size' => '14',
			'sub_content_color'     => '#000000',

			'content_box_padding'                => '0',
			'content_font_size'                  => '14',
			'content_color'                      => '#565e66',
			'content_variation_link_color'       => '#e15353',
			'content_variation_link_hover_color' => '#E20707',

			'enable_featured_image_border' => 'false',
			'featured_image_border_width'  => '1',
			'featured_image_border_style'  => 'solid',
			'featured_image_border_color'  => '#f2f2f2',

			'box_padding'          => 20,
			'bump_max_width'       => '',
			'box_background'       => '#ffffff',
			'box_background_hover' => '#ffffff',
			'enable_box_border'    => 'true',
			'border_width'         => '2',
			'border_style'         => 'dashed',
			'border_color'         => '#bfbfbf',

			'layout'      => 'layout_3',
			'error_color' => '#E20707',

			'product_title'              => __( 'Exclusive Offer', 'woofunnels-order-bump' ),
			'product_small_title'        => __( 'Add {{product_name}} for just {{price}}', 'woofunnels-order-bump' ),
			'product_featured_image'     => true,
			'product_description'        => __( 'The long description can come here', 'woofunnels-order-bump' ),
			'product_small_description'  => __( 'Click ADD to include in your order. Aperiam consecttur quisquam. Lorem Ipsum is simply dummy text. {{more}}', 'woofunnels-order-bump' ),
			'product_add_button_text'    => __( 'ADD', 'woofunnels-order-bump' ),
			'product_added_button_text'  => __( 'ADDED', 'woofunnels-order-bump' ),
			'product_remove_button_text' => __( 'REMOVE', 'woofunnels-order-bump' ),
			'product_read_more'          => __( ' more...', 'woofunnels-order-bump' ),
		);
		self::$design_default_model_data['layout_4'] = array(
			'heading_font_size'       => '13',
			'heading_color'           => '#009900',
			'heading_hover_color'     => '#009900',
			'sub_heading_font_size'   => '17',
			'sub_heading_color'       => '#444444',
			'sub_heading_hover_color' => '#444444',

			'add_button_padding'   => '6',
			'add_button_width'     => '100',
			'add_button_font_size' => '15',

			'add_button_enable_box_border'       => 'true',
			'add_button_border_width'            => '1',
			'add_button_border_style'            => 'solid',
			'add_button_border_color'            => '#c4c4c4',
			'add_button_color'                   => '#5f5d5d',
			'add_button_hover_color'             => '#5f5d5d',
			'add_button_bg_color'                => '#f2f2f2',
			'add_button_hover_bg_color'          => '#efefef',
			'added_button_color'                 => '#dcdcdc',
			'added_button_bg_color'              => '#f7f7f7',
			'remove_button_color'                => '#ffffff',
			'remove_button_bg_color'             => '#e43b2c',
			'enable_price'                       => '1',
			'price_font_size'                    => '14',
			'price_color'                        => '#222222',
			'price_sale_color'                   => '#222222',
			'sub_content_font_size'              => '14',
			'sub_content_color'                  => '#000000',
			'content_box_padding'                => '0',
			'content_font_size'                  => '14',
			'content_color'                      => '#565e66',
			'content_variation_link_color'       => '#e15353',
			'content_variation_link_hover_color' => '#E20707',
			'enable_featured_image_border'       => 'false',
			'featured_image_border_width'        => '1',
			'featured_image_border_style'        => 'solid',
			'featured_image_border_color'        => '#f2f2f2',
			'box_padding'                        => 20,
			'bump_max_width'                     => '',
			'box_background'                     => '#ffffff',
			'box_background_hover'               => '#ffffff',
			'enable_box_border'                  => 'true',
			'border_width'                       => '2',
			'border_style'                       => 'dashed',
			'border_color'                       => '#bfbfbf',
			'layout'                             => 'layout_4',
			'product_title'                      => __( "<span style='color:#ff0000'>Yes!</span> Add ", 'woofunnels-order-bump' ) . '{{product_name}}' . __( ' to my order', 'woofunnels-order-bump' ),
			'product_small_title'                => '',
			'product_featured_image'             => true,
			'product_description'                => __( 'The long description can come here', 'woofunnels-order-bump' ),
			'product_small_description'          => __( "<span><strong style='color: #008000;'>Exclusive Offer:</strong></span> Aperiam consecttur quisquam Aperiam consectetur. Lorem Ipsum is simply dummy text of the printing and typesetting industry. {{more}} ", 'woofunnels-order-bump' ),
			'product_add_button_text'            => __( 'ADD', 'woofunnels-order-bump' ),
			'product_added_button_text'          => __( 'ADDED', 'woofunnels-order-bump' ),
			'product_remove_button_text'         => __( 'REMOVE', 'woofunnels-order-bump' ),
			'product_read_more'                  => __( ' more...', 'woofunnels-order-bump' ),
		);
		self::$design_default_model_data             = apply_filters( 'wfob_default_color_scheme', self::$design_default_model_data );

		return self::$design_default_model_data;
	}

	public static function get_design_data_meta( $wfob_id ) {
		return self::get_post_meta_data( $wfob_id, '_wfob_design_data' );
	}

	public static function get_post_meta_data( $item_id, $meta_key = '', $force = false ) {

		if ( empty( $item_id ) ) {
			return array();
		}
		$wfob_cache_obj     = WooFunnels_Cache::get_instance();
		$wfob_transient_obj = WooFunnels_Transient::get_instance();

		$cache_key = 'wfob_post_meta_' . $item_id;

		/** When force enabled */

		if ( true === $force ) {
			$post_meta = get_post_meta( $item_id );
			$post_meta = self::parsed_query_results( $post_meta );
			$wfob_transient_obj->set_transient( $cache_key, $post_meta, DAY_IN_SECONDS, WFOB_SLUG );
			$wfob_cache_obj->set_cache( $cache_key, $post_meta, WFOB_SLUG );
		} else {
			/**
			 * Setting xl cache and transient for Free gift meta
			 */
			$cache_data = $wfob_cache_obj->get_cache( $cache_key, WFOB_SLUG );

			if ( false !== $cache_data ) {
				$post_meta = $cache_data;
			} else {
				$transient_data = $wfob_transient_obj->get_transient( $cache_key, WFOB_SLUG );
				if ( false !== $transient_data ) {
					$post_meta = $transient_data;
				} else {
					$post_meta = get_post_meta( $item_id );
					$post_meta = self::parsed_query_results( $post_meta );
					$wfob_transient_obj->set_transient( $cache_key, $post_meta, DAY_IN_SECONDS, WFOB_SLUG );
				}
				$wfob_cache_obj->set_cache( $cache_key, $post_meta, WFOB_SLUG );
			}
		}

		$fields = array();
		if ( $post_meta && is_array( $post_meta ) && count( $post_meta ) > 0 ) {
			foreach ( $post_meta as $key => $val ) {
				$newKey            = $key;
				$fields[ $newKey ] = $val;
			}
		}

		if ( '' != $meta_key ) {

			return isset( $fields[ $meta_key ] ) ? $fields[ $meta_key ] : '';
		}

		return $fields;
	}

	public static function parsed_query_results( $results ) {
		$parsed_results = array();
		if ( is_array( $results ) && count( $results ) > 0 ) {
			foreach ( $results as $key => $result ) {
				$parsed_results[ $key ] = maybe_unserialize( $result['0'] );
			}
		}

		return $parsed_results;
	}

	public static function get_design_product_fields( $wfob_id, $design_data ) {

		$products     = self::get_prepared_products( $wfob_id );
		$model        = $design_data['model'];
		$layout       = $model['layout'];
		$default_data = $design_data['design_default_model_data'][ $layout ];

		if ( is_array( $products ) && count( $products ) > 0 ) {

			foreach ( $products as $key => $product ) {

				if ( ! isset( $model[ 'product_' . $key . '_title' ] ) && isset( $default_data['product_title'] ) ) {
					$model[ 'product_' . $key . '_title' ] = $default_data['product_title'];
				}
				if ( ! isset( $model[ 'product_' . $key . '_small_description' ] ) && isset( $default_data['product_small_description'] ) ) {
					$model[ 'product_' . $key . '_small_description' ] = $default_data['product_small_description'];
				}
				if ( ! isset( $model[ 'product_' . $key . '_sub_title' ] ) && isset( $default_data['product_small_title'] ) ) {
					$model[ 'product_' . $key . '_sub_title' ] = $default_data['product_small_title'];
				}

				if ( ! isset( $model[ 'product_' . $key . '_add_btn_text' ] ) && isset( $default_data['product_add_button_text'] ) ) {
					$model[ 'product_' . $key . '_add_btn_text' ] = $default_data['product_add_button_text'];
				}
				if ( ! isset( $model[ 'product_' . $key . '_added_btn_text' ] ) && isset( $default_data['product_added_button_text'] ) ) {
					$model[ 'product_' . $key . '_added_btn_text' ] = $default_data['product_added_button_text'];
				}
				if ( ! isset( $model[ 'product_' . $key . '_remove_btn_text' ] ) && isset( $default_data['product_remove_button_text'] ) ) {
					$model[ 'product_' . $key . '_remove_btn_text' ] = $default_data['product_remove_button_text'];
				}

				if ( ! isset( $model[ 'product_' . $key . '_description' ] ) && isset( $default_data['product_description'] ) ) {
					$model[ 'product_' . $key . '_description' ] = $default_data['product_description'];
				}
				if ( ! isset( $model[ 'product_' . $key . '_featured_image' ] ) && isset( $default_data['product_featured_image'] ) ) {
					$model[ 'product_' . $key . '_featured_image' ] = $default_data['product_featured_image'];
				}
				if ( ! isset( $model[ 'product_' . $key . '_read_more' ] ) && isset( $default_data['product_read_more'] ) ) {
					$model[ 'product_' . $key . '_read_more' ] = $default_data['product_read_more'];
				}


			}
			$design_data['model'] = $model;

			return $design_data;
		}

	}

	public static function get_prepared_products( $wfob_id ) {
		$output   = [];
		$products = self::get_bump_products( $wfob_id );

		if ( is_array( $products ) && count( $products ) > 0 ) {
			foreach ( $products as $unique_id => $pdata ) {
				$product = wc_get_product( $pdata['id'] );
				if ( $product instanceof WC_Product ) {
					$image_id     = $product->get_image_id();
					$default      = self::get_default_product_config();
					$default      = array_merge( $default, $pdata );
					$product_type = $product->get_type();
					if ( '' == $default['title'] ) {
						$default['title'] = $product->get_title();
					}

					$product_image_url = '';
					$images            = wp_get_attachment_image_src( $image_id );
					if ( is_array( $images ) && count( $images ) > 0 ) {
						$product_image_url = wp_get_attachment_image_src( $image_id )[0];
					}
					$default['image'] = apply_filters( 'wfob_product_image', $product_image_url, $product );
					if ( '' == $default['image'] ) {
						$default['image'] = WFOB_PLUGIN_URL . '/admin/assets/img/product_default_icon.jpg';
					}
					$default['type'] = $product_type;
					/**
					 * @var $product WC_Product_Variable;
					 */

					$default['is_sold_individually'] = $product->is_sold_individually();
					$default['price_range']          = '';
					if ( in_array( $product_type, WFOB_Common::get_variable_product_type() ) ) {
						$default['variable'] = 'yes';
						$default['price']    = $product->get_price_html();
						$rg_price            = $product->get_regular_price();
						$rprice              = $product->get_price();
						if ( $rprice > 0 && $rg_price != $rprice ) {
							$default['price_range'] = wc_format_sale_price( $rg_price, $rprice );
						}
					} else {
						if ( in_array( $product_type, WFOB_Common::get_variation_product_type() ) ) {
							$default['title'] = $product->get_name();
						}
						$row_data                 = $product->get_data();
						$sale_price               = $row_data['sale_price'];
						$default['price']         = wc_price( $row_data['price'] );
						$default['regular_price'] = wc_price( $row_data['regular_price'] );
						if ( '' != $sale_price ) {
							$default['sale_price'] = wc_price( $sale_price );
						}
					}
					if ( 'variation' === $product_type ) {
						$description = get_the_excerpt( $product->get_parent_id() );
					} else {
						$description = $product->get_short_description();
					}

					$default['description']        = $description;
					$default['stock']              = $product->is_in_stock();
					$resp['product'][ $unique_id ] = $default;
					$output[ $unique_id ]          = $default;
				};
			}
			if ( count( $output ) > 0 ) {
				return $output;
			}
		} else {
			return new stdClass();
		}
	}

	public static function get_bump_products( $wfob_id ) {

		if ( ! is_int( $wfob_id ) ) {
			return [];
		}

		$product = self::get_post_meta_data( $wfob_id, '_wfob_selected_products' );

		if ( ! is_array( $product ) ) {
			return [];
		}

		return $product;

	}

	public static function get_default_product_config() {
		return [
			'title'           => '',
			'discount_type'   => 'percent_discount_sale',
			'discount_amount' => 0,
			'discount_price'  => 0,
			'quantity'        => 1,
		];

	}

	public static function get_variable_product_type() {
		return [ 'variable', 'variable-subscription' ];
	}

	public static function get_variation_product_type() {
		return [ 'variation', 'subscription_variation' ];
	}

	public static function get_global_setting() {
		$data = get_option( '_wfob_global_settings', [
			'css'                      => '',
			'number_bump_per_checkout' => '',
		] );

		return $data;
	}

	public static function load_bump_skin( $product_id, $design = 'layout-1' ) {

		include WFOB_SKIN_DIR . "/{$design}.php";
	}

	/**
	 * Get Product parent id  for both version of woocommerce 2.6 and >3.0
	 *
	 * @param WC_Product $product
	 *
	 * @return integer
	 */
	public static function get_product_parent_id( $product ) {
		$parent_id = 0;

		if ( $product instanceof WC_Product ) {
			$parent_id = wp_get_post_parent_id( $product->get_id() );
			if ( $parent_id == false ) {
				$parent_id = $product->get_id();
			}
		} elseif ( 0 !== $product ) {
			$parent_id = wp_get_post_parent_id( $product );

			if ( $parent_id == false ) {
				$parent_id = (int) $product;
			}
		}

		return $parent_id;

	}

	/**
	 * @param $product WC_Product_Variable;
	 */
	public static function get_default_variation( $product ) {

		if ( $product instanceof WC_Product_Variable ) {
			$var_data = $product->get_data();

			if ( isset( $var_data['default_attributes'] ) && count( $var_data['default_attributes'] ) > 0 ) {
				$attributes = $var_data['default_attributes'];
				$matched_id = self::find_matching_product_variation( $product, $attributes );

				if ( ! is_null( $matched_id ) && $matched_id > 0 ) {
					return self::get_first_variation( $product, $matched_id );
				}

				return self::get_first_variation( $product );

			} else {
				return self::get_first_variation( $product );
			}
		}

		return [];
	}

	/**
	 * Find matching product variation
	 *
	 * @param WC_Product $product
	 * @param array $attributes
	 *
	 * @return int Matching variation ID or 0.
	 */
	public static function find_matching_product_variation( $product, $attributes ) {

		foreach ( $attributes as $key => $value ) {
			if ( strpos( $key, 'attribute_' ) === 0 ) {
				continue;
			}

			unset( $attributes[ $key ] );
			$attributes[ sprintf( 'attribute_%s', $key ) ] = $value;
		}

		if ( class_exists( 'WC_Data_Store' ) ) {

			$data_store = WC_Data_Store::load( 'product' );

			return $data_store->find_matching_product_variation( $product, $attributes );

		} else {

			return $product->get_matching_variation( $attributes );

		}

		return null;
	}

	/**
	 * @param $product WC_Product_Variable;
	 */
	public static function get_first_variation( $product, $vars_id = 0 ) {
		if ( $product instanceof WC_Product_Variable ) {
			$vars = $product->get_available_variations();
			if ( count( $vars ) > 0 ) {
				if ( $vars_id > 0 ) {
					foreach ( $vars as $v ) {
						if ( $vars_id == $v['variation_id'] ) {
							return $v;
						}
					}
				}

				return $vars[0];
			}
		}

		return [];
	}

	public static function get_cart_item_key( $product_key ) {
		$cart = WC()->cart->get_cart_contents();
		if ( count( $cart ) == 0 ) {
			return null;
		}

		foreach ( $cart as $item_key => $item_data ) {
			if ( isset( $item_data['_wfob_product_key'] ) && $product_key === $item_data['_wfob_product_key'] ) {
				return [ $item_key, $item_data ];
			}
		}

		return null;
	}

	/**
	 * Set Product price like regular, sale price on basis of discount
	 *
	 * @param $pro WC_Product
	 * @param $product
	 */
	public static function set_product_price( $pro, $data, $cart_item_key = '' ) {
		if ( ! $pro instanceof WC_Product ) {
			return null;
		}
		$qty           = 1;
		$raw_data      = $pro->get_data();
		$discount_type = trim( $data['discount_type'] );
		$raw_data      = apply_filters( 'wfob_product_raw_data', $raw_data, $pro, $cart_item_key );

		$regular_price   = apply_filters( 'wfob_discount_regular_price_data', $raw_data['regular_price'], $cart_item_key );
		$price           = apply_filters( 'wfob_discount_price_data', $raw_data['price'], $cart_item_key );
		$discount_amount = floatval( apply_filters( 'wfob_discount_amount_data', $data['discount_amount'], $discount_type, $cart_item_key ) );

		$discount_data = [
			'wfob_product_rp'      => $regular_price * $qty,
			'wfob_product_p'       => $price * $qty,
			'wfob_discount_amount' => $discount_amount,
			'wfob_discount_type'   => $discount_type,
		];

		if ( 'fixed_discount_sale' == $discount_type || 'fixed_discount_reg' == $discount_type ) {
			$discount_data['wfob_discount_amount'] = $discount_amount * $qty;
		}

		$new_price = self::calculate_discount( $discount_data );
		if ( ! is_null( $new_price ) ) {
			$pro->set_regular_price( $regular_price * $qty );
			$pro->set_price( $new_price );
			$pro->set_sale_price( $new_price );
		}

		return $pro;
	}

	/**
	 * Calculate product disoucnt using options meta
	 * [wfob_options] => Array
	 * (
	 * [discount_type] => percentage
	 * [discount_amount] => 5
	 * [discount_price] => 0
	 * [quantity] => 1
	 * [id] => 121
	 * [parent_product_id] => 117
	 * [type] => variation
	 * )
	 *
	 * @param $product_price
	 * @param $options
	 *
	 * @return float;
	 */
	public static function calculate_discount( $options ) {
		if ( ! isset( $options['wfob_product_rp'] ) ) {
			return null;
		}

		$discount_type = $options['wfob_discount_type'];
		$reg_price     = floatval( $options['wfob_product_rp'] );
		$price         = floatval( $options['wfob_product_p'] );
		$value         = floatval( $options['wfob_discount_amount'] );

		switch ( $discount_type ) {
			case 'fixed_discount_reg':
				if ( 0 == $value ) {
					$discounted_price = $reg_price;
					break;
				}
				$discounted_price = $reg_price - ( $value );
				break;
			case 'fixed_discount_sale':
				if ( 0 == $value ) {
					$discounted_price = $price;
					break;
				}
				$discounted_price = $price - ( $value );
				break;
			case 'percent_discount_reg':
				if ( 0 == $value ) {
					$discounted_price = $reg_price;
					break;
				}
				$discounted_price = ( $value > 0 ) ? ( $reg_price - ( ( $value / 100 ) * $reg_price ) ) : $reg_price;
				break;
			case 'percent_discount_sale':
				if ( 0 == $value ) {
					$discounted_price = $price;
					break;
				}
				$discounted_price = ( $value > 0 ) ? $price - ( ( $value / 100 ) * $price ) : $price;
				break;
			case 'flat_price':
				$discounted_price = ( $value > 0 ) ? ( $value ) : $price;
				break;
			default:
				$discounted_price = $price;
				break;
		}
		if ( $discounted_price < 0 ) {
			$discounted_price = 0;
		}

		return $discounted_price;
	}

	public static function delete_transient( $post_id = 0 ) {
		$wfob_transient_obj = WooFunnels_Transient::get_instance();
		if ( $post_id > 0 ) {
			$meta_key = 'wfob_post_meta_' . absint( $post_id );
			$wfob_transient_obj->delete_transient( $meta_key, WFOB_SLUG );
		} else {
			$wfob_transient_obj->delete_transient( 'wfob_instances', WFOB_SLUG );
		}
	}

	public static function make_duplicate( $post_id ) {
		if ( $post_id > 0 ) {
			$post = get_post( $post_id );
			if ( ! is_null( $post ) && $post->post_type === self::get_bump_post_type_slug() ) {

				$menu_order  = self::get_highest_menu_order();
				$args        = [
					'post_title'   => $post->post_title . ' - ' . __( 'Copy', 'woofunnels-order-bump' ),
					'post_content' => $post->post_content,
					'menu_order'   => $menu_order + 1,
					'post_type'    => self::get_bump_post_type_slug(),
					'post_status'  => 'draft',
				];
				$new_post_id = wp_insert_post( $args );
				if ( ! is_wp_error( $new_post_id ) ) {
					self::get_duplicate_data( $new_post_id, $post_id );

					return $new_post_id;
				}
			}
		}

		return null;
	}

	public static function get_highest_menu_order() {
		global $wpdb;
		$menu_order = 0;
		$result     = $wpdb->get_results( sprintf( "SELECT menu_order FROM `%s` where `post_type`='%s' ORDER BY `%s`.`menu_order`  DESC LIMIT 1", $wpdb->prefix . 'posts', self::get_bump_post_type_slug(), $wpdb->prefix . 'posts' ), ARRAY_A );
		if ( is_array( $result ) && count( $result ) > 0 ) {
			$menu_order = $result[0]['menu_order'];
		}

		return $menu_order;
	}

	public static function get_duplicate_data( $new_post_id, $post_id ) {
		if ( $new_post_id > 0 && $post_id > 0 ) {
			update_post_meta( $new_post_id, '_wfob_rules', get_post_meta( $post_id, '_wfob_rules', true ) );
			update_post_meta( $new_post_id, '_wfob_is_rules_saved', get_post_meta( $post_id, '_wfob_is_rules_saved', true ) );
			update_post_meta( $new_post_id, '_wfob_selected_products', get_post_meta( $post_id, '_wfob_selected_products', true ) );
			update_post_meta( $new_post_id, '_wfob_design_data', get_post_meta( $post_id, '_wfob_design_data', true ) );
			update_post_meta( $new_post_id, '_wfob_settings', get_post_meta( $post_id, '_wfob_settings', true ) );
			do_action( 'wfob_duplicate_pages', $new_post_id, $post_id );
		}

	}

	/**
	 * get global price data after tax calculation based
	 *
	 * @param $pro
	 * @param $cart_item
	 * @param int $qty
	 *
	 * @return array
	 */
	public static function get_product_price_data( $pro, $price_data, $qty = 1 ) {
		if ( $pro instanceof WC_Product ) {
			$display_type = get_option( 'woocommerce_tax_display_cart' );
			if ( 'incl' == $display_type ) {

				$price_data['regular_org'] = wc_get_price_including_tax( $pro, [
					'qty'   => $qty,
					'price' => $price_data['regular_org'],
				] );
				$price_data['price']       = wc_get_price_including_tax( $pro, [
					'qty'   => $qty,
					'price' => $price_data['price'],
				] );

			} else {
				$price_data['regular_org'] = wc_get_price_excluding_tax( $pro, [
					'qty'   => $qty,
					'price' => $price_data['regular_org'],
				] );
				$price_data['price']       = wc_get_price_excluding_tax( $pro, [
					'qty'   => $qty,
					'price' => $price_data['price'],
				] );
			}

			$price_data['quantity'] = $qty;
		}

		return $price_data;
	}

	/**
	 * get global price data after tax calculation based
	 *
	 * @param $pro
	 * @param $cart_item
	 * @param int $qty
	 *
	 * @return array
	 */
	public static function get_cart_product_price_data( $pro, $cart_item, $qty = 1 ) {
		$price_data = [];
		if ( $pro instanceof WC_Product ) {
			$display_type = get_option( 'woocommerce_tax_display_cart' );
			if ( 'incl' == $display_type ) {
				$price_data['regular_org'] = wc_get_price_including_tax( $pro, [
					'qty'   => $qty,
					'price' => $pro->get_regular_price(),
				] );
				$price_data['price']       = round( $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'], wc_get_price_decimals() );
			} else {
				$price_data['regular_org'] = wc_get_price_excluding_tax( $pro, [
					'qty'   => $qty,
					'price' => $pro->get_regular_price(),
				] );
				$price_data['price']       = round( $cart_item['line_subtotal'], wc_get_price_decimals() );
			}

			$price_data['quantity'] = $qty;
		}

		return $price_data;
	}

	public static function get_subscription_product_type() {
		return [ 'variable-subscription', 'subscription', 'subscription_variation' ];
	}

	/**
	 *
	 * @param $pro WC_Subscriptions_Product
	 * @param $price_data []
	 */
	public static function get_subscription_price( $pro, $price_data, $cart_item_key = '' ) {
		if ( '' !== $cart_item_key ) {
			return $price_data['price'];
		}

		$trial_length = WC_Subscriptions_Product::get_trial_length( $pro );
		$signup_fee   = WC_Subscriptions_Product::get_sign_up_fee( $pro );

		// Product now in free trial and with signup fee
		if ( $trial_length > 0 && $signup_fee > 0 ) {
			return $signup_fee * $price_data['quantity'];
		} elseif ( $trial_length > 0 && $signup_fee == 0 ) {
			return 0;
		} elseif ( $trial_length == 0 && $signup_fee > 0 ) {
			return $price_data['price'] + ( $signup_fee * $price_data['quantity'] );
		} else {
			return $price_data['price'];
		}

	}

	public static function get_signup_fee( $price ) {
		global $wfob_product_switcher_quantity;
		if ( ! is_null( $wfob_product_switcher_quantity ) && $wfob_product_switcher_quantity > 0 ) {
			$price *= $wfob_product_switcher_quantity;
		}

		return $price;
	}

	/**
	 * @param $pro WC_Product_Subscription
	 * @param $product_data
	 * @param $cart_item
	 * @param $cart_item_key
	 *
	 * @return string
	 */

	public static function subscription_product_string( $pro, $product_data, $cart_item, $cart_item_key ) {

		$temp_price = $pro->get_price();
		$temp_price *= ( $product_data['quantity'] > 0 ? $product_data['quantity'] : 1 );
		$temp_data  = [
			'price' => wc_price( $temp_price ),
		];
		global $wfob_product_switcher_quantity;
		if ( '' !== $cart_item_key ) {
			$wfob_product_switcher_quantity = $cart_item['quantity'];
		} else {
			$wfob_product_switcher_quantity = $product_data['quantity'];

		}

		add_filter( 'woocommerce_subscriptions_product_sign_up_fee', 'WFOB_Common::get_signup_fee' );
		$final_price = WC_Subscriptions_Product::get_price_string( $pro, $temp_data );
		remove_filter( 'woocommerce_subscriptions_product_sign_up_fee', 'WFOB_Common::get_signup_fee' );
		unset( $wfob_product_switcher_quantity );


		return "<span class='wfob_subs_wrap'>" . $final_price . "</span>";
	}

	public static function decode_merge_tags( $content, $price_data, $pro = false, $product_data = [], $cart_item = [], $cart_item_key = '', $product_key = '', $design_data = [] ) {

		return WFOB_Product_Switcher_Merge_Tags::maybe_parse_merge_tags( $content, $price_data, $pro, $product_data, $cart_item, $cart_item_key, $product_key, $design_data );
	}

	public static function add_bump_to_order( $wfob_id, $product_key ) {
		$products         = self::get_bump_products( absint( $wfob_id ) );
		$product_design   = self::get_design_data_meta( $wfob_id );
		$session_products = WC()->session->get( 'wfob_added_bump_product', [] );
		$product          = $products[ $product_key ];
		$product_id       = absint( $product['id'] );
		$quantity         = absint( $product['quantity'] );
		$custom_data      = [];
		$variation_id     = 0;

		if ( $product['parent_product_id'] && $product['parent_product_id'] > 0 ) {
			$product_id   = absint( $product['parent_product_id'] );
			$variation_id = absint( $product['id'] );
		}

		$product_obj = self::wc_get_product( $product_id );
		if ( ! $product_obj instanceof WC_Product ) {
			return false;
		}

		if ( ! $product_obj->is_purchasable() ) {
			return false;
		}
		if ( $variation_id > 0 ) {
			// if variation_id found then we fetch attributes of variation from below function
			$is_found_variation = self::get_first_variation( $product_obj, $variation_id );
			$attributes         = $is_found_variation['attributes'];

		} else {
			if ( isset( $product['variable'] ) ) {
				$variation_id = absint( $product['default_variation'] );
				$attributes   = $product['default_variation_attr'];
			}
		}
		if ( $variation_id > 0 ) {
			$custom_data['wfob_variable_attributes'] = $attributes;
			$product_obj                             = wc_get_product( $variation_id );
		}

		$stock_status = self::check_manage_stock( $product_obj, $quantity );

		if ( false == $stock_status ) {
			return false;
		}

		if ( isset( $product_design["product_{$product_key}_title"] ) && '' != $product_design["product_{$product_key}_title"] ) {
			$product['title'] = $product_design["product_{$product_key}_title"];
		};

		$custom_data['_wfob_product']             = true;
		$custom_data['_wfob_product_key']         = $product_key;
		$custom_data['_wfob_options']             = $product;
		$custom_data['_wfob_options']['_wfob_id'] = $wfob_id;
		try {
			do_action( 'wfob_before_add_to_cart', $_REQUEST );
			$success = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $attributes, $custom_data );
			if ( '' !== $success && false !== $success ) {
				$session_products[ $wfob_id ][ $product_key ] = true;
				$session_products[ $wfob_id ]['cart_key']     = $success;
				WC()->session->set( 'wfob_added_bump_product', $session_products );
			}
			$_REQUEST['cart_key'] = $success;
			do_action( 'wfob_after_add_to_cart', $_REQUEST );
		} catch ( Exception $e ) {

		}

		return true;
	}

	public static function wc_get_product( $product_id, $unique_key = '' ) {

		if ( empty( $unique_key ) ) {
			$unique_key = uniqid( 'wfob_' );
		}

		if ( isset( self::$product_data[ $unique_key ][ $product_id ] ) ) {
			return self::$product_data[ $unique_key ][ $product_id ];
		}
		self::$product_data[ $unique_key ][ $product_id ] = wc_get_product( $product_id );

		return self::$product_data[ $unique_key ][ $product_id ];
	}

	public static function check_manage_stock( $product_obj, $new_qty ) {

		if ( ! $product_obj instanceof WC_Product ) {
			return false;
		}
		if ( $new_qty < 1 ) {
			return false;
		}

		// when stock management is on in product
		if ( true == $product_obj->managing_stock() ) {
			$available_qty = $product_obj->get_stock_quantity();
			if ( $available_qty < $new_qty ) {
				if ( ! in_array( $product_obj->get_backorders(), [ 'yes', 'notify' ] ) ) {
					return false;
				}
			}
		} else {
			// for non stock managerment
			return $product_obj->is_in_stock();
		}

		return true;
	}

	/**
	 * Detect builder page is open
	 * @return bool
	 */

	public static function is_builder() {
		if ( is_admin() && isset( $_GET['page'] ) && 'wfob' === $_GET['page'] ) {
			return true;
		}

		return false;

	}

	public static function remove_actions( $hook, $cls, $function = '' ) {

		global $wp_filter;
		$object = null;
		if ( class_exists( $cls ) && isset( $wp_filter[ $hook ] ) && ( $wp_filter[ $hook ] instanceof WP_Hook ) ) {
			$hooks = $wp_filter[ $hook ]->callbacks;
			foreach ( $hooks as $priority => $refrence ) {
				if ( is_array( $refrence ) && count( $refrence ) > 0 ) {
					foreach ( $refrence as $index => $calls ) {
						if ( isset( $calls['function'] ) && is_array( $calls['function'] ) && count( $calls['function'] ) > 0 ) {
							if ( is_object( $calls['function'][0] ) ) {
								$cls_name = get_class( $calls['function'][0] );
								if ( $cls_name == $cls && $calls['function'][1] == $function ) {
									$object = $calls['function'][0];
									unset( $wp_filter[ $hook ]->callbacks[ $priority ][ $index ] );
								}
							} elseif ( $index == $cls . '::' . $function ) {
								// For Static Classess
								$object = $cls;
								unset( $wp_filter[ $hook ]->callbacks[ $priority ][ $cls . '::' . $function ] );
							}
						}
					}
				}
			}
		}

		add_action( 'woocommerce_checkout_update_order_review', function () {
			if ( isset( $_REQUEST['wc-ajax'] ) && in_array( $_REQUEST['wc-ajax'], [
					'wfob_add_order_bump',
					'wfob_remove_order_bump'
				] ) ) {
				WFOB_Common::remove_actions( 'woocommerce_checkout_update_order_review', 'WDP_Frontend', 'woocommerce_checkout_update_order_review' );
			}
		}, 0 );

		return $object;

	}

	/**
	 * @param $product_obj WC_Product
	 * @param $cart_item []
	 */
	public static function get_pixel_item( $product_obj, $cart_item ) {
		$item_added_data['pixel'] = [
			'value'        => isset( $cart_item['line_subtotal'] ) ? $cart_item['line_subtotal'] : 0,
			'content_name' => $product_obj->get_title(),
			'content_type' => 'product',
			'currency'     => get_woocommerce_currency(),
			'content_ids'  => $product_obj->get_id(),
		];

		return apply_filters( 'wfob_item_added_to_cart', $item_added_data, $product_obj, $cart_item );
	}

	public static function analytics_item( $product_obj, $cart_item ) {
		if ( ! class_exists( 'WFACP_Analytics' ) ) {
			return self::get_pixel_item( $product_obj, $cart_item );
		}

		$final    = [];
		$services = WFACP_Analytics::get_available_service();
		foreach ( $services as $service => $analytic ) {
			/**
			 * @var $analytic WFACP_Analytics;
			 */
			$a_data            = $analytic->get_item( $product_obj, $cart_item );
			$final[ $service ] = $a_data;
		}

		return apply_filters( 'wfob_item_added_to_cart', $final, $product_obj, $cart_item );
	}


	public static function is_cart_is_virtual() {
		$cart_items      = WC()->cart->get_cart_contents();
		$virtual_product = 0;
		if ( ! empty( $cart_items ) ) {
			foreach ( $cart_items as $key => $cart_item ) {
				$pro = $cart_item['data'];
				if ( $pro instanceof WC_Product && $pro->is_virtual() ) {
					$virtual_product ++;
				}
			}
		}
		if ( count( $cart_items ) == $virtual_product ) {
			return true;
		}

		return false;
	}

	public static function remove_bump_from_cart( $post ) {
		$resp = [];
		if ( isset( $post['cart_key'] ) && '' !== $post['cart_key'] ) {
			do_action( 'wfob_before_remove_bump_from_cart', $post );
			$cart_key = trim( $post['cart_key'] );
			$item     = WC()->cart->get_cart_item( $cart_key );
			if ( isset( $item['_wfob_product_key'] ) ) {
				$wfob_id          = $post['wfob_id'];
				$item_key         = $item['_wfob_product_key'];
				$session_products = WC()->session->get( 'wfob_added_bump_product', [] );
				if ( isset( $session_products[ $wfob_id ] ) && $session_products[ $wfob_id ] [ $item_key ] ) {
					unset( $session_products[ $wfob_id ] [ $item_key ] );
					WC()->session->set( 'wfob_added_bump_product', $session_products );
				}
			}
			WC()->cart->remove_cart_item( $cart_key );
			do_action( 'wfob_after_remove_bump_from_cart', $post );
			$resp['status'] = true;

		}

		return $resp;
	}

	public static function get_fragments() {

		do_action( 'wfob_order_bump_fragments', $_REQUEST );

		// Get order review fragment
		ob_start();
		woocommerce_order_review();
		$woocommerce_order_review = ob_get_clean();


		return apply_filters( 'woocommerce_update_order_review_fragments', array(
			'.woocommerce-checkout-review-order-table' => $woocommerce_order_review,

		) );
	}

	/**
	 * get pixel initiated pixel checkout data
	 * @return array
	 */

	public static function pixel_checkout_data() {
		$output = new stdClass();
		if ( function_exists( 'WC' ) ) {
			$subtotal = WC()->cart->get_subtotal();
			$contents = WC()->cart->get_cart_contents();
			if ( count( $contents ) > 0 ) {
				$output = [];
				foreach ( $contents as $item_key => $item ) {
					if ( $item['data'] instanceof WC_Product ) {
						$output['content_ids'][] = $item['data']->get_id();
					}
				}
				$output['currency']     = get_woocommerce_currency();
				$output['value']        = $subtotal;
				$output['content_name'] = __( 'Order Bump', 'woofunnels-aero-checkout' );
				$output['num_ids']      = count( $output['content_ids'] );
			}
		}

		$final['pixel'] = $output;

		return apply_filters( 'wfob_checkout_data', $final, WC()->cart );
	}


	/**
	 * get pixel initiated pixel checkout data
	 * @return array
	 */

	public static function analytics_checkout_data() {
		if ( ! class_exists( 'WFACP_Analytics' ) ) {
			return self::pixel_checkout_data();
		}

		$final    = [];
		$services = WFACP_Analytics::get_available_service();
		foreach ( $services as $service => $analytic ) {
			/**
			 * @var $analytic WFACP_Analytics;
			 */
			$final[ $service ] = $analytic->get_checkout_data();

		}

		return apply_filters( 'wfob_checkout_data', $final, WC()->cart );
	}

	/**
	 * Filter callback for finding variation attributes.
	 *
	 * @param WC_Product_Attribute $attribute Product attribute.
	 *
	 * @return bool
	 */
	public static function filter_variation_attributes( $attribute ) {
		return true === $attribute->get_variation();
	}


	public static final function display_not_selected_attribute( $product_data, $pro ) {
		return apply_filters( 'wfob_display_not_selected_attribute', false, $product_data, $pro );
	}

	public static function wc_dropdown_variation_attribute_options( $args = array() ) {
		$args = wp_parse_args( apply_filters( 'woocommerce_wfob_dropdown_variation_attribute_options_args', $args ), array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'woocommerce' ),
		) );

		// Get selected value.
		if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
			$selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
			$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( urldecode( wp_unslash( $_REQUEST[ $selected_key ] ) ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
		}

		$options               = $args['options'];
		$product               = $args['product'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
		$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$class                 = $args['class'];
		$show_option_none      = (bool) $args['show_option_none'];
		$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}

		$html = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
		$html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

		if ( ! empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms( $product->get_id(), $attribute, array(
					'fields' => 'all',
				) );

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options, true ) ) {
						$html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
					}
				}
			} else {
				foreach ( $options as $option ) {
					// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
					$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
					$html     .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
				}
			}
		}

		$html .= '</select>';

		echo apply_filters( 'woocommerce_wfob_dropdown_variation_attribute_options_html', $html, $args ); // WPCS: XSS ok.
	}


}
