#!/bin/bash

set -e

# Get script name
SCRIPT=$(basename "$0")

# Check params
[[ -z "$2" ]] && { echo "Parameter filter is empty." ; exit 1; }

# Get protocol
proto="$(echo "$1" | grep :// | sed -e 's,^\(.*://\).*,\1,g')"

# Remove the protocol
url="$(echo "${1/$proto/}")"

# Extract the user (if any)
userpass="$(echo "$url" | grep @ | cut -d@ -f1)"
pass="$(echo "$userpass" | grep : | cut -d: -f2)"
if [ -n "$pass" ]; then
    user="$(echo "$userpass" | grep : | cut -d: -f1)"
else
    user="$userpass"
fi

# Extract the host
host="$(echo "${url/$user@/}" | cut -d/ -f1)"

# Try to extract the port
port=""
if [[ $host == *":"* ]]; then
    port=$(echo "$host" | awk -F':' '{print $2}')
    host=$(echo "$host" | awk -F':' '{print $1}')
fi
path="$(echo "$url" | grep / | cut -d/ -f2-)"

# Extract domain and subdomain
IFS='.' read -r -a parts <<< "$host"
len=${#parts[@]}

# Check if the host is an IP address
ip_regex='^([0-9]+\.){3}[0-9]+$'
if [[ $host =~ $ip_regex ]]; then
    domain="$host"
    subdomain=""
else
    # Check if the last part is a number (indicative of an IP address in the form x.y.z.w)
    if [[ "${parts[@]: -1:1}" =~ ^[0-9]+$ ]]; then
        domain="${parts[@]: -4:4}" # Last 4 parts for IP
        domain="$(IFS=.; echo "${domain[*]}")"
        subdomain="${parts[*]:0:len-4}" # Exclude IP parts
        subdomain="$(IFS=.; echo "${subdomain[*]}")"
        subdomain="${subdomain%.}"
    else
        domain="${parts[len-2]}.${parts[len-1]}"
        subdomain=""
        for ((i=0; i<len-2; i++)); do
            subdomain+="${parts[i]}."
        done
        subdomain="${subdomain%.}"
    fi
fi

for f in $(echo "$2" | sed "s/,/ /g")
do
    case $f in
        url)
            echo "$url"
            ;;
        proto)
            echo "$proto"
            ;;
        user)
            echo "$user"
            ;;
        pass)
            echo "$pass"
            ;;
        host)
            echo "$host"
            ;;
        domain)
            echo "$domain"
            ;;
        subdomain)
            echo "$subdomain"
            ;;
        port)
            echo "$port"
            ;;
        path)
            echo "$path"
            ;;
    esac
done
echo "{
  \"url\": \"$url\",
  \"proto\": \"$proto\",
  \"user\": \"$user\",
  \"pass\": \"$pass\",
  \"host\": \"$host\",
  \"domain\": \"$domain\",
  \"subdomain\": \"$subdomain\",
  \"port\": \"$port\",
  \"path\": \"$path\"
}" | jq .