#!/bin/bash
#
# e.g. ./deploy.sh myserver.com drupal/ branch
#

SERVER="$1"
FOLDER="$2"
BRANCH="$3"
ssh "$SERVER" "cd $FOLDER && git pull origin $BRANCH"
