<?php
/*
Plugin Name: Teste
*/
function check_int($n){
	$number = (int) $n;
	do_action('custom_check', $number);
	if ($number > 0)
		$msg = 'Positivo';
	else 
		$msg = 'Negativo';
	return apply_filters('check_msg', $msg);
}

function fn_check($n){
	var_dump($n);
	exit;
}

add_filter('check_msg', 'fn_msg');

function fn_msg($msg){
	return "<strong>$msg</strong>";
}

echo check_int(-3);