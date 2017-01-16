<?php
	
	function services_sidebar(){
		global $application_list ;
		global $user;
		global $side_menu;
		
		$side_menu = array( 
			array('url' => URL.'/?p=services|home', 			'class' => 'fa fa-home fa-fw', 					'text' => 'Επικαιροποίηση Υπηρεσιών'),
			array('url' => URL.'/?p=services|set', 				'class' => 'fa fa-bar-chart-o fa-fw', 			'text' => 'Προτεραιοποίηση/Επιλογές'),
		);
		
	}
	
	function prepare_pages(){
		global $css_files;
		global $js_files;
		global $application_list;
		
		$page = '';
		$params = explode('|', trim($_GET['p'])); //Έλεγχος ορισμάτων URL
		if(array_key_exists($params[0], $application_list)){	
			if(empty($params[1]) or $params[1] == '')
				$page = 'home';
			else{
				$path_temp = explode('&', $params[1]);
				$page = $path_temp[0];
			}
		} 

		$css_files[] = array('path' => 'apps/services/style.css');
		
		switch($page){	
			case 'home':			
				$js_files[] =  array('head' => false, 'path' => 'apps/services/js/edit_services.js');
				break;
			case 'set':			
				$js_files[] =  array('head' => false, 'path' => 'apps/services/js/set_services.js');
				break;
		}
		
	}
?>