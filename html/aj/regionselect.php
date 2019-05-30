<?php
header('Content-type: application/json');
header('Content-Type: application/json; charset=utf-8');
/*
ini_set( 'default_charset', 'UTF-8' );
iconv_set_encoding("internal_encoding", "UTF-8");
iconv_set_encoding("input_encoding", "UTF-8");
iconv_set_encoding("output_encoding", "UTF-8");
*/


include_once("config.php"); //include the config
include_once("Regions.php");
//include_once("geocode.php");

$sess = new Session();
$sess->start_session('_s', false);

$user = new Users();
$lic = $user->login_check();

$regionobj = new Regions();

//error_log(print_r($_POST,true));
$sid = isset($_POST['selectid']) ? $_POST['selectid'] : 'submit';
if($sid == null) {
    $result = array("success" => false,"error" => 'This only works with a selectid');
    echo json_encode($result);
    exit;
}
//error_log("SID: $sid");

$country = isset($_POST['country']) ? $_POST['country'] : null;
$state = isset($_POST['state']) ? $_POST['state'] : null;
$city = isset($_POST['city']) ? $_POST['city'] : null;
$searchradius = isset($_POST['searchradius']) ? $_POST['searchradius'] : 0;
$searchunits = isset($_POST['searchunits']) ? $_POST['searchunits'] : 'M';

$_SESSION['searchradius'] = $searchradius;
$_SESSION['searchunits'] = $searchunits;

//error_log(print_r($_POST,true));

//error_log("CITY $city");
try {


    switch($sid) {
        case 'country':
            $state = null;
        case 'state':
            $city = null;
        case 'city':
            $closelocations = null;
    }

    $regionobj->setCountry($country);
    $countries = $regionobj->printSelectedCountries($country);

    try {
        //error_log("printSelectedStates($state, $country)");
        $states = $regionobj->printSelectedStates($state);
        //error_log($states);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        throw new Exception("DB Error");
    } catch (Exception $e) {
        $state = '';
        //$_SESSION['state'] = '';
    }
        

    try {
        $cities = $regionobj->printSelectedCities($city);
        //error_log("AJAX ENCODING:".mb_detect_encoding($cities));
    } catch (PDOException $e) {
        error_log($e->getMessage());
        throw new Exception("DB Error");
    } catch (Exception $e) {
        $city = '';
        //$_SESSION['city'] = '';
    }
        



    // Only set session on submit bc I use this in several places
    // For select boxes in other forms.
    if($sid == 'submit') {
        if($searchradius > 500) {
            unset($_SESSION['closelocations']);
        } else {
            $closelocations = $regionobj->getCloseLocations($searchradius, $searchunits);
            $_SESSION['closelocations'] = $closelocations;
            //error_log("SET SESSION = ".print_r($closelocations,true));
        }
        $_SESSION['city'] = $regionobj->getCity($city);
        $_SESSION['state'] = $regionobj->getState($state);
        $_SESSION['country'] = $regionobj->getCountry($country);
        $_SESSION['mylocid'] = $regionobj->getLocId();
    } elseif($sid == 'clear') {
        unset($_SESSION['closelocations']);
        $closelocations = '';
    } else {
        $closelocations = '';
    }

    //$closecities = $regionobj->getCloseCities();
    $closecities = array();

} catch ( PDOException $e) {
	error_log($e->getMessage());
    $result = array("success" => false,"error" => "Database Error");
    echo json_encode($result);
	exit;
} catch ( Exception $e ) {
	error_log($e->getMessage());
    $result = array("success" => false,"error" => $error . $e->getMessage());
    echo json_encode($result);
	exit;
}

//error_log("++++++++++++++".print_r($cities,true));
$result = array("success" => true, "countries" => $countries, "states" => $states, "cities" => $cities, "closecities" => $closecities, "closelocations" => $closelocations );
//error_log(print_r($mc,true));
echo json_encode($result);

?>
