This script allows you to resolve your Hetzner Cloud server names via DNS.

__Installation and Usage:__<br>
1. Clone this repository
2. Run 'composer install' to fetch dependencies
3. Run 'php index.php --bind=0.0.0.0 --apikey=XXXXXXXXXXXXX'

Hint: Replace XXX with an API key you created earlier using the Hetzner Cloud Console.

__How IPv6 addresses are resolved:__<br>
IPv6 addresses are resolved using the rDNS configuration, but only if the hostname matches the query.
If no rDNS is configured, the default IPv6 address ending in '::1' is returned.

__What about floating IP configurations?__<br>
In current state, floating IPs are not taken into account. So your DNS query will never
return a floating IP. If you need this feature, feel free to create an issue on GitHub.