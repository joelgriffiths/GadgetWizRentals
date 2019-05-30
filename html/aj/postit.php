<?php
header('Content-type: application/json');

include_once("config.php"); //include the config
include_once("CatSelector.php");
include_once("Item.php");
include_once("user.php");
include_once("userinfo.php");
include_once("cGeocode.php");
include_once("image.php");

$sess = new Session();
$sess->start_session('_s', false);

$user = new Users();
$lic = $user->login_check();
$userid = $user->getUserID();
$userinfo = new Userinfo($userid);

$itemid = isset($_POST['itemid']) && $_POST['itemid'] > 0 && is_numeric($_POST['itemid']) ? $_POST['itemid'] : 0;
$country = isset($_POST['country']) ? $_POST['country'] : '';
$state = isset($_POST['state']) ? $_POST['state'] : '';
$city = isset($_POST['city']) ? $_POST['city'] : '';
$zip = isset($_POST['itemzipcode']) ? $_POST['itemzipcode'] : '';

$ready = $userinfo->ready2Sell();
if($ready === false) {
        $result = array("success" => false,"error" => "Please complete your <a href='profile-contact.php'>Contact Information</a> before selling");
        error_log(json_encode($result));
        echo json_encode($result);
        exit;
}

if($itemid) {
    $item = new Item($itemid);
    if($userid !== $item->getUserID()) {
        error_log("ABUSE: $userid is a bad boy.");
        $result = array("success" => false,"error" => 'You cannot edit an item that\'s not yours');
        error_log(json_encode($result));
        echo json_encode($result);
        exit;
    }
} else {
    $item = new Item();
    $item->setUserID($userid);
}

//error_log("!!!!!!!!!!!!!!!!!!!!".print_r($_POST,true));
//$val = new CatSelector();
$val = new Validate($user->getUsername());

