<?php
	

	function get_my_wiki_services(){
		global $db, $user;
	
		// Get the Top Level
		$query = $db->prepare('SELECT * FROM `wiki_departments` where pdm_id=:unit_g'  );
			
		$query->bindValue(':unit_g', 	$user->unit_g,				PDO::PARAM_STR); 	 
		$query->execute();
		$wiki_dept_id = $query->fetchObject();	
		
		$wiki_parents = array();
		$wiki_parents[]= $wiki_dept_id->wiki_id;
		
		// Get Children
		$query = $db->prepare('SELECT * FROM `wiki_hierarchy` where wiki_parent=:wiki_parent'  );
		$query->bindValue(':wiki_parent', 	 $wiki_dept_id->wiki_id,				PDO::PARAM_STR); 	 
		$query->execute();
		$wiki_tmima_ids = $query->fetchAll();	
		
		foreach($wiki_tmima_ids as $wiki_tmima_id) $wiki_parents[]= $wiki_tmima_id['wiki_id'];
		
		// Get the Services
		$query = $db->prepare('SELECT * FROM `wiki_services` where wiki_parent in ('.implode(',', $wiki_parents).')'  );
		$query->execute();
		$services = $query->fetchAll();
		//echo  $query->getSQL().'<br />';
		return $services;
	}
	
	function get_my_services(){
		global $db, $user;

		$query = $db->prepare('SELECT * FROM `main_services` where afm_editor=:afm'  );
			
		$query->bindValue(':afm', 		$user->afm,				PDO::PARAM_STR); 	
		$query->execute();
		$services = $query->fetchAll();	
		
		return $services;
	}
	
	function get_my_service($wiki_id){
		global $db, $user;

		$query = $db->prepare('SELECT * FROM `main_services` where wiki_id=:wiki_id'  );
			
		$query->bindValue(':wiki_id', 		$user->wiki_id,				PDO::PARAM_INT); 	
		$query->execute();
		$service = $query->fetchObject();	
		
		return $service;
	}
	
	function get_service_values($wiki_id, $service_list){
		foreach($service_list as $service){
			if($service['wiki_id'] == $wiki_id){
				return $service;
			}
 		}
		return false;
	}
	
	function get_my_notes(){
		global $db, $user;

		$query = $db->prepare('SELECT * FROM `main_services_notes` where afm_editor=:afm'  );
			
		$query->bindValue(':afm', 		$user->afm,				PDO::PARAM_STR); 	
		$query->execute();
		$notes = $query->fetchAll();	
		
		return $notes;
	}
	

	function  get_service($wiki_id){
		global $db, $user;

		$query = $db->prepare('SELECT * FROM `wiki_services` where wiki_id=:wiki_id'  );	
		$query->bindValue(':wiki_id', 	$wiki_id,				PDO::PARAM_INT); 
		$query->execute();
		$service = $query->fetchObject();	
		
		return $service;
	}
	
	function find_tmima_name($tmimata, $tmima_id){
		foreach($tmimata as $tmima){
			if($tmima['unit_t'] == $tmima_id) return $tmima['office'];
		}
	}
	

	function save_new_services(){
		global $db;
		global $user;
		
		if(isset($_POST['save_service'])){
			
			$exclude = array('new_service_title', 'new_service_dept', 'save_service', 'submit');
			
			//print_r($_POST);
			
			$items_to_process = array();
			
			foreach($_POST as $key => $value){
				
				if(in_array($key, $exclude)) continue;
				$key_parts = explode('-', $key);
				if(count($key_parts) > 1)
					$items_to_process[$key_parts[1]][$key_parts[0]] = $value;
			}
			
			// Also add the status where not set
			foreach($items_to_process as $wiki_id => $valuez){
				if(!array_key_exists('status', $valuez)) $items_to_process[$wiki_id ]['status'] = 0;
			}
			
			//echo print_pretty($items_to_process);
			
			// Save the Services Under this User
			foreach($items_to_process as $wiki_id => $valuez){
				
				if(isset($_POST['is_new_submit'])){
				
					$query = $db->prepare("INSERT INTO main_services (id, afm_editor, date_added, wiki_id, status, comment, usage_2014, usage_2015, usage_2016) VALUES(NULL, :afm_editor, :date_added, :wiki_id, :status, :comment, :usage_2014, :usage_2015, :usage_2016)");
					
					$query->bindValue(':date_added', 		date("Y-m-d_H_i_s"), 		PDO::PARAM_STR);   
				
				} else{
				
					$query = $db->prepare("UPDATE main_services set date_edited = :date_edited, status = :status, comment = :comment, usage_2014 = :usage_2014 , usage_2015 = :usage_2015, usage_2016 = :usage_2016 where afm_editor = :afm_editor and wiki_id = :wiki_id");
					
					$query->bindValue(':date_edited', 		date("Y-m-d_H_i_s"), 		PDO::PARAM_STR); 
				}
				
				$query->bindValue(':afm_editor', 		$user->afm, 				PDO::PARAM_STR);   
				$query->bindValue(':wiki_id', 			$wiki_id,  					PDO::PARAM_STR);   
				$query->bindValue(':status', 			$valuez['status'],			PDO::PARAM_INT);  
				$query->bindValue(':comment', 			$valuez['comment'],			PDO::PARAM_STR);   
				$query->bindValue(':usage_2014', 		$valuez['usage_2014'],		PDO::PARAM_INT);  
				$query->bindValue(':usage_2015', 		$valuez['usage_2015'],		PDO::PARAM_INT);  
				$query->bindValue(':usage_2016', 		$valuez['usage_2016'],		PDO::PARAM_INT); 				
				
				$query->execute(); 
				//echo $query->getSQL().'<br />';
			} 
		
			// Here Prepare the Extra Servies // Searialize it
			$extra_services = array();
			
			foreach($_POST as $key => $value){
				
				if($key == 'extra_service_name'){
					// Save new Services Names
					foreach($value as $key => $service_name){
						$extra_services[$key] = array( 'name' => $service_name);
					}
				}
				
				if($key == 'extra_service_tmima'){
					// Save new Services Tmima
					foreach($value as $key => $service_tmima){
						$extra_services[$key]['tmima'] = $service_tmima;
					}
				}
			}
			
			//print_r($extra_services);
			
			if(isset($_POST['is_new_submit'])){
				$query = $db->prepare("INSERT INTO main_services_notes (id, afm_editor, extra_services, comments, selection, applications) VALUES(NULL, :afm_editor, :extra_services, :comments, '', '')");
			} else {
				$query = $db->prepare("UPDATE main_services_notes set extra_services = :extra_services, comments = :comments  where afm_editor = :afm_editor");
			}
			
			$query->bindValue(':afm_editor', 		$user->afm, 					PDO::PARAM_STR);  
			$query->bindValue(':extra_services', 	serialize($extra_services),		PDO::PARAM_STR);			
			$query->bindValue(':comments', 			trim($_POST['comments']),		PDO::PARAM_STR);
			
			$query->execute(); 
			//echo $query->getSQL().'<br />';
		}
	}
	
	function save_services_notes(){
		global $db;
		global $user;
		
		if(isset($_POST['save_service'])){
		
			// Here Prepare the Extra Servies // Searialize it
			$extra_services = array();
			
			foreach($_POST as $key => $value){
				
				if($key == 'extra_service_name'){
					// Save new Services Names
					foreach($value as $key => $service_name){
						$extra_services[$key] = array( 'name' => $service_name);
					}
				}
				
				if($key == 'extra_service_url'){
					// Save new Services Tmima
					foreach($value as $key => $service_url){
						$extra_services[$key]['url'] = $service_url;
					}
				}
				
				if($key == 'extra_service_foreas'){
					// Save new Services Tmima
					foreach($value as $key => $service_foreas){
						$extra_services[$key]['foreas'] = $service_foreas;
					}
				}
			}
		
			$query = $db->prepare("UPDATE main_services_notes set selection = :selection, applications = :applications  where afm_editor = :afm_editor");
		
			//print_r($_POST);
			$query->bindValue(':afm_editor', 		$user->afm, 							PDO::PARAM_STR);  
			$query->bindValue(':selection', 		implode(',', $_POST['set_electronic']),	PDO::PARAM_STR);			
			$query->bindValue(':applications', 		serialize($extra_services),				PDO::PARAM_STR);
			
			$query->execute(); 
			$message_list[] = array( 'type' => 'success', 'message'	=> 'Επιτυχής Αποθήκευση! Μπορείτε να επεξεργαστείες τις επιλογές σας ανα πάσα στιγμή.' );
			//echo $query->getSQL().'<br />';
		}
	}
	
	function get_user_tmima(){
		global $db, $user;

		$query = $db->prepare("SELECT * FROM `main_departments` where unit_g = :unit_g and unit_gd=:unit_gd and unit_t != ''"  );
			
		$query->bindValue(':unit_g', 	$user->unit_g,				PDO::PARAM_STR); 	
		$query->bindValue(':unit_gd', 	$user->unit_gd,				PDO::PARAM_STR); 
		$query->execute();
		$tmimata = $query->fetchAll();	
		
		return $tmimata;
	}
	

?>