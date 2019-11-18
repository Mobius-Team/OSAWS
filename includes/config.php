<?php
	// Where you would like OSAWS to keep a log
	// On Linux I like to use /var/log/osaws/
	define( "LogDirectory", './log/' );
	
	// What message to show people snooping URLs
	define( "AreYouLostMessage", "Are you lost, friend?" );
	
	// To use OSGrid style fsassets set this to TRUE.
	define( "OS_GridStyle", FALSE );  
	
	// Set this to the toplevel of your asset directory
	define( "AssetPath", '/srv/fsassets/data/' );
	
	// You probably shouldn't enable this unless you're a closed garden
	define( "AllowRemoteDelete", FALSE );
	
	// This is how many directories are at the end of your URL before it reaches OSAWS
	// e.g. "assets.main.examplegrid.com" will have a start index of 0 because the subdomain point to the folder
	// 	"main.examplegrid.com/osaws/" has an index of 1 because of the osaws directory at the end
	//	"main.examplegrid.com/www/osaws/" has an index of 2 because of /www/osaws/
	//	"osaws.main.examplegrid.com/www/osaws/" still has an index of 2 because of /www/osaws/
	// For best results, just setup a subdomain for each feature and set the URLStartIndex to 0
	define( "URLStartIndex", 0 );
	
	// SQL Credentials
	define( "SQLServerName", "localhost" );
	define( "SQLDatabase", "robust" );
	define( "SQLUserName", "robust" );
	define( "SQLPassword", "password" );
	
	// Set this to the table used for assets fsassets is default in an fsassets configuration
	define( "AssetTableName", 'fsassets' );
	
	// Where your LSLSyntax XMLs are stored
	define( "LSLSyntaxDir", '/srv/lslsyntax/' );
?>