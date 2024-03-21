#/bin/bash

DESTINATION_SET_IN_SSH_CONFIG="wiki"
DEST_PATH="/home/robuwikipix/wiki.robnugen.com/w/extensions/ArtRobNugenComPermalinks/"

# This will watch for changes in the source directory and scp them to the destination
inotifywait --exclude '.git/*' -mr -e close_write . | sed -ue 's/ CLOSE_WRITE,CLOSE //' | xargs -d$'\n' -I% scp  -P 22  % $DESTINATION_SET_IN_SSH_CONFIG:$DEST_PATH%
