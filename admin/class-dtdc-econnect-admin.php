<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://shipsy.io/
 * @since      1.0.0
 *
 * @package    Dtdc_Econnect
 * @subpackage Dtdc_Econnect/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dtdc_Econnect
 * @subpackage Dtdc_Econnect/admin
 * @author     shipsyplugins <pradeep.mishra@shipsy.co.in>
 */
class Dtdc_Econnect_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function enqueue_styles() {
		$valid_pages = array("shipsy-configuration","shipsy-setup","shipsy-vseries","sync-form","manage-form");
		$page = isset($_REQUEST['page'])? sanitize_text_field($_REQUEST['page']) : "" ;
		if(in_array($page,$valid_pages)){
			// adding css files in valid pages
			wp_enqueue_style("ec-bootstrap", DTDC_ECONNECT_URL . 'assets/css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style("ec-datatable", DTDC_ECONNECT_URL . 'assets/css/jquery.dataTables.min.css', array(), $this->version, 'all' );
			wp_enqueue_style("ec-sweetalert", DTDC_ECONNECT_URL . 'assets/css/sweetalert.css', array(), $this->version, 'all' );
		} 	
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$valid_pages = array("shipsy-configuration","shipsy-setup","shipsy-vseries","sync-form","manage-form");
		$page = isset($_REQUEST['page'])? sanitize_text_field($_REQUEST['page']) : "" ;
		if(in_array($page,$valid_pages)){
			// adding js files in valid pages
			wp_enqueue_script("jquery");
			wp_enqueue_script( "ec-bootstrap-js", DTDC_ECONNECT_URL . 'assets/js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( "ec-datatable-js", DTDC_ECONNECT_URL . 'assets/js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( "ec-validate-js", DTDC_ECONNECT_URL . 'assets/js/jquery.validate.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( "ec-sweetalert-js", DTDC_ECONNECT_URL . 'assets/js/sweetalert.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dtdc-econnect-admin.js', array( 'jquery' ), $this->version, false );

		}

	}

	public function wpse_73623_render_hidden_page()
	{
		ob_start(); // started buffer
		include_once(DTDC_ECONNECT_PATH."admin/partials/ec-manage.php"); // included template file
		$template = ob_get_contents(); //reading content
		ob_end_clean(); //closing and cleaning buffer
		_e($template);
		
	}

	public function wpse_73622_render_hidden_page()
	{
		ob_start(); // started buffer
		include_once(DTDC_ECONNECT_PATH."admin/partials/ec-sync-form.php"); // included template file
		$template = ob_get_contents(); //reading content
		ob_end_clean(); //closing and cleaning buffer
		_e($template);
		
	}

	public function shipsy_config_menu(){
		// create menu method

		add_menu_page("Shipsy Configuration","Shipsy Configuration","manage_options","shipsy-configuration",array($this, "shipsy_config"),"dashicons-admin-site-alt3",3);
		// create submenu methods
		add_submenu_page("shipsy-configuration","Configuration","Configuration","manage_options","shipsy-configuration",array($this,"shipsy_config"));
		add_submenu_page("shipsy-configuration","Setup","Setup","manage_options","shipsy-setup",array($this,"shipsy_setup"));
		add_submenu_page("shipsy-configuration","Virtual Series","Virtual Series","manage_options","shipsy-vseries",array($this,"shipsy_vseries"));

		add_submenu_page(null,null,null,'manage_options','sync-form',array($this,"wpse_73622_render_hidden_page"));
		add_submenu_page(null,null,null,'manage_options','manage-form',array($this,"wpse_73623_render_hidden_page"));

	
	}
	//menu and submenu callback functions
	
	public function shipsy_vseries(){
		ob_start(); // started buffer
		include_once(DTDC_ECONNECT_PATH."admin/partials/ec-vseries.php"); // included template file
		$template = ob_get_contents(); //reading content
		ob_end_clean(); //closing and cleaning buffer
		_e($template);
	}
	public function shipsy_setup(){
		ob_start(); // started buffer
		include_once(DTDC_ECONNECT_PATH."admin/partials/ec-setup.php"); // included template file
		$template = ob_get_contents(); //reading content
		ob_end_clean(); //closing and cleaning buffer
		_e($template);
	}

	public function shipsy_config(){
		ob_start(); // started buffer
		include_once(DTDC_ECONNECT_PATH."admin/partials/ec-config.php"); // included template file
		$template = ob_get_contents(); //reading content
		ob_end_clean(); //closing and cleaning buffer
		_e($template);

	}

	public function shipsy_config_submit(){
		include_once(DTDC_ECONNECT_PATH."admin/helper/helper.php");
		$_REQUEST = shipsy_sanitizeArrays( $_REQUEST );
		dtdcConfig($_REQUEST);
	}

	public function shipsy_setup_submit(){
		include_once(DTDC_ECONNECT_PATH."admin/helper/helper.php");
		$_REQUEST = shipsy_sanitizeArrays( $_REQUEST );
		dtdcUpdateAddresses($_REQUEST);
	}

	public function shipsy_sync_submit(){
		include_once(DTDC_ECONNECT_PATH."admin/helper/helper.php");
		// $_REQUEST = shipsy_sanitizeArrays( $_REQUEST );
		dtdcSoftdataapi($_REQUEST);
	}

	public function track_manage_multiple_order($actions){
		$actions['track_multiple'] = 'Track orders';
		$actions['manage_multiple'] = 'Manage orders';

        return $actions;
	}

	public function multiple_track_manage($redirect_to, $action, $post_ids){
		include_once(DTDC_ECONNECT_PATH."admin/helper/helper.php");
		if ( $action === 'manage_multiple'){
			$synced_orders = array();
			$unsynced_orders = array();
			foreach ( $post_ids as $post_id ) {
				$order = wc_get_order($post_id);
				$orderId 	= $order->get_order_number();
				if(dtdcGetShipsyRefNo($orderId) != NULL){
					array_push($synced_orders,$orderId);
				}
				else {
					array_push($unsynced_orders,$orderId);
				}
			}
			$notifications = array();
			$notifications['page'] = "manage-form";
			$notifications['synced_orders'] = $synced_orders;
			return $redirect_to = add_query_arg( $notifications, admin_url('admin.php'), $redirect_to );	
		}

		else if ($action === 'track_multiple'){
			$success_tracked_orders =array();
			$failed_tracked_orders =array();
			$warning_tracked_orders =array();
			$message_tracked_orders =array(); 
			foreach ( $post_ids as $post_id ) {
				$order = wc_get_order($post_id);
				$orderId 	= $order->get_order_number();
				if(dtdcGetShipsyRefNo($orderId) != NULL){
					_e(dtdcGetTrackingUrl($orderId));
					if (dtdcGetTrackingUrl($orderId) == NULL){
						if (addTrackingUrl($orderId)==1){
							array_push($success_tracked_orders,$orderId);
						}
						else {
							array_push($failed_tracked_orders,$orderId);
						}
					}
					else{
						array_push($message_tracked_orders,$orderId);
					}
				}
				else {
					array_push($warning_tracked_orders,$orderId);
				}
			}
			$warning_tracked_orders = implode(' , ', $warning_tracked_orders);
			$success_tracked_orders = implode(' , ', $success_tracked_orders);
			$failure_tracked_orders = implode(' , ', $failed_tracked_orders);
			$message_tracked_orders = implode(' , ', $message_tracked_orders);
			$notifications = array();
			$notifications['post_type'] = "shop_order";
			if (!empty($message_tracked_orders)){
				$notifications['message'] = "Track URL already generated for order Id $message_tracked_orders";
			}
			if(!empty($warning_tracked_orders)){
				$notifications['warning'] = "Order with order Ids $warning_tracked_orders is not synced.";
			}
			if(!empty($success_tracked_orders)){
				$notifications['success'] = "Success: Added track URL for order Ids $success_tracked_orders";
			}
			if(!empty($failure_tracked_orders)){
				$notifications['failure'] = "Failed to add tracking URL for order with order Id $failure_tracked_orders";
			}
			return $redirect_to = add_query_arg( $notifications, admin_url('edit.php'), $redirect_to );	
		}
		else {
			return $redirect_to;
		}
	}

	public function wc_add_column($columns){

		$columns['sync_order'] = 'Sync Order';
		$columns['track_order'] = 'Track Order';
		return $columns;

	}


	public function column_value($column){
		include_once(DTDC_ECONNECT_PATH."admin/helper/helper.php");
		global $post;
		global $wpdb;
		$url 		= admin_url('admin.php?page=sync-form');
		$order  	= wc_get_order( sanitize_text_field($post->ID) );
		$orderId 	= $order->get_order_number();
		if ( 'sync_order' === $column ) {
			$url = (add_query_arg( array( 'orderid' => $orderId ),  $url));
			if (dtdcGetShipsyRefNo(sanitize_text_field($orderId))){
				_e(dtdcGetShipsyRefNo($orderId));
			}
			else{
				_e( '<a href="' . $url . '"> Sync Order </a>');
			}
		}
		if ( 'track_order' === $column ) {
			if(dtdcGetShipsyRefNo(sanitize_text_field($orderId)) != NULL){
				if (dtdcGetTrackingUrl($orderId)){
					_e('<a href="' . dtdcGetTrackingUrl(sanitize_text_field($orderId)) . '"> Tracking Link </a>');
				}
				else{
					_e( "Not Tracked");
				}
			}
			else {
				_e("Not Synced");
			}

		}
	}

	public function sync_enqueue_scripts(){
		$shippingAddress = array();
		if (isset($_GET['orderid'])){
			$orderid = sanitize_text_field($_GET['orderid']);
			$order = wc_get_order(sanitize_text_field($orderid));			
			$shippingAddress['name']=$order->get_formatted_shipping_full_name();
			$shippingAddress['state']=$order->get_shipping_state();
			$shippingAddress['country']=$order->get_shipping_country();
			$shippingAddress['city']=$order->get_shipping_city();
			$shippingAddress['pincode']=$order->get_shipping_postcode();
			$shippingAddress['address_1']=$order->get_shipping_address_1();
			$shippingAddress['address_2']=$order->get_shipping_address_2();
			$shippingAddress['phone']=$order->get_billing_phone();
		}

		include_once(DTDC_ECONNECT_PATH."admin/helper/helper.php");	
		$response = dtdcGetAddresses();
		if (array_key_exists('data', $response) && !empty($response['data'])) {
			wp_enqueue_script('custom-script',plugins_url("/js/dtdc-econnect-admin.js"),array('jquery'), null, true);
			wp_localize_script('custom-script','php_data',array(
				"message" => $response, "shippingAddress" => $shippingAddress
			));	
		}
	}

	public function notice_messages(){
			if ( isset($_GET['success'])) {
				?>
				<div class="notice notice-success is-dismissible">
					<p><?php echo esc_attr(($_GET['success'])); ?></p>
				</div>
				<?php
			}	 
			if(isset($_GET['failure'])) {
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php echo esc_attr(($_GET['failure'])) ?></p>
				</div>
				<?php
			}
			if(isset($_GET['warning'])) {
				?>
				<div class="notice notice-warning is-dismissible">
					<p><?php echo esc_attr(($_GET['warning'])) ?></p>
				</div>
				<?php
			} 
			if(isset($_GET['message'])) {
				?>
				<div class="notice notice-info is-dismissible">
					<p><?php echo esc_attr(($_GET['message'])) ?></p>
				</div>
				<?php
			} 
	}
}
