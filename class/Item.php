<?php

include_once 'config.php';
include_once 'cGeocode.php';
include_once 'image.php';

// Gotta Read AND Write for this one
class Item {

	private $conn = null;
	private $valid = null;

    // These define the base category type
	private $itemid = 0;

    private $userid = null;
    private $category = null;
    private $condition = null;
    private $thumbnailid = 0;
    private $minnotice = 1;
    private $price = null;
    private $interval = null;
    private $deposit = "0.00";
    private $calculatedhourlyrate = null;
    private $pickup = null;
    private $toschecked = false;
    private $fee = "0.00";
    private $created = null;
    private $priority = null;
    private $country = null;
    private $city = null;
    private $state = null;
    private $zip = null;
    private $locid = null;
    private $deliveryoptions = null;

    private $title = null;
    private $description = null;
    private $tax = null;
    private $taxstate = null;
    private $taxshipping = null;
    private $deliveryradius = null;
    private $lat = null;
    private $lon = null;

    private $geocodeobj = null;

	function __construct( $itemid = 0 ) {
		try {
			$this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Ugh I should just make two functions, but I'm being stubborn
            if( is_numeric($itemid) && $itemid > 0 ) {
                $this->loadFromDB($itemid);
            } else {
                // Accept the defaults -- itemid = 0
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

	private function loadFromDB($itemid) {
		try {

			$sql = "SELECT `itemid`, `userid`, `category`, `condition`, `thumbnailid`,".
                    "`title`, `description`, `price`, `interval`, `deposit`, ".
                    "`tax`, `taxstate`, `taxshipping`,`deliveryradius`, ".
                    "`calculatedhourlyrate`, `pickup`, `toschecked`, `fee`, ".
                    "`lat`, `lon`, `created`, `priority`, `country`,`city`, `state`, `zip`, ".
                    "`locid`, `deliveryoptions`,`minnotice`".
                    "FROM items where itemid = :itemid";
			$stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":itemid", $itemid, PDO::PARAM_INT );
			$stmt->execute();
	
			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
                throw new Exception("That item does not exists: $itemid");
            }

			list( $this->itemid, $this->userid, $this->category, $this->condition,
                  $this->thumbnailid, 
                  $this->title, $this->description, $this->price, $this->interval, $this->deposit,
                  $this->tax, $this->taxstate, $this->taxshipping, $this->deliveryradius,
                  $this->calculatedhourlyrate, $this->pickup, $this->toschecked, $this->fee,
                  $this->lat, $this->lon, $this->created, $this->priority, $this->country, $this->city, $this->state, $this->zip,$this->locid, $this->deliveryoptions, $this->minnotice)
                  = $stmt->fetch(PDO::FETCH_NUM);

            try {
                //$this->geocodeobj = new Geocode($this->zip, $this->country);
                $this->geocodeobj = new Geocode('','','','',$this->locid);
            } catch (Exception $e) {
                error_log("Cannot get coordinates, but don't want to break the website");
            }

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


	public function saveToDB($uid) {
		try {

            /*
            //error_log("-------------------------------------------------");
            //error_log("$this->lon, $this->lat");
            //error_log(print_r(debug_backtrace(), TRUE));
            //error_log("----------------------%--------------------------");
            */

            if($uid !== $this->userid) {
                error_log("ABUSE: $userid tried to add records for $this->userid");
                exit;
            }
            if($this->itemid) {
			    $sql = "UPDATE `items` SET `userid`=:userid, `category`=:category, `condition`=:condition, `thumbnailid`=:thumbnailid, `minnotice`=:minnotice, `title`=:title, `description`=:description, `price`=:price, `interval`=:interval, `deposit`=:deposit, `tax`=:tax, `taxstate`=:taxstate, `taxshipping`=:taxshipping, `deliveryradius`=:deliveryradius, `calculatedhourlyrate`=:calculatedhourlyrate, `pickup`=:pickup, `toschecked`=:toschecked, `fee`=:fee, `lat`=:lat, `lon`=:lon, `priority`=:priority, `country`=:country, `city`=:city, `state`=:state, `zip`=:zip, `locid`=:locid, `deliveryoptions`=:deliveryoptions where `itemid`=:itemid";
			    $stmt = $this->conn->prepare( $sql );
                $stmt->bindValue(":itemid", $this->itemid, PDO::PARAM_INT );
            } else {
			    $sql = "INSERT INTO `items` (`userid`, `category`, `condition`, `thumbnailid`, `minnotice`, `title`, `description`, `price`, `interval`, `deposit`, `tax`, `taxstate`, `taxshipping`, `deliveryradius`, `calculatedhourlyrate`, `pickup`, `toschecked`, `fee`,`lat`, `lon`, `priority`, `country`, `city`, `state`, `zip`, `locid`, `deliveryoptions`) values (:userid, :category, :condition, :thumbnailid, :minnotice, :title, :description, :price, :interval, :deposit, :tax, :taxstate, :taxshipping, :deliveryradius, :calculatedhourlyrate, :pickup, :toschecked, :fee, :lat, :lon, :priority, :country, :city, :state, :zip, :locid, :deliveryoptions)";
			    $stmt = $this->conn->prepare( $sql );
            }
            $stmt->bindValue(":userid", $this->userid, PDO::PARAM_INT );
            $stmt->bindValue(":category", $this->category, PDO::PARAM_INT );
            $stmt->bindValue(":condition", $this->condition, PDO::PARAM_STR );
            $stmt->bindValue(":thumbnailid", $this->thumbnailid, PDO::PARAM_INT );
            $stmt->bindValue(":minnotice", $this->minnotice, PDO::PARAM_INT );
            $stmt->bindValue(":title", $this->title, PDO::PARAM_STR );
            $stmt->bindValue(":description", $this->description, PDO::PARAM_STR );
            $stmt->bindValue(":price", $this->price, PDO::PARAM_STR );
            $stmt->bindValue(":interval", $this->interval, PDO::PARAM_STR );
            $stmt->bindValue(":deposit", $this->deposit, PDO::PARAM_STR );
            $stmt->bindValue(":tax", $this->tax, PDO::PARAM_STR );
            $stmt->bindValue(":taxstate", $this->taxstate, PDO::PARAM_STR );
            $stmt->bindValue(":taxshipping", $this->taxshipping, PDO::PARAM_INT );
            $stmt->bindValue(":deliveryradius", $this->deliveryradius, PDO::PARAM_INT );
            $stmt->bindValue(":calculatedhourlyrate", $this->calculatedhourlyrate, PDO::PARAM_STR );
            $stmt->bindValue(":pickup", $this->pickup, PDO::PARAM_STR );
            $stmt->bindValue(":toschecked", $this->toschecked, PDO::PARAM_STR );
            $stmt->bindValue(":fee", $this->fee, PDO::PARAM_STR );
            $stmt->bindValue(":lat", $this->lat, PDO::PARAM_STR );
            $stmt->bindValue(":lon", $this->lon, PDO::PARAM_STR );
            $stmt->bindValue(":priority", $this->priority, PDO::PARAM_INT );
            $stmt->bindValue(":country", $this->country, PDO::PARAM_STR );
            $stmt->bindValue(":city", $this->city, PDO::PARAM_STR );
            $stmt->bindValue(":state", $this->state, PDO::PARAM_STR );
            $stmt->bindValue(":zip", $this->zip, PDO::PARAM_STR );
            $stmt->bindValue(":locid", $this->locid, PDO::PARAM_STR );
            $stmt->bindValue(":deliveryoptions", $this->deliveryoptions, PDO::PARAM_STR );
			$stmt->execute();

            if($this->itemid == 0) {
                $this->itemid = $this->conn->lastInsertId();
            }

			$sql = "REPLACE INTO `itemsearch` (`itemid`, `itemtitle`, `itemdescription`) values (:itemid, :title, :description)";
		    $stmt = $this->conn->prepare( $sql );
            $stmt->bindValue(":itemid", $this->itemid, PDO::PARAM_INT );
            $stmt->bindValue(":title", $this->title, PDO::PARAM_STR );
            $stmt->bindValue(":description", $this->description, PDO::PARAM_STR );
			$stmt->execute();
	
        } catch ( PDOException $e ) {
              error_log("save2DB db: ".$e->getMessage());
              throw new Exception("saveToDB DB Failure");
        } catch ( Exception $e ) {
              error_log("save2DB: " . $e->getMessage());
              throw($e);
        }
        return $this->itemid;
	}


    public function getSubtotal($startdate, $numintervals) {
            $subtotal = $numintervals * $this->price;

            // let's print the international format for the en_US locale
            return($subtotal);

    }

	public function deleteFromDB($userid) {
		try {

            if($userid !== $this->userid) {
                error_log("ABUSE: $userid tried to delete records from $this->userid");
                exit;
            }
            if($this->itemid) {
                $imageobj = new Image();
                $images = $imageobj->getAllDBImages($this->itemid, 'listing');
			    $sql = 'DELETE from items where `itemid`=:itemid and `userid`=:userid';
			    $stmt = $this->conn->prepare( $sql );
                $stmt->bindValue(":itemid", $this->itemid, PDO::PARAM_INT );
                $stmt->bindValue(":userid", $this->userid, PDO::PARAM_INT );
			    $rows = $stmt->execute();
                if($rows == 1) {
                    error_log("Item Deleted:".$this->itemid);
                    foreach($images as $imgid) {
                        error_log("    Delete Image:".$imgid);
                        $imageobj->deleteImage($imgid, $userid);
                    }

			        $sql = 'DELETE from itemsearch where `itemid`=:itemid';
			        $stmt = $this->conn->prepare( $sql );
                    $stmt->bindValue(":itemid", $this->itemid, PDO::PARAM_INT );
			        $rows = $stmt->execute();
                }
            }
	
        } catch ( PDOException $e ) {
              error_log("deleteFromDB: ".$e->getMessage());
              throw new Exception("saveToDB DB Failure");
        } catch ( Exception $e ) {
              error_log("deleteFromDB: " . $e->getMessage());
              throw($e);
        }
        return $this->itemid;
	}


    public function getItemID() {
        return($this->itemid);
    }

    public function checkBuyerDistance($zip,$country) {
        return($this->geocodeobj->getDistance($zip,$country));
    }

    public function setUserID($userid) {
        $this->userid = $userid;
    }
    public function getUserID() {
        return($this->userid);
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

    public function setMinNotice($days = 1) {
        $this->minnotice = $cat;
    }
    public function getMinNotice() {
        return($this->minnotice);
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
    public function printIntervalSB() {
        $options = '';
        foreach(array('hour','day','week','month','year') as $interval) {
            $selected = '';
            if($this->interval == $interval)
                $selected=" selected";

            $options .= '<option value="'.$interval.'"'.$selected.'>'.ucfirst($interval)."</option>\n";
        }
        return $options;
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
        $desc = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $this->description);
        return($desc);
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
    public function getTaxRate() {
        return((float)$this->tax);
    }
    public function getTaxState() {
        return($this->taxstate);
    }
    public function getTaxShipping() {
        return($this->taxshipping);
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

    public function setCountry($country) {
        $this->country = $country;
    }
    public function getCountry() {
        if($this->country)
            $country = $this->country;
        else
            $country = $this->geocodeobj->getCountry();

        return($country);
    }

    public function setCity($city) {
        $this->city = $city;
    }
    public function getCity() {
        if($this->city)
            $city = $this->city;
        else
            $city = $this->geocodeobj->getCity();

        return($city);
    }


    public function setState($state) {
        $this->state = $state;
    }
    public function getState() {
        if($this->state)
            $state = $this->state;
        else
            $state = $this->geocodeobj->getState();

        return($state);
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

    public function printRadiusSB() {
        $options = '';
        foreach(array(0,10,20,30,40,50,60,70,80,90,100,200,500,999999) as $distance) {
            $selected = '';
            if($this->deliveryradius == $distance)
                $selected=" selected";

            $engdist = $distance == 999999 ? 'Anywhere' : $distance.' Miles';
            $options .= '<option value="'.$distance.'"'.$selected.'>'.$engdist."</option>\n";
        }
        return $options;
    }

    public function printUnitsSB() {
        $options = '';
        foreach(array('M','KM') as $distance) {
            $selected = '';
            if($this->deliveryradius == $distance)
                $selected=" selected";

            $engdist = $distance == 999999 ? 'Anywhere' : $distance.' Miles';
            $options .= '<option value="'.$distance.'"'.$selected.'>'.$engdist."</option>\n";
        }
        return $options;
    }


    public function setLocId($locid) {
        $geo = new Geocode('','','','',$locid);
        if($geo->getLon() === 0)
            throw new Exception("A Valid Zip Code is Required");

        $this->lon = $geo->getLon();
        $this->lat = $geo->getLat();

        $this->locid = $locid;
    }
    public function getLocId() {
        return($this->locid);
    }

    public function setZip($zip, $country) {
        $geo = new Geocode($zip, $country);
        if($geo->getLon() === 0)
            throw new Exception("A Valid Postal Code is Required");

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

        if($this->deliveryoptions == 'delivertofixedlocation')
            return "Delivered To Specific Location";
    }
    public function getDeliveryOptionSB($choice) {
        if($this->deliveryoptions == $choice)
            return "selected";
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
