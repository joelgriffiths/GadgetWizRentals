
        </div> <!-- class="content"-->
    </div> <!-- id="content"-->
    <div id="sidebar">
            <div id="box2">
             <div id='itempics'><?php include "postit-pic.php"?></div><br />
            </div>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div class="b"></div>

</div> <!-- class="box-leftsb"-->

<script type="text/javascript">
<!--
$(function(){

        // Default usage
        $('.default_popup').popup();

        // Function for content
        $('.function_popup').popup({
                content         : function(){
                        return '<p>'+$(this.ele).attr('title')+'</p>';
                }
        });

        // jQuery for content
        $('.jquery_popup').popup({
                content         : $('#inline')
        });

        // HTML for content
        $('.html_popup').popup({
                content         : '<h1>This is some HTML</h1>',
                type            : 'html'
        });

        // Custom YouTube content
        $('.youtube_popup').popup({
                types           : {
                        youtube                 : function(content, callback){

                                content = '<iframe width="420" height="315" src="'+content+'" frameborder="0" allowfullscreen></iframe>';

                                // Don't forget to call the callback!
                                callback.call(this, content);

                        }
                },
                width                           : 420,
                height                          : 315
        });
});


/* Oh yeah. You're welcome. Hope you didn't look too hard for this code */
$("#itemform").submit(function(event) {

        event.preventDefault();
        // if(request) {
        //      request.abort();
        //}

        //var $form = $("#myForm :input");
        var $inputs = $("#itemform").find("input, select, button, textarea");
        //var images = $("#itemimages").find("input");
        //if(images != '') {
        //    serializedData = inputs+'&'+images;
        //}

        //var values = {};
        var serializedData = $('#itemform,#itemimages').serialize();
        //alert(serializedData);
        //return;


        // let's disable the inputs for the duration of the ajax request
        $inputs.prop("disabled", true);

        var request = $.ajax({
                type: "POST",
                url: "aj/postit.php",
		        dataType: 'json',
                data: serializedData
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
                //alert("success");
                $("#ack").empty();
		        if(response.success == true) {
                	$("#ack2").html("Updating Your Settings! Hang Tight." + response.itemID);
			        //window.location = 'login.php';
                    <?php if($itemid) :?>
			            setTimeout("window.location = '/listings.php'",1500);
                    <?php else: ?>
			            window.location = 'rental.php?itemid='+response.itemID;
			            //setTimeout("window.location = '/rental.php?itemid='+response.itemID",1500);
                    <?php endif ?>

                    //profile.php'", 1500);
                	return true;
		        } else {
                	$("#ack2").html(response.error);
                	return false;
		        }
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
                $("#ack2").empty();
                $("#ack2").html("The following error occured: "+ textStatus, errorThrown);
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

<?php
if($lic === false) :
?>
var popup = new $.Popup();
popup.open('login-short.php');
<?php endif;?>
-->
</script>

<!-- End Content -->
<?php
include "bottom.php";

?>
