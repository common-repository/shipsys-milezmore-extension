

jQuery(function(){

	

	function disableFieldStyle(errorTextClass, selectedFieldId){
		jQuery(errorTextClass).css({display:'block'});
		jQuery(selectedFieldId).css("border-color", "red");
		jQuery('#softdataSubmitButton').prop('disabled', true);
		jQuery('#softdataSubmitButton').css("border-color", "grey");
		jQuery('#softdataSubmitButton').css("background-color", "grey");
	}

	function enableFieldStyle(errorTextClass , selectedFieldId){
		jQuery(errorTextClass).css({display : "none"});
		jQuery(selectedFieldId).css("border-color", "grey");
		jQuery('#softdataSubmitButton').prop('disabled', false);
		jQuery('#softdataSubmitButton').css("border-color", '#eb5202');
		jQuery('#softdataSubmitButton').css("background-color", '#eb5202');
		}
	  

	jQuery('#useForwardCheck').on('change', function(){
		if(this.checked) {
			jQuery('#reverse-address').find("*").prop('disabled', true);
		}
		else {
			jQuery('#reverse-address').find("*").prop('disabled', false);
		}
	});   
	
	jQuery('#select-consignment-type').change( function() {

			const allAddresses = php_data.message['data'];
			const forwardAddress = allAddresses['forwardAddress'];
			const reverseAddress = allAddresses['reverseAddress'];
			const exceptionalReturnAddress = allAddresses['exceptionalReturnAddress'];
			const validServiceTypes = allAddresses['serviceTypes'];
			const shippingAddress = php_data.shippingAddress;

		const selectedValue = jQuery(this).val();
		if (selectedValue === 'reverse') {
			/*
			For reverse consignment type
			Origin details - Shipping Address
			Destination details - Reverse Address (getting value for Shipsy)
			*/
			document.getElementById("origin-name").value = shippingAddress['name'];
			document.getElementById("origin-number").value = shippingAddress['phone'];
			document.getElementById("origin-alt-number").value = shippingAddress['phone'];
			document.getElementById("origin-line-1").value = shippingAddress['address_1'];
			document.getElementById("origin-line-2").value = shippingAddress['address_2'];
			document.getElementById("origin-city").value = shippingAddress['city'];
			document.getElementById("origin-state").value = shippingAddress['state'];
			document.getElementById("origin-country").value = shippingAddress['country'];
			document.getElementById("origin-pincode").value = shippingAddress['pincode'];

			document.getElementById("destination-name").value = reverseAddress['name'];
			document.getElementById("destination-number").value = reverseAddress['phone'];
			document.getElementById("destination-alt-number").value = reverseAddress['alternate_phone'];
			document.getElementById("destination-line-1").value = reverseAddress['address_line_1'];
			document.getElementById("destination-line-2").value = reverseAddress['address_line_2'];
			document.getElementById("destination-city").value = reverseAddress['city'];
			document.getElementById("destination-state").value = reverseAddress['state'];
			document.getElementById("destination-country").value = reverseAddress['country'];
			document.getElementById("destination-pincode").value = reverseAddress['pincode'];
		}
		else if (selectedValue === 'forward') {
			/*
			For forward consignment type
			Origin details - Forward Address (getting value for Shipsy)
			Destination details - Shipping Address 
			*/
		   document.getElementById("origin-name").value = forwardAddress['name'];
		   document.getElementById("origin-number").value = forwardAddress['phone'];
		   document.getElementById("origin-alt-number").value = forwardAddress['alternate_phone'];
		   document.getElementById("origin-line-1").value = forwardAddress['address_line_1'];
		   document.getElementById("origin-line-2").value = forwardAddress['address_line_2'];
		   document.getElementById("origin-city").value = forwardAddress['city'];
		   document.getElementById("origin-state").value = forwardAddress['state'];
		   document.getElementById("origin-country").value = forwardAddress['country'];
		   document.getElementById("origin-pincode").value = forwardAddress['pincode'];
		   
		   document.getElementById("destination-name").value = shippingAddress['name'];
		   document.getElementById("destination-number").value = shippingAddress['phone'];
		   document.getElementById("destination-alt-number").value = shippingAddress['phone'];
		   document.getElementById("destination-line-1").value = shippingAddress['address_1'];
		   document.getElementById("destination-line-2").value = shippingAddress['address_2'];;
		   document.getElementById("destination-city").value = shippingAddress['city'];
		   document.getElementById("destination-state").value = shippingAddress['state'];
		   document.getElementById("destination-country").value = shippingAddress['country'];
		   document.getElementById("destination-pincode").value = shippingAddress['pincode'];
		}
	});

	jQuery('#multiPieceCheck').on('change', function(){
		if(this.checked) {
			var divlength =  jQuery("#piece-det > div").length;
			if(divlength -1 >0){
				var flag = divlength;
				for(var i=0;i<divlength-1;i++){
					jQuery('#piece-detail-'+flag).remove();
					flag -- ;
				}
			}
		} else {
			const numpieceval  = jQuery('#num-pieces').val();
			var pieceDet1 = jQuery('#piece-detail-1');
			if(numpieceval>0){
				 var newCount = 2;
				 for(var i=0;i<numpieceval-1;i++){
					pieceDet1.clone().attr('id', 'piece-detail-'+ newCount).appendTo("#piece-det");
					jQuery('#piece-detail-'+newCount).find('input:text').val('');
					jQuery('#piece-detail-'+newCount).find('input:text').attr('id', 'description'+newCount);
					jQuery('#piece-detail-'+newCount).find("input[name^='weight']").val('0');
					jQuery('#piece-detail-'+newCount).find("input[name^='length']").val('1');
					jQuery('#piece-detail-'+newCount).find("input[name^='width']").val('1');
					jQuery('#piece-detail-'+newCount).find("input[name^='height']").val('1');
					jQuery('#piece-detail-'+newCount).find("input[name^='declared-value']").val('0');
					newCount++;

				 }
			}
		}
	}); 

	jQuery('#num-pieces').on('change keyup', function(){
        if(jQuery(this).val() == 0){
            disableFieldStyle('.numpiecesError', '#num-pieces');
        }
        else {
            enableFieldStyle('.numpiecesError', '#num-pieces');
        }
        var checklength  = jQuery("#piece-det > div").length;
        var pieceDetail1 = jQuery('#piece-detail-1');
        var diff  = jQuery(this).val() - checklength;
        var multicheckval = jQuery('#multiPieceCheck').prop('checked');
        if(jQuery(this).val()>0  && !multicheckval){
            if(diff > 0){
                var curr = checklength+1;
                    for(var i =0 ;i<diff;i++){
                        pieceDetail1.clone().attr('id', 'piece-detail-'+ curr).appendTo("#piece-det");
                        jQuery('#piece-detail-'+curr).find('input:text').val('');
                        jQuery('#piece-detail-'+curr).find('input:text').attr('id', 'description'+curr);
                        jQuery('#piece-detail-'+curr).find("input[name^='weight']").val('0');
                        jQuery('#piece-detail-'+curr).find("input[name^='length']").val('1');
                        jQuery('#piece-detail-'+curr).find("input[name^='width']").val('1');
                        jQuery('#piece-detail-'+curr).find("input[name^='height']").val('1');
                        jQuery('#piece-detail-'+curr).find("input[name^='declared-value']").val('0');
                        curr++;
                    }
            } else {
                var rem = jQuery("#piece-det > div").length;
                for(var i=0;i<Math.abs(diff);i++){
                    jQuery('#piece-detail-'+rem).remove();
                    rem--;
                }
            }
        }
            
	});
	
	jQuery('#origin-name').keyup(function(){
		if(jQuery(this).val() == ''){
			disableFieldStyle('.nameErrorText', '#origin-name');
		}
		else {
			enableFieldStyle('.nameErrorText', '#origin-name');
		}
		});
	
		jQuery('#origin-number').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.phoneErrorText', '#origin-number');
			}
			else {
				enableFieldStyle('.phoneErrorText', '#origin-number');
			}
		});
	
		jQuery('#origin-line-1').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.addressErrorText', '#origin-line-1');
			}
			else {
				enableFieldStyle('.addressErrorText', '#origin-line-1');
			}
		});
	
		jQuery('#origin-city').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.cityErrorText', '#origin-city');
			}
			else {
				enableFieldStyle('.cityErrorText', '#origin-city');
			}
		});
	
		jQuery('#origin-state').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.stateErrorText', '#origin-state');
			}
			else {
				enableFieldStyle('.stateErrorText', '#origin-state');
			}
		});
	
		jQuery('#origin-country').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.countryErrorText', '#origin-country');
			}
			else {
				enableFieldStyle('.countryErrorText', '#origin-country');
			}
		});
	
		jQuery('#destination-name').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.dNameErrorText', '#destination-name');
			}
			else {
				enableFieldStyle('.dNameErrorText', '#destination-name');
			}
		});
	
		jQuery('#destination-number').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.dPhoneErrorText', '#destination-number');
			}
			else {
				enableFieldStyle('.dPhoneErrorText', '#destination-number');
			}
		});
	
		jQuery('#destination-line-1').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.dAddressErrorText', '#destination-line-1');
			}
			else {
				enableFieldStyle('.dAddressErrorText', '#destination-line-1');
			}
		});
	
		jQuery('#destination-city').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.dCityErrorText', '#destination-city');
			}
			else {
				enableFieldStyle('.dCityErrorText', '#destination-city');
			}
		});
	
		jQuery('#destination-state').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.dStateErrorText', '#destination-state');
			}
			else {
				enableFieldStyle('.dStateErrorText', '#destination-state');
			}
		});
	
		jQuery('#destination-country').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.dCountryErrorText', '#destination-country');
			}
			else {
				enableFieldStyle('.dCountryErrorText', '#destination-country');
			}
		});
	
		jQuery('#customer-reference-number').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.orderText', '#customer-reference-number');
			}
			else {
				enableFieldStyle('.orderText', '#customer-reference-number');
			}
		});
	
		jQuery('.description-tag').keyup(function(){
			if(jQuery(this).val() == ''){
				disableFieldStyle('.descText', '');
			}
			else {
				enableFieldStyle('.descText', '');
			}
		});

			
});

