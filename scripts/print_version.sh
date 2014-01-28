#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
. "${DIR}/version_management.sh"

initialize_version
echo "$MAJOR.$MINOR.$FIX"
