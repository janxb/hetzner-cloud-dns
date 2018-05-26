This script allows you to resolve your Hetzner Cloud server names via DNS.

__Installation and Usage:__<br>
1. Clone this repository
1. Run 'sudo add-apt-repository ppa:ondrej/php' to get access to required (newer) PHP versions
1. Run 'sudo apt install composer php-zip php-curl' to install binary dependencies
1. Run 'composer install' to install PHP dependencies
1. Run 'sudo php index.php --bind=0.0.0.0 --apikey=XXXXXXXXXXXXX'

Hint: Replace XXX with an API key you created earlier using the Hetzner Cloud Console.

__How IPv6 addresses are resolved:__<br>
IPv6 addresses are resolved using the rDNS configuration, but only if the hostname matches the query.
If no rDNS is configured, the default IPv6 address ending in '::1' is returned.

__What about floating IP configurations?__<br>
In current state, floating IPs are not taken into account. So your DNS query will never
return a floating IP. If you need this feature, feel free to create an issue on GitHub.
