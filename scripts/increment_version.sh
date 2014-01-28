#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
. "${DIR}/version_management.sh"

if [ "$#" != "1" ]
then
    help
fi

initialize_version
echo "Current version number: $MAJOR.$MINOR.$FIX"

case $1 in
    fix)
        set_fix $(($FIX + 1))
        ;;
    minor)
        set_minor $(($MINOR + 1))
        ;;
    major)
        set_major $(($MAJOR + 1))
        ;;
    *)
        echo "Invalid value: $1\n"
        help
        ;;
esac

echo "New version number: $MAJOR.$MINOR.$FIX"
update
