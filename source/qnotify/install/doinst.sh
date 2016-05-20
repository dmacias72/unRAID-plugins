#!/bin/sh
RC_SCRIPT="/etc/rc.d/rc.qnotify"
SD_RCFILE="/etc/rc.d/rc.local_shutdown"

# Update file permissions of scripts
chmod +0755 $RC_SCRIPT

ln -sfT /boot/config/plugins/qnotify/config.py /usr/local/emhttp/plugins/qnotify/PyQuassel/config.py

# add stop to shutdown script	
if ! grep "$RC_SCRIPT" $SD_RCFILE >/dev/null 2>&1
	then echo -e "\n[ -x $RC_SCRIPT ] && $RC_SCRIPT stop" >> $SD_RCFILE
fi
[ ! -x $SD_RCFILE ] && chmod u+x $SD_RCFILE
