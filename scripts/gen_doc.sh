#! /bin/bash
#*******************************************************************************
#
# AFS © Antidot 2013
#
#*******************************************************************************
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

function vercomp () {
	if [[ $1 == $2 ]]
	then
		return 0
	fi
	local IFS=.
	local i ver1=($1) ver2=($2)
	for ((i=${#ver1[@]}; i<${#ver2[@]}; i++))
	do
		ver1[i]=0
	done
	for ((i=0; i<${#ver1[@]}; i++))
	do
		if [[ -z ${ver2[i]} ]]
		then
			ver2[i]=0
		fi
		if ((10#${ver1[i]} > 10#${ver2[i]}))
		then
			return 1
		fi
		if ((10#${ver1[i]} < 10#${ver2[i]}))
		then
			return 2
		fi
	done
	return 0
}

DUMMY=$(which doxygen)
if [ "$?" != "0" ]
then
    echo "Please install doxygen on your computer or add it to your PATH"
    exit 1
fi

VERSION=$(doxygen --version)
EXPECTED="1.8.5"
vercomp $VERSION $EXPECTED
COMP=$?

if [ "$COMP" -eq "2" ]
then
	echo "[1;31m[WARNING] Recommanded doxygen version is $EXPECTED whereas your version is $VERSION"
    echo "[WARNING] You should consider to use a more recent version of doxygen.[0m"
fi

DOX_FILE="${DIR}/../afs_lib.doxygen"
if [ -f "${DOX_FILE}" ]
then
    sed -i -e "s/^\(PROJECT_NUMBER.*=[[:space:]]*\).*$/\1\"version $(${DIR}/print_version.sh)\"/" "${DOX_FILE}"
    doxygen "${DOX_FILE}"
fi
