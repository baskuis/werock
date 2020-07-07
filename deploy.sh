#!/usr/bin/env bash

PROD_IP="159.89.48.155"

#bring down latest
git pull origin master

#capture in bitbucket
date >> deploy.log
git add . -A
git commit -m 'deploy'
git push origin master

#pull apache vhosts configuration over
scp apache/* root@$PROD_IP:/etc/apache2/sites-available

#run deploy procedure
ssh root@$PROD_IP 'bash -s' < vagrant/scripts/deploy.sh
