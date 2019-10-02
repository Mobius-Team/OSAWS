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

include("includes/config.php");
include("includes/functions.php");

#
# The XMLRPC server object
#

$xmlrpc_server = xmlrpc_server_create();

#
# Search avatars
#

xmlrpc_server_register_method( $xmlrpc_server, "searchavatars", "searchavatars" );

function searchavatars( $method_name, $params, $app_data ) {
	$request = $params[0];
	$theSearch = $request["SearchValue"];
	
	srLog( "AvatarPicker", "Search for '" . $theSearch . "' by " . getRealIpAddr() );

	$chunks = explode( " ", $theSearch );
	$theSearch = "%" . $theSearch . "%";

	// Create connection
	$conn = new mysqli( SQLServerName, SQLUserName, SQLPassword, SQLDatabase );
	
	// Check connection
	if ( $conn->connect_error ) {
		die;
	}

	// Set the charset
	mysqli_set_charset( $conn, "utf8mb4" ); 
	header('Content-Type: text/html; charset=utf-8');

	$sql = "SELECT UserAccounts.PrincipalID as UserID, FirstName, LastName, FALSE as IsHG, DisplayName, UserAccounts.NameChanged as Changed
			FROM UserAccounts
			WHERE UserAccounts.FirstName LIKE ? OR UserAccounts.LastName LIKE ? OR UserAccounts.DisplayName LIKE ?
			UNION ALL
			SELECT UserID, NULL as FirstName, NULL as LastName, TRUE as IsHG, GridUser.DisplayName as DisplayName, GridUser.NameCached as Changed 
			FROM GridUser
			WHERE GridUser.UserID LIKE ? OR GridUser.DisplayName LIKE ?";
		
	$stmt = $conn->stmt_init();

	if( !$stmt->prepare( $sql ) )
	{
		echo "Failed to prepare statement\n";
		die();
	}
	
	$userIDSearch = "%;%;%" . $theSearch . "%";
	
	if( count( $chunks ) == 2 ) {
		$first = "%" . $chunks[0] . "%";
		$last = "%" . $chunks[1] . "%";
		
		$stmt->bind_param( "sssss", $first, $last, $theSearch, $userIDSearch, $theSearch );
	}
	else {
		$stmt->bind_param( "sssss", $theSearch, $theSearch, $theSearch, $userIDSearch, $theSearch );
	}

	$stmt->bind_result( $UserID, $FirstName, $LastName, $IsHG, $DisplayName, $Changed );
	$result = $stmt->execute();
	
	$cool = array();

	while ( $stmt->fetch() ) {
		$currentEntry = array();
	
		$currentEntry['UserID'] = $UserID;
		$currentEntry['FirstName'] = $FirstName;
		$currentEntry['LastName'] = $LastName;
		$currentEntry['IsHG'] = (bool)$IsHG;
		$currentEntry['DisplayName'] = rawurlencode( $DisplayName );
		$currentEntry['Changed'] = $Changed;
	
		$cool[ $UserID ] = $currentEntry;
	}
	
	$response_xml = xmlrpc_encode(array(
		'success'      	=> $result,
		'names' 	=> $cool
	));

	print $response_xml;
}

#
# Process the request
#

$request_xml = file_get_contents("php://input");

xmlrpc_server_call_method($xmlrpc_server, $request_xml, '');
xmlrpc_server_destroy($xmlrpc_server);
?>
