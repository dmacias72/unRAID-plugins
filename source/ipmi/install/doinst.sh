#!/bin/sh
RC_SCRIPT="/etc/rc.d/rc.ipmiseld"
RC_SCRIPT2="/etc/rc.d/rc.ipmitail"
RC_SCRIPT3="/usr/sbin/ipmifan"
SD_RCFILE="/etc/rc.d/rc.local_shutdown"

ln -sf /usr/local/emhttp/plugins/ipmi/scripts/ipmi2json /usr/sbin/ipmi2json

# Update file permissions of scripts
chmod +0755 /usr/local/emhttp/plugins/ipmi/scripts/* \
    /usr/sbin/ipmi2json \
    /usr/sbin/ipmifan \
    /usr/sbin/ipmisel \
    /usr/sbin/ipmisensors \
    /usr/sbin/ipmitail \
    /etc/rc.d/rc.ipmicfg \
    $RC_SCRIPT \
    $RC_SCRIPT2 \
    $RC_SCRIPT3

###Stop Scripts###

# Add stop script to rc.local_shutdown script
if ! grep "$RC_SCRIPT" $SD_RCFILE >/dev/null 2>&1
    then echo -e "\n[ -x $RC_SCRIPT ] && $RC_SCRIPT stop" >> $SD_RCFILE
fi

# Add stop script to rc.local_shutdown script
if ! grep "$RC_SCRIPT2" $SD_RCFILE >/dev/null 2>&1
    then echo -e "\n[ -x $RC_SCRIPT2 ] && $RC_SCRIPT2 stop" >> $SD_RCFILE
fi

# Add stop script to rc.local_shutdown script
if ! grep "$RC_SCRIPT3" $SD_RCFILE >/dev/null 2>&1
    then echo -e "\n[ -x $RC_SCRIPT3 ] && $RC_SCRIPT3 -q" >> $SD_RCFILE
fi
[ ! -x $SD_RCFILE ] && chmod u+x $SD_RCFILE
