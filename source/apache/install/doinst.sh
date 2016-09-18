#!/bin/sh

# Update file permissions of scripts
chmod +0755 /usr/local/emhttp/plugins/apache/scripts/* \
	/usr/local/emhttp/plugins/apache/event/* \
	/etc/rc.d/rc.apache

if [ ! -L /var/www/htdocs ]; then
    mv -T /var/www/htdocs /var/www/html
fi
ln -sfT /var/www/html /srv/httpd/htdocs

cp -nr /usr/local/emhttp/plugins/apache/httpd /boot/config/plugins/apache
rm -rf /etc/httpd
ln -sfT /boot/config/plugins/apache/httpd /etc/httpd
ln -sfT /boot/config/plugins/apache/httpd/php.ini /etc/php.d/php.ini
