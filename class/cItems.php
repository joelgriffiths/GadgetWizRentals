<?php

include_once 'config.php';
include_once 'Item.php';

// Gotta Read AND Write for this one
class cItems {

	private $conn = null;
	private $valid = null;

    // These define the base category type
	private $itemids = array();
	private $items = array();

	function __construct( $category, $userid, $locid = null, $orderby = null, $search = null ) {
		try {
			$this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Two ways to search for a list of items
            if( is_numeric($category) || is_numeric($userid) ) {
                $this->loadFromDB($category, $userid, $locid, $orderby, $search );
            } else {
                throw new Exception("Category OR User Must Be defined");
            }
        } catch ( PDOException $e ) {
            error_log('Item Constructor: '.$e->getMessage());
            throw new Exception('Item Constructor: DB Error');
        } catch ( Exception $e ) {
            error_log('Item Constructor: '.$e->getMessage());
            throw($e);
        }
		return $this;
	}

	function close() {
        //$this->saveToDB();
		$this->conn = null;
	}

	private function loadFromDB($category, $userid, $locids, $orderby, $search, $activeonly=1) {
		try {

   
            //error_log(print_r(debug_backtrace(),true));
			$sql = "SELECT `itemid`, `category`, `condition`, `thumbnailid`,".
                    "`title`, `description`, `price`, `interval`, `deposit`, ".
                    "`tax`, `taxstate`, `taxshipping`,`deliveryradius`, ".
                    "`calculatedhourlyrate`, `pickup`, `toschecked`, `fee`, ".
                    "`lat`, `lon`, `created`, `priority`, `city`, `state`, `locid`,".
                    "`deliveryoptions` ".
                    "FROM items";
            //error_log("$search <---");
            if($search) {
			    $sql = "select itemsearch.itemid,items.itemid as searchitemid,match(itemsearch.itemtitle, itemsearch.itemdescription) AGAINST (? IN BOOLEAN MODE) as score from itemsearch, items WHERE itemsearch.itemid=items.itemid";
            } else {
			    $sql = 'SELECT `itemid` FROM items';
            }

            $and = '';
            if($search) {
                $and = ' and ';
            } elseif($category || $userid || $locids || $activeonly) { 
                $sql .= ' WHERE ';
            }

            if($category > 0) {
                $sql .= ' category = ? ';
                $and = ' and ';
            }

            if($userid > 0) {
                $sql .= $and . ' userid = ? ';
                $and = ' and ';
            }

            if($activeonly > 0) {
                $sql .= $and . ' enabled = 1 ';
                $and = ' and ';
            }

            // Deal with array of locid codes
            if($locids) {
                $scalarlocids = implode(',', array_fill(0, count($locids), '?'));
                $sql .= $and . " locid IN( $scalarlocids )";
                $and = ' and ';
            }

            // This needs serious help. Right now, no items so it's easy.
            // Definitely needs a real index ASAFP if people visit.
            /*
            select itemid from items where itemid IN (select itemid from itemsearch where match(itemtitle, itemdescription) AGAINST ('skis wakeboard water hyperlite' IN NATURAL LANGUAGE MODE));

select itemsearch.itemid,items.itemid as searchitemid,match(itemsearch.itemtitle, itemsearch.itemdescription) AGAINST ('skis wakeboard 147cm' IN BOOLEAN MODE) as score from itemsearch, items where itemsearch.itemid=items.itemid  order by score desc;
            if($search) {
                $searcharray = explode(" ", $search);
                foreach($searcharray as $searchstring)
                $sql .= $and . ' userid = ? ';
            }
            */

            if($orderby == 'priority' || 
               $orderby == 'price' ||
               $orderby == 'created' ||
               $orderby == 'created desc' ||
               $orderby == 'calculatedhourlyrate' ||
               $orderby == 'title') {
                $sql .= " order by $orderby";
            } elseif($search) {
                $sql .= " order by score desc";
            } else {
                $sql .= " order by 'priority'";
            }

			$stmt = $this->conn->prepare( $sql );

            // Sorry. Lazy coding

            $count = 1;
            if($search) {
                $stmt->bindValue($count++, $search, PDO::PARAM_STR );
            }

            if($category > 0) {
                $stmt->bindValue($count++, $category, PDO::PARAM_INT );
            }

            if($userid > 0) {
                $stmt->bindValue($count++, $userid, PDO::PARAM_INT );
            }

            // Deal with array of locid codes
            if($locids)
            foreach($locids as $locid) {
                $stmt->bindValue($count++, $locid, PDO::PARAM_STR );
            }
			$stmt->execute();
	

            $this->itemids = $stmt->fetchAll(PDO::FETCH_COLUMN,0);

        } catch ( PDOException $e ) {
              error_log("loadFromDB Exception db:".$e->getMessage());
              return false;
        } catch ( Exception $e ) {
              error_log("loadFromDB: " . $e->getMessage());
              throw $e;
              return false;
        }
        return true;
	}

    public function getItems($start, $stop) {
        if($stop == 0)
            $stop = key( array_slice( $this->itemids, -1, 1, TRUE ) );

        $numresults = $stop - $start;
        if($numresults < 0 || $start < 0 || $numresults > 100) {
            error_log("ABUSE: Negative (or too many) number of results requested");
            //return array();
        }

        //$items = array_fill($start, $numresults, new Item($this->itemids));
        $aitems = array();
        for ($i = $start; $i <= $stop; $i++) {
            if(!empty($this->itemids[$i]))
                array_push($aitems, new Item($this->itemids[$i]));
        }
        return($aitems);
    }

