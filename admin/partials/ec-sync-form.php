<?php
$order_id = sanitize_text_field($_GET["orderid"]);
$order = wc_get_order($order_id);
$shippingAddress = $order->get_address('shipping');
?>

<?php
include_once(DTDC_ECONNECT_PATH . "admin/helper/helper.php");
$response = dtdcGetAddresses();
$allAddresses = $response['data'];
if (array_key_exists('data', $response) && !empty($response['data'])) {
    $allAddresses = $response['data'];
    $forwardAddress = (array_key_exists('forwardAddress', $allAddresses)) ? $allAddresses['forwardAddress'] : array();
    $reverseAddress = (array_key_exists('reverseAddress', $allAddresses)) ? $allAddresses['reverseAddress'] : array();
    $exceptionalReturnAddress = (array_key_exists('exceptionalReturnAddress', $allAddresses)) ? $allAddresses['exceptionalReturnAddress'] : array();
    $validServiceTypes = $allAddresses['serviceTypes'];
?>

    <div id="content">
        <div class="page-header">
            <div class="container-fluid">
                <div class="float-right">
                    <button type="submit" form="form-module" id="softdataSubmitButton" data-toggle="tooltip" title="Save" class="btn btn-primary">
                        <i class="fa fa-save">Save</i>
                    </button>

                </div>
                <h4>Sync Order</h4>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card" style="max-width: 98rem;">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="fa fa-pencil"></i>Kindly check details before proceeding further
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?php _e(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
                        <input type="hidden" name="action" value="on_sync_submit" />
                        <div class="form-group" id="order-details" style="padding : 15px">
                            <div class="header-style" style="width : 90% !important">
                                <span class="header-font">Order Details</span>
                            </div>
                            <div class="container" style="margin-left : 0px">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label for="textInput" class="label-font">Order Number <span class="required-text">*</span></label>

                                        <input type="text" required="true" value="<?php _e(sanitize_text_field($_GET["orderid"])) ?>" id="customer-reference-number" name="customer-reference-number" class="form-control    " style="border-radius : 0px" readonly>
                                        <div class="orderText" style="color : red ; font-size : 10px;display:none"> Order Number is required</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="textInput" class="label-font">AWB Number</label>
                                        <input type="text" name="awb-number" id="awb-number" class="form-control    " style="border-radius : 0px" placeholder="Text input">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="select" class="label-font">Service Type <span class="required-text">*</span></label>
                                        <select class="custom-select" required="true" name="service-type" id="select-service-type" style="border-radius : 0px; height : 33px; font-size:12px">
                                            <?php foreach ($validServiceTypes as $serviceType) { ?>
                                                <?php if ($serviceType['name'] == 'PREMIUM') : ?>
                                                    <option value="<?php _e($serviceType['id']) ?>" selected=""><?php _e($serviceType['name']) ?></option>
                                                <?php else : ?>
                                                    <option value="<?php _e($serviceType['id']) ?>"><?php _e($serviceType['name']) ?></option>
                                                <?php endif; ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-sm-4">
                                        <label for="select" class="label-font">Courier Type <span class="required-text">*</span></label>
                                        <select class="custom-select " required="true" name="courier-type" id="select-courier-type" style="border-radius : 0px; height : 33px; font-size:12px">
                                            <option value="NON-DOCUMENT" selected>NON-DOCUMENT</option>
                                            <option value="DOCUMENT">DOCUMENT</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="select" class="label-font">Consignment Type <span class="required-text">*</span></label>
                                        <select class=" custom-select" required="true" name="consignment-type" id="select-consignment-type" style="border-radius : 0px; height : 33px; font-size:12px">
                                            <option disabled selected value> -- select consignment type ---</option>
                                            <option value="forward" selected>FORWARD</option>
                                            <option value="reverse">REVERSE</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="num-pieces" class="label-font">Number of Pieces <span class="required-text">*</span></label>
                                        <input type="number" id="num-pieces" required="true" style="border-radius : 0px" oninput="this.value = Math.abs(this.value)" min="1" pattern="\d+" name="num-pieces" class="form-control    " value="1">
                                        <div class="numpiecesError" style="color : red ; font-size : 10px;display:none"> Value should be greater than 0</div>
                                        <div class="form-check" style=" margin-top:8px">
                                            <input class="form-check-input" style="border-radius : 0px" type="checkbox" name="multiPieceCheck" value="true" id="multiPieceCheck">
                                            <label class="form-check-label label-font" for="multiPieceCheck" style="margin-left:10px;font-size:12px !important">All pieces same</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="origin-details" style="padding:15px">
                            <div class="header-style" style="width : 90% !important">
                                <span class="header-font">Origin Details</span>
                            </div>
                            <div class="container" style="margin-left : 0px">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label for="origin-name" class="label-font">Name <span class="required-text">*</span></label>
                                        <input type="text" id="origin-name" required="true" style="border-radius : 0px" name="origin-name" class="form-control" value="<?php _e($forwardAddress['name']) ?>">
                                        <div class="nameErrorText" style="color : red ; font-size : 10px;display:none"> Origin Name is required</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="origin-number" class="label-font">Phone Number <span class="required-text">*</span></label>
                                        <input type="tel" id="origin-number" required="true" oninput="this.value=this.value.slice(0,this.maxLength)" maxlength="10" style="border-radius : 0px" name="origin-number" class="form-control    " value="<?php _e($forwardAddress['phone']) ?>">
                                        <div class="phoneErrorText" style="color : red ; font-size : 10px;display:none"> Phone number is required</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="origin-alt-number" class="label-font">Alternate Phone Number</label>
                                        <input type="tel" id="origin-alt-number" oninput="this.value=this.value.slice(0,this.maxLength)" maxlength="10" style="border-radius : 0px" name="origin-alt-number" class="form-control    " value="<?php _e($forwardAddress['alternate_phone']) ?>">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-sm-6">
                                        <label for="origin-line-1" class="label-font">Address Line 1 <span class="required-text">*</span></label>
                                        <input type="text" id="origin-line-1" required="true" style="border-radius : 0px" name="origin-line-1" class="form-control" value="<?php _e($forwardAddress['address_line_1']) ?>">
                                        <div class="addressErrorText" style="color : red ; font-size : 10px;display:none"> Origin Address is required</div>

                                    </div>
                                    <div class="col-sm-6">
                                        <label for="origin-line-2" class="label-font">Address Line 2</label>
                                        <input type="text" id="origin-line-2" style="border-radius : 0px" name="origin-line-2" class="form-control" value="<?php _e($forwardAddress['address_line_2']) ?>">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-sm-3">
                                        <label for="origin-city" class="label-font"> City <span class="required-text">*</span></label>
                                        <input type="text" id="origin-city" name="origin-city" required="true" style="border-radius : 0px" class="form-control    " value="<?php _e($forwardAddress['city']) ?>">
                                        <div class="cityErrorText" style="color : red ; font-size : 10px;display:none">Origin City is required</div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="origin-state" class="label-font">State <span class="required-text">*</span></label>
                                        <input type="text" id="origin-state" name="origin-state" required="true" style="border-radius : 0px" class="form-control   " value="<?php _e($forwardAddress['state']) ?>">
                                        <div class="stateErrorText" style="color : red ; font-size : 10px;display:none">Origin State is required</div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="origin-country" class="label-font">Country <span class="required-text">*</span></label>
                                        <input type="text" id="origin-country" style="border-radius : 0px" required="true" name="origin-country" class="form-control    " value="<?php _e($forwardAddress['country']) ?>">
                                        <div class="countryErrorText" style="color : red ; font-size : 10px;display:none">Origin Country is required</div>

                                    </div>
                                    <div class="col-sm-3">
                                        <label for="origin-pincode">Pincode</label>
                                        <input type="text" id="origin-pincode" style="border-radius : 0px" name="origin-pincode" class="form-control    " value="<?php _e($forwardAddress['pincode']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="destination-details" style="padding:15px">
                            <div class="header-style" style="width : 90% !important">
                                <span class="header-font">Destination Details</span>
                            </div>
                            <div class="container" style="margin-left : 0px">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label for="destination-name" class="label-font">Name <span class="required-text">*</span></label>
                                        <input type="text" id="destination-name" required="true" name="destination-name" style="border-radius : 0px" class="form-control   " value="<?php _e(($shippingAddress['first_name'] . ' ' . $shippingAddress['last_name'])); ?>">
                                        <div class="dNameErrorText" style="color : red ; font-size : 10px;display:none">Destination Name is required</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="destination-number" class="label-font">Phone Number <span class="required-text">*</span></label>
                                        <input type="tel" id="destination-number" required="true" oninput="this.value=this.value.slice(0,this.maxLength)" maxlength="10" name="destination-number" style="border-radius : 0px" class="form-control   " value="<?php _e($order->get_billing_phone()) ?>">
                                        <div class="dPhoneErrorText" style="color : red ; font-size : 10px;display:none">Phone number is required</div>

                                    </div>
                                    <div class="col-sm-4">
                                        <label for="destination-alt-number" class="label-font">Alternate Phone Number </label>
                                        <input type="tel" id="destination-alt-number" oninput="this.value=this.value.slice(0,this.maxLength)" maxlength="10" style="border-radius : 0px" name="destination-alt-number" class="form-control   " value="<?php _e($order->get_billing_phone()) ?>">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-sm-6">
                                        <label for="destination-line-1" class="label-font">Address Line 1 <span class="required-text">*</span></label>
                                        <input type="text" id="destination-line-1" required="true" name="destination-line-1" style="border-radius : 0px" class="form-control   " value="<?php _e($shippingAddress['address_1']) ?>">
                                        <div class="dAddressErrorText" style="color : red ; font-size : 10px;display:none">Destination Address is required</div>

                                    </div>
                                    <div class="col-sm-6">
                                        <label for="destination-line-2" class="label-font">Address Line 2</label>
                                        <input type="text" id="destination-line-2" name="destination-line-2" style="border-radius : 0px" class="form-control" value="<?php _e($shippingAddress['address_2']) ?>">
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-sm-3">
                                        <label for="destination-city" class="label-font">City <span class="required-text">*</span></label>
                                        <input type="text" id="destination-city" required="true" name="destination-city" style="border-radius : 0px" class="form-control   " value="<?php _e($shippingAddress['city']) ?>">
                                        <div class="dCityErrorText" style="color : red ; font-size : 10px;display:none">Destination City is required</div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="destination-state" class="label-font">State <span class="required-text">*</span></label>
                                        <input type="text" id="destination-state" required="true" name="destination-state" style="border-radius : 0px" class="form-control   " value="<?php _e($shippingAddress['state']) ?>">
                                        <div class="dStateErrorText" style="color : red ; font-size : 10px;display:none">Destination State is required</div>

                                    </div>
                                    <div class="col-sm-3">
                                        <label for="destination-country" class="label-font">Country <span class="required-text">*</span></label>
                                        <input type="text" id="destination-country" required="true" name="destination-country" style="border-radius : 0px" class="form-control   " value="<?php _e($shippingAddress['country']) ?>">
                                        <div class="dCountryErrorText" style="color : red ; font-size : 10px;display:none">Destination Country is required</div>

                                    </div>
                                    <div class="col-sm-3">
                                        <label for="destination-pincode" class="label-font">Pincode</label>
                                        <input type="text" id="destination-pincode" name="destination-pincode" style="border-radius : 0px" class="form-control   " value="<?php _e($shippingAddress['postcode']) ?>">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group" id="payment-details" style="padding:15px">
                            <div class="header-style" style="width : 90% !important">
                                <span class="header-font">COD Details</span>
                            </div>
                            <div class="container" style="margin-left : 0px">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label for="select" class="label-font">COD Collection Mode <span class="required-text">*</span></label>
                                        <select class="custom-select " name="cod-collection-mode" required="true" id="select-cod-collection-mode" style="border-radius : 0px; height : 33px; font-size:12px">
                                            <option value="<?php _e($order->get_payment_method()); ?>" selected><?php _e($order->get_payment_method()); ?></option>
                                        </select>
                                    </div>
                                    <?php if ($order->get_payment_method() == "cod") { ?>
                                        <div class="col-sm-6">
                                            <label for="cod-amount" class="label-font">COD Amount <span class="required-text">*</span></label>
                                            <input type="number" value="<?php _e($order->get_total()); ?>" id="cod-amount" required="true" name="cod-amount" oninput="this.value = Math.abs(this.value)" style="border-radius : 0px" class="form-control   " readonly>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-sm-6">
                                            <label for="cod-amount" class="label-font">COD Amount <span class="required-text">*</span></label>
                                            <input type="number" value="0" id="cod-amount" required="true" name="cod-amount" style="border-radius : 0px" class="form-control   " readonly>
                                        </div>
                                    <?php } ?>
                                    <div class="col-sm-4">
                                        <label for="customer-notes" class="label-font">Customer Notes</label>
                                        <input type="text" value="<?php _e($order->get_customer_note()); ?>" id="customer-notes" required="false" name="customer-notes" style="border-radius : 0px" class="form-control form-control-lg" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="piece-details" style="padding  : 15px">
                            <div class="header-style" style="width : 90% !important">
                                <span class="header-font">Piece Details</span>
                            </div>
                            <div class="container piece-details-div" id="piece-det" style="margin-left : 0px">
                                <?php
                                $description = array();
                                $declaredValue = 0;
                                $orderItems = $order->get_items();
                                foreach ($orderItems as $key => $item) {
                                    $description[] = (int)$item['quantity'] . ' ' . $item['name'];
                                    $declaredValue += $item['total'] + $item['total_tax'];
                                }
                                ?>
                                <div class="row mb-4" id="piece-detail-1">
                                    <div class="col-sm-3">
                                        <label for="textInput" class="label-font">Description <span class="required-text">*</span></label>
                                        <input type="text" name="description[]" required="true" id="description1" style="border-radius : 0px" class="form-control    description-tag" value="<?php _e(implode(', ', $description)); ?>">
                                        <div class="descText" style="color : red ; font-size : 10px;display:none">Description is required</div>

                                    </div>
                                    <div class="col-sm-3">
                                        <label for="textInput" class="label-font">Weight (in Kg) <span class="required-text">*</span></label>
                                        <input type="number" required="true" name="weight[]" oninput="check(this)" step="any" min="0" style="border-radius : 0px" class="form-control   " value="1">
                                    </div>
                                    <div class="col-sm-1">
                                        <label for="textInput" class="label-font">Length<span class="required-text">*</span></label>
                                        <input type="number" name="length[]" required="true" oninput="this.value = Math.abs(this.value)" style="border-radius : 0px" class="form-control   " value="1">
                                    </div>
                                    <div class="col-sm-1">
                                        <label for="textInput" class="label-font">Breadth<span class="required-text">*</span></label>
                                        <input type="number" name="width[]" required="true" oninput="this.value = Math.abs(this.value)" style="border-radius : 0px" class="form-control   " value="1" style="width:20px;">
                                    </div>
                                    <div class="col-sm-1">
                                        <label for="textInput" class="label-font">Height <span class="required-text">*</span></label>
                                        <input type="number" required="true" name="height[]" oninput="this.value = Math.abs(this.value)" style="border-radius : 0px" class="form-control   " value="1">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="textInput" class="label-font">Declared Value <span class="required-text">*</span></label>
                                        <input type="number" name="declared-value[]" required="true" oninput="this.value = Math.abs(this.value)" id="declared-value<?php _e($orderIds[0]); ?>" style="border-radius : 0px" class="form-control   " value="<?php _e($declaredValue); ?>">
                                        <div class="declaredText" style="color : red ; font-size : 10px;display:none">Declared value required</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    <?php } else if (array_key_exists('error', $response) && $response['error']['statusCode'] == 401) { ?>
                    <div class="alert alert-danger" role="alert"><?php _e($response['error']['reason'] .': ' . 'Please login in Shipsy Configuration!') ?></div>
                
                    <?php } else if (array_key_exists('error', $response)) { ?>
                        <div class="alert alert-danger" role="alert"><?php _e($response['error']['message']) ?></div>

                    <?php } else if (array_key_exists('error', $response)) { ?>
                        <div class="alert alert-danger" role="alert"><?php _e($allAddresses['error'] ?? $validServiceTypes['error']) ?></div>
                    <?php
                }
                    ?>


                    <style>
                        /* Chrome, Safari, Edge, Opera */
                        input::-webkit-outer-spin-button,
                        input::-webkit-inner-spin-button {
                            -webkit-appearance: none;
                            margin: 0;
                        }

                        /* Firefox */
                        input[type=number] {
                            -moz-appearance: textfield;
                        }
                    </style>