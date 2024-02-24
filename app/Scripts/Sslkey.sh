#!/bin/bash

if [[ $1 ]]; then
    host -t A $1 > /dev/null
    if [ $? -ne 0 ]; then
        echo "{ \"resolved\": false }"
        exit 1
    fi
fi

today=$(date +%F)
expires=$(echo | openssl s_client -servername $1 -connect $1:443 2>/dev/null | openssl x509 -noout -dates | grep 'notAfter' | sed 's/notAfter=//')

OS="`uname`"
case $OS in
  'Linux'|'FreeBSD'|'SunOS'|'AIX')
    days_left=$(( ( $(date -ud "$expires" +'%s') - $(date -ud "$today" +'%s') )/60/60/24 ))
    echo "{ \"resolved\": true, \"days_left\": $days_left }" | jq .
    ;;
  'Darwin') 
    OS='Mac'
    export LC_TIME="en_US"    
    days_left=$(( ( $(date -j -f "%b %d %T %Y %Z" "$expires" +'%s') - $(date -j -f "%F" "$today" +'%s') )/60/60/24 ))
    echo "{ \"resolved\": true, \"days_left\": $days_left }" | jq .
    ;;
  *) 
    echo "Error: Can't Find DATE command for your OS version!"
    ;;
esac
