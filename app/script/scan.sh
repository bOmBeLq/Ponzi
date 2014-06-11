DIR="`dirname $0`"
echo "\n`date +"%Y-%m-%d %H:%M:%S"`\n" >> $DIR/../logs/command-scan/`date +"%Y-%m-%d.log"`
$DIR/../console bml:wal:sca >> $DIR/../logs/command-scan/`date +"%Y-%m-%d.log"`
