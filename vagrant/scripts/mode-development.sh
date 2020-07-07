
PROD_IP="159.89.48.155"
PHP_CONFIG_FILE="/etc/php5/apache2/php.ini"
XDEBUG_CONFIG_FILE="/etc/php5/mods-available/xdebug.ini"
MYSQL_CONFIG_FILE="/etc/mysql/my.cnf"
APACHE_DIR_CONF="/etc/apache2/mods-enabled/dir.conf"
APACHE_VHOSTS_CONF="/etc/apache2/sites-available/000-default.conf"
APACHE_ENVVARS="/etc/apache2/envvars"
APACHE_SITES_AVAILABLE="/etc/apache2/sites-available/"
ENVIRONMENTS_DIRECTORY="/var/www/vhosts/werock.com/environments/"

# Create werock databases
for f in $ENVIRONMENTS_DIRECTORY*
do

	#turn off dev mode
	sed -i "s/define('DEV_MODE', false);/define('DEV_MODE', true);/g" $f
	sed -i "s/define('CACHING_ENABLED', true);/define('CACHING_ENABLED', false);/g" $f
	sed -i "s/define('STATIC_ASSET_CACHING_ENABLED', true);/define('STATIC_ASSET_CACHING_ENABLED', false);/g" $f
	sed -i "s/define('MINIFY_JAVASCRIPT', true);/define('MINIFY_JAVASCRIPT', false);/g" $f
	sed -i "s/define('BUILD_SCHEMA', false);/define('BUILD_SCHEMA', true);/g" $f

done