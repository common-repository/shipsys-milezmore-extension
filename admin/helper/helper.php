<?php

function shipsy_sanitizeArrays( $input ) {

    // Initialize the new array that will hold the sanitize values
    $new_input = array();

    // Loop through the input and sanitize each of the values
    foreach ( $input as $key => $val ) {
        $new_input[ $key ] = sanitize_text_field( $val );
    }

    return $new_input;

}

function dtdcGetAwbNumber($synced_orders){
    $headers = array(
        'Content-Type'=> 'application/json',
        'organisation-id' => getOrgId(),
        'shop-origin'=> 'wordpress',
        'shop-url' => dtdcGetShopUrl(),
        'customer-id' => dtdcGetCustId(),
        'access-token'=> dtdcGetAccessToken()
    );
    
    $dataToSendJson = json_encode(array('customerReferenceNumberList' => $synced_orders));
    $args = array(
        'body'        => $dataToSendJson,
        'timeout'     => '5',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => $headers,
    );
    $response = wp_remote_post( 'https://app.shipsy.in/api/ecommerce/getawbnumber', $args );
    $result = wp_remote_retrieve_body( $response );
    $resultdata = json_decode($result, true);
    return $resultdata;

}


function dtdcGetVirtualSeries(){
    $headers = array(
        'Content-Type'=> 'application/json', 
        'organisation-id' => getOrgId(),  
        'shop-origin'=> 'wordpress',
        'shop-url' => dtdcGetShopUrl(),
        'customer-id' => dtdcGetCustId(),
        'access-token'=> dtdcGetAccessToken()
    );
    $args = array(
        'headers'     => $headers,
    );
    $response   = wp_remote_get( 'https://app.shipsy.in/api/ecommerce/getSeries',$args);
    $result     = wp_remote_retrieve_body( $response );
    $resultdata = json_decode($result, true);    
    return $resultdata;

}

function dtdcGetAddresses() {
    $headers = array(
        'Content-Type'=> 'application/json', 
        'organisation-id' => getOrgId(),  
        'shop-origin'=> 'wordpress',
        'shop-url' => dtdcGetShopUrl(),
        'customer-id' => dtdcGetCustId(),
        'access-token'=> dtdcGetAccessToken()
    );
    $args = array(
        'timeout'     => '5',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => $headers,
    );
    $response = wp_remote_post( 'https://app.shipsy.in/api/ecommerce/getshopdata', $args );
    $result = wp_remote_retrieve_body( $response );
    $resultdata = json_decode($result, true);
    $resultdata[organisationId] = getOrgId();
    return $resultdata;
}

function dtdcConfig($postRequestParams){
    $headers = array(
        'Content-Type'=> 'application/json', 
        'organisation-id' => getOrgId(),  
        'shop-origin'=> 'wordpress',
        'shop-url' => dtdcGetShopUrl(),
    );
    $dataToSendArray = array(      
                'username' => $postRequestParams['user-name'],
                'password' => $postRequestParams['password']
    );
    $dataToSendJson = json_encode($dataToSendArray);
    $args = array(
        'body'        => $dataToSendJson,
        'timeout'     => '5',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => $headers,
    );
    $response = wp_remote_post( 'https://app.shipsy.in/api/ecommerce/registershop', $args );
    $result = wp_remote_retrieve_body( $response );
    $resultdata = json_decode($result, true);    
    $notifications = array();
    $notifications['page'] = "shipsy-configuration";
    
    if (array_key_exists('data', $resultdata)) {
		if (array_key_exists('access_token', $resultdata['data'])) {
            $accesstoken = $resultdata["data"]["access_token"];
            $notifications["success"] = "Configuration is successful";
            setcookie("access_token",$accesstoken,time() + 24 * 3600 * 30);
		}
		if (array_key_exists('customer', $resultdata['data']) && 
			array_key_exists('id', $resultdata["data"]["customer"]) && 
			array_key_exists('code', $resultdata['data']['customer'])) {
			$customerId = $resultdata["data"]['customer']['id'];
            $customerCode = $resultdata["data"]['customer']['code'];
            setcookie("cust_id",$customerId, time() + 24 * 3600 * 30);
            setcookie("cust_code",$customerCode, time() + 24 * 3600 * 30);
		}
	}
    else{
        $notifications["failure"] = $resultdata['error']['message']; 
    }
    wp_safe_redirect( add_query_arg( $notifications, admin_url( 'admin.php' ) ) );
    
}

