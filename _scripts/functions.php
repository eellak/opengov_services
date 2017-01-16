<?php
	
	function replacer($text){
		$replacers  = array('Κατηγορία:', ' (Διεύθυνση)');
		return str_replace($replacers ,'',$text);
	}
	
	function wiki_page_exists($page_id){
		global $db;

		$query = $db->prepare('SELECT * FROM `wiki_hierarchy` where wiki_id=:wiki_id '  );
		
		$query->bindValue(':wiki_id', 			$page_id, 			PDO::PARAM_INT);  
		$query->execute();
		$service = $query->fetchObject();	
		
		if(empty($service)) return false;
		
		return true;
	}
	
	function wiki_service_exists($page_id){
		global $db;

		$query = $db->prepare('SELECT * FROM `wiki_services` where wiki_id=:wiki_id '  );
		
		$query->bindValue(':wiki_id', 			$page_id, 			PDO::PARAM_INT);  
		$query->execute();
		$service = $query->fetchObject();	
		
		if(empty($service)) return false;
		
		return true;
	}
	
	function save_wiki_cat($item, $parent = 0){
		
		global $db;
		
		if(wiki_page_exists($item['pageid'])) return;
		
		$query = $db->prepare("INSERT INTO wiki_hierarchy (id, wiki_id, wiki_ns, wiki_parent, wiki_title) VALUES (NULL, :wiki_id, :wiki_ns, :wiki_parent, :wiki_title)");
							
		$query->bindValue(':wiki_id', 			$item['pageid'], 			PDO::PARAM_INT);  
		$query->bindValue(':wiki_ns', 			$item['ns'], 				PDO::PARAM_INT);   
		$query->bindValue(':wiki_parent', 		$parent, 					PDO::PARAM_INT);   
		$query->bindValue(':wiki_title', 		replacer($item['title']), 			PDO::PARAM_STR);  
		
		$query->execute();
		
		$id = $db->lastInsertId();
		
		if ($id == 0) {
			echo $query->getSQL().'<br />';
			echo 'Error for '.$item['title'].'<br />';
			return 0;
		} else 
			return $id;
	}
	
	
	function wiki_save_service($parent, $wiki_id, $title, $note = ''){
		
		global $db;
		
		if(wiki_service_exists($wiki_id)) return false;
		
		$query = $db->prepare("INSERT INTO wiki_services (id, wiki_parent, wiki_id, title, note, active) VALUES (NULL, :wiki_parent, :wiki_id, :title, :note, 1)");
							
		$query->bindValue(':wiki_id', 			$wiki_id, 			PDO::PARAM_INT);  
		$query->bindValue(':wiki_parent', 		$parent, 					PDO::PARAM_INT);   
		$query->bindValue(':title', 			$title, 			PDO::PARAM_STR);  
		$query->bindValue(':note', 				$note, 			PDO::PARAM_STR);  
		
		$query->execute();
		
		$id = $db->lastInsertId();
		
		if ($id == 0) {
			echo $query->getSQL().'<br />';
			echo 'Error for '.$item['title'].'<br />';
			return false;
		} else 
			return true;
	}
	
	
	function get_gen_dief_from_wiki(){
		global $print;
		global $stats;

		$list = file_get_contents('https://diadikasies.gr/api.php?action=query&format=json&list=categorymembers&cmtitle=Κατηγορία:Υπηρεσίες_ανά_Γενική_Διεύθυνση_(περιφέρεια)&continue');
		$list_items = json_decode($list, true);
		
		$print .= '<ul>';
		foreach($list_items['query']['categorymembers'] as $item){
			$stats['gendief'] = $stats['gendief'] + 1;
			$print .= '<li><strong> ΓΔ: '.replacer($item['title']).' (<a href="https://diadikasies.gr/api.php?action=query&format=json&list=categorymembers&cmlimit=100&cmpageid='.$item['pageid'].'" target="_blank">'.$item['pageid'].'</a>)</strong><ul>';
			get_children($item['pageid']);
			$print .= '</ul></li>';
			save_wiki_cat($item, 0);
		}
		$print .= '</ul>';
	}
	
	function get_children($page_id){
			global $print;
			global $stats;
		
			$list = file_get_contents('https://diadikasies.gr/api.php?action=query&format=json&list=categorymembers&cmlimit=100&cmpageid='.$page_id);
			$list_items = json_decode($list, true);
			
			foreach($list_items['query']['categorymembers'] as $item){
				if(intval($item['ns']) == 14){ // Only Categories
					$stats['dief'] = $stats['dief'] + 1;
					$print .= '<li><strong> Δ : '.replacer($item['title']).' (<a href="https://diadikasies.gr/api.php?action=query&format=json&list=categorymembers&cmlimit=100&cmpageid='.$item['pageid'].'" target="_blank">'.$item['pageid'].'</a>)</strong><ul>';
					$print .= get_tmima_details($item['pageid']);
					$print .= '</ul></li>';
					save_wiki_cat($item, $page_id);
				}
			}
	}
	
	function get_tmima_details($page_id){
		global $print;
		global $stats;
		global $all_services_dief;
		
		$list = file_get_contents('https://diadikasies.gr/api.php?action=query&format=json&list=categorymembers&cmlimit=100&cmpageid='.$page_id);
		$list_items = json_decode($list, true);
		
		$print_cat = '';
		$print_srv = '';
		$all_services_dief = array();
		
		foreach($list_items['query']['categorymembers'] as $item){
			if(intval($item['ns']) == 0){ 
				$all_services_dief[$item['pageid']] = replacer($item['title']);
			}
		}

		foreach($list_items['query']['categorymembers'] as $item){
			if(intval($item['ns']) == 14){ // Only Categories
				$stats['tmima'] = $stats['tmima'] + 1;
				$print_cat .= '<li><strong> T: '.replacer($item['title']).' (<a href="https://diadikasies.gr/api.php?action=query&format=json&list=categorymembers&cmlimit=100&cmpageid='.$item['pageid'].'" target="_blank">'.$item['pageid'].'</a>)</strong><ul>';
				$print_cat .= get_tmima_services($item['pageid']);
				$print_cat .= '</ul></li>';
				
				save_wiki_cat($item, $page_id);
				
				//Weird things
				if(($key = array_search(replacer($item['title']), $all_services_dief)) !== false) {
					
					$notice = ' [Tmima/Page] -> '.$item['title'];
					if(!in_array($notice, $stats['notices'])) $stats['notices'][] = $notice;
					
					unset($all_services_dief[$key]);
				}
			}
		}

		foreach($all_services_dief as $service_id=>$srv){
		
			$pos = strpos('Τμήμα', $srv);
			if ($pos !== false) {
				$notice = ' [Tmima/String] -> '.$item['title'];
				if(!in_array($notice, $stats['notices'])) $stats['notices'][] = $notice;
			} else {
				$stats['counter'] = $stats['counter'] + 1;
				$stats['noncat'] = $stats['noncat'] + 1;
				if(wiki_save_service($page_id, $service_id, $srv, 'needtmima'))
					$print_srv .= '<li><span style="color:red;"> Y: '.$srv.'</span></li>';
				else
					$print_srv .= '<li><span style="color:red;"> Y: '.$srv.' [<strong>NOT INSERTED</strong>]</span></li>';
				
			}
		}
		
		$print .=  $print_srv;
		$print .=  $print_cat;
	}
	
	function get_tmima_services($page_id){
		$print_services;
		global $stats;
		global $all_services_dief;
		
		$list = file_get_contents('https://diadikasies.gr/api.php?action=query&format=json&list=categorymembers&cmlimit=100&cmpageid='.$page_id);
		$list_items = json_decode($list, true);
				
		foreach($list_items['query']['categorymembers'] as $item){
			if(intval($item['ns']) == 0){ 
			
				if(array_key_exists($item['pageid'], $all_services_dief)){ 
					
					$notice = ' [Duplicated] -> '.$item['title'];
					if(!in_array($notice, $stats['notices'])) $stats['notices'][] = $notice;
					
					unset( $all_services_dief[$item['pageid']]);
				}
			
				$stats['counter'] = $stats['counter'] + 1;
				if(wiki_save_service($page_id, $item['pageid'], replacer($item['title'])))
					$print_services .= '<li> Y: '.replacer($item['title']).'</li>';
				else
					$print_services .= '<li> Y: '.replacer($item['title']).' [<strong>NOT INSERTED</strong>]</li>';				
				
			}
		}
		return $print_services;
	}
	
	
	function get_gen_department($id){
		global $db; 
		$query_dept = $db->prepare("SELECT * from main_departments where unit_gd=:unit_d");
		$query_dept->bindValue(':unit_d', $id, PDO::PARAM_STR);
		$query_dept->execute();
		$query_dept_results = $query_dept->fetchAll();
		return $query_dept_results[0];
	}

	function get_department($idgd){
		global $db; 
		$query_dept = $db->prepare("SELECT * from main_departments where unit_gd=:unit_d and unit_t = ''");
		$query_dept->bindValue(':unit_d', $idgd, PDO::PARAM_STR);
		$query_dept->execute();
		$query_dept_results = $query_dept->fetchAll();
		return $query_dept_results;
	}

	function get_office($idg, $idgd, $idt){
		global $db; 
		$query_dept = $db->prepare("SELECT * from main_departments where unit_g=:unit_g and unit_gd=:unit_d and unit_t=:unit_t");
		$query_dept->bindValue(':unit_g', $idg, PDO::PARAM_STR);
		$query_dept->bindValue(':unit_d', $idgd, PDO::PARAM_STR);
		$query_dept->bindValue(':unit_t', $idt, PDO::PARAM_STR);
		$query_dept->execute();
		$query_dept_results = $query_dept->fetchAll();
		//echo $query_dept->getSQL() ;
			if(isset($_GET['debug']))
			return $query_dept_results[0]['office'].' ('.$idgd.' -> '.$idg.' -> '.$idt.')';
		else
			return $query_dept_results[0]['office'];	
	}
	

?>