// Use try block for immediate failues
// Don't want to alert on existence username and password
// in the same response since it can be used maliciously
try {
	$error = '';
    $errors = array();
    $error_text = array();

    // No longer needed with zip in form
    try {
        //$userzip = $userinfo->getZip();
        if($country && $city && $state) {
            $geocode = new Geocode('', $country, $state, $city);
        } else {
            $geocode = new Geocode($zip, $country, $state, $city);
        }
        $locid = $geocode->getLocId();
        $country = $geocode->getCountry();

        //error_log("DISTANCE TO 29: ".$geocode->getDistance('85268'));
        //error_log("DISTANCE TO Home: ".$geocode->getDistance('78239'));
    } catch (Exception $e){
        error_log("I CAN'T FIND THE LOCATION");
        //array_push($errors, 'zipcode');
        //$error_text['zipcode'] = "I cannot find your location. Is your Zip Code Valid in your profile?";
    }


    # Get the last category that's not 0 or -1. No need to validate prior categories
    $category = !empty($_POST['category1']) && intval($_POST['category1']) > 0 ? intval($_POST['category1']):0; 
    $category = !empty($_POST['category2']) && intval($_POST['category2']) > 0 ? intval($_POST['category2']):$category; 
    $category = !empty($_POST['category3']) && intval($_POST['category3']) > 0 ? intval($_POST['category3']):$category; 
    $category = !empty($_POST['category4']) && intval($_POST['category4']) > 0 ? intval($_POST['category4']):$category; 
    $category = !empty($_POST['category5']) && intval($_POST['category5']) > 0 ? intval($_POST['category5']):$category; 
    $category = !empty($_POST['category6']) && intval($_POST['category6']) > 0 ? intval($_POST['category6']):$category; 
    try {
        $val->validateCategory($category);
        $item->setCategory($category);
    } catch (Exception $e ) {
        array_push($errors, 'category');
        $error_text['category'] = $e->getMessage();
    }

    try {
        $itemtitle = !empty($_POST['itemtitle']) ? $_POST['itemtitle'] : '';
        $val->validateTitle($itemtitle);
        $item->setTitle($itemtitle);
    } catch (Exception $e ) {
        array_push($errors, 'itemtitle');
        $error_text['itemtitle'] = $e->getMessage();
    }


    try {
        $itemdesc = !empty($_POST['itemdesc']) ? $_POST['itemdesc'] : '';
        $val->validateDescription($itemdesc);
        $itemdesc = preg_replace("/\r\n/", "<br />", $itemdesc);
        $item->setDescription($itemdesc);
    } catch (Exception $e ) {
        array_push($errors, 'itemdesc');
        $error_text['itemdesc'] = $e->getMessage();
    }


    try {
        $itemrentalfee = !empty($_POST['itemrentalfee']) ? $_POST['itemrentalfee'] : '';
        $iteminterval = !empty($_POST['iteminterval']) ? $_POST['iteminterval'] : '';
        $itemrentalfee = $val->validateRentalFee($itemrentalfee, $iteminterval);
        $item->setPrice($itemrentalfee);
        $item->setInterval($iteminterval);
    } catch (Exception $e ) {
        array_push($errors, 'itemrentalfee');
        $error_text['itemrentalfee'] = $e->getMessage();
    }


    try {
        $itemtax = !empty($_POST['tax']) ? $_POST['tax'] : 0;
        $itemtaxstate = !empty($_POST['taxstate']) ? $_POST['taxstate'] : '';
        $itemtax = $val->validateItemTax($itemtax, $itemtaxstate);
        $item->setTax($itemtax, $itemtaxstate, '');
    } catch (Exception $e ) {
        array_push($errors, 'itemrentalfee');
        $error_text['itemrentalfee'] = $e->getMessage();
    }

    try {
        $deliveryoptions = !empty($_POST['deliveryoptions']) ? $_POST['deliveryoptions'] : '';
        $deliveryradius = !empty($_POST['deliveryradius']) ? $_POST['deliveryradius'] : '';
        //$itemzipcode = !empty($_POST['itemzipcode']) ? $_POST['itemzipcode'] : '';
        $itemonetimefee = !empty($_POST['itemonetimefee']) ? $_POST['itemonetimefee'] : 0;
        list($deliveryoption, $locid, $radius, $deliveryfee) = $val->validateDeliveryOptions($deliveryoptions, $locid, $deliveryradius, $itemonetimefee);

        $item->setDeliveryOption($deliveryoption);
        //$item->setZip($zip);
        error_log("11111111111111 $zip, $country, $state, $city");
        $item->setLocId($locid);
        $item->setLat($geocode->getLat());
        $item->setLon($geocode->getLon());
        $item->setCountry($geocode->getCountry());
        $item->setCity($geocode->getCity());
        $item->setState($geocode->getState());
        $zippy = $geocode->getZip();
        /* Getting the center of the city */
        //$item->setZip($zippy, $country);
        $item->setOneTimeFee($deliveryfee);
        $item->setRadius($radius);
    } catch (Exception $e ) {
        array_push($errors, 'deliveryoptions');
        $error_text['deliveryoptions'] = $e->getMessage();
    }

    try {
        $deposit = !empty($_POST['itemdeposit']) ? $_POST['itemdeposit'] : 0;
        $deposit = $val->validateDeposit($deposit);
        $item->setDeposit($deposit);
    } catch (Exception $e ) {
        array_push($errors, 'itemdeposit');
        $error_text['itemdeposit'] = $e->getMessage();
    }

    $item->setPriority(0);

    $agreed = !empty($_POST['agreed']) && $_POST['agreed'] == 'on' ? 1 : 0;
    error_log("Agreed: $agreed");
    $item->setTos($agreed);
    if($agreed != 1) {
        array_push($errors, 'agreed');
        $error_text['agreed'] = "Please read and accept the Leasor Terms of Service";;
    }


    $errorstring = '';
    foreach($errors as $error) {
        error_log($error_text[$error]);
        $errorstring .= $error_text[$error] . '<br />';
    }

    if($errorstring !== '')
        throw new Exception("$errorstring");



} catch ( Exception $e ) {
	$result = array("success" => false,"error" => $errorstring,"badfields" => "$errors");
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
} catch ( PDOException $e) {
	error_log($e->getMessage());
	$error .= "Database Error";
	$result = array("success" => false,"error" => "DB Error");
	echo json_encode($result);
	exit;
}


if ($error !== '') {
	$result = array("success" => false,"error" => $error);
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}
try {
    $itemid = $item->saveToDB($userid);

    $image = new Image();
    for($i = 0; $i < 10; $i++) {
        $imgname = 'image'.$i;
        if(!empty($_POST[$imgname]))
            $image->makeImagePermanent($_POST[$imgname], $userid, $itemid, $i);
    }
} catch (Exception $e) {
	$result = array("success" => false,"error" => $e->getMessage());
	error_log(json_encode($result));
	echo json_encode($result);
	exit;
}

error_log("ITEMID ITEMID $itemid");
$result = array("success" => true, "itemID" => $itemid);

echo json_encode($result);
?>
