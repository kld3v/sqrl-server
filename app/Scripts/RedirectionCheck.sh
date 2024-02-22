#!/bin/bash

# Check if the correct number of arguments are passed
if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <url>"
    exit 1
fi

url="$1"  # The initial URL provided as an argument

# Loop to follow redirects until the final URL is reached
while true; do
  # Capture redirect URL, if any, and HTTP status
  output=$(curl -I -s -S -f -L --max-redirs 10 -w "%{http_code};%{url_effective}\n" -o /dev/null "$url")
  
  # Split the output into status and the last effective URL
  IFS=';' read -r status final_url <<< "$output"
  
  # Check if the HTTP status is not a 3xx (redirect), then break
  if [[ ! $status =~ ^3 ]]; then
    url=$final_url
    break
  fi
  
  # Update the URL to the redirect URL for the next iteration
  url=$final_url
done

# Prepare the JSON output with the final URL
json=$(jq -n --arg fd "$url" '{"fd": $fd}')

# Output the final JSON
echo "$json"