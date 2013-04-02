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
});