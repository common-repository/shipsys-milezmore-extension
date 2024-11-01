<?php

    include_once(DTDC_ECONNECT_PATH."admin/helper/helper.php");
    $response = dtdcGetAwbNumber(($_GET['synced_orders']));
    $organisation_id = 'milezmore';
    $shopUrl = dtdcGetShopUrl();
    if (array_key_exists('data', $response) && !empty($response['data'])) { 
        $orderDetails = $response['data'];

?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h4>Manage Orders</h4>
        </div>
    </div>   
<div class="table-responsive">
    <table class="table table-hover"> 
        <thead class="thead-dark">
        <tr> 
            <th scope="col">#</th> 
            <th scope="col">AWB Number</th> 
            <th scope="col">Type</th>
            <th scope="col">Status</th>
            <th scope="col">Pieces</th>
            <th scope="col">Shipping Label</th>
            <th scope="col">Cancel</th>
        </tr> 
        </thead>
        <tbody>
            <?php foreach ($orderDetails as $magentoOrderNumber => $singleOrderDetail): ?>
                <?php foreach ($singleOrderDetail as $key => $data): ?>
                <tr scope="row">
                    <td><?php _e( $magentoOrderNumber) ?></td>
                    <td><?php _e( $data['reference_number']) ?></td>
                    <td><?php _e( $data['consignment_type']) ?></td>
                    <td><?php _e( $data['status']) ?></td>
                    <td><?php _e( $data['num_pieces']) ?></td>


                
                    <?php if($data['status'] === 'cancelled') { ?>
                    <td><button type="button"  id="<?php _e( $data['reference_number']) ?>" onclick="getShippingLabel('<?php _e( $data['reference_number']) ?>','<?php _e( $shopUrl) ?>','<?php _e( $data['reference_number']) ?>');" class="btn btn-primary">GET</button></td>
                    <?php } else { ?>
                    <td><button type="button"  id="<?php _e( $data['reference_number']) ?>" onclick="getShippingLabel('<?php _e( $data['reference_number']) ?>','<?php _e( $shopUrl) ?>','<?php _e( $data['reference_number']) ?>');" class="btn btn-primary">GET</button></td>
                    <?php } ?>
                    <?php if($data['status'] === 'cancelled') { ?>
                        <td><a data-toggle="tooltip"  class="btn btn-danger" disabled>Cancelled</a></td>
                    <?php } else { ?>
                    <td><button type="button" id="cancel<?php _e( $data['reference_number']) ?>" class="btn btn-danger" onclick="cancelOrderOnClick('<?php _e( $data['reference_number']) ?>','<?php _e( $shopUrl) ?>','cancel<?php _e( $data['reference_number']) ?>');">Cancel</button></td>
                    <?php } ?>
  
                </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table> 
</div>
<div id="popup-modal" style="display:none;">
    <h3> Are you sure you want to cancel the order ? </h3>
</div>
 
<?php 
    } else if (array_key_exists('data', $response) && empty($response['data'])) { ?>
        <div class="alert alert-danger" role="alert">No AWB Numbers found</div>
<?php
    } else if (array_key_exists('error', $response) && $response['error']['statusCode'] == 401) { ?>
        <div class="alert alert-danger" role="alert"><?php _e($response['error']['reason'] .': ' . 'Please login in Shipsy Configuration!') ?></div>
<?php
    } else if (array_key_exists('error', $response)) { ?>
        <div class="alert alert-danger" role="alert"><?php _e( $response['error']['message'])?></div>
<?php   
    }
?>