#!/bin/bash
ENVIRONMENTS_DIRECTORY="/var/www/vhosts/werock.com/environments/"
SCHEDULE_FILE="/var/www/vhosts/werock.com/public/schedule.php"

for f in $ENVIRONMENTS_DIRECTORY*
do
    NAME=$f
    NAME=${NAME//$ENVIRONMENTS_DIRECTORY/''}
    NAME=${NAME/"configuration."/""}
    NAME=${NAME/".php"/""}
    echo "php $SCHEDULE_FILE $NAME"
    php $SCHEDULE_FILE $NAME &>/dev/null &
done