#!/bin/bash
#only to get the country. the whois php lib doesnt have country object for some reason
if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <domain>"
    exit 1
fi

domain_name="$1"

get_domain_country() {
    domain=$1
    country=$(whois $domain | grep -iE "country|country code" | head -n 1 | awk '{print $NF}')
    echo $country
}

register_country=$(get_domain_country $domain_name)

json_result=$(jq -n --arg rg "$register_country" \
    '{
      "register_country": $rg
    }')

echo "$json_result"
