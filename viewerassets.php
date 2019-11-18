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
	include_once "includes/typearrays.php";
	
	$found = FALSE;
	
	$asset_id = null;
	$asset_type = 0;
	
	foreach ($_GET as $key => $value) {
		if( array_key_exists( $key, $asset_types ) ) {
			$found = TRUE;
			$asset_id = $value;
			$asset_type = $asset_types[ $key ];
			break;
		}
	}
	
	if( $found ) {
		// Create connection
		$conn = new mysqli(SQLServerName, SQLUserName, SQLPassword, SQLDatabase);

		// Check connection
		if ($conn->connect_error) {
			die;
		}
		
		$sql  = "SELECT hash FROM " . AssetTableName . " WHERE id=? AND type=? LIMIT 1";
		$stmt = $conn->stmt_init();
		
		if( !$stmt->prepare( $sql ) )
		{
			echo "Failed to prepare statement\n";
			die();
		}
		
		$stmt->bind_param( "si", $asset_id, $asset_type );
		
		$stmt->bind_result( $hash );
		
		$stmt->execute();
		
		$fetch = $stmt->fetch();
		
		if( empty( $fetch ) ) {
			echo "Not found!";
			die();
		}
		
		$conn->close();
		
		$hash_upper = strtoupper( $hash );

		$compressed = true;
		
		$file = getPath( $hash_upper, TRUE ) . '.gz';
		if( !file_exists( $file ) ) {
			$file = getPath( $hash_upper, FALSE ) . '.gz';
			if( !file_exists( $file ) ) {
				$file = getPath( $hash_upper, TRUE );
				if( !file_exists( $file ) ) {
					$file = getPath( $hash_upper, FALSE );
					if( !file_exists( $file ) ) {
						echo "Not found!";
						die();
					}
					$compressed = false;
				}
				$compressed = false;
			}
		}
		
		header( 'Expires: 0' );
		header( 'Content-Type: "' . $asset_type_to_content_type[ $asset_type ] . '"' );
		
		if( $compressed ){
			header('Expires: 0');
			echo file_get_contents( 'compress.zlib://' . $file );
		}
		else
			echo file_get_contents( $file );
	}
	else
		echo "Incorrect Syntax";
?>