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

alidity=$(openssl s_client -connect $url:443 -showcerts </dev/null 2>/dev/null | openssl x509 -dates -noout)

current_date=$(date '+%s')
expiration_date=$(date -d "$(echo "$validity" | grep "notAfter" | cut -d= -f2)" '+%s')

if [ $current_date -gt $expiration_date ]; then
    echo "Certificate has expired."
else
    echo "Certificate is still valid."
fi
issuer=$(openssl s_client -connect $url:443 -showcerts </dev/null 2>/dev/null | openssl x509 -issuer -noout)

trusted_issuer="CN=YourTrustedCA"
if [ "$issuer" == "$trusted_issuer" ]; then
    echo "Issuer is trusted."
else
    echo "Issuer is not trusted."
fi
openssl s_client -connect $url:443 -showcerts </dev/null 2>/dev/null | openssl x509 -text -noout | grep "Issuer:"
exit 0
