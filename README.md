# OpenSim Advanced Web Services

OpenSim Advanced Web Services, more commonly known as OSAWS, is a (small) collection of PHP scripts designed to take some of the load off of the ROBUST. Please note that, with the exception of FSAssets, you will need to be using an OpenSim version that supports these features.

## OSAWS includes the following:
#### FSAssets
This provides a faster alternative for the region to fetch assets from the database, instead of making it go through the ROBUST.
#### ViewerAsset
A region that supports external ViewerAsset can forward a user's asset request to this script so they can directly fetch the asset from the ROBUST, instead of the region fetching the asset and handing it to the user.
#### AvatarPicker
The AvatarPicker is an XMLRPC (for now) handler for quickly retrieving avatar picker results from the ROBUST. This way the region simply forwards the search queary to the script, processes the results, and forwards them to the user.
#### LSLSyntax
This provides a centralised place for clients to retrieve LSLSyntax XMLs, instead of hosting them on every region.

## Setting up OSAWS

To setup OSAWS, start by copying the OSAWS folder to your www directory.

In the includes directory of OSAWS there is a file named `srconfig.php`. In this file you need to specify your assets directory and enter the SQL credentials you want OSAWS to use.

OSAWS needs PHP and PHP-MySQL to run. It also needs XMLRPC to be enabled in your PHP config for the avatar picker (for now).

You will need to configure your web service for these services. I personally prefer to give each service its own subdomain, however this is not a requirement. You will however need to change the config file to match this.   

<details>
  <summary>Apache2 Config Example</summary>
     
```
<VirtualHost *:80>
	DocumentRoot /var/www/osaws
	ServerName avatarpicker.main.examplegrid.com

	<Directory /var/www/osaws>
		DirectoryIndex disabled
		DirectoryIndex avatarpicker.php

		RewriteEngine on
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule . avatarpicker.php [L]
    	</Directory>
</VirtualHost>

<VirtualHost *:80>
	DocumentRoot /var/www/osaws
	ServerName viewerasset.main.examplegrid.com

	<Directory /var/www/osaws>
		DirectoryIndex disabled
		DirectoryIndex viewerasset.php

		RewriteEngine on
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule . viewerasset.php [L]
	</Directory>
</VirtualHost>

<VirtualHost *:80>
	DocumentRoot /var/www/osaws
	ServerName assets.main.examplegrid.com

	<Directory />
		DirectoryIndex disabled
		DirectoryIndex fsassets.php

		RewriteEngine on
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule . fsassets.php [L]
	</Directory>
</VirtualHost>

<VirtualHost *:80>
	DocumentRoot /var/www/osaws
	ServerName lslsyntax.main.examplegrid.com

	<Directory />
		DirectoryIndex disabled
		DirectoryIndex lslsyntax.php

		RewriteEngine on
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule . lslsyntax.php [L]
	</Directory>
</VirtualHost>
```
</details>

When setting up the FSAssets script, you need to make sure it has read and write access to your assets directory.

## Configuring your regions

To make your regions use the FSAssets script you need to set your `AssetServiceURI` to its URL in the `AssetService` section of your config. e.g. `AssetServerURI = "http://assets.main.examplegrid.com"`

To have your users retrieve their LSLSyntax XMLs from the LSLSyntax script, set `ExternalSyntaxURL` to its URL in the `LSLSyntax` section of your config. e.g. `ExternalSyntaxURL = "lslsyntax.main.examplegrid.com"`

To have your regions forward asset requests from clients to the ViewerAsset script, set `ExternalViewerAssetsURL` to its URL in the `ClientStack.LindenCaps` of your config. e.g. `ExternalViewerAssetsURL = "http://viewerasset.main.examplegrid.com"`

To have your region use the AvatarPicker script, set the `ExternalAvatarPickerURL` to its URL in the `AvatarPicker` section of your config. e.g. `ExternalAvatarPickerURL = "http://avatarpicker.main.examplegrid.com"`

## Configuring your ROBUST

If you would like HG regions to also pull from the FSAssets script instead of the ROBUST, you can change the `SRV_AssetServerURI` URL in the `LoginServices` section of your ROBUST config to its URL. e.g. `SRV_AssetServerURI = "http://assets.main.examplegrid.com"`

No addition configuration is required on the ROBUST for OSAWS.

