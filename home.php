<div class="row">

<div class="panel panel-primary">
	<div class="panel-heading">
		Εφαρμογή Επικαιροποίησης Υπηρεσιών
	</div>
	<div class="panel-body">
		<p>Παρακαλούμε ελέγξτε τα στοιχεία των υπηρεσιών σας και συμπληρώστε τις ζητούμενες πληροφορίες παρακάτω.</p>
	</div>
</div>
</div>
<?php 
	
	save_new_services();
	print_messages();
	
	$my_services 		= get_my_services();
	$my_notes 			= get_my_notes();
	$my_wiki_services 	= get_my_wiki_services();
	
	$is_new_submit = true;
	if(!empty($my_services)) $is_new_submit = false;
?>
<div class="row">
<div class="col-md-12">
	<?php if(isset($_POST['submit'])){ ?>
		<div class="alert alert-success">Οι επιλογές σας Αποθηκεύτηκαν. <a class="btn btn-primary" href="<?php echo URL; ?>/?p=services|set">Προχωρήστε</a></div>
	<?php } ?>
	<form method="post" id="serviceform" action="<?=URL?>/?p=services|home">
	  <table class="table table-bordered table-hover" id="dataTables-example">
		<thead>
		  <tr>
			<th rowspan="2">ID</th>
			<th rowspan="2">Όνομασία Υπηρεσίας</th>
			<th rowspan="2" style="width: 380px;">Επιλογές</th>
			<th colspan="3">Πόσες φορές παρείχατε κάθε υπηρεσία; Συμπληρώστε έναν αριθμό για κάθε έτος.</th>
			<th rowspan="2">Σύνδεσμος</th>
		  </tr>
		   <tr>
			<th style="width: 120px;">2014</th>
			<th style="width: 120px;">2015</th>
			<th style="width: 120px;">2016</th>
		   </tr>
		</thead>
		<tbody>
		<?php
			foreach($my_wiki_services as $srv){
				
				if(!$is_new_submit)
					$values = get_service_values($srv['wiki_id'], $my_services);
				else
					$values = array('status' => 0, 'comment' => '', 'usage_2014' => '', 'usage_2015' => '', 'usage_2016' => '');
				
				$checked = array(' ', ' ');
				$class = '';
				
				if(intval($values['status']) == 1){ $checked[0] = 'checked'; $class = 'success'; }
				if(intval($values['status']) == 2){ $checked[1] = 'checked'; $class = 'danger'; }
				
				echo '<tr class="'.$class.'"><td><strong>'.$srv['wiki_id'].'</strong></td><td><strong>'.$srv['title'].'</strong></td>';
			
				echo '<td>
						<div class="form-group">
							<div class="radio-inline">
								<label><input type="radio" data-srv="'.$srv['wiki_id'].'" name="status-'.$srv['wiki_id'].'" value="1" '.$checked[0].'>Σωστή ονομασία</label>
							</div>
							<div class="radio-inline">
							  <label><input type="radio" data-srv="'.$srv['wiki_id'].'" name="status-'.$srv['wiki_id'].'" value="2" '.$checked[1].'>Αλλαγή ονομασίας</label>
							</div>
						</div>';
				
				$display = 'display:none;';
				if(intval($values['status']) == 2) $display = '';
				
				echo '<div class="form-group" id="srv-comm-container-'.$srv['wiki_id'].'" style="'. $display.'">
							<textarea class="form-control" rows="3" name="comment-'.$srv['wiki_id'].'" id="comment-'.$srv['wiki_id'].'" placeholder="Συμπληρώστε τη σωστή ονομασία">'.$values['comment'].'</textarea>
						</div></td>';
				echo '<td>
						<div class="form-group">
						<input class="form-control" name="usage_2014-'.$srv['wiki_id'].'" id="usage_2014-'.$srv['wiki_id'].'" type="number" value="'.$values['usage_2014'].'" size="6" />
						</div></td>';
				echo '<td>
						<div class="form-group">
						<input class="form-control" name="usage_2015-'.$srv['wiki_id'].'" id="usage_2015-'.$srv['wiki_id'].'" type="number" value="'.$values['usage_2015'].'" size="6" />
						</div></td>';
				echo '<td>
						<div class="form-group">
						<input class="form-control" name="usage_2016-'.$srv['wiki_id'].'" id="usage_2016-'.$srv['wiki_id'].'" type="number" value="'.$values['usage_2016'].'" size="6" />
						</div></td>';
				echo '<td><a href="http://diadikasies.gr/?curid='.$srv['wiki_id'].'" target="_blank">Προβολή</a></td>';
				echo '</tr>';
			}
		?>  
		</tbody>
	  </table>
	  <?php 	if($is_new_submit) echo '<input type="hidden" name="is_new_submit" value = "yes">'; ?>
	<div class="row">
		<div class="col-md-6 serviceadder">
			<p><strong>Υπάρχουν Υπηρεσίες που ΔΕΝ περιλαμβάνονται στον παραπάνω κατάλογο;</strong> </p>
			
			<p>Προσθέστε τις εδώ!</p>
			<?php $tmimata= get_user_tmima(); ?>
			<div id="new_service_container">
			<?php 
				$cnt = 1;
				if(!empty($my_notes)){ 
					$extra_services = unserialize($my_notes[0]['extra_services']); 
					foreach($extra_services as $service){
						echo '<div class="extra_service extra_service_'.$cnt.'">';
						echo '<input type="hidden" name="extra_service_name['.$cnt.']" value="'.$service['name'].'">';
						echo '<input type="hidden" name="extra_service_tmima['.$cnt.']" value="'.$service['tmima'].'">';
						echo '<div class="name">'.$service['name'].'';
						echo '<span class="tmima">'.find_tmima_name($tmimata, $service['tmima']).'</span></div>';
						echo '<a href="extra_service_'.$cnt.'" class="deleteme">X</a>';
						echo '</div>';
						$cnt++; 
					}
				}
			?>
			</div>
			<input type="hidden" id="nextservice" value="<?php echo $cnt; ?>" />
			<div class="form-group">
				<label>Ονομασία Υπηρεσίας (Υποχρεωτικό)</label>
				<input class="form-control" name="new_service_title" id="new_service_title" type="text"  />
			</div>
			<div class="form-group">
			<label>Τμήμα (Υποχρεωτικό)</label>
			<select name="new_service_dept" class="form-control" id="new_service_dept">
				<option value="0" >Επιλέξτε Τμήμα</option>
			<?php 
				foreach($tmimata as $tmima){
					echo '<option value="'.$tmima['unit_t'].'" >'.$tmima['office'].'</option>';
				}
			?>
			</select>
		</div>
			<p style="display: inline; margin-right: 30px;"><em>Προσθέστε ξεχωριστά το όνομα κάθε υπηρεσίας που λείπει από τον κατάλογο, επιλέξτε το τμήμα που την παρέχει και πατήστε προσθήκη</em></p> 
			<button class="btn btn-success pull-right" id="addnewservice">Προσθήκη</button>
		</div>
		<div class="col-md-1"></div>
		<div class="col-md-5 serviceadder">
			<div class="form-group ">
				  <label class="control-label " for="comments">
					<strong>Παρατηρήσεις</strong>
				  </label>
				  <textarea class="form-control" cols="40" id="comments" name="comments" rows="10"><?php if(!empty($my_notes)) echo $my_notes[0]['comments']; ?></textarea>
				  <span class="help-block" id="hint_comments">
				 Αν έχετε γενικές παρατηρήσεις για τις υπηρεσίες παρακαλούμε καταχωρήστε τις εδω, αναγράφοντας τον αναγνωριστικό αριθμό (ID που βρίσκεται στην αριστερή στήλη του καταλόγου) της υπηρεσίας που αφορούν.
				  </span>
			</div>
		 
		</div>
	</div>
	<input type="hidden" name="save_service" value = "yes">
	 <div class="form-group text-center">
	  <div>
	   <button class="btn btn-primary btn-large" name="submit" type="submit">
		Αποθήκευση
	   </button>
	  </div>
	 </div>
	 
	</form>
	
</div>
</div>