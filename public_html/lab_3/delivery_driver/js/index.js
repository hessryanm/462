$(function(){
	$("#save_number").click(function(){
		var uname = $("#uname").val();
		var number = $("#phone_number").val();
		
		$.ajax({
			url: "save_number.php",
			type: "POST",
			data: {uname: uname, number: number},
			success:function(response){
				if (response == "done") alert("Number Saved");
				else alert(response);
			},
			error:function(response){
				alert("Error, number not saved");
				console.log(response);
			}
		});
	});
});