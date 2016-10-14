#!/bin/bash
result1=`/usr/local/sphinx2/bin/indexer --rotate  didazp_company_zl`
result2=`/usr/local/sphinx2/bin/indexer --merge  didazp_company  didazp_company_zl  --rotate`
#echo $result1
#echo $result2
