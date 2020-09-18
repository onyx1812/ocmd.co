<?php
global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}

$user_ip = apply_filters( 'wpb_get_ip', $ip );

if( !isset($_GET["test"]) ):

	$page_ID = get_the_ID();

	$table_analytics = $wpdb->prefix . "analytics";
	$sql = "CREATE TABLE {$table_analytics} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		date DATETIME NOT NULL,
		ip text NOT NULL,
		last_activity text NOT NULL,
		count_activity INT DEFAULT 1,
		page_id INT DEFAULT 0,
		UNIQUE KEY id (id)
	) {$charset_collate};";
	dbDelta($sql);

	$table_analytics_pages = $wpdb->prefix . "analytics_pages";
	$sql = "CREATE TABLE {$table_analytics_pages} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		page_id INT DEFAULT 0,
		page_name text NOT NULL,
		page_counter INT DEFAULT 0,
		page_activity INT DEFAULT 0,
		page_orders INT DEFAULT 0,
		UNIQUE KEY id (id)
	) {$charset_collate};";
	dbDelta($sql);

	$date = date('Y-m-d H:i:s');
	$table_analytics = $wpdb->prefix . "analytics";
	$insert_ip = true;
	$datas = $wpdb->get_results("SELECT * FROM $table_analytics");
	foreach( $datas as $data ){
		if( $data->ip == $user_ip ){
			$cur_date = date('Y-m-d H:i:s');
			$count_activity = $data->count_activity + 1;
			$wpdb->update(
				$table_analytics,
				array(
					'last_activity' => $cur_date,
					'count_activity' => $count_activity,
				),
				array( 'id' => $data->id )
			);
			$insert_ip = false;
		}
	}

	if($insert_ip){
		$wpdb->insert(
			$table_analytics,
			array(
				'date' 			=> $date,
				'ip' 				=> $user_ip,
				'last_activity' => $date,
				'page_id' 		=> $page_ID
			)
		);
	}

	// Page activity
	$pages = $wpdb->get_results(" SELECT * FROM $table_analytics_pages WHERE page_id = $page_ID ");
	$page_activity = $pages[0]->page_activity + 1;
	$wpdb->update(
		$table_analytics_pages,
		array(
			'page_activity' => $page_activity,
		),
		array( 'id' => $pages[0]->id )
	);

	// Page Counter
	$users = $wpdb->get_results(" SELECT page_id FROM $table_analytics");
	$page_visitors = 0;
	foreach ($users as $user) {
		if($page_ID == $user->page_id){
			++$page_visitors;
		}
	}

	if( isset($_GET['pid']) ){
		global $wpdb;
		$page_order_id = $_GET['pid'];
		$page_order = $wpdb->get_results(" SELECT * FROM $table_analytics_pages WHERE page_id = '$page_order_id' ");
		$page_orders = $page_order[0]->page_orders + 1;
		$wpdb->update(
			$table_analytics_pages,
			array(
				'page_orders' => $page_orders,
			),
			array( 'id' => $page_order[0]->id )
		);
	}

	$page_analitics = $wpdb->get_results(" SELECT * FROM $table_analytics_pages WHERE page_id = '$page_ID' ");

	echo '<script>
		console.log("Page visitors - '.$page_visitors.'");
		console.log("Page activity - '.$page_analitics[0]->page_activity.'");
		console.log("Page successed orders - '.$page_analitics[0]->page_orders.'");
	</script>';

endif;