function dtdcUpdateAddresses($postRequestParams) {
    
    $headers = array(
        'Content-Type'=> 'application/json', 
        'organisation-id' => getOrgId(),  
        'shop-origin'=> 'wordpress',
        'shop-url' => dtdcGetShopUrl(),
        'customer-id' => dtdcGetCustId(),
        'access-token'=> dtdcGetAccessToken()
    );

    if (isset($postRequestParams['useForwardCheck']) && $postRequestParams['useForwardCheck'] === 'true') {
        $useForwardAddress = true;
        $reverseAddress = array (
            'name' => $postRequestParams['forward-name'],
            'phone' => $postRequestParams['forward-phone'],
            'alternate_phone' => $postRequestParams['forward-alt-phone'] ?? '',
            'address_line_1' => $postRequestParams['forward-line-1'],
            'address_line_2' => $postRequestParams['forward-line-2'],
            'pincode' => $postRequestParams['forward-pincode'],
            'city' => $postRequestParams['forward-city'],
            'state' => $postRequestParams['forward-state'],
            'country' => $postRequestParams['forward-country']
        );
    }
    else {
        $useForwardAddress = false;
        $reverseAddress = array(
            'name' => $postRequestParams['reverse-name'],
            'phone' => $postRequestParams['reverse-phone'],
            'alternate_phone' => $postRequestParams['reverse-alt-phone'] ?? '',
            'address_line_1' => $postRequestParams['reverse-line-1'],
            'address_line_2' => $postRequestParams['reverse-line-2'],
            'pincode' => $postRequestParams['reverse-pincode'],
            'city' => $postRequestParams['reverse-city'],
            'state' => $postRequestParams['reverse-state'],
            'country' => $postRequestParams['reverse-country']
        );
    }
    $dataToSendArray = array(
        'forwardAddress' => array(
            'name' => $postRequestParams['forward-name'],
            'phone' => $postRequestParams['forward-phone'],
            'alternate_phone' => $postRequestParams['forward-alt-phone'] ?? '',
            'address_line_1' => $postRequestParams['forward-line-1'],
            'address_line_2' => $postRequestParams['forward-line-2'],
            'pincode' => $postRequestParams['forward-pincode'],
            'city' => $postRequestParams['forward-city'],
            'state' => $postRequestParams['forward-state'],
            'country' => $postRequestParams['forward-country']
        ),
        'reverseAddress' => $reverseAddress,
        'useForwardAddress' => $useForwardAddress,
        'returnAddress' => array(
            'name' => $postRequestParams['return-name'],
            'phone' => $postRequestParams['return-phone'],
            'alternate_phone' =>$postRequestParams['return-alt-phone'] ?? '',
            'address_line_1' => $postRequestParams['return-line-1'],
            'address_line_2' => $postRequestParams['return-line-2'],
            'pincode' => $postRequestParams['return-pincode'],
            'city' => $postRequestParams['return-city'],
            'state' => $postRequestParams['return-state'],
            'country' => $postRequestParams['return-country']
        ),
        'exceptionalReturnAddress' => array(
            'name' => $postRequestParams['exp-return-name'],
            'phone' => $postRequestParams['exp-return-phone'],
            'alternate_phone' =>$postRequestParams['exp-return-alt-phone'] ?? '',
            'address_line_1' => $postRequestParams['exp-return-line-1'],
            'address_line_2' => $postRequestParams['exp-return-line-2'],
            'pincode' => $postRequestParams['exp-return-pincode'],
            'city' => $postRequestParams['exp-return-city'],
            'state' => $postRequestParams['exp-return-state'],
            'country' => $postRequestParams['exp-return-country']
        )
        
    );
    $dataToSendJson = json_encode($dataToSendArray);

    $args = array(
        'body'        => $dataToSendJson,
        'timeout'     => '5',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => $headers,
    );
    $response = wp_remote_post( 'https://app.shipsy.in/api/ecommerce/updateaddress', $args );
    $result = wp_remote_retrieve_body( $response );
    $array2 = json_decode($result, true);
 
    $notifications = array();
    $notifications['page'] = "shipsy-setup";
    if (is_array($array2)){
        if (array_key_exists('success',$array2)){
            if ($array2['success']==1){
                $notifications["success"]  = "Setup is Succesful"; 
            }
        }
        else {
            $notifications["failure"] = $array2['error']['message'];
        }
    }
    wp_safe_redirect( add_query_arg( $notifications, admin_url( 'admin.php' ) ) );
    
}

function dtdcGetCustId() {
    return sanitize_text_field($_COOKIE['cust_id']);
} 

function dtdcGetAccessToken() {
    return sanitize_text_field($_COOKIE['access_token']);
}

function dtdcGetCustCode() {
    return sanitize_text_field($_COOKIE['cust_code']);
}

