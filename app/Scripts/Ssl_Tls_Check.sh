#!/bin/bash
if [ -z "$1" ]; then
    echo "Usage: $0 <url>"
    exit 1
fi
url=$1
echo "SSL/TLS Information for $url:"
echo -e "\nCertificate:"
openssl s_client -connect $url:443 -showcerts </dev/null 2>/dev/null | openssl x509 -text -noout
echo -e "\nSupported Protocols and Ciphers:"
openssl s_client -connect $url:443 -no_ssl2 -no_ssl3 -cipher "ALL" </dev/null 2>/dev/null | grep "Protocol\|Cipher"
exit 0