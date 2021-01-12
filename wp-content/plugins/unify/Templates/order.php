<?php
use CodeClouds\Unify\Model\Order as OrderModel;
?>

<div style="margin-top: 10px; display: inline-block;">
    <h3>Payment Information</h3>

    <p><strong><?php echo __('Connection') ?>:</strong> <?php echo CodeClouds\Unify\Model\Config\Connection::get(OrderModel::get_connection($order->get_id(), 'connection')) ?></p>

    <?php
    if (!empty(OrderModel::get_connection($order->get_id(), 'connection_id')))
    {
        $connection = \CodeClouds\Unify\Model\Connection::get_post_meta(OrderModel::get_connection($order->get_id(), 'connection_id'));

        if (!empty($connection['unify_connection_campaign_id'][0]))
        {
            ?>
            <p><strong><?php echo __('Campaign ID') ?>:</strong> <?php echo $connection['unify_connection_campaign_id'][0] ?></p>
            <?php
        }

        if (!empty($connection['unify_connection_shipping_id'][0]))
        {
            ?>
            <p><strong><?php echo __('Shipping ID') ?>:</strong> <?php echo $connection['unify_connection_shipping_id'][0] ?></p>
            <?php
        }
    }
    ?>
</div>