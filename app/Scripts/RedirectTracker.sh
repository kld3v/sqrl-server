#!/bin/bash

if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <url>"
    exit 1
fi

url="$1"

while redirect_url=$(
  curl -I -s -S -f -w "%{redirect_url}\n" -o /dev/null "$url"
); do
  echo "$url"
  url=$redirect_url
  [[ -z "$url" ]] && break
done
echo "Final URL: $url"