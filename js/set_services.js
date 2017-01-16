$(document).ready(function(){
	
	$('input[type=checkbox]').change(function(e){
		$('#errorer').hide();
	   if ($('input[type=checkbox]:checked').length > 3) {
			$(this).prop('checked', false)
			$('#errorer').fadeIn();
	   }
	})
	
	deleter();
	
	function deleter(){
		$('.deleteme').on('click', function(e){
			e.preventDefault();
			$(this).parent().remove();
		});
	}
	
	$('#addnewservice').on('click', function(e){
		e.preventDefault();
		
		var service_name = $('#new_service_title').val();
		var service_url = $('#new_service_url').val();
		var service_foreas = $('#new_service_foreas').val();
		
		if(service_name == '') return false;

		var next_service = $('#nextservice').val();	

		var new_service = '<div class="extra_service extra_service_'+next_service+'">';
		new_service = new_service+'<input type="hidden" name="extra_service_name['+next_service+']" value="'+service_name+'">';
		new_service = new_service+'<input type="hidden" name="extra_service_url['+next_service+']" value="'+service_url+'">';
		new_service = new_service+'<input type="hidden" name="extra_service_foreas['+next_service+']" value="'+service_foreas+'">';
		new_service = new_service+'<div class="name">'+service_name;
		
		if(service_url != '')
			new_service = new_service+'<span class="tmima"><a href="'+service_url+'" target="_blank">'+service_url+'</a></span>';
		
		if(service_foreas != '')
			new_service = new_service+'<span class="tmima">'+service_foreas+'</span>';
		
		new_service = new_service+'</div><a href="extra_service_'+next_service+'" class="deleteme">X</a></div>';
		
		$('#new_service_container').append(new_service);
		
		$('#new_service_title').val('');
		$('#new_service_url').val('');
		$('#new_service_foreas').val('');
		 
		deleter();
	});
	
	
});