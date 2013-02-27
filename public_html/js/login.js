 
 $(function(){
 	$("#login_submit").click(function(){
 		var $uname_input = $(this).closest("form").find("input[name='uname']");
 		var $pass_input = $(this).closest("form").find("input[name='pass']");
 		var uname = $uname_input.val();
 		var pass = $pass_input.val();
 		
 		var error = false;
 		if (uname == ""){
 			error = true;
 			$uname_input.addClass("error");
 		} else $uname_input.removeClass("error");
 		
 		if (pass == ""){
 			error = true;
 			$pass_input.addClass("error");
 		} else $pass_input.removeClass("error");
 		
 		if (error) $("div.login.error").html("Username and Password required");
 		else{
 			$("div.login.error").html("");
 			$(this).closest("form").submit();
 		}
 	});
 	
 	$("#add_submit").click(function(evt){
 		evt.preventDefault();
 		var $uname_input = $(this).closest("form").find("input[name='uname']");
 		var $pass_input = $(this).closest("form").find("input[name='pass']");
 		var $confirm_input = $(this).closest("form").find("input[name='confirm']");
 		var uname = $uname_input.val();
 		var pass = $pass_input.val();
 		var confirm = $confirm_input.val();
 		
 		var error = false;
 		if (uname == ""){
 			error = true;
 			$uname_input.addClass("error");
 		} else $uname_input.removeClass("error");
 		
 		if (pass == ""){
 			error = true;
 			$pass_input.addClass("error");
 		} else $pass_input.removeClass("error");
 		
 		if (confirm == ""){
 			error = true;
 			$confirm_input.addClass("error");
 		} else $confirm_input.removeClass("error");
 		
 		if (error) $("div.add.error").html("All fields are required");
 		else $("div.add.error").html("");
 		
 		if (pass != "" && confirm != "" && pass != confirm){
 			if (error) $("div.add.error").append("<br/>");
 			else{
 				error = true;
 			}
 			$pass_input.addClass("error");
 			$confirm_input.addClass("error");
 			$("div.add.error").append("Passwords do not match");
 		}
 		
 		if (!error){
 			$("div.add.error").html("");
 			
 			$.ajax({
 				url:"/create.php",
 				type:"POST",
 				data:{uname: uname, pass: pass},
 				dataType: "json",
 				success:function(response){
 					if (response.result == true){
 						alert("Account Created");
 						window.location.href = "";
 					} else{
 						alert(response.error);
 					}
 				},
 				error:function(response){
 					console.log(response);
 				}
 			});
 		}
 	});
 });