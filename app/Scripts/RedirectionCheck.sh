#!/bin/bash

if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <url>"
    exit 1
fi

url="$1" 

while true; do
  output=$(curl -I -s -S -f -L --max-redirs 10 -w "%{http_code};%{url_effective}\n" -o /dev/null "$url")
  IFS=';' read -r status final_url <<< "$output"
  if [[ ! $status =~ ^3 ]]; then
    url=$final_url
    break
  fi
  
  url=$final_url
done

# Prepare the JSON output with the final URL
json=$(jq -n --arg fd "$url" '{"fd": $fd}')

echo "$json"