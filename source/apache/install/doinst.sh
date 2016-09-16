#!/bin/sh

# Update file permissions of scripts
chmod +0755 /usr/local/emhttp/plugins/apache/scripts/* \
	/etc/rc.d/rc.apache

if [ ! -L /var/www/htdocs ]; then
    mv -T /var/www/htdocs /var/www/html
fi
ln -sfT /var/www/html /srv/httpd/htdocs

cp -nr /etc/httpd /boot/config/plugins/apache
rm -rf /etc/httpd
ln -sfT /boot/config/plugins/apache/httpd /etc/httpd
