<?php
if ( ! defined( 'ABSPATH' ) ) {
     exit;
 }                                        
 ?><div class="vx_div">
      <div class="vx_head">
<div class="crm_head_div"> <?php _e('3. Map Form Fields to QuickBooks Fields.',  'wp-woocommerce-quickbooks' ); ?></div>
<div class="crm_btn_div"><i class="fa crm_toggle_btn fa-minus" title="<?php _e('Expand / Collapse','wp-woocommerce-quickbooks') ?>"></i></div>
<div class="crm_clear"></div> 
  </div>

  <div class="vx_group  fields_div" style="padding: 0px; border-width: 0px; background: transparent;">
<?php
 $req_span=" <span class='vx_red vx_required'>(".__('Required','wp-woocommerce-quickbooks').")</span>";
 $req_span2=" <span class='vx_red vx_required vx_req_parent'>(".__('Required','wp-woocommerce-quickbooks').")</span>";
$options=$this->wc_select();
  foreach($map_fields as $k=>$v){
  $req=$this->post('req',$v);
  $v['type']=ucfirst($v['type']);
      
  if(isset($v['name_c'])){
  $v['name']=$v['name_c'];      
  $v['label']=__('Custom Field','wp-woocommerce-quickbooks');      
  } 
if( in_array($v['name'] , array("OwnerId","AccountId","ContractId") )){
  //  continue;
}
if($module == "Order" && in_array($v['name'] , array("Status" ))){
 //   continue;
}

  $sel_val=isset($map[$k]['field']) ? $map[$k]['field'] : ""; 
    $val_type=isset($map[$k]['type']) && !empty($map[$k]['type']) ? $map[$k]['type'] : "field";  
  $options=$this->wc_select($sel_val);  
  $display="none"; $btn_icon="fa-plus";
  if(isset($map[$k][$val_type]) && !empty($map[$k][$val_type])){
    $display="block"; 
    $btn_icon="fa-minus";   
  }

  $req_html=$req == "true" ? $req_span : "";
 ?> 
<div class="crm_panel crm_panel_100">
<div class="crm_panel_head2 ">
<div class="crm_head_div"><span class="crm_head_text"> <?php echo $v['label']?></span>
<?php echo $req_html; ?>
</div>
<div class="crm_btn_div">
<?php
 if(isset($v['name_c']) || ($api_type != 'web' && $req != 'true')){   
?>
<i class="vx_icons vx_remove_btn vx_remove_custom fa fa-trash-o" title="<?php _e('Delete','wp-woocommerce-quickbooks'); ?>"></i>
<?php } ?>
<i class="fa crm_toggle_btn vx_btn_inner <?php echo $btn_icon ?>" title="<?php _e('Expand / Collapse','wp-woocommerce-quickbooks') ?>"></i>

</div>
<div class="crm_clear"></div> </div>
<div class="more_options crm_panel_content " style="display: <?php echo $display ?>;">
  <?php if(!isset($v['name_c'])){ ?>

  <div class="crm-panel-description">
  <span class="crm-desc-name-div"><?php echo __('Name:','wp-woocommerce-quickbooks')." ";?><span class="crm-desc-name"><?php echo $v['name']; ?></span> </span>
  <?php if($this->post('type',$v) !=""){ ?>
    <span class="crm-desc-type-div">, <?php echo __('Type:','wp-woocommerce-quickbooks')." ";?><span class="crm-desc-type"><?php echo $v['type'] ?></span> </span>
<?php
   }
  if($this->post('maxlength',$v) !=""){ 
   ?>
   <span class="crm-desc-len-div">, <?php echo __('Max Length:','wp-woocommerce-quickbooks')." ";?><span class="crm-desc-len"><?php echo $v['maxlength']; ?></span> </span>
  <?php 
  }
  ?>
   </div> 
  <?php
  }
  ?>
<div class="vx_margin">

<div class="entry_row">
<div class="entry_col1 vx_label"><label for="vx_type_<?php echo $k ?>"><?php _e('Field Type','wp-woocommerce-quickbooks') ?></label></div>
<div class="entry_col2">
<select name='meta[map][<?php echo $k ?>][type]' id="vx_type_<?php echo $k ?>"  class='vxc_field_type vx_input_100'>
<?php
  foreach($sel_fields as $f_key=>$f_val){
  $select="";
  if($this->post2($k,'type',$map) == $f_key)
  $select='selected="selected"';
  ?>
  <option value="<?php echo $f_key ?>" <?php echo $select ?>><?php echo $f_val?></option>    
  <?php } ?> 
</select>
</div>
<div class="crm_clear"></div>
</div>  
 
<div class="entry_row entry_row2">
<div class="entry_col1 vx_label">

<div class="vx_label vxc_fields vxc_field_" style="<?php if($this->post2($k,'type',$map) != ''){echo 'display:none';} ?>">
<label for="vx_field_<?php echo $k ?>"><?php _e('Select Field','wp-woocommerce-quickbooks') ?></label>
</div>

<div class="vxc_fields vxc_field_custom" style="<?php if($this->post2($k,'type',$map) != 'custom'){echo 'display:none';} ?>">
<label for="vx_custom_<?php echo $k ?>"> <?php _e('Custom Field','wp-woocommerce-quickbooks') ?></label>
</div>

<div class="vxc_fields vxc_field_value" style="<?php if($this->post2($k,'type',$map) != 'value'){echo 'display:none';} ?>">
<label for="vx_value_<?php echo $k ?>"> <?php _e('Custom Value','wp-woocommerce-quickbooks') ?></label>
</div>

</div>

<div class="entry_col2">


<div class="vxc_fields vxc_field_custom" style="<?php if($this->post2($k,'type',$map) != 'custom'){echo 'display:none';} ?>">
<input type="text" name='meta[map][<?php echo $k?>][custom]' id="vx_custom_<?php echo $k ?>"  value='<?php echo $this->post2($k,'custom',$map)?>' placeholder='<?php _e("Custom Field Name",'wp-woocommerce-quickbooks')?>' class='vx_input_100' >
</div>

<div class="vxc_fields vxc_field_value" style="<?php if($this->post2($k,'type',$map) != 'value'){echo 'display:none';} ?>">
<input type="text" name='meta[map][<?php echo $k?>][value]'  id="vx_value_<?php echo $k ?>" value='<?php echo $this->post2($k,'value',$map)?>' placeholder='<?php _e("Custom Value",'wp-woocommerce-quickbooks')?>' class='vx_input_100 vxc_field_input'>
<div class="howto"><?php echo sprintf(__('You can add a form field %s in custom value from following form fields','wp-woocommerce-quickbooks'),'<code>{field_id}</code>')?></div>
</div>

<div class="vxc_fields vxc_field_ vxc_field_standard" style="<?php if($this->post2($k,'type',$map) == 'custom'){echo 'display:none';} ?>">
<select name="meta[map][<?php echo $k ?>][field]"  id="vx_field_<?php echo $k ?>" class="vxc_field_option vx_input_100">
<?php echo $options ?>
</select>
<?php
    if($k == 'CustomerRef'){
?>
<div class="howto"><?php _e('Create a feed for "Customer" and select that here','wp-woocommerce-quickbooks') ?></div>
<?php
    }
?>
</div>


</div> 

<div class="crm_clear"></div>
</div>  

  </div></div>
  <div class="clear"></div>
  </div>
  <?php
  }
  ?>
