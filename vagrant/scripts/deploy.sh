#!/bin/bash

#TODO: find a way to centralize this
PROD_IP="159.89.48.155"
PHP_CONFIG_FILE="/etc/php5/apache2/php.ini"
XDEBUG_CONFIG_FILE="/etc/php5/mods-available/xdebug.ini"
MYSQL_CONFIG_FILE="/etc/mysql/my.cnf"
APACHE_DIR_CONF="/etc/apache2/mods-enabled/dir.conf"
APACHE_VHOSTS_CONF="/etc/apache2/sites-available/000-default.conf"
APACHE_ENVVARS="/etc/apache2/envvars"
APACHE_SITES_AVAILABLE="/etc/apache2/sites-available/"
PUBLIC_DIRECTORY="/var/www/vhosts/werock.com/public"
ENVIRONMENTS_DIRECTORY="/var/www/vhosts/werock.com/environments/"
RESOURCES_FOLDER="/var/www/vhosts/werock.com/public/resources/"

#install updates
#apt-get update
#apt-get -y upgrade

#remove resources
rm $RESOURCES_FOLDER* -R
chmod 777 $RESOURCES_FOLDER -R
chown root:www-data $PUBLIC_DIRECTORY -R

#pull down latest
cd /var/www/vhosts/werock.com

#checkout master - forcefully
git checkout -f master

#pull all submodules
git submodule foreach git pull origin master && git submodule init && git submodule update && git submodule status

#pull down delta
git pull origin master

# Allow root access from localhost host
echo "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY 'root' WITH GRANT OPTION" | mysql -u root --password=root
echo "GRANT PROXY ON ''@'' TO 'root'@'%' WITH GRANT OPTION" | mysql -u root --password=root

# Disable xdebug
cat << EOF > ${XDEBUG_CONFIG_FILE}
zend_extension=xdebug.so
xdebug.remote_autostart=0
xdebug.remote_enable=0
xdebug.profiler_enable=0
xdebug.max_nesting_level=50000
EOF

# Create werock databases
for f in $ENVIRONMENTS_DIRECTORY*
do
	
	#turn off dev mode
	sed -i "s/define('DEV_MODE', true);/define('DEV_MODE', false);/g" $f
	sed -i "s/define('CACHING_ENABLED', false);/define('CACHING_ENABLED', true);/g" $f
	sed -i "s/define('STATIC_ASSET_CACHING_ENABLED', false);/define('STATIC_ASSET_CACHING_ENABLED', true);/g" $f
	sed -i "s/define('MINIFY_JAVASCRIPT', false);/define('MINIFY_JAVASCRIPT', true);/g" $f

    #assure database exists/is created
    eval $(php /var/www/vhosts/werock.com/vagrant/utils/mysqlSetup.php $f)

done

# Enable vhost
for f in $APACHE_SITES_AVAILABLE/*
do
    NAME=$f
    NAME=${NAME/$APACHE_SITES_AVAILABLE/""}
    NAME=${NAME/".conf"/""}
    a2ensite "${NAME}"
done

# enable apache modules
a2enmod headers
a2enmod expires

# Restart Services
service memcached restart
service apache2 restart
