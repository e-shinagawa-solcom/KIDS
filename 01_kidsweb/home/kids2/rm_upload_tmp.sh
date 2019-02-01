#!/bin/sh


# home dir
export HOME=/home/kids2

#
# delete upload_tmp/* 
#			old file which passed for 10 hours
#
find $HOME/upload_tmp/ -cmin +60 -exec rm -f {} \;