<div id="vx_field_temp" style="display:none">
<div class="crm_panel crm_panel_100 vx_fields">
<div class="crm_panel_head2">
<div class="crm_head_div"><span class="crm_head_text">  <label class="crm_text_label"><?php _e('Custom Field','wp-woocommerce-quickbooks');?></label></span></div>
<div class="crm_btn_div">
<i class="vx_icons vx_remove_btn vx_remove_custom fa fa-trash-o" data-tip="<?php _e('Delete','wp-woocommerce-quickbooks'); ?>"></i>
<i class="fa crm_toggle_btn vx_btn_inner fa-minus " title="<?php _e('Expand / Collapse','wp-woocommerce-quickbooks') ?>"></i>
</div>
<div class="crm_clear"></div> </div>
<div class="more_options crm_panel_content" style="display: block;">
<?php
    if($api_type  != 'web'){
?>

  <div class="crm-panel-description">
  <span class="crm-desc-name-div"><?php echo __('Name:','wp-woocommerce-quickbooks')." ";?><span class="crm-desc-name"></span> </span>
  <span class="crm-desc-type-div">, <?php echo __('Type:','wp-woocommerce-quickbooks')." ";?><span class="crm-desc-type"></span> </span>
  <span class="crm-desc-len-div">, <?php echo __('Max Length:','wp-woocommerce-quickbooks')." ";?><span class="crm-desc-len"></span> </span>

   </div> 

<?php
    }
?>
<div class="vx_margin">
<?php
    if($api_type  == 'web'){
?>
<div class="entry_row">
<div class="entry_col1 vx_label"><?php _e('Field API Name','wp-woocommerce-quickbooks') ?></div>
<div class="entry_col2">
<input type="text" name="name_c" placeholder="<?php _e('Field API Name','wp-woocommerce-quickbooks') ?>" class="vx_input_100">
</div>
<div class="crm_clear"></div>
</div> 
<?php
    }
