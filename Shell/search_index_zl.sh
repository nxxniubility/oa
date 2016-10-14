#!/bin/bash
result1=`/usr/local/sphinx2/bin/indexer --rotate  didazp_job_zl`
result2=`/usr/local/sphinx2/bin/indexer --merge didazp_job  didazp_job_zl  --rotate`
#echo $result1
#echo $result2
