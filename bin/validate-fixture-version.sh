#!/bin/bash
set -euo pipefail
IFS=$'\n\t'

main(){
    export TERMINUS_HIDE_GIT_MODE_WARNING=1
    local DIRNAME=$(dirname "$0")

    if [ -z "${TERMINUS_SITE}" ]; then
        echo "TERMINUS_SITE environment variable must be set"
        exit 1
    fi

    if ! terminus whoami > /dev/null; then
        if [ -z "${TERMINUS_TOKEN}" ]; then
            echo "TERMINUS_TOKEN environment variable must be set or terminus already logged in."
            exit 1
        fi
        terminus auth:login --machine-token="${TERMINUS_TOKEN}"
    fi

    # Use find to locate the file with a case-insensitive search
    README_FILE_PATH=$(find ${DIRNAME}/.. -iname "readme.txt" -print -quit)
    if [[ -z "$README_FILE_PATH" ]]; then
        echo "readme.txt not found."
        exit 1
    fi

    # Grep the version and pipe to xargs to trim any leading/trailing whitespace
    local TESTED_UP_TO
    TESTED_UP_TO=$(grep -i "Tested up to:" "${README_FILE_PATH}" | tr -d '\r\n' | awk -F ': ' '{ print $2 }' | xargs)
    echo "Tested Up To: ${TESTED_UP_TO}"

    # Use TERMINUS_ENV variable if it exists, otherwise default to "dev"
    local TERMINUS_ENV=${TERMINUS_ENV:-"dev"}
    local FIXTURE_VERSION
    FIXTURE_VERSION=$(terminus wp "${TERMINUS_SITE}.${TERMINUS_ENV}" -- core version)
    echo "Fixture Version (${TERMINUS_ENV} env): ${FIXTURE_VERSION}"

    compare_result=$(php -r "echo version_compare('${TESTED_UP_TO}', '${FIXTURE_VERSION}');")

    if [ $compare_result == "-1" ]; then
        echo "${FIXTURE_VERSION} is greater than ${TESTED_UP_TO}"
        echo "You should update the 'Tested up to' in your plugin's readme.txt to '${FIXTURE_VERSION}'."
        exit 1
  elif [ $compare_result == "1" ]; then
        echo "${FIXTURE_VERSION} is less than ${TESTED_UP_TO}"
        echo "Please update ${TERMINUS_SITE}.${TERMINUS_ENV} to at least WordPress ${TESTED_UP_TO}"
        exit 1
    elif [ $compare_result == "0" ]; then
        echo "${FIXTURE_VERSION} is equal to ${TESTED_UP_TO}"
        echo "No action required."
    else
        echo "An error occurred during version comparison."
        exit 1
    fi
}

main
