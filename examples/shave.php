<?php

$jaime = require('lib/jaime.php');

$jaime->please(function($jaime){
	$template = "the first number is {{number}} and the second is {{square|duplicate|number}}";
	$string = $jaime->shave($template,array('number'=>2),array(
		'duplicate'=>function($v){
			return $v * 2;
		},
		'square'=>function($v){
			return $v * 2;
		},
	));
	$jaime->say($string);
})->work();
?>