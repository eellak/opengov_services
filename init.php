<?php
	
	define('LEAVES_DEBUG_USER_NAME', 'Fotis Routsis');
	define('LEAVES_DEBUG_USER_EMAIL', 'fotis.routsis@gmail.com');
	
    //Επιλογή php αρχείων για εισαγωγή
	require_once(ABSPATH.'apps/services/functions.php');
	require_once(ABSPATH.'apps/services/views.php');
	
	init_services();
	
	function init_services(){
		services_sidebar();		// Initiate the menus
		prepare_pages();		// Prepare pages (css, javascripts etc)
	}
	
?>