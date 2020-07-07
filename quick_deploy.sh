#!/usr/bin/env bash

PROD_IP="159.89.48.155"

#bring down laest
git pull origin master

#capture in bitbucket
date >> deploy.log
git add . -A
git commit -m 'quick deploy'
git push origin master

#run deploy procedure
ssh root@$PROD_IP 'bash -s' < vagrant/scripts/quick_deploy.sh
