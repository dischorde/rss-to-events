<?php
/*
Plugin Name: RSS to The Events Calendar Importer 
Description: Imports Events from an RSS feed to The Events Calendar by Modern Tribe.
Version: 1.0
Author: Katarina Rossi
License:     GPL3

RSS to The Events Calendar Importer is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.
 
RSS to The Events Calendar Importer is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

// Suggested in WordPress documentation
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Runs on plugin uninstall, removes options
register_uninstall_hook( __FILE__, 'rte_rssevents_remove' );

// Set up deactivation function because it only seems to work if here
function rte_deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

// Require helpers file with all other function definitions
require_once dirname( __FILE__ ) . '/helpers.php'; 

// Check to Ensure Proper Requirements
if ( rte_check_requirements() == true ) {
	// Initialize Options and Menu
	add_action( 'admin_init', 'rssevents_options_init' );
	add_action( 'admin_menu', 'rssevents_setup_menu' );

	// Set Up Auto-Import Hook
	add_action( 'rte_auto_import', 'rte_import_rss' );
	rte_schedule_hook();
}

?>