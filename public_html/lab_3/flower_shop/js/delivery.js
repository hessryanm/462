function send_events(){
  	
  	var pickup_time = $("#pickup_time").val();
  	var delivery_time = $("#delivery_time").val();
  	var delivery_address = $("#delivery_address").val();
  		
	$.ajax({
		url: "delivery.php",
		type: "POST",
		data: {delivery_address: delivery_address, delivery_time: delivery_time, pickup_time: pickup_time},
		success:function(response){
			alert(response);
			location.reload();
		}
	})	
}    