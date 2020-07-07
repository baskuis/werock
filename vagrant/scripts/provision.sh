#!/bin/bash

#Load configuration
PROD_IP="162.243.31.224"
PHP_CONFIG_FILE="/etc/php5/apache2/php.ini"
XDEBUG_CONFIG_FILE="/etc/php5/mods-available/xdebug.ini"
MYSQL_CONFIG_FILE="/etc/mysql/my.cnf"
APACHE_DIR_CONF="/etc/apache2/mods-enabled/dir.conf"
APACHE_VHOSTS_CONF="/etc/apache2/sites-available/000-default.conf"
APACHE_ENVVARS="/etc/apache2/envvars"
APACHE_SITES_AVAILABLE="/etc/apache2/sites-available/"
ENVIRONMENTS_DIRECTORY="/var/www/vhosts/werock.com/environments/"

if [[ -e /var/lock/vagrant-provision ]]; then
    cat 1>&2 << EOD
################################################################################
# To re-run full provisioning, delete /var/lock/vagrant-provision and run
#
#    $ vagrant provision
#
# From the host machine
################################################################################
EOD
    exit
fi

# Update the server
apt-get update
apt-get -y upgrade

################################################################################
# Everything below this line should only need to be done once
# To re-run full provisioning, delete /var/lock/vagrant-provision and run
#
#    $ vagrant provision
#
# From the host machine
################################################################################

IPADDR=$(/sbin/ifconfig eth0 | awk '/inet / { print $2 }' | sed 's/addr://')
sed -i "s/^${IPADDR}.*//" /etc/hosts
echo $IPADDR ubuntu.localhost >> /etc/hosts			# Just to quiet down some error messages

# Set root password
echo -e "Tiller215\!\nTiller215\!" | passwd root

# Install basic tools
apt-get -y install build-essential binutils-doc git

# Install Apache
apt-get -y install htop apache2

# Install PHP modules
add-apt-repository ppa:ondrej/php
apt-get update
apt-get install php7.1

apt-get -y install libmemcached-dev zlib1g-dev libssl-dev python-dev build-essential memcached php7.1 php7.1-curl php-pear libapache2-mod-php7.1 php7.1-mysql php-apc php7.1-xdebug php7.1-geoip php7.1-mcrypt php7.1-tidy php7.1-memcache php7.1-ldap php7.1-gd php7.1-cgi php7.1-cli php7.1-memcached php7.0-xml
apt-get -y install libpspell-dev php7.1-pspell aspell-en php7.1-mbstring

# Enable php crypt module
php5enmod mcrypt

# Enable Show Errors
sed -i "s/display_startup_errors = Off/display_startup_errors = On/g" ${PHP_CONFIG_FILE}
sed -i "s/display_errors = Off/display_errors = On/g" ${PHP_CONFIG_FILE}

cat << EOF > ${XDEBUG_CONFIG_FILE}
zend_extension=xdebug.so
xdebug.remote_enable=1
xdebug.remote_connect_back=1
xdebug.remote_port=9000
xdebug.remote_host=10.0.2.2
xdebug.max_nesting_level=2000
xdebug.profiler_enable=1
xdebug.profiler_output_dir=/var/www/vhosts/werock.com/xdebug
xdebug.profiler_enable_trigger=1
EOF

# Execute php over all
cat << EOF > ${APACHE_DIR_CONF}
<IfModule mod_dir.c>
    DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
EOF

# Configure run user
sed -i "s/APACHE_RUN_USER=www-data/APACHE_RUN_USER=vagrant/g" ${APACHE_ENVVARS}
sed -i "s/APACHE_RUN_GROUP=www-data/APACHE_RUN_GROUP=vagrant/g" ${APACHE_ENVVARS}

# Configure environment
sed -i "s/\"GPCS\"/\"EGPCS\"/g" ${PHP_CONFIG_FILE}

# Configure/link vhost
rm /etc/apache2/sites-available/000-default.conf
rm /etc/apache2/sites-available/default-ssl.conf
rm /etc/apache2/sites-enabled/000-default.conf

# Enable vhost
for f in $APACHE_SITES_AVAILABLE*
do
    NAME=$f
    NAME=${NAME/$APACHE_SITES_AVAILABLE/""}
    NAME=${NAME/".conf"/""}
    a2ensite "${NAME}"
done

# Enable apache modules
a2enmod rewrite
a2enmod headers
a2enmod expires
a2enmod ssl

# Install MySQL
echo "mysql-server mysql-server/root_password password root" | sudo debconf-set-selections
echo "mysql-server mysql-server/root_password_again password root" | sudo debconf-set-selections
apt-get -y install mysql-client mysql-server

# Set null bind address
sed -i "s/bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/" ${MYSQL_CONFIG_FILE}

# Install MongoDB
apt-get -y update
apt-get -y install git build-essential openssl libssl-dev pkg-config

# Allow root access from localhost host
echo "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY 'root' WITH GRANT OPTION" | mysql -u root --password=root
echo "GRANT PROXY ON ''@'' TO 'root'@'%' WITH GRANT OPTION" | mysql -u root --password=root

# Create werock databases
# Output of php file will create databases
# or fail when database already exists
for f in $ENVIRONMENTS_DIRECTORY*
do
    eval $(php /var/www/vhosts/werock.com/vagrant/utils/mysqlSetup.php $f)
done

# Cleanup the default HTML file created by Apache
rm /var/www/html/index.html
rm /var/www/html -R

# Setup cron schedule
crontab -l | { cat; echo "* * * * * /bin/bash /var/www/vhosts/werock.com/schedule.sh"; } | crontab -

# Restart Services
service apache2 restart
service mysql restart

# make sure memcache is running
memcached -u memcache -d -m 30 -l 127.0.0.1 -p 11211

# Already provisioned
if [[ -e /var/lock/vagrant-provision ]]; then
    exit;
fi

touch /var/lock/vagrant-provision