#!/bin/bash
result=`/usr/local/php /mnt/www/oa_project/www/cmd.php mail`
while [ ${result} == 1 ]
do
result=`/usr/local/php /mnt/www/oa_project/www/cmd.php mail`
done

