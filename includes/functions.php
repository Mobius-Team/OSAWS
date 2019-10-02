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
	function getFlags( $flags ) {
		if( $flags == 0 ) return "Normal";
		
		$result = array();
		
		if( ( $flags & 1 ) ) $result[] = "Maptile";
		if( ( $flags & 2 ) ) $result[] = "Rewritable";
		if( ( $flags & 4 ) ) $result[] = "Collectable";
		
		return implode( " ", $result );
	}

	function getPath( $hash, $lower ) {
		if( !OS_GridStyle )
			$path = substr( $hash, 0, 2 ) . "/" . substr( $hash, 2, 2 ) . "/" . substr( $hash, 4, 2 ) . "/" . substr( $hash, 6, 4 ) . "/";
		else
			$path = substr( $hash, 0, 3 ) . "/" . substr( $hash, 3, 3 ) . "/";
		
		$path = $path . $hash;
		
		if( $lower ) $path = strtolower( $path );
		
		return AssetPath . $path;
	}

	function getData( $hash ) {
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
						return NULL;
					}
					$compressed = false;
				}
				$compressed = false;
			}
		}

		if( $compressed ) {
			return file_get_contents( 'compress.zlib://' . $file );
		}
		else
			return file_get_contents( $file );
	}

	function gzCompressFile($file, $data, $level = 9){ 
		$mode = 'wb' . $level; 
		$error = false; 
		if ( $fp_out = gzopen( $file, $mode ) ) { 
			gzwrite( $fp_out, $data ); 
			gzclose( $fp_out ); 
		} 
		else {
			$error = true; 
		}
		if ( $error )
			return false; 
		else
			return true; 
	}

	function getRealIpAddr() {
		if ( !empty($_SERVER['HTTP_CLIENT_IP'] ) ) { //check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { //to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	function srLog( $from, $message ) {
		$datetime = new DateTime();
		$date = $datetime->format("y-m-d");
		$time = $datetime->format("h:i:s");
		
		file_put_contents( LogDirectory . $date . ".log", $time . " [" . $from . "] " . $message . "\n", FILE_APPEND );
	}
?>