    public function getItemCount() {
        return count($this->itemids);
    }

    public function getPageNumber($firstitem, $itemsperpage) {
        if($itemsperpage > 0)
            return 1;

        $items = $this->getItemCount();
        $page = (int) $firstitem/$itemsperpage;
    }

    public function getItemID() {
        return($this->itemid);
    }

    public function setCategory($cat = '') {
        $this->category = $cat;
    }
    public function getCategory() {
        return($this->category);
    }

    //'Poor','Fair','Good','Like New'
    public function setCondition($condition = '') {
        $this->condition = $condition;
    }
    public function getCondition() {
        return($this->condition);
    }

    public function setThumbnailID($cat = '') {
        $this->thumbnailid = $cat;
    }
    public function getThumbnailID() {
        return($this->thumbnailid);
    }

    private function calculateRate() {
        if(!$this->interval || !$this->price) {
            return false;
        }

        // I was thinking of a sneaky way to do this, so consider yourself lucky
        if($this->interval == 'hour') {
            $this->calculatedhourlyrate = $this->price;
        } elseif($this->interval == 'day') {
            $this->calculatedhourlyrate = $this->price/24;
        } elseif($this->interval == 'week') {
            $this->calculatedhourlyrate = $this->price/24/7;
        } elseif($this->interval == 'month') {
            $this->calculatedhourlyrate = $this->price/24/30;
        } elseif($this->interval == 'year') {
            $this->calculatedhourlyrate = $this->price/24/365;
        }
        //error_log("CALC RATE: $this->calculatedhourlyrate");
    }

    public function setPrice($price = '') {
        $this->price = $price;
        $this->calculateRate();
    }
    public function getPrice() {
        return($this->price);
    }

    // 'hour','day','week','month','year'
    public function setInterval($interval = '') {
        $this->calculateRate();
        $this->interval = $interval;
        $this->calculateRate();
    }
    public function getInterval() {
        return($this->interval);
    }

    public function setDeposit($deposit = '') {
        $this->deposit = $deposit;
    }
    public function getDeposit() {
        return($this->deposit);
    }

    public function setOneTimeFee($fee) {
        $this->fee = $fee;
    }
    public function getOneTimeFee() {
        return($this->fee);
    }


    public function setTitle($title = '') {
        $this->title = $title;
    }
    public function getTitle() {
    //error_log(print_r($this,true));
        return($this->title);
    }

    public function setDescription($desc = '') {
        $this->description = $desc;
    }
    public function getDescription() {
        return($this->description);
    }

    public function setTax($tax, $taxstate, $taxshipping) {
        if($taxstate == 'No Tax')
            $this->taxstate = '';
        else
            $this->taxstate = $taxstate;

        $this->tax = $tax;
        $this->taxshipping = $taxshipping;
    }
    public function getTax() {
        return(array($this->tax, $this->taxstate, $this->taxshipping));
    }

    public function setPickup($pickup = false) {
        $this->pickup = $pickup;
    }
    public function getPickup() {
        return($this->pickup);
    }

    public function setPriority($priority) {
        $this->priority = $priority;
    }
    public function getPriority() {
        return($this->priority);
    }

    public function setCity($city) {
        $this->city = $city;
    }
    public function getCity() {
        return($this->city);
    }


    public function setState($state) {
        $this->state = $state;
    }
    public function getState() {
        return($this->state);
    }


    public function setRadius($deliveryradius) {
        $this->deliveryradius = $deliveryradius;
    }
    public function getRadius() {
        return($this->deliveryradius);
    }

    public function getEnRadius() {
        return($this->deliveryradius." Miles");
    }

    public function setLocId($locid = '') {
        if($locid === 0)
            throw new Exception("A Valid Zip Code is Required");

        $geo = new Geocode('','','',$locid);
        $this->lon = $geo->getLon();
        $this->lat = $geo->getLat();

        $this->locid = $locid;
    }
    public function getLocId() {
        return($this->locid);
    }

    public function setZip($zip = '') {
        if($zip === '')
            throw new Exception("A Valid Zip Code is Required");

        $geo = new Geocode($zip);
        $this->lon = $geo->getLon();
        $this->lat = $geo->getLat();

        $this->zip = $zip;
    }
    public function getZip() {
        return($this->zip);
    }


    public function setDeliveryOption($deliveryoptions) {
        $this->deliveryoptions = $deliveryoptions;
    }
    public function getDeliveryOption() {
        return($this->deliveryoptions);
    }
    public function getEnDeliveryOption() {
        if($this->deliveryoptions == 'pickuponly')
            return "Pickup Only";

        if($this->deliveryoptions == 'deliveryavailable')
            return "Delivery Available";

        if($this->deliveryoptions == 'deliveryrequired')
            return "Delivered Required";

    }

    public function setTos($checked) {
        if($checked === 1)
            $this->toschecked = true;
        else
            $this->toschecked = false;
    }
    public function getTos() {
        return($this->toschecked);
    }

    // 0 means no delivery offered
    public function setDeliveryRadius($DeliveryRadius = 0 ) {
        $this->deliveryradius = $DeliveryRadius;
    }
    public function getDeliveryRadius() {
        return($this->deliveryradius);
    }

    public function setLat($lat = 0) {
        $this->lat = $lat;
    }
    public function getLat() {
        return($this->lat);
    }

    public function setLon($lon = 0) {
        $this->lon = $lon;
    }
    public function getLon() {
        return($this->lon);
    }


}
