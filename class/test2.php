#!/usr/bin/php
<?php

include_once 'states.php';

function testStates() {
    $states = new States();
    print_r($states);
    print($states->getStateOptions('TX'));
}

testStates();
?>