function dtdcGetShopUrl(){
    return get_bloginfo('wpurl');
}

function dtdcAddSynctrack($data){
    global $wpdb;
    $table = $wpdb->prefix.'sync_track_order';
    $data = array('orderId' => $data['orderId'], 'shipsy_refno' => $data['shipsy_refno']);
    $format = array('%s','%s');
    $wpdb->insert($table,$data,$format);
    
}

function dtdcGetShipsyRefNo($orderId){
    global $wpdb;
    $value = $wpdb->get_var( $wpdb->prepare(
        " SELECT shipsy_refno FROM {$wpdb->prefix}sync_track_order WHERE orderId = %d ", $orderId
    ) );
    return $value;
}

function dtdcGetTrackingUrl($orderId){
    global $wpdb;
    $value = $wpdb->get_var( $wpdb->prepare(
        " SELECT track_url FROM {$wpdb->prefix}sync_track_order WHERE orderId = %d ", $orderId
    ) );
    return $value;
}

function addTrackingUrl($orderId){
    global $wpdb;
    $headers = array(
        'Content-Type'=> 'application/json', 
        'organisation-id' => getOrgId(),  
        'shop-origin'=> 'wordpress',
        'shop-url' => dtdcGetShopUrl(),
        'customer-id' => dtdcGetCustId(),
        'access-token'=> dtdcGetAccessToken()
    );
    $data['cust_refno'] = $orderId;
    $dataToSendJson = json_encode(array('customerReferenceNumberList' => [$data['cust_refno']]));
    $args = array(
        'body'        => $dataToSendJson,
        'timeout'     => '5',
        'redirection' => '5',
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => $headers,
    );
    $response = wp_remote_post( 'https://app.shipsy.in/api/ecommerce/gettracking', $args );
    $result = wp_remote_retrieve_body( $response );
    $array2 = json_decode($result, true); 
    if (!empty($array2['data']) and $array2['success']==1){
        $track_url = $array2['data'][$orderId];
        $table_name = $wpdb->prefix.'sync_track_order';
        $wpdb->query($wpdb->prepare("UPDATE $table_name SET track_url='$track_url' WHERE orderId=$orderId"));
        return true;
    }
    else {
        return false;
    }
}

