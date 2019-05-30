$("#regform").submit(function() {

        // if(request) {
        //      request.abort();
        //}

        //var $form = $("#myForm :input");
        var $inputs = $("#regform").find("input, select, button, textarea");
        //var serializedData = $form.serialize();

        var values = {};
        var serializedData = $("#regform").serialize();


        // let's disable the inputs for the duration of the ajax request
        $inputs.prop("disabled", true);

        var request = $.ajax({
                type: "POST",
                url: "validate.php",
                dataType: 'json',
                data: serializedData
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
                alert("success");
                $("#ack").empty();
                if(response.success == true) {
                        $("#ack").html("Hooray, it worked!");
                } else {
                        $("#ack").html("Error: "+response.error);
                }
                return false;
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
                alert("failure");
                $("#ack").empty();
                $("#ack").html("The following error occured: "+ textStatus, errorThrown);
                return false;
        });

        // callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function () {
                // reenable the inputs
                $inputs.prop("disabled", false);
                return false;
        });

        // prevent default posting of form
        //event.preventDefault();
        return false;
});

$("#xregform").submit(function() {
	
	// if(request) {
	//	request.abort();
	//}

	//var $form = $("#myForm :input");
	//var $inputs = $form.find("input, select, button, textarea");
	//var serializedData = $form.serialize();

	var values = {};
	$.each($('#regform').serializeArray(), function(i, field) {
		values[field.name] = field.value;
	});


	alert(values);
	// let's disable the inputs for the duration of the ajax request
	$inputs.prop("disabled", true);

	request = $.ajax({
		type: "POST",
		url: "class/validate.php",
		data: serializedData
	});
 
	// callback handler that will be called on success
	request.done(function (response, textStatus, jqXHR){
		alert("success");
		$("#ack").empty();
		$("#ack").html("Hooray, it worked!");
		return false;
	});

	request.fail(function (jqXHR, textStatus, errorThrown){
		alert("failure");
		$("#ack").empty();
		$("#ack").html("The following error occured: "+ textStatus, errorThrown);
		return false;
	});

	// callback handler that will be called regardless
	// if the request failed or succeeded
	request.always(function () {
		// reenable the inputs
		$inputs.prop("disabled", false);
		return false;
	});

	// prevent default posting of form
	event.preventDefault();	
		return false;
});

function origSubmitReg() {
	if( $("#usn").val() == "" || $("#passwd").val() == "" || $("#first").val() == "" || $("#last").val() == "" || $("#email").val() == "")
	  $("#ack").html("All Fields are required");
	else
	  $.post( $("#myform").attr("action"),
	         $("#myform :input").serializeArray(),
			 function(info) {
 
			   $("#ack").empty();
			   $("#ack").html(info);
				clear();
			 });
 
	$("#myform").submit( function() {
	   return false;	
	});
}
//});
 
function clear() {
 
	$("#myform :input").each( function() {
	      $(this).val("");
	});
}

function checkVal()  
{  
    if (charPassword.length >= minPasswordLength)  
    {  
        baseScore = 50;   
        analyzeString();      
        calcComplexity();         
    }  
    else  
    {  
        baseScore = 0;  
    }  
      
    outputResult();  
} 


function analyzeString ()  
{     
    for (i=0; i<charPassword.length;i++)  
    {  
        if (charPassword[i].match(/[A-Z]/g)) {num.Upper++;}  
        if (charPassword[i].match(/[0-9]/g)) {num.Numbers++;}  
        if (charPassword[i].match(/(.*[!,@,#,$,%,^,&,*,?,_,~])/)) {num.Symbols++;}   
    }  
      
    num.Excess = charPassword.length - minPasswordLength;  
      
    if (num.Upper && num.Numbers && num.Symbols)  
    {  
        bonus.Combo = 25;   
    }  
  
    else if ((num.Upper && num.Numbers) || (num.Upper && num.Symbols) || (num.Numbers && num.Symbols))  
    {  
        bonus.Combo = 15;   
    }  
      
    if (strPassword.match(/^[\sa-z]+$/))  
    {   
        bonus.FlatLower = -15;  
    }  
      
    if (strPassword.match(/^[\s0-9]+$/))  
    {   
        bonus.FlatNumber = -35;  
    }  
} 
