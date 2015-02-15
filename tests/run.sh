#!/bin/sh
dir=$(cd `dirname $0` && pwd)

if [[ "$#" -lt "1" ]]; then
	FILES="$dir/cases"
else
	FILES=$@
fi

"$dir/../vendor/bin/tester" -p php --setup "$dir/inc/setup.php" $FILES