function dtdcSoftdataapi($postRequestParams){
    // print_r($postRequestParams);
	$samePiece = false;
    	if (array_key_exists('multiPieceCheck', sanitize_text_field($_POST))) {
            $samePiece = true;
    	}
		$result = dtdcGetAddresses();
        $result = $result['data'];
        // print_r($result);
        
		$dataToSendArray = [
				'consignments' =>  [
				[
					'customer_code' => dtdcGetCustCode(),
					'consignment_type' => $postRequestParams['consignment-type'],
					'service_type_id' => $postRequestParams['service-type'],
					'reference_number' => $postRequestParams['awb-number'],
					'load_type' => $postRequestParams['courier-type'],
					'customer_reference_number' => $postRequestParams['customer-reference-number'],
					'commodity_name' => 'Other',
					'num_pieces' => $postRequestParams['num-pieces'],
					'origin_details' =>  [
					'name' => $postRequestParams['origin-name'],
					'phone' => $postRequestParams['origin-number'],
				    'alternate_phone' => ($postRequestParams['origin-alt-number']== "")?$postRequestParams['origin-number']:$postRequestParams['origin-alt-number'] ,
					// 'alternate_phone' => $postRequestParams['origin-alt-number'],
					'address_line_1' => $postRequestParams['origin-line-1'],
					'address_line_2' => $postRequestParams['origin-line-2'],
					'pincode' => $postRequestParams['origin-pincode'] ?? '',
					'city' => $postRequestParams['origin-city'],
					'state' => $postRequestParams['origin-state'],
					'country' => $postRequestParams['origin-country'],
				    ],
					'destination_details' =>  [
					'name' => $postRequestParams['destination-name'],
					'phone' => $postRequestParams['destination-number'],
				    'alternate_phone' => ($postRequestParams['destination-alt-number']== "")?$postRequestParams['destination-number']:$postRequestParams['destination-alt-number'] ,
					'address_line_1' => $postRequestParams['destination-line-1'],
					'address_line_2' => $postRequestParams['destination-line-2'],
					'pincode' => $postRequestParams['destination-pincode'] ?? '',
    				'city' => $postRequestParams['destination-city'],
					'state' => $postRequestParams['destination-state'],
					'country' => $postRequestParams['destination-country'],
				    ],
					'same_pieces' => $samePiece,
					'cod_favor_of' => '',
				    'pieces_detail' => [],
					'cod_collection_mode' =>  ($postRequestParams['cod-collection-mode']=='cod')?'cash':"",
					'cod_amount' => $postRequestParams['cod-amount'],
                    'notes' => $postRequestParams['customer-notes'],
					"return_details"=> [
						"name"				=> $result['reverseAddress']['name'],
						"phone"				=> $result['reverseAddress']['phone'],
						"alternate_phone"	=> $result['reverseAddress']['alternate_phone'],
						"address_line_1"	=> $result['reverseAddress']['address_line_1'],
						"address_line_2"	=> $result['reverseAddress']['address_line_2'],
						"pincode"			=> $result['reverseAddress']['pincode'],
						"city"				=> $result['reverseAddress']['city'],
						"state"				=> $result['reverseAddress']['state']
					],
                    "rto_details"=> [
						"name"				=> $result['returnAddress']['name'],
						"phone"				=> $result['returnAddress']['phone'],
						"alternate_phone"	=> $result['returnAddress']['alternate_phone'],
						"address_line_1"	=> $result['returnAddress']['address_line_1'],
						"address_line_2"	=> $result['returnAddress']['address_line_2'],
						"pincode"			=> $result['returnAddress']['pincode'],
						"city"				=> $result['returnAddress']['city'],
						"state"				=> $result['returnAddress']['state']
					],
					"exceptional_return_details"=> [
						"name"				=> $result['exceptionalReturnAddress']['name'],
						"phone"				=> $result['exceptionalReturnAddress']['phone'],
						"alternate_phone"	=> $result['exceptionalReturnAddress']['alternate_phone'],
						"address_line_1"	=> $result['exceptionalReturnAddress']['address_line_1'],
						"address_line_2"	=> $result['exceptionalReturnAddress']['address_line_2'],
						"pincode"			=> $result['exceptionalReturnAddress']['pincode'],
						"city"				=> $result['exceptionalReturnAddress']['city'],
						"state"				=> $result['exceptionalReturnAddress']['state']
					]
				],
			]
		];


		if ($postRequestParams['num-pieces'] === 1 || $samePiece === true) {
    			$temp_pieces_details = [
					'description' => $postRequestParams['description'],
					'declared_value' => $postRequestParams['declared-value'],
					'weight' => $postRequestParams['weight'],
					'height' => $postRequestParams['height'],
					'length' => $postRequestParams['length'],
					'width' => $postRequestParams['width']
			    ];
				array_push($dataToSendArray['consignments'][0]['pieces_detail'], $temp_pieces_details);
		}
		else {
			for($index = 0; $index < $postRequestParams['num-pieces']; $index++) {
	    		$temp_pieces_details = [
				'description' => $postRequestParams['description'][$index],
				'declared_value' => $postRequestParams['declared-value'][$index],
				'weight' => $postRequestParams['weight'][$index],
				'height' => $postRequestParams['height'][$index],
				'length' => $postRequestParams['length'][$index],
				'width' => $postRequestParams['width'][$index]
			];
			array_push($dataToSendArray['consignments'][0]['pieces_detail'], $temp_pieces_details);
		    };
        }

        $headers = array(
            'Content-Type'=> 'application/json', 
            'organisation-id' => getOrgId(),  
            'shop-origin'=> 'wordpress',
            'shop-url' => dtdcGetShopUrl(),
            'customer-id' => dtdcGetCustId(),
            'access-token'=> dtdcGetAccessToken()
        );
		$dataToSendJson = json_encode($dataToSendArray);
        $args = array(
            'body'        => $dataToSendJson,
            'timeout'     => '30',
            'headers'     => $headers,
        );
        $response = wp_remote_post( 'https://app.shipsy.in/api/ecommerce/softdata', $args );
        $result = wp_remote_retrieve_body( $response );
        $array = json_decode($result, true);
		$data = array();
        $data['orderId'] = $postRequestParams['customer-reference-number'];
        $notifications = array();
        $notifications['post_type'] = "shop_order";
        if (array_key_exists('data', $array) && array_key_exists('reference_number', $array['data'][0])) {
            $data['shipsy_refno'] = $array['data'][0]["reference_number"];
		    dtdcAddSynctrack($data);
			$notifications['success'] = "Order is Synced Successfully!";
        }
        else {
            if (array_key_exists('data', $array) && array_key_exists('message', $array['data'][0])) {
                $notifications['message'] = $array['data'][0]['message'];
            }
            else {
                $notifications['failure'] = $array['error']['message'];
            }
        }
        wp_safe_redirect( add_query_arg( $notifications, admin_url('edit.php')));
}

function getOrgId(){
    $OrgId = 'milezmore';
    return $OrgId;
}