<?php
/**
 * Update counts to database.
 *
 * @package   Top_Ten
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      http://ajaydsouza.com
 * @copyright 2008-2015 Ajay D'Souza
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

	global $wpdb;

	$tptn_settings = get_option( 'ald_tptn_settings' );

	if ( true == $tptn_settings['uninstall_clean_tables'] ) {

		$table_name = $wpdb->base_prefix . "top_ten";
		$table_name_daily = $wpdb->base_prefix . "top_ten_daily";

		$wpdb->query( "DROP TABLE $table_name" );
		$wpdb->query( "DROP TABLE $table_name_daily" );
		delete_option( 'tptn_db_version' );

	}

	if ( true == $tptn_settings['uninstall_clean_options'] ) {

		if ( wp_next_scheduled('ald_tptn_hook') ) {
			wp_clear_scheduled_hook('ald_tptn_hook');
		}
		delete_option( 'ald_tptn_settings' );
	}
?>