?>
<div class="entry_row">
<div class="entry_col1 vx_label"><?php _e('Field Type','wp-woocommerce-quickbooks') ?></div>
<div class="entry_col2">
<select name='type' class='vxc_field_type vx_input_100'>
<?php
  foreach($sel_fields as $f_key=>$f_val){
  ?>
  <option value="<?php echo $f_key ?>"><?php echo $f_val?></option>    
  <?php } ?> 
</select>
</div>
<div class="crm_clear"></div>
</div>  

<div class="entry_row entry_row2">
<div class="entry_col1 vx_label">

<div class="vx_label vxc_fields vxc_field_">
<label><?php _e('Select Field','wp-woocommerce-quickbooks') ?></label>
</div>

<div class="vxc_fields vxc_field_custom" style="display:none;">
<label> <?php _e('Custom Field','wp-woocommerce-quickbooks') ?></label>
</div>

<div class="vxc_fields vxc_field_value" style="display:none;">
<label> <?php _e('Custom Value','wp-woocommerce-quickbooks') ?></label>
</div>

</div>

<div class="entry_col2">

<div class="vxc_fields vxc_field_custom" style="display:none;">
<input type="text" name='custom'   value='' placeholder='<?php _e("Custom Field Name",'wp-woocommerce-quickbooks')?>' class='vx_input_100' >
</div>

<div class="vxc_fields vxc_field_value" style="display:none">
<input type="text" name="value"  value="" placeholder='<?php _e("Custom Value",'wp-woocommerce-quickbooks')?>' class='vx_input_100 vxc_field_input'>
<div class="howto"><?php echo sprintf(__('You can add a form field %s in custom value from following form fields','wp-woocommerce-quickbooks'),'<code>{field_id}</code>')?></div>
</div>

<div class="vxc_fields vxc_field_ vxc_field_standard">
<select name="field" class="vxc_field_option vx_input_100">
<?php echo $this->wc_select();  ?>
</select>
</div>


</div> 

<div class="crm_clear"></div>
</div> 
<i class="vx_icons-h  vx vx-bin-2" data-tip="Delete"></i>    
 
  </div></div>
  <div class="clear"></div>
  </div>
  
  </div>
  <?php
  if($api_type =="web"){ ?>
  <div class="vx_fields_footer">
  <div class="vx_row">
  <div class="vx_col1"> &nbsp;</div><div class="vx_col2">
  <button type="button" class="button button-default" id="xv_add_custom_field"><i class=" fa fa-plus-circle" ></i> <?php _e('Add Custom Field','wp-woocommerce-quickbooks')?></button></div>
  <div class="clear"></div></div>
   </div>
 <?php }else{ ?> 
<div class="crm_panel crm_panel_100 vx_fields">
<div class="crm_panel_head2">
<div class="crm_head_div"><span class="crm_head_text">  <label class="crm_text_label"><?php _e('Add New Field','wp-woocommerce-quickbooks');?></label></span></div>
<div class="crm_btn_div"><i class="fa crm_toggle_btn vx_btn_inner fa-minus" style="display: none;" title="<?php _e('Expand / Collapse','wp-woocommerce-quickbooks') ?>"></i></div>
<div class="crm_clear"></div> </div>
<div class="more_options crm_panel_content" style="display: block;">

<div class="vx_margin">

<div class="vx_tr">
<div class="vx_td">
<select id="vx_add_fields_select" class="vx_input_100" style="width: 100%" autocomplete="off">
<option value=""></option>
<?php
$json_fields=array();
 foreach($fields as $k=>$v){
     $v['type']=ucfirst($v['type']);
     $json_fields[$k]=$v;
   $disable='';
   if(isset($map_fields[$k])){
    $disable='disabled="disabled"';   
   } 
echo "<option value='{$k}' {$disable} >{$v['label']}</option>";   
} ?>
</select>
</div>
<div class="vx_td2">
 <button type="button" class="button button-default" style="vertical-align: middle;" id="xv_add_custom_field"><i class="fa fa-plus-circle" ></i> <?php _e('Add Field','wp-woocommerce-quickbooks')?></button>
 </div>
</div> 
<div class="entry_row vxc_fields vxc_field_custom" style="text-align: center;">
 
</div> 

<i class="vx_icons-h  vx vx-bin-2" data-tip="Delete"></i>    
 
  </div></div>
  <div class="clear"></div>
</div>
<script type="text/javascript">
var crm_fields=<?php echo json_encode($json_fields); ?>;
</script> 
 <?php
 }
 ?>
  </div> 
 <!---fields end--->
  </div>
  <div class="vx_div ">
    <div class="vx_head ">