async function getShippingLabel(ref_no,shop_url,id) {
	const referenceNumber = ref_no;
	const cookieValue = Object.fromEntries(document.cookie.split('; ').map(c => {
				  const [ key, ...v ] = c.split('=');
				  return [ key, v.join('=') ];
			  }));

	const url = 'https://app.shipsy.in/api/ecommerce/shippinglabel/link?reference_number=' + referenceNumber;
	let response =  await fetch(url, {
		method: 'GET',
		headers: {
				  'Content-Type': 'application/json',
				  'organisation-id':'milezmore' ,
				  'shop-url': shop_url,
				  'shop-origin':'wordpress',
				  'customer-id': cookieValue['cust_id'],
				  'access-token': cookieValue['access_token']
			  },
		  });
		let data = await response.json();
		if ('data' in data) {
			const requiredData = data.data;
			document.getElementById(id).innerHTML = "Download"; 
			document.getElementById(id).className = "btn btn-link";
			document.getElementById(id).onclick = function () {
			location.href = requiredData.url;
		  };
			
		  }
		else {
			alert("Error occurred while generating label: " + data.error.message);
		  }
}


async function cancelOrderOnClick(ref_no,shop_url,id) {
		const referenceNumberList = [ref_no];
		const cookieValue = Object.fromEntries(document.cookie.split('; ').map(c => {
					  const [ key, ...v ] = c.split('=');
					  return [ key, v.join('=') ];
				  }));
  
		const url = 'https://app.shipsy.in/api/ecommerce/cancelconsignment';
	  if (confirm("Are you sure you want to cancel the consignment?") == true) {  
		let response =  await fetch(url, {
			method: 'POST',
			headers: {
					  'Content-Type': 'application/json',
					  'organisation-id':'milezmore' ,
					  'shop-url': shop_url,
					  'shop-origin':'wordpress',
					  'customer-id': cookieValue['cust_id'],
					  'access-token': cookieValue['access_token']
				  },
			body: JSON.stringify({'referenceNumberList': referenceNumberList})
			  });
			let data = await response.json();
			if (data.success) {
				document.getElementById(id).innerHTML = "Cancelled"; 
				document.getElementById(id).disabled = true;
				document.getElementById(id.split("_")[0]).style.visibility="hidden";
  
			  }
			else {
				alert(data.failures[0].message);
			  }
	  }
	}
  
function check(input) {
	if (input.value == 0) {
		input.setCustomValidity('The number must not be greater than zero.');
	} else {
		input.setCustomValidity('');
	}
}