<?php

include_once "Reservation.php";
include_once "ShortIPN.php";

$email = 'ipntest@gadgetwiz.com';

// tell PHP to log errors to ipn_errors.log in this directory
ini_set('log_errors', true);
ini_set('error_log', '/var/log/ipn_errors.log');
//echo dirname(__FILE__).'/ipn_errors.log';

// intantiate the IPN listener
include('ipnlistener.php');
$listener = new IpnListener();

// tell the IPN listener to use the PayPal test sandbox
$listener->use_sandbox = USESANDBOX;

// try to process the IPN POST
try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}

if ($verified) {

    $errmsg = '';   // stores errors from fraud checks
    $warnmsg = '';   // stores warnings that fail, but result in a payment anyways
    

    $reservationid = isset($_POST['item_number']) ? $_POST['item_number'] : 0;
    $test_ipn = isset($_POST['test_ipn']) ? $_POST['test_ipn'] : 0;
    $txn_id = isset($_POST['txn_id']) ? $_POST['txn_id'] : null;
    $payer_email = isset($_POST['payer_email']) ? $_POST['payer_email'] : null;
    $mc_gross = isset($_POST['mc_gross']) ? $_POST['mc_gross'] : null;

error_log("---------------------------IPN--------------------------");
error_log(print_r($_POST,true));
error_log('reservationid:'.$reservationid);
error_log('test_ipn:'.$test_ipn);
error_log('txn_id:'.$txn_id);
error_log('payer_email:'.$payer_email);
error_log('mc_gross: '.$mc_gross);
error_log("--------------------------------------------------------");
    
    try {
        $resobj = new Reservation($reservationid);
    } catch (Exception $e) {
        $errmsg .= "Invalid Reservation $reservationid ".$e->getMessage();
        // Need to make this a function
    }

    // 1. Make sure the payment status is "Completed" 
    if ($_POST['payment_status'] != 'Completed') { 
        // simply ignore any IPN that is not completed
        exit(0); 
    }

    // 2. Make sure seller email matches your primary account email.
    if ($_POST['receiver_email'] != SELLER_EMAIL) {
        $warnmsg .= "'receiver_email' does not match: ".SELLER_EMAIL ." != ";
        $warnmsg .= $_POST['receiver_email']."\n";
    }
    
    // 3. Make sure the amount(s) paid match
    $totaldue = $resobj->getTotal();
    $resfee = $resobj->getResFee();
    if ($mc_gross == $totaldue && $totaldue > 0) {
        $resobj->setStatus('paidinfull');
    } elseif ($mc_gross == $resfee && $resfee > 0) {
        $resobj->setStatus('resfeepaid');
    } else {
        $warnmsg .= "'mc_gross' ($mc_gross) does not match Deposit ($deposit) or Total Due ($totaldue)\n";
    }
    
    // 4. Make sure the currency code matches
    if ($_POST['mc_currency'] != 'USD') {
        $warnmsg .= "'mc_currency' does not match: ";
        $warnmsg .= $_POST['mc_currency']."\n";
    }

    // TODO: Check for duplicate txn_id
    $ipn = new ShortIPN();
    if($ipn->checkDuplicateTXN($txn_id)) {
        $errmsg .= "Duplicate TXN_ID $txn_id\n";
    }
    
    if (!empty($errmsg)) {
    
        // manually investigate errors from the fraud checking
        $body = "IPN failed fraud checks: \n$errmsg\n\n";
        $body .= $listener->getTextReport();
        error_log($errmsg);
        mail('$email', "IPN Fraud Warning for $reservationid", $body);
        
    } else {
        $ipn->recordPayment($reservationid, 0, $txn_id, $payer_email, $mc_gross, $test_ipn);
        error_log("Payment for Reservation $reservationid is confirmed");
        if (!empty($warnmsg)) {
            // manually investigate errors from the fraud checking
            $body = "IPN Warning: \n$warnmsg\n\n";
            $body = "Payment has been processed, but Reseration Status has not changed.\n\n";
            $body .= $listener->getTextReport();
            error_log($warnmsg);
            mail('$email', "IPN Fraud Warning for ORDER $reservationid", $body);
        } else {
            $resobj->saveReservation();
            error_log("Reservation $reservationid is confirmed");
        }
    }
    
} else {
    // manually investigate the invalid IPN
    mail('$email', 'Invalid IPN', $listener->getTextReport());
}


/*
if ($verified) {
    // TODO: Implement additional fraud checks and MySQL storage
    mail('joelg@gadgetwiz.com', 'Valid IPN', $listener->getTextReport());
} else {
    // manually investigate the invalid IPN
    mail('joelg@gadgetwiz.com', 'Invalid IPN', $listener->getTextReport());
}
*/

?>
