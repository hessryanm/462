$(function(){
	$("tr.delivery").click(function(){
		$popdown = $(this).next(".popdown");
		if ($popdown.css("display") == "none") show = true;
		else show = false;
		$(".popdown").hide();
		if (show) $(this).next(".popdown").show();
	});
	
	$(".select_bid").click(function(){
		var bid = $(this).prev().val();
		$.ajax({
			url: "select_bid.php",
			type: "POST",
			data: {bid: bid},
			success:function(response){
				if (response == "done"){
					alert("Bid Selected");
					location.reload();
				} else alert(response);
			},
			error:function(response){
				alert("Error Selecting Bid");
				console.log(response);
			}
		});
	});
	
	$(".delivery_picked_up").click(function(){
		var delivery = $(this).prev().val();
		$.ajax({
			url: "picked_up.php",
			type: "POST",
			data: {delivery: delivery},
			success:function(response){
				if (response == "done"){
					alert("Marked as Picked Up");
					location.reload();
				} else alert(response);
			},
			error:function(response){
				alert("Error Marking as Picked Up");
				console.log(response);
			}
		});
	});
});

function save_delivery(){
  	
  	var pickup_time = $("#pickup_time").val();
  	var delivery_time = $("#delivery_time").val();
  	var delivery_address = $("#delivery_address").val();
  		
	$.ajax({
		url: "new_delivery.php",
		type: "POST",
		data: {address: delivery_address, delivery_time: delivery_time, pickup_time: pickup_time},
		success:function(response){
			alert(response);
			location.reload();
		},
		error:function(response){
			alert("There has been an error.  Please try again.");
			console.log(response);
		}
	})	
}    