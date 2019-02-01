#!/bin/sh

echo "-- convert to euc-jp --"

pwd=`pwd`

# 変換したいファイルの拡張子
#for f in `find $pwd -name \*.\[ch\] -o -name \*.tex -o -name \*.aux -o -name \*.toc -o -name \*.log` 
for f in `find $pwd -name \*.\[ch\] -o -name \*.html` 

do
    echo -n "Converting "
    echo $f
    tmpfile=/tmp/$$.`basename $f`
    nkf -e -d $f > $tmpfile
    mv $tmpfile $f
done

echo "done."

exit 0
