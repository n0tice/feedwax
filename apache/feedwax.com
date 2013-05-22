<VirtualHost *:80>

	ServerName feedwax.com
	ServerAdmin feedwax@n0tice.com
       
	CustomLog /var/log/apache2/feedwax.com-access_log combined
	ErrorLog /var/log/apache2/feedwax.com-error.log

	DocumentRoot /home/feedwax/htdocs

	<Directory /home/feedwax/htdocs>
		AllowOverride None
	</Directory>

</VirtualHost>
