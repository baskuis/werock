#!/usr/bin/env bash

cd vagrant
vagrant suspend

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
        echo "removing $NAME localhost from /etc/hosts"
        sudo sed -i '' "/$IP $NAME/d" /etc/hosts
        sudo sed -i '' "/$IP www.$NAME/d" /etc/hosts
    else
        echo "$NAME already removed from /etc/hosts"
    fi
done

#Disable port forwarding
sudo pfctl -d