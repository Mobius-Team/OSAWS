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
	$Method = $_SERVER['REQUEST_METHOD'];
	
	if( $URL[0] == "/" )
		$URL = substr( $URL, 1 );

	$Neil = explode( "/", $URL );
	
	if( ( $Neil[URLStartIndex] != "assets" && ( $Neil[URLStartIndex] != "get_assets_exist" || ( $Neil[URLStartIndex] == "get_assets_exist" && $Method != "POST" ) ) ) ) 
	{
		srLog( "ASSETS", "Invalid request from " . getRealIpAddr() . ' (' . $_SERVER['REQUEST_URI'] . ')' );
		header( 'Content-Type: text/html; charset=utf-8' );
		die( AreYouLostMessage );
	}
	
	if( $Method == "GET" && $Neil[URLStartIndex] == "assets" ) {
		if( count( $Neil ) < URLStartIndex + 2 ) die( "Invalid URL" );

		$UUID = $Neil[URLStartIndex + 1];
		
		$UUIDv4 = '/[a-f0-9]{8}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{12}/';
		
		preg_match( $UUIDv4, $UUID ) or die( 'Not valid UUID' );
		
		$func = "";
		if( count( $Neil ) == URLStartIndex + 3 ) {
			$func = $Neil[URLStartIndex + 2];
		}
		
		include_once "includes/typearrays.php";
		
		// Create connection
		$conn = new mysqli(SQLServerName, SQLUserName, SQLPassword, SQLDatabase);

		// Check connection
		if ( $conn->connect_error ) {
			die;
		}
		
		$sql  = "SELECT id, name, description, type, hash, create_time, access_time, asset_flags FROM " . AssetTableName . " WHERE id=? LIMIT 1";
		$stmt = $conn->stmt_init();
		
		if( !$stmt->prepare( $sql ) ) {
			die( "Failed to prepare statement!" );
		}
		
		$stmt->bind_param( "s", $UUID );
		
		$stmt->bind_result( $id, $name, $description, $type, $hash, $create_time, $access_time, $asset_flags );
		
		$stmt->execute();
		
		$fetch = $stmt->fetch();

		$conn->close();
		$stmt->close();
		
		if( empty( $fetch ) ) {
			header('HTTP/1.0 404 Not Found');
			die( "Not found!" );
		}
		
		if( $func == "data" ) {
			$data = getData( $hash );
			if( $data !== NULL ) {
				header( 'Expires: 0' );
				header( 'Content-Type: "' . $asset_type_to_content_type[ $type ] . '"' );
				echo $data;
			}
			else echo "Not found!";
		}
		else if( $func == "metadata" ) {
			header('Content-Type: application/xml');
			echo '<?xml version="1.0" encoding="utf-8"?>
<AssetMetadata xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <FullID>
    <Guid>' . $UUID . '</Guid>
  </FullID>
  <ID>' . $UUID . '</ID>
  <Name>' . htmlspecialchars( $name, ENT_QUOTES, 'UTF-8' ) . '</Name>
  <Description>' . htmlspecialchars( $description, ENT_QUOTES, 'UTF-8' ) . '</Description>
  <CreationDate>' . date('Y-m-d\TH:i:s\Z', $create_time ) . '</CreationDate>
  <Type>' . $type . '</Type>
  <ContentType>' . $asset_type_to_content_type[ $type ] . '</ContentType>
  <Local>false</Local>
  <Temporary>false</Temporary>
  <Flags>' . getFlags( $asset_flags ) . '</Flags>
</AssetMetadata>';
		}
		else {
			$data = getData( $hash );
			if( $data === NULL )
			{
				header('HTTP/1.0 404 Not Found');
				die("no data");
			}
			
			header('Content-Type: application/xml');
			echo '<?xml version="1.0" encoding="utf-8"?>
<AssetBase xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <Data>' . base64_encode( $data ) . '</Data>
  <FullID>
    <Guid>' . $UUID . '</Guid>
  </FullID>
  <ID>' . $UUID . '</ID>
  <Name>' . htmlspecialchars( $name, ENT_QUOTES, 'UTF-8' ) . '</Name>
  <Description>' . htmlspecialchars( $description, ENT_QUOTES, 'UTF-8' ) . '</Description>
  <Type>' . $type . '</Type>
  <UploadAttempts>0</UploadAttempts>
  <Local>false</Local>
  <Temporary>false</Temporary>
  <Flags>' . getFlags( $asset_flags ) . '</Flags>
</AssetBase>';
		}
	}
	else if( $Method == "POST" ) {
		if( $Neil[URLStartIndex] === "get_assets_exist" ) {
			$PostXML = simplexml_load_string( file_get_contents( "php://input" ) );
			
			// Create connection
			$conn = new mysqli(SQLServerName, SQLUserName, SQLPassword, SQLDatabase);

			// Check connection
			if ( $conn->connect_error ) {
				die;
			}
		
			$sql  = "SELECT hash FROM " . AssetTableName . " WHERE id=? LIMIT 1";
			$stmt = $conn->stmt_init();

			if( !$stmt->prepare( $sql ) )
				die("failed to prepare");
			
			$stmt->bind_param( "s", $assetID );
			$stmt->bind_result( $hash );
				
			$booleans = "";
			foreach( $PostXML->children() as $name ) {
				$boolean = "false";
				
				$assetID = $name;
				$stmt->execute();
				$fetch = $stmt->fetch();

				if( !empty( $fetch ) ) {
					$boolean = "true";
				}
				$booleans = $booleans . "\n\t<boolean>" . $boolean . "</boolean>";
			}

			$stmt->close();
			$conn->close();
			
			header('Content-Type: application/xml');
			echo '<?xml version="1.0" encoding="utf-8"?>
<ArrayOfBoolean xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $booleans . '
</ArrayOfBoolean>';
		}
		else if( $Neil[URLStartIndex] === "assets" ) {
			$contents = file_get_contents( "php://input" );

			$sxe = simplexml_load_string( $contents );
			if (false === $sxe) {
				die( "failed to parse xml" );
			}
			
			$assetID = $sxe->ID;
			$assetName = $sxe->Name;
			$assetDesc = $sxe->Description;
			$assetType = $sxe->Type;
			$assetLocal = isset( $sxe->Local );
			$assetTemporary = isset( $sxe->Temporary );
			$assetData = base64_decode( $sxe->Data );
			
			include_once "typearrays.php";

			// Create connection
			$conn = new mysqli(SQLServerName, SQLUserName, SQLPassword, SQLDatabase);

			// Check connection
			if ( $conn->connect_error ) {
				srLog( "ASSETS", "Failed to upload " . $assetID . " (sql failed to connect)" );
				die( "Connection error" );
			}

			$sql  = "SELECT id FROM " . AssetTableName . " WHERE id=? LIMIT 1";
			$stmt = $conn->stmt_init();

			if( !$stmt->prepare( $sql ) ) {
				$stmt->close();
				$conn->close();
				srLog( "ASSETS", "Failed to upload " . $assetID . " (sql failed to bind params)" );
				die( "Failed to prepare statement" );
			}
			
			$stmt->bind_param( "s", $assetID );
			$stmt->bind_result( $id );
			$stmt->execute();
			$fetch = $stmt->fetch();

			if( empty( $fetch ) ) {
				$theHash = strtoupper( hash( 'sha256', $assetData ) );
				
				/* Prepare an insert statement */
				$query = "INSERT INTO " . AssetTableName . " (id, name, description, type, hash, create_time, access_time, asset_flags) VALUES (?,?,?,?,?,?,?,0)";

				if( !( $stmt = $conn->prepare( $query ) ) ) {
					$stmt->close();
					$conn->close();
					srLog( "ASSETS", "Failed to upload " . $assetID . " (sql failed to prepare)" );
					die( "failed to prepare statement" );
				}
			
				if( !( $stmt->bind_param('sssisii', $test, $test1, $test2, $test3, $test4, $test5, $test6 ) ) ) {
					$stmt->close();
					$conn->close();
					srLog( "ASSETS", "Failed to upload " . $assetID . " (sql failed to bind params)" );
					die( "failed to bind params" );
				}
				
				$test = $assetID;
				$test1 = $assetName;
				$test2 = $assetDesc;
				$test3 = $assetType;
				$test4 = $theHash;
				$test5 = time();
				$test6 = time();
					
				/* Execute the statement */
				if( !$stmt->execute() ) {
					$stmt->close();
					$conn->close();
					srLog( "ASSETS", "Failed to upload " . $assetID . " (sql failed to execute)" );
					die( "failed to execute" );
				}
				
				$dir = getPath( $theHash, FALSE );
				
				// ignore it if the hash exists
				if( !( file_exists( $dir ) || file_exists( $dir . '.gz' ) ) ) {
					if( !file_exists( dirname( $dir ) ) ) {
						mkdir( dirname( $dir ), 0777, true );
					}
					
					if( !gzCompressFile( $dir . '.gz', $assetData ) ) {
						$stmt->close();
						$conn->close();
						srLog( "ASSETS", "Failed to upload " . $assetID . " (file failed to create)" );
						die("upload failed");
					}
				}
				
				srLog( "ASSETS", getRealIpAddr() . " uploaded $assetID as type $assetType" );
			}
			// todo: tbd optional asset overwrite
			
			/* close statement */
			$stmt->close();
			$conn->close();

			header('Content-Type: application/xml');
			echo '<?xml version="1.0" encoding="utf-8"?>
<string>'. $assetID .'</string>';
		}
	}
	else if( $Method == "DELETE" ) {
		if( count( $Neil ) < URLStartIndex + 2 ) die( "Invalid URL" );

		$UUID = $Neil[URLStartIndex + 1];
		
		$UUIDv4 = '/[a-f0-9]{8}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{12}/';
		
		preg_match( $UUIDv4, $UUID ) or die( 'Not valid UUID' );
		
		$success = "false";
		
		if( AllowRemoteDelete === TRUE ) {
			// todo
			//$success = "true";
		}
			
		header('Content-Type: application/xml');
		echo '<?xml version="1.0" encoding="utf-8"?>
<boolean>'. $success .'</boolean>';
	}
?>
