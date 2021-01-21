<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://myworks.design/software/wordpress/woocommerce/myworks-wc-qbo-sync
 * @since      1.0.0
 *
 * @package    MyWorks_WC_QBO_Sync
 * @subpackage MyWorks_WC_QBO_Sync/public/partials
 */
 
 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$page_url = get_site_url(null,'/my-account/salesreceipts/');
$pdf_url = get_site_url(null,'index.php?mw_qbo_sync_public_get_user_invoice_pdf=1&id=');

global $wpdb;
global $MWQS_OF;
global $MSQS_QL;

$wc_user_id = (int) get_current_user_id();
$qb_customer_id = (int) $MSQS_QL->get_field_by_val($wpdb->prefix.'mw_wc_qbo_sync_customer_pairs','qbo_customerid','wc_customerid',$wc_user_id);
$qbo_invoice_list = array();

$qrts = $MSQS_QL->get_option('mw_wc_qbo_sync_wam_mng_inv_qrts');
if(empty($qrts)){$qrts = 'SalesReceipt';}

if($qb_customer_id > 0 && ($qrts == 'SalesReceipt' || $qrts == 'Invoice_SalesReceipt') && !$MSQS_QL->is_plg_lc_p_l() && $MSQS_QL->option_checked('mw_wc_qbo_sync_wam_mng_inv_ed')){
	if($MSQS_QL->option_checked('mw_wc_qbo_sync_pause_up_qbo_conection')){
		$MSQS_QL = new MyWorks_WC_QBO_Sync_QBO_Lib(true);	
	}	
	
	$MSQS_QL->set_per_page_from_url();
	$items_per_page = $MSQS_QL->get_item_per_page();

	$MSQS_QL->set_and_get('invoice_manage_search');
	$invoice_manage_search = $MSQS_QL->get_session_val('invoice_manage_search');

	$MSQS_QL->set_and_get('invoice_manage_date_from');
	$invoice_manage_date_from = $MSQS_QL->get_session_val('invoice_manage_date_from');

	$MSQS_QL->set_and_get('invoice_manage_date_to');
	$invoice_manage_date_to = $MSQS_QL->get_session_val('invoice_manage_date_to');

	$total_records = $MSQS_QL->count_qbo_salesreceipt_list($invoice_manage_search,$invoice_manage_date_from,$invoice_manage_date_to,$qb_customer_id);	
	
	$offset = $MSQS_QL->get_offset($MSQS_QL->get_page_var(true),$items_per_page,true);
	$pagination_links = $MSQS_QL->get_paginate_links($total_records,$items_per_page,true,$MSQS_QL->get_page_var(true));

	$qbo_salesreceipt_list = $MSQS_QL->get_qbo_salesreceipt_list($invoice_manage_search," STARTPOSITION $offset MaxResults $items_per_page",$invoice_manage_date_from,$invoice_manage_date_to,$qb_customer_id);
	
	//$order_statuses = wc_get_order_statuses();
	
	//$wc_currency = get_woocommerce_currency();
	//$wc_currency_symbol = get_woocommerce_currency_symbol($wc_currency);
	//$MSQS_QL->_p($qbo_salesreceipt_list);
}

?>

<!DOCTYPE html>
<html>
<head>

	<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.js"></script>
	<script type='text/javascript' src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
	<link rel='stylesheet' href='<?php echo dirname(plugin_dir_url( __FILE__ ));?>/css/myworks-wc-qbo-sync-public.css' type='text/css' media='all' />
</head>
<body>
	<div class="invoice_container">
	  <h2>Manage Sales Receipts</h2>   
	  <?php if(is_array($qbo_salesreceipt_list) && count($qbo_salesreceipt_list)):?>
	  <table class="invoice_table">
	    <thead>
	      <tr>
	        <th>#</th>
	        <th>Date</th>
	        <th>Total</th>			
	        <th>Actions</th>
	      </tr>
	    </thead>
	    <tbody>
		<?php 
		foreach($qbo_salesreceipt_list as $SalesReceipt):
		$qbo_id = $MSQS_QL->qbo_clear_braces($SalesReceipt->getId());
		
		/**/
		$qbo_href = '';		
		
		$qbo_inv_currency = str_replace(array('{','-','}'),array('','',''),$SalesReceipt->getCurrencyRef());
		$qbo_inv_currency_symbol = get_woocommerce_currency_symbol($qbo_inv_currency);
		
		
		$TotalAmt = (float) $SalesReceipt->getTotalAmt();
		
		?>
	      <tr>
	        <td><?php echo $SalesReceipt->getDocNumber();?></td>
	        <td><?php echo $SalesReceipt->getTxnDate();?></td>
	        <td><?php echo $qbo_inv_currency_symbol;?><?php echo $SalesReceipt->getTotalAmt();?></td>			
	        <td>
				<a target="_blank" href="<?php echo $pdf_url.$qbo_id;?>&type=SalesReceipt"><button type="button" class="btn btn-pdf">PDF</button></a>
			</td>
	      </tr>
		  <?php endforeach;?>
	    </tbody>
	  </table>
	  <?php echo $pagination_links?>
	  
	 <?php else:?>
		 <?php if($qb_customer_id < 1): ?>
		 	  <div class="alert alert-warning">
		 		<strong>No</strong> salesreceipts available - not mapped to a QuickBooks customer.
		 	  </div>
		 <?php else: ?>
		 	  <div class="alert alert-warning">
		 		<strong>No</strong> salesreceipts available.
		 	  </div>
		 <?php endif;?>	
	<?php endif;?>
	</div>
</body>
</html>