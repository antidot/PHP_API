#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
. "${DIR}/version_management.sh"

initialize_version
echo -n "$MAJOR"
if [ "$1" == "major" ]
then
    echo ""
    exit 0
fi

echo -n ".$MINOR"
if [ "$1" == "minor" ]
then
    echo ""
    exit 0
fi

echo ".$FIX"
