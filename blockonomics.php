<?php

/**
 * Plugin Name: Blockonomics BTC Price plugin
 * Plugin URI: https://github.com/freefiona85/blockonomics-test
 * Description: Displaying BTC Price from Blockonomics API.
 * Version: 1.0
 * Author: Fiorina Liberta
 * Author URI: https://github.com/freefiona85
 */
wp_register_script('prefix_bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
wp_enqueue_script('prefix_bootstrap');
wp_register_style('prefix_bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
wp_enqueue_style('prefix_bootstrap');
function show_btc($atts) {
	$a = shortcode_atts( array(
      'addr' => '1dice8EMZmqKvrGE4Qc9bUFf9PX3xaYDp'
   ), $atts );
   $curr = shortcode_atts( array(
      'currency' => 'USD'
   ), $atts );
   $url = 'https://www.blockonomics.co/api/balance';
   $priceURL = 'https://www.blockonomics.co/api/price?currency='.$curr['currency'];
	$options =  array(
        'header'  => 'Content-Type:application/json',
        'method'  => 'POST',
        'body' => json_encode($a),
        'ignore_errors' => true
		 
	);  
	$contents = wp_remote_post($url, $options);
	$bodyresult = wp_remote_retrieve_body($contents);
	$object = json_decode($bodyresult);
	$response1 = $object->response[0];
	$confbalanceinsat = (int)$response1->confirmed;
	$confbalanceinbtc = $confbalanceinsat/100000000;
	$pricefiat = wp_remote_get($priceURL);
	$pricefiatbody = wp_remote_retrieve_body($pricefiat);
	$objectfiat = json_decode($pricefiatbody);
	$fiatrate = (int)$objectfiat->price;
	$confbalanceinfiat = round($confbalanceinbtc*$fiatrate,2);
	$cardreturn = "<div class='card'> <ul class='list-group list-group-flush'><li class='list-group-item'><span class='float-left'>Address</span><span style='float:right;' >".$a['addr']."</span></li>";
	$cardreturn .= "<li class='list-group-item'><span class='float-left'>Balance</span><span style='float:right;'><span>".$confbalanceinbtc." BTC / </span><span style='float:right;'> &nbsp;".$confbalanceinfiat." ".$curr['currency']." </span></span></li></ul></div>";
	return $cardreturn;
}
add_shortcode('btc', 'show_btc');