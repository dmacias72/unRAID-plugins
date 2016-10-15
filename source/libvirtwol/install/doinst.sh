#!/bin/sh
SCRIPT="<? include_once '/usr/local/emhttp/plugins/libvirtwol/include/libvirtwol.php';?>"
FILE="/usr/local/emhttp/plugins/dynamix.vm.manager/VMSettings.page"

# Update file permissions of scripts
chmod +0755 /usr/local/emhttp/plugins/libvirtwol/scripts/* \
    /usr/local/emhttp/plugins/libvirtwol/event/* \
    /etc/rc.d/rc.libvirtwol

# add stop to shutdown script	
if ! grep "$SCRIPT" $FILE >/dev/null 2>&1
    then echo -e "\n$SCRIPT" >> $FILE
fi
