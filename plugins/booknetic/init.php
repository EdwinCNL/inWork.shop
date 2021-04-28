<?php
/*
 * Plugin Name: Booknetic
 * Description: WordPress Appointment Booking and Scheduling system
 * Version: 2.3.3
 * Author: FS-Code
 * Author URI: https://www.booknetic.com
 * License: Commercial
 * Text Domain: booknetic
 */

defined( 'ABSPATH' ) or exit;

if ( is_admin() ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$bkntc_version = get_plugin_data( __FILE__ )['Version'];
	update_site_option( 'bkntc_plugin_version', $bkntc_version );
	update_site_option( 'bkntc_purchase_code', 'purchase_code' );
	update_site_option( 'bkntc_plugin_alert', '' );
	update_site_option( 'bkntc_plugin_disabled', '0' );
	update_site_option( 'bkntc_license_last_checked_time', time() );

	$bkntc_version = str_replace( '.', '_', $bkntc_version );
	
	global $wpdb;
	if ( empty ( get_site_option( 'bkntc_plugin_installed_' . $bkntc_version ) ) ) {
		$bkntc_data = file_get_contents( __DIR__.'/install.dat' );
		$bkntc_data = json_decode( $bkntc_data , true );
		if ( isset( $bkntc_data['sql'] ) ) {
			$sql = str_replace( [ '{tableprefix}', '{tableprefixbase}' ] , [ ( $wpdb->base_prefix . 'bkntc_' ), $wpdb->base_prefix ] , base64_decode( $bkntc_data['sql'] ) );

			foreach( explode(';' , $sql) AS $sqlQueryOne ) {
				$checkIfEmpty = preg_replace('/\s/', '', $sqlQueryOne);
				if( !empty( $checkIfEmpty ) ) {
					$wpdb->query( $sqlQueryOne );
				}
			}

			update_site_option( 'bkntc_plugin_installed_' . $bkntc_version, '1' );
		}
	}
}

require_once __DIR__ . '/vendor/autoload.php';

new \BookneticApp\Providers\Bootstrap();
