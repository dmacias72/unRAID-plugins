#!/bin/sh
RC_SCRIPT="/etc/rc.d/rc.ipmiseld"
RC_SCRIPT2="/etc/rc.d/rc.ipmitail"
RC_SCRIPT3="/etc/rc.d/rc.ipmifan"
SD_RCFILE="/etc/rc.d/rc.local_shutdown"

# Update file permissions of scripts
chmod +0755 /usr/local/emhttp/plugins/ipmi/scripts/* \
 /usr/sbin/ipmitail \
 /usr/sbin/ipmifan \
 /usr/sbin/ipmisel \
 /usr/sbin/ipmisensors \
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
	then echo -e "\n[ -x $RC_SCRIPT3 ] && $RC_SCRIPT3 stop" >> $SD_RCFILE
fi
[ ! -x $SD_RCFILE ] && chmod u+x $SD_RCFILE