#!/usr/bin/env bash

cd vagrant;
vagrant ssh -c "sudo rm /var/www/vhosts/werock.com/public/resources/style* ; sudo rm /var/www/vhosts/werock.com/public/resources/scrip* ; sudo service apache2 stop ; sudo echo 'flush_all' | sudo nc localhost 11211 ; sudo service apache2 start ; memcached -u memcache -d -m 1 -I 1m -l 127.0.0.1 -p 11211"