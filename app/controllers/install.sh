#!/bin/bash
INST_CLI_VER=aegis_00_33
INST_UPDATE_VER=update_00_33
if [ `uname -m` = "x86_64" ]; then
    ARCH="linux64"
else
    ARCH="linux32"
fi

#check linux Gentoo os 
var=`lsb_release -a 2>/dev/null | grep Gentoo`
if [ -z "${var}" ]; then 
	var=`cat /etc/issue | grep Gentoo`
fi

if [ -d "/etc/runlevels/default" -a -n "${var}" ]; then
	LINUX_RELEASE="GENTOO"
else
	LINUX_RELEASE="OTHER"
fi

AEGIS_UPDATE_SITE="http://update.aegis.aliyun.com/download"
AEGIS_INSTALL_DIR="/usr/local/aegis"


install_aegis() {
    killall aegis_update 2>/dev/null
    killall aegis_cli  2>/dev/null
    if [ -d "${AEGIS_INSTALL_DIR}/aegis_client" ];then
        rm -rf "${AEGIS_INSTALL_DIR}/aegis_client"
    fi 
    if [ -d "${AEGIS_INSTALL_DIR}/aegis_update" ];then
        rm -rf "${AEGIS_INSTALL_DIR}/aegis_update"
    fi 
    mkdir -p "${AEGIS_INSTALL_DIR}/aegis_client"
    mkdir -p "${AEGIS_INSTALL_DIR}/aegis_update"


    wget "${AEGIS_UPDATE_SITE}/$ARCH/${INST_UPDATE_VER}/aegis_update" -O "${AEGIS_INSTALL_DIR}/aegis_update/aegis_update" -T 120
    if [ $? != 0 ]; then
        echo "wget aegis_update error" 1>&2
        exit 1
    fi
    wget "${AEGIS_UPDATE_SITE}/$ARCH/${INST_UPDATE_VER}/agx_update.cfg" -O "${AEGIS_INSTALL_DIR}/aegis_update/agx_update.cfg" -T 120
    if [ $? != 0 ]; then
        echo "wget agx_update.cfg error" 1>&2
        exit 1
    fi

    chmod +x "${AEGIS_INSTALL_DIR}/aegis_update/aegis_update"
    echo "${INST_CLI_VER}" > ${AEGIS_INSTALL_DIR}/aegis_update/up_cmd.txt
}


uninstall_service() {
   
   if [ -f "/etc/init.d/aegis" ]; then
		/etc/init.d/aegis stop  >/dev/null 2>&1
		rm -f /etc/init.d/aegis 
   fi

	if [ $LINUX_RELEASE = "GENTOO" ]; then
		rc-update del aegis default
		if [ -f "/etc/runlevels/default/aegis" ]; then
			rm -f "/etc/runlevels/default/aegis" >/dev/null 2>&1;
		fi
    elif [ -f /etc/init.d/aegis ]; then
         /etc/init.d/aegis  uninstall
	    for ((var=2; var<=5; var++)) do
			if [ -d "/etc/rc${var}.d/" ];then
				 rm -f "/etc/rc${var}.d/S80aegis"
		    elif [ -d "/etc/rc.d/rc${var}.d" ];then
				rm -f "/etc/rc.d/rc${var}.d/S80aegis"
			fi
		done
    fi
}

install_service(){	
	if [ $LINUX_RELEASE = "GENTOO" ];then
		wget "${AEGIS_UPDATE_SITE}/aegis_gentoo" -O /etc/init.d/aegis
	else
		wget "${AEGIS_UPDATE_SITE}/aegis" -O /etc/init.d/aegis
	fi

    if [ $? != 0 ]; then
		echo "download error"
        echo "wget aegis error" 1>&2
        exit 1
    fi

    chmod +x /etc/init.d/aegis
    
	#delete old aegis sever
	if [ $LINUX_RELEASE = "GENTOO" ]; then
		rc-update del aegis default 2> /dev/null
		if [ -f "/etc/runlevels/default/aegis" ]; then
			rm -f "/etc/runlevels/default/aegis"
		fi
	else
		for ((var=2; var<=5; var++))
		do
			if [ -f "/etc/rc${var}.d/S80aegis" ]; then
				 rm -f "/etc/rc${var}.d/S80aegis"
		    elif [ -f "/etc/rc.d/rc${var}.d/S80aegis" ];then
				 rm -f "/etc/rc.d/rc${var}.d/S80aegis"
		    fi
        done
    fi

    # install new aegis server
	if [ $LINUX_RELEASE = "GENTOO" ]; then
		rc-update add aegis default 2>/dev/null
	else
		for ((var=2; var<=5; var++)) do
			if [ -d "/etc/rc${var}.d/" ];then
			    #redhat 
                ln -s /etc/init.d/aegis /etc/rc${var}.d/S80aegis 2>/dev/null
            elif [ -d "/etc/rc.d/rc${var}.d" ]; then
				 #suse
				 ln -s /etc/init.d/aegis /etc/rc.d/rc${var}.d/S80aegis  2>/dev/null
			fi
		done
	fi

    path="/etc/debian_version"

    if [ -f "$path" -a -s "$path" ];
    then
        var=`awk -F. '{print $1}' $path`

        if [ -z $(echo $var|grep "[^0-9]") ]; then
            if [ $var -ge 6 ]; then
                echo "insserv aegis"
                insserv aegis  >/dev/null 2>&1
            fi
        fi
    fi
}

check_aegis(){
    var=1
    limit=18
    echo "Aegis client is installing , please wait for 1 to 3 minutes.";

    while [[ $var -lt $limit ]]; do 
        if [ -n "$(ps -ef|grep aegis_client|grep -v grep)" ]; then
             break
	     else
		    sleep 10
	     fi
	     
        ((var++))
	done	 
}

if [ `id -u` -ne "0" ]; then
    echo "ERROR: This script must be run as root." 1>&2
    exit 1
fi


uninstall_service
install_aegis
install_service
if [ -f /etc/init.d/aegis ];then
    /etc/init.d/aegis start
fi 

check_aegis
echo "Aegis successfully installed."
exit 0
