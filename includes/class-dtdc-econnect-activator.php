<?php

/**
 * Fired during plugin activation
 *
 * @link       https://shipsy.io/
 * @since      1.0.0
 *
 * @package    Dtdc_Econnect
 * @subpackage Dtdc_Econnect/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Dtdc_Econnect
 * @subpackage Dtdc_Econnect/includes
 * @author     shipsyplugins <pradeep.mishra@shipsy.co.in>
 */
class Dtdc_Econnect_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public function activate() {

		global $wpdb;
		// to create table if the table does not exists
		if ($wpdb->get_var("SHOW tables like '".$this->wp_sync_track_order()."'")!=$this->wp_sync_track_order()){
			// dynamic table generating code while activating plugin
			$sql = "CREATE TABLE `".$this->wp_sync_track_order()."` (
				`orderId` int(11) NOT NULL,
				`shipsy_refno` varchar(100) DEFAULT NULL,
				`track_url` varchar(300) DEFAULT NULL,
				PRIMARY KEY (`orderId`)
			   ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
	
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}

	public function wp_sync_track_order(){
		global $wpdb;
		return $wpdb->prefix."sync_track_order"; 
	}

}
