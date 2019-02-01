#!/bin/sh

# ****************************************************************************
#
# K.I.D.S. Open RSYNC
#
# Producer: Kazushi Saito
# MakeDate: 2006/10/08
# EditDate: 
# Note:
#     > crontab setting sample.
#       0 4 * * * /home/kids2/rsync_image_tmp.sh > /dev/null
#
#     > REMOTE -> LOCAL  RSYNC
#
#
# ****************************************************************************

TARGET_SERVER="122.220.14.82"
REMOTE_LOGIN_USER="kids2"

REMOTE_PATH="/home/kids2/"
LOCAL_PATH="/home/kids2/"

# TARGET 1 upfiles/
SRC_DIR=${LOCAL_PATH}"src/p/edit/image_tmp/"
DEST_DIR=${REMOTE_PATH}"kids/src/p/edit/image_tmp/"
ID_DSA=${LOCAL_PATH}".ssh/id_dsa.passno"

rsync -avz  -e "ssh -i ${ID_DSA}" ${SRC_DIR}  ${REMOTE_LOGIN_USER}@${TARGET_SERVER}:${DEST_DIR}

