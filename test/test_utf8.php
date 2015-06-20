<?php
	$t = "Av Combarbal\xc3\xa1 030 La Granja Santiago";
	echo utf8_decode($t) . "\n";
	echo utf8_encode($t) . "\n";
	$t = "&#039;3&#039;";
	echo utf8_decode($t) . "\n";
	echo utf8_encode($t) . "\n";
	echo html_entity_decode($t, ENT_QUOTES) . "\n";
?>
