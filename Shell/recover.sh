#!/bin/bash
result=`/usr/bin/php /mnt/www/oa_project/www/cmd.php recover`
spath='/mnt/www/oa_project/Shell/log/recover/'
if [  "${result}" != ""  ]
then

     date=$(date +%Y%m%d)
    log_file="${path}${date}.log"

    cd   ${spath}
    touch ${log_file}

    IFS="\r\n" arr=($result)
    for x in ${arr[@]};
    do
        echo ${x} >> ${log_file}
    done

   
fi