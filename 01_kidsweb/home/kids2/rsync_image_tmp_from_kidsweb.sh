#!/bin/sh

# ****************************************************************************
#
# K.I.D.S. Open RSYNC
#
# Producer: Kazushi Saito
# MakeDate: 2008/09/23
# EditDate:
# Note:
#     > crontab setting sample.
#       0 4 * * * /home/kids2/rsync_image_tmp.sh > /dev/null
#
#     > REMOTE -> LOCAL  RSYNC
#
#
# ****************************************************************************

TARGET_SERVER="192.168.10.218"
REMOTE_LOGIN_USER="kids2"

REMOTE_PATH="/home/kids2/"
LOCAL_PATH="/home/kids2/"

# TARGET 1 upfiles/
SRC_DIR=${REMOTE_PATH}"src/p/edit/image_tmp/"
DEST_DIR=${LOCAL_PATH}"src/p/edit/image_tmp/"
ID_DSA=${LOCAL_PATH}".ssh/id_dsa.passno2"

rsync -avz  -e "ssh -i ${ID_DSA}" ${REMOTE_LOGIN_USER}@${TARGET_SERVER}:${SRC_DIR}  ${DEST_DIR}




