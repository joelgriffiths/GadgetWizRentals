<?php
$title = "Linux Technical Notes";
$header = "Welcome to Zalaxy.com";
$valid=true;
include "header.php";
?>

<div id="content_wrapper">
    <div class="grid_12" id="search_box">
        <form id="search_form" name="search_form" action="/search/" method="GET">
            <div id="search_type" class="grid_2">
                <label>Your dog prefers to stay:</label>            
                <input type="radio" name="type" id="searchtype_homes" class="greendot" value="homes"><label class="search_type_label" for="searchtype_homes">In your sitter's home</label>
                <div class="clear"></div>
                <input type="radio" name="type" id="searchtype_sitters" class="greendot" value="sitters"><label class="search_type_label" for="searchtype_sitters">In my home</label>
            </div>
            <div id="search_text" class="grid_1">
                <label>Location:</label>
                <input type="text" name="location" value="" id="id_location" placeholder="Location" class="searchInput"/>
            </div>
            <div id="search_date" class="grid_4">
                <div class="grid_2">
                <label>From:</label>
                <input type="text" id="id_fromdate" name="fromdate" placeholder="mm/dd/yyyy" class="text-input datepicker" />
                </div>
                <div class="grid_2">
                <label>To:</label>
                <input type="text" id="id_todate" name="todate" placeholder="mm/dd/yyyy" class="text-input datepicker" />
                </div>
            </div>
            <div id="search_size" class="grid_2">
                
                    <span id="mypets">
                        <label>Your Dog</label>
                        <div id="petstay">
                            
                                <div class="pet">
                                    <input type="checkbox" id="pet_35896" name="pet" value="35896" />
                                    <label for="pet_35896">Riley</label>
                                </div>
                            
                        </div>
                    </span>
                
            </div>

            <!-- Parameters for manual updates of the map -->
            <input type="hidden" name="mapupdated" />
            <input type="hidden" name="zoomlevel" />

            <input type="hidden" name="page" value="1">
            <input type="hidden" name="minlat" />
            <input type="hidden" name="minlng" />
            <input type="hidden" name="maxlat" />
            <input type="hidden" name="maxlng" />
            <input type="hidden" name="centerlat" />
            <input type="hidden" name="centerlng" />
            <input type="hidden" name="source" value="hosts" />
        </form>
    </div>
</div>

<?php
require 'footer.php';
?>
