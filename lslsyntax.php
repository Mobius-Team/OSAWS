<?php
/*
 * OSAWS BSD License
 * Copyright (c) Contributors, Stolen Ruby and Mobius Team https://mobiusteam.us/
 * See CONTRIBUTORS.TXT for a full list of copyright holders.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Stolen Ruby, Mobius Team and the Mobius Project nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE DEVELOPERS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
	include_once "includes/config.php";
	include_once "includes/functions.php";

	$URL = $_SERVER['REQUEST_URI'];
	
	if($URL[0] == "/")
		$URL = substr( $URL, 1 );

	$UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
	
	preg_match($UUIDv4, $URL) or die('Not valid UUID');
	
	$PATH = LSLSyntaxDir . $URL . ".xml";
	
	if( file_exists( $PATH ) ) {
		srLog( "LSLSyntax", "Serving " . $URL . ".xml to " . getRealIpAddr() );
		header('Content-Type: application/xml; charset=utf-8');
		echo file_get_contents( $PATH );
	}
	else 
	{
		srLog( "LSLSyntax", getRealIpAddr() . " requested an xml that doesn't exist. (" . $URL . ")" );
		die('Invalid Syntax ID');
	}
?>