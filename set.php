<div class="row">
	<div class="col-lg-12">
		<h3 class="page-title">Προτεραιοποίηση Υπηρεσιών</h3>
	</div>
</div>

 <?php 
	save_services_notes();
	print_messages();
	
	$my_notes 			= get_my_notes();
	
	if(!empty($my_notes)){
	
	$my_services 		= get_my_services();
	$my_wiki_services 	= get_my_wiki_services();
?>

<form method="post" id="serviceform" action="<?=URL?>/?p=services|set">
<div class="row">
	<div class="col-md-5 serviceadder">
		<div class="form-group ">
		  <label class="control-label" style="margin-bottom:20px;">
			<strong>Επιλέξτε έως τρείς (3) Υπηρεσίες που κατά τη γνώμη σας πρέπει να ηλεκτρονικοποιηθούν κατά προτεραιότητα.</strong>
		  </label>
		  
		  <div id="errorer" class="alert alert-danger" style="display:none;">Επιλέξτε αυστηρά τρείς (3) υπηρεσίες!</div>
		  
		  <div class="service-list-container">
			<?php 
				$selected = explode(',', $my_notes[0]['selection']);
				foreach($my_wiki_services as $srv){ 
					$checked = '';
					if(in_array($srv['wiki_id'], $selected)) $checked = 'checked';
				?>
			   <div class="checkbox">
				<label class="checkbox">
				 <input class="servicecheckbox" name="set_electronic[]" type="checkbox" value="<?php echo $srv['wiki_id']; ?>" <?php echo $checked; ?>/>
				 <?php echo  $srv['title']; ?>
				</label>
			   </div>
			<?php } ?>
		  </div>
		</div>
	</div>
	<div class="col-md-1"></div>
	<div class="col-md-6 serviceadder">
		<?php /*
		 <div class="form-group ">
		  <label class="control-label " for="comments">
			<strong>Καταχώριση Πληροφοριακών Συστημάτων και Ιστοχώρων</strong>
		  </label><br />
		  <textarea class="form-control" cols="40" id="comments" name="comments" rows="10"><?php echo $my_notes[0]['applications']; ?></textarea>
		  <span class="help-block" id="hint_comments">
		   Καταχωρίστε παραπάνω ιστοχώρους ή/και Πληροφοριακά Συτήματα τα οποία χρησιμοποιεί η Διεύθυνσή σας για την παροχή και διεκπεραίωση των Υπηρεσιών της.
		  </span>
		 </div>
		*/ ?>
		
		<p><strong>Καταχώριση Πληροφοριακών Συστημάτων και Ιστοχώρων</strong> </p>
			
			<?php $tmimata= get_user_tmima(); ?>
			<div id="new_service_container">
			<?php 
				$cnt = 1;
				if(!empty($my_notes)){ 
					$extra_services = unserialize($my_notes[0]['applications']); 
					foreach($extra_services as $service){
						echo '<div class="extra_service extra_service_'.$cnt.'">';
						echo '<input type="hidden" name="extra_service_name['.$cnt.']" value="'.$service['name'].'">';
						echo '<input type="hidden" name="extra_service_url['.$cnt.']" value="'.$service['url'].'">';
						echo '<input type="hidden" name="extra_service_foreas['.$cnt.']" value="'.$service['foreas'].'">';
						echo '<div class="name">'.$service['name'].'';
						if($service['foreas'] != '')
							echo '<span class="tmima">'.$service['foreas'].'</span>';
						if($service['url'] != '')
							echo '<span class="tmima"><a href="'.$service['url'].'" target="_blank">'.$service['url'].'</a></span>';
						echo '</div>';
						echo '<a href="extra_service_'.$cnt.'" class="deleteme">X</a>';
						echo '</div>';
						$cnt++; 
					}
				}
			?>
			</div>
			<input type="hidden" id="nextservice" value="<?php echo $cnt; ?>" />
			<div class="form-group">
				<label>Όνομα ΠΣ/Ιστοχώρου (Υποχρεωτικό)</label>
				<input class="form-control" name="new_service_title" id="new_service_title" type="text"  />
			</div>
			<div class="form-group">
				<label>Ιστοχώρος/URL (αν υφίσταται)</label>
				<input class="form-control" name="new_service_url" id="new_service_url" type="text"  />
			</div>
			<div class="form-group">
				<label>Πάροχος (πχ Υπουργείο Εργασίας, ΠΔΜ)</label>
				<input class="form-control" name="new_service_foreas" id="new_service_foreas" type="text"  />
			</div>
		
			<p style="display: inline; margin-right: 30px;"><em>Καταχωρίστε ιστοχώρους ή/και Πληροφοριακά Συτήματα τα οποία χρησιμοποιεί η Διεύθυνσή σας για την παροχή και διεκπεραίωση των Υπηρεσιών της.</em></p> 
			<button class="btn btn-success pull-right" id="addnewservice">Προσθήκη</button>
			<br />
			<p style="display: block; margin-top: 40px; text-align:center;"><em>Πατήστε Αποθήκευση στο τέλος της σελίδας για να αποθηκευθούν οι αλλαγές.</em></p> 
	</div>
</div>
<div class="row">
<input type="hidden" name="save_service" value = "yes">
<div class="col-md-12 text-center">
	<div class="form-group">
	  <div>
	   <button class="btn btn-primary  btn-large" name="submit" type="submit">
		Αποθήκευση
	   </button>
	  </div>
	 </div>
 </div>
	</form>
<?php } else { ?>
	<div id="errorer" class="alert alert-notice">Παρακαλούμε καταχωρίστε πρώτα τις Υπηρεσίες <a href="<?=URL?>/?p=services|home" class="btn">εδω</a></div>
<?php } ?>