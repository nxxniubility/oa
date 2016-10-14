#!/bin/bash
result=`/usr/local/php/bin/php /mnt/www/didazp/www/cmd.php refuse`
spath='/mnt/www/didazp/Shell/log/Refuse/'
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

