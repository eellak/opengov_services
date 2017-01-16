$(document).ready(function(){
	
	$("input[type='radio']").change(function(){ 
		//console.log($(this).val());
		if($(this).val() != 1){
			$('#srv-comm-container-'+$(this).data('srv')).fadeIn();
		} else {
			$('#srv-comm-container-'+$(this).data('srv')).fadeOut();
		}
    });
	
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
		var service_tmima = $('#new_service_dept').val();
		
		if(service_name == '') return false;
		if(service_tmima == 0) return false;
		
		var next_service = $('#nextservice').val();	
		var service_tmima_name = $('#new_service_dept').find(":selected").text();

		var new_service = '<div class="extra_service extra_service_'+next_service+'"><input type="hidden" name="extra_service_name['+next_service+']" value="'+service_name+'"><input type="hidden" name="extra_service_tmima['+next_service+']" value="'+service_tmima+'"><div class="name">'+service_name+'<span class="tmima">'+service_tmima_name+'</span></div><a href="extra_service_'+next_service+'" class="deleteme">X</a></div>';
		
		$('#new_service_container').append(new_service);
		$('#new_service_title').val('');
		 $('#new_service_dept').val(0);
		deleter();
	});
	
});