<?php
?>
<style>
    <?php if ( $design_data['enable_box_border'] == 'true' ) { ?>

    body #wfob_wrap .wfob_wrapper[data-wfob-id='<?php echo $this->get_id(); ?>'] .wfob_bump {
        background: <?php echo $design_data['box_background']; ?>;
        padding: <?php echo $design_data['box_padding']; ?>px;
        border: <?php echo sprintf( '%dpx %s %s', $design_data['border_width'], $design_data['border_style'], $design_data['border_color'] ); ?>;
    }

    <?php }
	?>
</style>
