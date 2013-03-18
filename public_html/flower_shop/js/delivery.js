function send_events(){
	var attrs = {};
	var domain = "rfq";
	var name = "delivery_ready";
	
	attrs['_domain'] = domain;
  	attrs['_name'] = name;
  	attrs['pickup_address'] = "This is the pickup address!";
  	
  	var pickup_time = $("#pickup_time").val();
  	var delivery_time = $("#delivery_time").val();
  	var delivery_address = $("#delivery_address").val();
  	
  	attrs['pickup_time'] = pickup_time;
  	attrs['delivery_time'] = delivery_time;
  	attrs['delivery_address'] = delivery_address;
  	
  	console.log(attrs);
	
	_(esls).each(function(val){
		console.log(val);
		$.ajax({
			url: val,
			type: "GET",
			data: attrs,
			success:function(response){console.log(response)},
			error:function(response){console.log(response)}
		});
	});
	
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