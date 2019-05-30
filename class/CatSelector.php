<?php


class CatSelector {

    function __construct( $category = 0 ) {
        try {
            $this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

    // Used to populate existing item select boxes
    public function printSelectedOptions($catid) {
        $parentid = $this->getParentId($catid);
        echo $this->getOptions($parentid,$catid);
    }

    // Not Used to populate anything right now I don't think
    public function printOptions($parentid) {
        echo $this->getOptions($parentid);
    }

    public function getOptions($parentid, $catid='') {
        $peers = $this->getAssocPeers($parentid);

        $options = '<option value="-1"></option>';
        foreach($peers as $option) {
            if($option['catid'] === $catid)
                $options .= '<option value="'.$option['catid'].'" selected>'.$option['humanname']."</option>\n";
            else
                $options .= '<option value="'.$option['catid'].'">'.$option['humanname']."</option>\n";
        }
        return $options;
    }

    public function getNextLevel() {
        try {
            $sql = "SELECT catid,humanname from category where parentid = :catid";
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":catid", $this->catid, PDO::PARAM_INT );

            $stmt->execute();

            $numRows = $stmt->rowCount();
            if( $numRows == 0 ) {
                return array();
            }

            $subcats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $subcats;
        } catch ( Exception $e ) {
              error_log("GetPeers($parentid): $e->getMessage()");
              throw new Exception('Could not get children for Category ' . $parentid);
        }
    }


    private function getAssocPeers($parentid) {
        try {
            $sql = "SELECT catid,humanname from category where parentid = :parentid";
            //error_log("SELECT catid from category where parentid = $parentid");
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":parentid", $parentid, PDO::PARAM_INT );

            $stmt->execute();

            $numRows = $stmt->rowCount();
            if( $numRows == 0 ) {
                return array();
            }

            $peers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $peers;
        } catch ( Exception $e ) {
              error_log("GetPeers($parentid): $e->getMessage()");
              throw new Exception('Could not get children for Category ' . $parentid);
        }
    }

    private function getParentId($catid) {
        try {
            $sql = "SELECT parentid from category where catid = :catid";
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":catid", $catid, PDO::PARAM_INT );

            $stmt->execute();

            $numRows = $stmt->rowCount();
            if( $numRows == 0 ) {
                return '';
            }

            $parentid = $stmt->fetch(PDO::FETCH_COLUMN);
            return $parentid;
        } catch ( Exception $e ) {
              error_log("GetParentId($parentid): $e->getMessage()");
              throw new Exception('Could not get ParentID for Category ' . $catid);
        }
    }
}
