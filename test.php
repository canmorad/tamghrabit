<?php

$ch = curl_init("https://api.stripe.com/v1/charges");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo curl_error($ch);
} else {
    echo "OK";
}