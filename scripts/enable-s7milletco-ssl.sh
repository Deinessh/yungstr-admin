#!/bin/bash
# Run after DNS for s7milletco.com points to this server (31.97.205.102)
set -euo pipefail

echo "Checking DNS..."
SERVER_IP="31.97.205.102"
for host in s7milletco.com www.s7milletco.com; do
  # Accept direct A record or CNAME to apex (e.g. www -> s7milletco.com)
  resolved=$(dig +short "$host" | grep -E '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$' | head -1)
  if [ "$resolved" != "$SERVER_IP" ]; then
    echo "ERROR: $host resolves to $resolved (expected $SERVER_IP)"
    echo "Update Hostinger DNS A records first, then run this script again."
    exit 1
  fi
  echo "OK: $host -> $resolved"
done

certbot --nginx -d s7milletco.com -d www.s7milletco.com --non-interactive --agree-tos --register-unsafely-without-email --redirect

nginx -t && systemctl reload nginx
echo "SSL enabled for https://s7milletco.com"
