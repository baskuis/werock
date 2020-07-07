#!/usr/bin/env bash

PROD_IP="162.243.31.224"

ssh root@$PROD_IP 'bash -s' < vagrant/scripts/mode-production.sh