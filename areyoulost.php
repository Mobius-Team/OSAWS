<?php
	include_once "includes/config.php";
	
	echo AreYouLostMessage;
	
	include_once "includes/functions.php";
	
	srLog( "LOST", getRealIpAddr() . " visited IP directly." );
?>