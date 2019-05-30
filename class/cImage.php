<?php

// Great coding right!

include_once('config.php');
include_once('image.php');

// This is a real class - Sorry about the fake one.
// Your welcome. 

class cImage extends Image {

    public $conn = null;
    private $imageid;
    private $refid;
    private $imagetype;
    private $caption;
    private $path;
    private $basename;
    private $private;
    private $created;
    private $priority;

    public function __construct( $imageid ) {
        $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(
                                   PDO::ATTR_PERSISTENT => true
        ));
        $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        parent::__construct();
        $this->getImageStats($imageid);
    }

    public function __destruct() {
    }


    private function getImageStats($imageid) {
        try {
    
            $images = array();
            $sql = "SELECT `imageid`, `refid`, `imagetype`, `caption`, `path`, `basename`, `private`, `created`, `priority` FROM images WHERE imageid = :imageid";
    
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindvalue(":imageid", $imageid, PDO::PARAM_INT);
    
            $stmt->execute();
    
            $numRows = $stmt->rowCount();
            if( $numRows == 0 )
                return false;
    
            list($this->imageid, $this->refid, $this->imagetype, $this->caption, $this->path, $this->basename, $this->private, $this->created, $this->priority) = $stmt->fetch(PDO::FETCH_NUM);
            return(true);
    
        } catch ( PDOException $e) {
            error_log($e->getMessage());
        } catch ( Exception $e ) {
            error_log($e->getMessage());
        }
        return false;
    }
    
    function getPosition() {
        //error_log('POSITION: ' . $this->priority);
        return($this->priority);
    }
    
    function getImageID() {
        return $this->imageid;
    }

    function getProfileImageSrc($width=100) {
        $image = $this->getImagePath($this->imageid);
        $src = '/image.php?id='.$this->imageid.'&amp;width='.$width;
        return $src;
    }

    function getListingImageSrc($width=100) {
        $image = $this->getImagePath($this->imageid);
        $src = '/image.php?id='.$this->imageid.'&amp;width='.$width;
        return $src;
    }
}



?>
