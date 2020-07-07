#!/bin/bash

#pull down latest
cd /var/www/vhosts/werock.com

#checkout master - forcefully
git checkout -f master

#pull all submodules
git submodule foreach git pull origin master && git submodule init && git submodule update && git submodule status

#pull down delta
git pull origin master