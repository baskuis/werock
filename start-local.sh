#!/usr/bin/env bash

# Enable port forwarding
sudo pfctl -E

# Create/start VM
cd vagrant
vagrant plugin install vagrant-triggers
vagrant up --provision

# Step back to root
cd ..

# Reference
IP="127.0.0.1"

# Modify hosts file
APACHE_SITES_AVAILABLE="apache/"
for f in $APACHE_SITES_AVAILABLE*
do
    NAME=$f
    NAME=${NAME/$APACHE_SITES_AVAILABLE/""}
    NAME=${NAME/".conf"/""}
    if sudo grep -Fxq "$IP $NAME" /etc/hosts
    then
        echo "$NAME already added to /etc/hosts"
    else
        echo "$IP $NAME" | sudo tee -a /etc/hosts
        echo "$IP www.$NAME" | sudo tee -a /etc/hosts
    fi
done
