<?php

include_once 'config.php';

// Only for reading categories now. Perhaps an admin interface later
// will force updates.
class Category {

	private $conn = null;

    // These define the base category type
	private $catid = 0;
	private $parentid = 0;
	private $children = array();
    private $urlname = '';
    private $humanname = '';
    private $cattree = array();

	function __construct( $category = 0 ) {
		try {
			$this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Ugh I should just make two functions, but I'm being stubborn
            $category = preg_replace("/[^A-Za-z0-9-]/", '', $category);
            if( (is_numeric($category) && $category > 0) ||
                (!is_numeric($category) &&  strlen($category) > 0)) {
                $this->loadFromDB($category);
            } else {
                // Accept the defaults - Root Dir
            }
        } catch ( PDOException $e ) {
            error_log($e->getMessage());
            return false;
        } catch ( Exception $e ) {
            error_log($e->getMessage());
            throw($e);
        }
		return $this;
	}

	function close() {
		$this->conn = null;
	}

	function loadFromDB($category) {
		try {

            if(is_numeric($category)) {
			    $sql = "SELECT catid,parentid,urlname,humanname FROM category where catid = :catid";
			    $stmt = $this->conn->prepare( $sql );
                $stmt->bindValue(":catid", $category, PDO::PARAM_INT );
            } else {
			    $sql = "SELECT catid,parentid,urlname,humanname FROM category where urlname = :urlname";
			    $stmt = $this->conn->prepare( $sql );
                $stmt->bindValue(":urlname", $category, PDO::PARAM_INT );
            }
			$stmt->execute();
	
			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
                throw new Exception("Invalid Category ID $category");
            }

			list($this->catid, $this->parentid, $this->urlname, $this->humanname) = $stmt->fetch(PDO::FETCH_NUM);

            if(!($this->catid === $category || $this->urlname === $category)) {
                //throw new Exception("Record not found");
            }

        } catch ( PDOException $e ) {
              error_log("Catgory constuctor failure db");
              return false;
        } catch ( Exception $e ) {
              error_log("Catgory constuctor failure" . $e->getMessage());
              throw $e;
              return false;
        }
        return true;
	}


    function addCategory($humanname, $urlname) {
    	try {
            if(strtolower($urlname) !== $urlname)
                throw new Exception("urlname must be lowercase");

            $sql = "SELECT catid, humanname,urlname FROM category where humanname = :humanname or urlname = :urlname";
			$stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":humanname", $humanname, PDO::PARAM_INT );
            $stmt->bindValue(":urlname", $urlname, PDO::PARAM_INT );
			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows != 0 ) {
			    list ($badcat, $badhn, $badurl) = $stmt->fetch();

                throw new Exception("Duplicate Human Name or URL Names for $badcat, $badhn, $badurl");
            }

			$sql = "INSERT INTO category (parentid, humanname, urlname) VALUES (:catid, :humanname, :urlname)";
			$stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":catid", $catid, PDO::PARAM_INT );
            $stmt->bindValue(":humanname", $humanname, PDO::PARAM_INT );
            $stmt->bindValue(":urlname", $urlname, PDO::PARAM_INT );

			$stmt->execute();
	
			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
                throw new Exception("Impossible");
            }

			$topcat = $stmt->fetchAll(PDO::FETCH_COLUMN,0);

            return $topcat;
        } catch ( PDOException $e ) {
              throw new Exception('DB Error ');
        }
    }

    // Create a secondary record for that
	function getURL() {
        if($this->urlname == '')
            return "0";
        else
            return $this->urlname;
	}

	function getURLHTML() {
        if($this->urlname == '')
            return "0.html";
        else
            return $this->urlname.'.html';
	}

	function GetName() {
        return $this->humanname;
	}

	function getCatID() {
        return $this->catid;
	}

	function GetParentID() {
        return $this->parentid;
	}

    // I could do this with new object, but it seems too intensive
    private function getNextParent($catid) {
    	try {
			$sql = "SELECT parentid from category where catid = :catid";
			$stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":catid", $catid, PDO::PARAM_INT );
	
			$stmt->execute();
	
			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
                return false;
            }

			$parent = $stmt->fetch(PDO::FETCH_NUM);

            //error_log($this->urlname.print_r($children,true));
            return $parent[0];
        } catch ( Exception $e ) {
              error_log("GetChildMembers($this->itemid): $e->getMessage()");
              throw new Exception('Could not get children for Category ' . $this->itemid);
        }
    }

    // Peers AND Level1 values
    private function getTopCat() {
    	try {
			$sql = "SELECT catid from category where parentid = 0";
			$stmt = $this->conn->prepare( $sql );
	
			$stmt->execute();
	
			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
                throw new Exception("Impossible");
            }

			$topcat = $stmt->fetchAll(PDO::FETCH_COLUMN,0);

            return $topcat;
        } catch ( Exception $e ) {
              error_log("GetChildMembers($this->itemid): $e->getMessage()");
              throw new Exception('Could not get children for Category ' . $this->itemid);
        }
    }


    // Peers 
    private function getPeers($parentid) {
    	try {
			$sql = "SELECT catid from category where parentid = :parentid";
			//error_log("SELECT catid from category where parentid = $parentid");
			$stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":parentid", $parentid, PDO::PARAM_INT );
	
			$stmt->execute();
	
			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
                throw new Exception("Impossible");
            }

			$peers = $stmt->fetchAll(PDO::FETCH_COLUMN,0);
            return $peers;
        } catch ( Exception $e ) {
              error_log("GetPeers($parentid): $e->getMessage()");
              throw new Exception('Could not get children for Category ' . $parentid);
        }
    }

    // Get an array with all the proper select boxes.
	function getSBArray() {
        $parents = array(null,null,null,null,null,null);
        if($this->catid != 0)
            array_unshift($parents, $this->catid);

        if($this->parentid != 0)
            array_unshift($parents, $this->parentid);

        $newParent = $this->parentid;
        while($newParent = $this->getNextParent($newParent)) {
            error_log("Push: ".print_r($newParent,true));
            if($newParent != 0)
                array_unshift($parents, $newParent);
        }
        return $parents;
	}

	function getVisArray() {
        $parents = array_merge($this->getPeers($this->parentid), $this->getTopCat());
        array_push($parents, $this->parentid);
        $newParent = $this->parentid;
        while($newParent = $this->getNextParent($newParent)) {
            //error_log("Push: ".print_r($newParent,true));
            array_push($parents, $newParent);
            $parents = array_merge($parents, $this->getPeers($newParent));
        }
        $aVis = array_merge($parents,$this->getChildMembers());
        //error_log("PAR:".print_r($aVis,true));
        return $aVis;
	}

	function getChildTree($catid = null) {
        $this->cattree = array();
        $this->buildChildTree($catid);
        return $this->cattree;
    }
    // Recursion -- Again
	function buildChildTree($catid = null, $loop = 0) {
        if($catid === null) {
            $catid = $this->catid;
        }
        $cats = $this->getChildMembers($catid);
        $tempcats = array();
        foreach($cats as $cat) {
            array_push($this->cattree, $cat);
            $tempcats = $this->buildChildTree($cat, $loop++);
        }
        array_merge($cats, $tempcats);
        return $cats;
    }

	function getChildMembers($catid = null) {
    	try {
			$sql = "SELECT catid from category where parentid = :parentid order by humanname";
			//error_log("SELECT catid from category where parentid = ".$this->catid." order by humanname");
			$stmt = $this->conn->prepare( $sql );
            if($catid === null) {
                $stmt->bindValue(":parentid", $this->catid, PDO::PARAM_INT );
            } else {
                //error_log("SELECT catid from category where parentid = $catid;");
                $stmt->bindValue(":parentid", $catid, PDO::PARAM_INT );
            }
	
			$stmt->execute();
	
			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
                return array();
            }

			//$children = $stmt->fetchAll(PDO::FETCH_NUM);
			$children = $stmt->fetchAll(PDO::FETCH_COLUMN,0);

            //error_log($this->urlname.print_r($children,true));
            return $children;
        } catch ( Exception $e ) {
              error_log("GetChildMembers($this->itemid): $e->getMessage()");
              throw new Exception('Could not get children for Category ' . $this->itemid);
         }
	}

 }
