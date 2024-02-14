#!/bin/bash

get_certificate_info() {
    url=$1
    info=$(openssl s_client -connect $url:443 -showcerts </dev/null 2>/dev/null | openssl x509 -noout -enddate -issuer)
    echo "$info"
}

get_public_key_hash() {
    url=$1
    hash=$(openssl s_client -connect $url:443 -showcerts </dev/null 2>/dev/null | openssl x509 -pubkey -noout | openssl pkey -pubin -outform DER | sha256sum | awk '{print $1}')
    echo "$hash"
}

extract_domain_name() {
    url=$1
    domain=$(echo $url | awk -F/ '{print $3}')
    echo "$domain"
}

if [ -z "$1" ]; then
    echo "Usage: $0 <url>"
    exit 1
fi

url=$1
domain=$(extract_domain_name $url)

certificate_info=$(get_certificate_info $domain)

if [ -z "$certificate_info" ]; then
    echo '{"error": "Certificate not found or SSL handshake failed."}' | jq .
    exit 1
fi

expected_hash=$(get_public_key_hash $domain)
actual_hash=$(get_public_key_hash $domain)

if [ -z "$actual_hash" ]; then
    echo '{"error": "No certificate information retrieved for the provided URL."}' | jq .
    exit 1
fi

validity_status=$(echo "$certificate_info" | grep -i "not after" | cut -d= -f2- | xargs -0 date -d)

trust_status=""
if [ "$actual_hash" == "$expected_hash" ]; then
    trust_status="URL is considered trustworthy based on the public key."
else
    trust_status="URL is not considered trustworthy."
fi

jq -n --arg ts "$trust_status" --arg vs "$validity_status" --arg ci "$certificate_info" --arg pk "$actual_hash" --arg eh "$expected_hash" --arg ah "$actual_hash" \
'{
  "trust_status": $ts,
  "validity_status": $vs,
  "issuer_info": $ci,
  "public_key": $pk,
  "expected_hash": $eh,
  "actual_hash": $ah
}'