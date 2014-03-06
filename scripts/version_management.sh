#!/bin/bash

function help()
{
    echo "You should specify which version number to increment:"
    echo " - fix,"
    echo " - minor,"
    echo " - major."
    exit 1
}

function retrieve_version()
{
    RESULT=$(cat afs_version.php | grep "'$1'" | sed -E "s/.*$1[^0-9]*([0-9]+)[^0-9]*/\1/")
}

function initialize_version()
{
    retrieve_version AFS_API_VERSION_MAJOR
    MAJOR=$RESULT
    retrieve_version AFS_API_VERSION_MINOR
    MINOR=$RESULT
    retrieve_version AFS_API_VERSION_FIX
    FIX=$RESULT
}

function update_version()
{
    sed -i -E "s/(.*$1[^0-9]*)[0-9]+([^0-9]*)/\1$2\2/" afs_version.php
}

function update()
{
    update_version AFS_API_VERSION_MAJOR $MAJOR
    update_version AFS_API_VERSION_MINOR $MINOR
    update_version AFS_API_VERSION_FIX $FIX
}

function set_fix()
{
    FIX=$1
}

function set_minor()
{
    MINOR=$1
    set_fix 0
}

function set_major()
{
    MAJOR=$1
    set_minor 0
}