<div class="crm_head_div"> <?php _e('4. When to Send the Order to QuickBooks.',  'wp-woocommerce-quickbooks' ); ?></div>
<div class="crm_btn_div"><i class="fa crm_toggle_btn fa-minus" title="<?php _e('Expand / Collapse','wp-woocommerce-quickbooks') ?>"></i></div>
<div class="crm_clear"></div> 
  </div> 
  <div class="vx_group ">
  <div class="vx_row">
  <div class="vx_col1">
  <label for="vxc_event"><?php _e('Select Event','wp-woocommerce-quickbooks'); $this->tooltip($tooltips['manual_export']); ?></label>
  </div>
  <div class="vx_col2">
  <select id="vxc_event" name="meta[event]" class="vx_sel" autocomplete="off">
  <?php  
  foreach($events as $f_key=>$f_val){
  $select="";
  if($feed['event'] == $f_key)
  $select='selected="selected"';
  echo '<option value="'.$f_key.'" '.$select.'>'.$f_val.'</option>';    
  }    
  ?>
  </select> 
</div>
<div class="clear"></div>
</div>
  <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ccc">
  <div class="vx_row">
  <div class="vx_col1">
  <label for="crm_optin"><?php _e('Custom Filter', 'wp-woocommerce-quickbooks'); $this->tooltip($tooltips['optin_condition']);?></label>
  </div>
  <div class="vx_col2">
  <input type="checkbox" style="margin-top: 0px;" id="crm_optin" class="crm_toggle_check" name="meta[optin_enabled]" value="1" <?php echo !empty($feed['optin_enabled']) ? 'checked="checked"' : ''?> autocomplete="off"/>
    <label for="crm_optin"><?php _e('Enable', 'wp-woocommerce-quickbooks'); ?></label>
  
  </div>
  <div class="clear"></div>
  </div>
  <div id="crm_optin_div" style="margin: 10px auto; width: 90%;<?php echo empty($feed['optin_enabled']) ? 'display:none' : ''?>">
  
        <div>
            <?php
            $sno=0; 
                foreach($filters as $filter_k=>$filter_v){
  $sno++;
                    ?>
  <div class="vx_filter_or" data-id="<?php echo $filter_k ?>"> 
  <?php if($sno>1){ ?>
  <div class="vx_filter_label">OR</div>
  <?php } ?>                 
  <div class="vx_filter_div">
  <?php
  if(is_array($filter_v)){
  $sno_i=0;
  foreach($filter_v as $s_k=>$s_v){   
  $sno_i++;
  
  ?> 
      <div class="vx_filter_and">
      <?php if($sno_i>1){ ?>
  <div class="vx_filter_label">AND</div>
  <?php } ?>   
     <div class="vx_filter_field vx_filter_field1">    
     <select id="crm_optin_field" name="meta[filters][<?php echo $filter_k ?>][<?php echo $s_k ?>][field]"><?php 
  echo $this->wc_select($this->post('field',$s_v));
      ?></select></div>
       <div class="vx_filter_field vx_filter_field2">   
    <select name="meta[filters][<?php echo $filter_k ?>][<?php echo $s_k ?>][op]" >
    <?php
       foreach($vx_op as $k=>$v){
  $sel="";
  if($this->post('op',$s_v) == $k)
  $sel='selected="selected"';
         echo "<option value='".$k."' $sel >".$v."</option>";
     } 
    ?>
            </select></div>
             <div class="vx_filter_field vx_filter_field3">    
           <input type="text" class="vxc_filter_text" placeholder="<?php _e('Value','wp-woocommerce-quickbooks') ?>" value="<?php echo esc_attr($this->post('value',$s_v)) ?>" name="meta[filters][<?php echo $filter_k ?>][<?php echo $s_k ?>][value]"> 
            </div>
                <?php if( $sno_i>1){ ?> 
  <div class="vx_filter_field vx_filter_field4"><i class="vx_icons-h vx_trash_and fa fa-trash-o"></i></div>
           <?php } ?>
           <div style="clear: both;"></div> 
           </div>
           <?php
  } }
           ?>
           <div class="vx_btn_div">
           <button class="button button-default button-small vx_add_and"><i class="vx_trash_and fa fa-hand-o-right"></i> <?php _e('Add AND Filter','wp-woocommerce-quickbooks') ?></button>
           <?php if($sno>1){ ?>
  <i class="vx_icons-h fa fa-trash-o vx_trash_or"></i>
  <?php } ?> 
        
           </div>
        </div>
        </div>
                    <?php
                }
            ?>
  
          <div class="vx_btn_div">
  <button class="button button-default  vx_add_or"><i class="vx_trash_and fa fa-check"></i> <?php _e('Add OR Filter','wp-woocommerce-quickbooks') ?></button></div>
        </div>
    </div>
  <div style="display: none;" id="vx_filter_temp">
  <div class="vx_filter_or"> 
  <div class="vx_filter_label">OR</div>
  <div class="vx_filter_div"> 
      <div class="vx_filter_and">  
      <div class="vx_filter_label vx_filter_label_and">AND</div> 
     <div class="vx_filter_field vx_filter_field1">    
     <select id="crm_optin_field" name="field" class='optin_selecta'><?php 
    echo $this->wc_select($this->post('field',$s_v));
      ?></select></div>
       <div class="vx_filter_field vx_filter_field2">    
    <select name="op" >
    <?php
       foreach($vx_op as $k=>$v){
  
         echo "<option value='".$k."' >".$v."</option>";
     } 
    ?>
            </select></div>
             <div class="vx_filter_field vx_filter_field3">    
           <input type="text" class="vxc_filter_text" placeholder="<?php _e('Value','wp-woocommerce-quickbooks') ?>" name="value"> 
            </div>
           <div class="vx_filter_field vx_filter_field4"><i class="vx_icons-h vx_trash_and fa fa-trash-o"></i></div>
           <div style="clear: both;"></div> 
           </div>
           <div class="vx_btn_div">
           <button class="button button-default button-small vx_add_and"><i class=" vx_trash_and fa fa-hand-o-right"></i> <?php _e('Add AND Filter','wp-woocommerce-quickbooks') ?></button>
           <i class="vx_icons-h vx_trash_and fa fa-trash-o vx_trash_or"></i>
           </div>
        </div>
        </div>
        </div>

      
  </div>  
  </div>  
  </div>  
  <?php
  $panel_count=4;
  if($api_type != "web"){ 
      $panel_count++; 
      $search_fields=array();
      foreach($fields  as $kk=>$vv){
          if(!empty($vv['search'])){
          if($kk == 'FullyQualifiedName'){
              $kk='name'; $vv['label']='First Name + Last Name';
          }    
        $search_fields[$kk]=$vv;      
          }
      }

  ?>     
  <div class="vx_div "> 
  <div class="vx_head ">
<div class="crm_head_div"> <?php  echo sprintf(__('%s. Choose Primary Key.',  'wp-woocommerce-quickbooks' ),$panel_count); ?></div>
<div class="crm_btn_div"><i class="fa crm_toggle_btn fa-minus" title="<?php _e('Expand / Collapse','wp-woocommerce-quickbooks') ?>"></i></div>
<div class="crm_clear"></div> 
  </div>                    
  <div class="vx_group ">
  <div class="vx_row">
  <div class="vx_col1">
  <label for="crm_primary_field"><?php _e('Select Primary Key','wp-woocommerce-quickbooks') ?></label>
  </div><div class="vx_col2">
  <select id="crm_primary_field" name="meta[primary_key]" class="vx_sel" autocomplete="off">
  <?php echo $this->crm_select($search_fields,$feed['primary_key']); ?>
  </select> 
  <div class="description" style="float: none; width: 90%"><?php _e('If you want to update a pre-existing object, select what should be used as a unique identifier ("Primary Key"). For example, this may be an email address, lead ID, or address. When a new order comes in with the same "Primary Key" you select, a new object will not be created, instead the pre-existing object will be updated.', 'wp-woocommerce-quickbooks'); ?></div>
  </div>
  <div class="clear"></div>
  </div>
  <div class="vx_row">
  <div class="vx_col1">
  <label for="vx_update">
  <?php _e('Update Entry', 'wp-woocommerce-quickbooks'); ?>
  </label>
  </div>
  <div class="vx_col2">
  <input type="checkbox" style="margin-top: 0px;" id="vx_update" class="crm_toggle_check" name="meta[update]" value="1" <?php echo !empty($feed['update']) ? "checked='checked'" : ""?>/>
  <label for="vx_update">
  <?php _e('Do not update entry, if already exists', 'wp-woocommerce-quickbooks'); ?>
  </label>
  </div>
  <div style="clear: both;"></div>
  </div>
  
     <div class="vx_row">
  <div class="vx_col1">
  <label for="vx_update">
  <?php _e('Repeat Feed ', 'wp-woocommerce-quickbooks'); ?>
  </label>
  </div>
  <div class="vx_col2">
  <input type="checkbox" style="margin-top: 0px;" id="vx_update" class="crm_toggle_check" name="meta[each_line]" value="1" <?php echo !empty($feed['each_line']) ? "checked='checked'" : ""?>/>
  <label for="vx_update">
  <?php _e('Repeat this feed for each line item of an Order', 'wp-woocommerce-quickbooks'); ?>
  </label>
  </div>
  <div style="clear: both;"></div>
  </div>
  
  </div>

  </div>
 <?php
  }
  if(isset($fields['CustomerRef']) ){ 
$panel_count++;
$account_feeds=$this->get_object_feeds('customer',$account);
  ?>
    <div class="vx_div vx_refresh_panel ">    
      <div class="vx_head ">
<div class="crm_head_div"> <?php echo sprintf(__('%s. Assign Customer',  'wp-woocommerce-quickbooks' ),$panel_count); 
echo  !in_array($module, array('customer')) ? $req_span2 : "";
?></div>
<div class="crm_btn_div"><i class="fa crm_toggle_btn fa-minus" title="<?php _e('Expand / Collapse','wp-woocommerce-quickbooks') ?>"></i></div>
<div class="crm_clear"></div> 
  </div>                 
    <div class="vx_group ">

        <div class="vx_row"> 
   <div class="vx_col1"> 
  <label for="account_check"><?php _e("Assign Customer", 'wp-woocommerce-quickbooks');?></label>
  </div>
  <div class="vx_col2">
  <input type="checkbox" style="margin-top: 0px;" id="account_check" class="crm_toggle_check" name="meta[customer_check]" value="1" <?php echo !empty($feed["customer_check"]) ? "checked='checked'" : ""?> autocomplete="off"/>
    <label for="contact_check"><?php _e("Enable", 'wp-woocommerce-quickbooks'); ?></label>
  </div>
<div class="clear"></div>
</div>
    <div id="account_check_div" style="<?php echo empty($feed["customer_check"]) ? "display:none" : ""?>">
         <div class="vx_row">
   <div class="vx_col1">
  <label for="object_account"><?php _e('Select Customer Feed','wp-woocommerce-quickbooks');  ?></label>
</div> 
<div class="vx_col2">

  <select id="object_account" name="meta[object_customer]" style="width: 100%;" autocomplete="off">
  <?php echo $this->gen_select($account_feeds ,$feed['object_customer'],__('Select Customer Feed','wp-woocommerce-quickbooks')); ?>
  </select>

   </div>

   <div class="clear"></div>
   </div>
    </div>

  </div>
  </div>
    <?php
  }    
  
