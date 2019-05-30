<?php

include_once 'config.php';
include_once "image.php";
include_once "cImage.php";
class cImgbox {

    private $conn = null;
    private $aImage = array();
    private $userid = null;
    private $imagetype = null;
    private $itemid = 0;
    private $newsession = array();

    public function __construct( $user, $itemid, $imagetype = 'listing' ) {
        $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(
                                   PDO::ATTR_PERSISTENT => true
        ));
        $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->userid = $user;

        if($imagetype == 'profile') {
            $this->itemid = null;
        } else {
            $this->itemid = $itemid;
        }

        $this->imagetype = $itemid;
        $images = new Image();
        $this->aImage = $images->getAllDBImagesByUserID($user, $itemid, $imagetype);
        error_log("$user, $itemid, $imagetype'");
        error_log("--->".print_r($this,true));
    }

    public function __destruct() {
    }

    public function printImages() {
        $count = 0;
        $output = "<form id='itemimages'><center><ul class='list-style4'>";
        $output  .= "<li>&nbsp;</li>";
        foreach($this->aImage as $image) {
            $img = new cImage($image);

            $pos = $img->GetPosition();

            // Doing this to address a different bug. Sorry. Annoying.
            if( $pos == '') {
                    $pos = 0;
                    continue;
            }

            // unset wasn't working, so I'll just do it this way
            array_push($this->newsession, $image);

            $output  .= "<!--li>$this->userid $image</li-->";
            //$output .= "<!--li><a href='up.php?pos=$pos' class='default_popup'>Change</a></li-->";
            if($this->itemid) {
                $output .= "<li><a href='img-box.php?img=$image&amp;itemid=$this->itemid' class='default_popup'><img src='".$img->GetListingImageSrc(100)."' width='100' alt='Image $image'/></a></li><br />";
            } else {
                $output .= "<li><a href='img-box.php?img=$image' class='default_popup'><img src='".$img->GetListingImageSrc(100)."' width='100' alt='Image $image'/></a></li><br />";
            }
            $output .= "<input type='hidden' class='image$count' name='image$count' value='$image' />";

            $count++;

        }

        if($count < 5) {
            $pos++;
            if($this->itemid) {
                $output .=  "<li><a href='up.php?pos=$pos&amp;itemid=$this->itemid' class='default_popup'>Add Another Photo</a></li>";
            } else {
                $output .=  "<li><a href='up.php?pos=$pos' class='default_popup'>Add Another Photo</a></li>";
            }
        } else {
            error_log("Too many images: $count , $pos");
        }

        $output .= "</ul></center></form>";

        //error_log('"'.$output.'"');
        return $output;

    }
}