if(in_array($module,array('invoice','estimate','salesreceipt','creditmemo','refundreceipt'))){
$panel_count++;
$books=$this->post('exp_accounts',$meta);
$tax_codes_list=$this->post('tax_codes',$meta);
 $tax_codes=array('map'=>__('According to map in WooCommerce Quickbooks account Settings',  'wp-woocommerce-quickbooks' ));
 if(!self::$is_pr){
     unset($tax_codes['map']);  
 }
if(is_array($tax_codes_list)){
   $tax_codes=$tax_codes+$tax_codes_list;
}
$classes=$this->post('classes',$meta);
$assets=$this->post('asset_accounts',$meta);
$income=$this->post('income_accounts',$meta);
$refund=$this->post('refund_accounts',$meta);
$discount=$this->post('discount_accounts',$meta);
?>
<!-------------------------- lead products -------------------->
<div class="vx_div vx_refresh_panel">    
<div class="vx_head ">
<div class="crm_head_div"> <?php echo sprintf(__('%s. Create Order Products',  'wp-woocommerce-quickbooks' ),$panel_count); ?></div>
<div class="crm_btn_div"><i class="fa crm_toggle_btn fa-minus" title="<?php _e('Expand / Collapse','wp-woocommerce-quickbooks') ?>"></i></div>
<div class="crm_clear"></div> 
  </div>    
            
    <div class="vx_group ">
 
   <div class="vx_row"> 
   <div class="vx_col1"> 
  <label for="order_items"><?php _e("Line Items", 'wp-woocommerce-quickbooks'); $this->tooltip($tooltips['vx_line_items']);?></label>
  </div>
  <div class="vx_col2">
  <input type="checkbox" checked="checked" disabled="disabled" style="margin-top: 0px;" id="crm_items" class="crm_toggle_check <?php if(empty($books) ){echo 'vx_refresh_btn';} ?>" name="meta[order_items]" value="1" />
    <label for="crm_items"><?php _e("Create an order product for each line item", 'wp-woocommerce-quickbooks'); ?></label>
  </div>
<div class="clear"></div>
</div>
    <div id="crm_items_div">
    
  <div class="vx_row">
  <div class="vx_col1">
  <label for="crm_sel_book"><?php _e('Item Accounts','wp-woocommerce-quickbooks'); ?></label>
  </div>
  <div class="vx_col2">
  <button class="button vx_refresh_data" data-id="refresh_accounts" type="button" autocomplete="off" style="vertical-align: baseline;">
  <span class="reg_ok"><i class="fa fa-refresh"></i> <?php _e('Refresh Data','wp-woocommerce-quickbooks') ?></span>
  <span class="reg_proc"><i class="fa fa-refresh fa-spin"></i> <?php _e('Refreshing...','wp-woocommerce-quickbooks') ?></span>
  </button>
  </div> 
   <div class="clear"></div>
  </div> 



   <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_income"><?php _e('Income Account','wp-woocommerce-quickbooks');  ?></label>
</div> 
<div class="vx_col2">

  <select id="crm_sel_income" name="meta[income_account]" style="width: 100%;" autocomplete="off">
  <?php echo $this->gen_select($income,$feed['income_account'],__('Select Income Account for new Intventory Items','wp-woocommerce-quickbooks')); ?>
  </select>

   </div>

   <div class="clear"></div>
   </div>
   

        <div class="vx_row">
   <div class="vx_col1">
  <label><?php _e('Deposit Account','wp-woocommerce-quickbooks');  ?></label>
</div> 
<div class="vx_col2">

  <select id="crm_sel_refund" name="meta[refund_account]" style="width: 100%;" autocomplete="off">
  <?php echo $this->gen_select($refund,$feed['refund_account'],__('Select Refund Account for new Intventory Items','wp-woocommerce-quickbooks')); ?>
  </select>

   </div>

   <div class="clear"></div>
   </div>
  
       <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_asset"><?php _e('Asset Account','wp-woocommerce-quickbooks');  ?></label>
</div> 
<div class="vx_col2">

  <select id="crm_sel_asset" name="meta[asset_account]" style="width: 100%;" autocomplete="off">
  <?php echo $this->gen_select($assets,$feed['asset_account'],__('Select Asset Account for new Intventory Items','wp-woocommerce-quickbooks')); ?>
  </select>
<div class="howto"><?php _e('(Optional) Required for creating new Inventory Products','wp-woocommerce-quickbooks');  ?></div>
   </div>

   <div class="clear"></div>
   </div>
   
     <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_exp"><?php _e('Expense Account','wp-woocommerce-quickbooks');  ?></label>
</div> 
<div class="vx_col2">

  <select id="crm_sel_exp" name="meta[exp_account]" style="width: 100%;" autocomplete="off">
  <?php echo $this->gen_select($books,$feed['exp_account'],__('Select Expense Account for new Intventory Items','wp-woocommerce-quickbooks')); ?>
  </select>
<div class="howto"><?php _e('(Optional) Required for creating new Inventory Products','wp-woocommerce-quickbooks');  ?></div>
   </div>

   <div class="clear"></div>
   </div>
      
     <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_tax"><?php _e('Line Items Tax Code','wp-woocommerce-quickbooks');  ?></label>
</div> 
<div class="vx_col2">
  <select class="crm_sel_tax_code" name="meta[tax_code]" style="width: 100%;" autocomplete="off">
  <?php echo $this->gen_select($tax_codes,$feed['tax_code'],__('Select Tax Code for Line Items','wp-woocommerce-quickbooks')); ?>
  </select>
<div class="howto"><?php _e('(Optional) Required if tax code is enabled ','wp-woocommerce-quickbooks');  ?></div>
   </div>

   <div class="clear"></div>
   </div>
   
       <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_tax_order"><?php _e('Transaction Tax Code','wp-woocommerce-quickbooks');  ?></label>
</div> 
<div class="vx_col2">
  <select id="crm_sel_tax_code_order" class="crm_sel_tax_code" name="meta[tax_code_order]" style="width: 100%;" autocomplete="off">
  <?php
   echo $this->gen_select($tax_codes,$feed['tax_code_order'],__('Do not appy a separate tax to whole transaction','wp-woocommerce-quickbooks')); ?>
  </select>
   </div>

   <div class="clear"></div>
   </div>
     
 <?php
       if(self::$is_pr){
   ?>    
     <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_ship"><?php _e('Shipping Tax Code','wp-woocommerce-quickbooks');  ?></label>
</div> 
<div class="vx_col2">
  <select id="crm_sel_ship" name="meta[ship_tax]" style="width: 100%;" autocomplete="off">
  <?php
  $tax_codes_ship=array('apply_tax'=>__('Apply Line Items Tax Code','wp-woocommerce-quickbooks'));
   echo $this->gen_select($tax_codes_ship,$feed['ship_tax'],__('Do not appy tax to shipping','wp-woocommerce-quickbooks')); ?>
  </select>
   </div>

   <div class="clear"></div>
   </div>
   
    <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_dis"><?php _e('Discount Tax Code','wp-woocommerce-quickbooks');  ?></label>
</div> 
<div class="vx_col2">
  <select id="crm_sel_dis" name="meta[dis_tax]" style="width: 100%;" autocomplete="off">
  <?php
   echo $this->gen_select($discount,$feed['dis_tax'],__('Select Discount Account for Discounts','wp-woocommerce-quickbooks')); ?>
  </select>
   </div>

   <div class="clear"></div>
   </div> 
    
  <?php
       }
     ?>   
     <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_class"><?php _e('Class ','wp-woocommerce-quickbooks');  ?></label>
</div> 
<div class="vx_col2">
  <select id="crm_sel_class" name="meta[class]" style="width: 100%;" autocomplete="off">
  <?php echo $this->gen_select($classes,$feed['class'],__('Select Class','wp-woocommerce-quickbooks')); ?>
  </select>
<div class="howto"><?php _e('(Optional) ','wp-woocommerce-quickbooks');  ?></div>
   </div>

   <div class="clear"></div>
   </div>
   
      <div class="vx_row">
   <div class="vx_col1">
  <label for="pro_desc"><?php _e('Item Type','wp-woocommerce-quickbooks'); ?></label>
</div> 
<div class="vx_col2">
  <select name="meta[item_type]" style="width: 100%;" autocomplete="off">
  <?php 
  if(empty($feed['item_type'])){ $feed['item_type']='NonInventory'; }
  $item_types=array('NonInventory'=>'Non Inventory','Inventory'=>'Inventory','Service'=>'Service');
  echo $this->gen_select($item_types,$feed['item_type']);
   ?>
  </select>
</div>

   <div class="clear"></div>
   </div>
   
   <div class="vx_row">
   <div class="vx_col1">
  <label for="pro_match"><?php _e('Match Items By','wp-woocommerce-quickbooks'); ?></label>
</div> 
<div class="vx_col2">
  <select name="meta[item_match]" style="width: 100%;" autocomplete="off">
  <?php 
  if(empty($feed['item_match'])){ $feed['item_match']=''; }
  $item_types=array(''=>'SKU','name'=>'Item Name');
  echo $this->gen_select($item_types,$feed['item_match']);
   ?>
  </select>
</div>

   <div class="clear"></div>
   </div>
<?php
    if(self::$is_pr){ ?>
    
     <div class="vx_row">
  <div class="vx_col1">
  <label for="crm_note_fields">
  <?php _e( 'Product Description', 'wp-woocommerce-quickbooks' );  ?>
  </label>
  </div>
   <div class="vx_col2 entry_col2" style="width: 70%;">
  <textarea name="meta[item_desc]"  placeholder="<?php _e("{field-id} text",'wp-woocommerce-quickbooks')?>" class="vx_input_100 vxc_field_input" style="height: 60px"><?php
   echo $this->post('item_desc',$feed); ?></textarea>
<div class="howto"><?php echo sprintf(__('You can add a form field %s in custom value from following form fields','wp-woocommerce-quickbooks'),'<code>{field_id}</code>')?></div>

<select name="field"  class="vxc_field_option vx_input_100">
<?php echo $options ?>
</select>
   </div>
  <div style="clear: both;"></div>
  </div>
   
     <div class="vx_row"> 
   <div class="vx_col1"> 
  <label for="order_total"><?php _e('Order Total mismatch Notice', 'wp-woocommerce-quickbooks'); ?></label>
  </div>
  <div class="vx_col2">
  <input type="checkbox" style="margin-top: 0px;" <?php echo !empty($feed['order_notice']) ? "checked='checked'" : ""?> id="order_total" class="crm_toggle_check"  name="meta[order_notice]" value="1" />
    <label for="order_total"><?php _e('Notify me(debug email in account settings) if Quickbooks order total does not match WooCommerce order total', 'wp-woocommerce-quickbooks'); ?></label>
  </div>
<div class="clear"></div>
</div>        
<?php    } ?>
  
  </div>


  </div>
  </div>
<?php
}

  $file=self::$path.'pro/pro-mapping.php';
if(self::$is_pr && file_exists($file)){
include_once($file);
}
  
 do_action('vx_plugin_upgrade_notice_plugin_'.$this->type);  
 
 
 