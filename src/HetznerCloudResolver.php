<?php

class HetznerCloudResolver implements \yswery\DNS\ResolverInterface
{
    private static $TYPE_A = 1;
    private static $TYPE_AAAA = 28;
    private static $API_BASE_URL = 'https://api.hetzner.cloud/v1/';

    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getAnswer(array $query)
    {
        $query[0]['qname'] = rtrim($query[0]['qname'], '.');

        $result = [];

        foreach ($this->apiCall('servers')->servers as $server) {
            if ($server->name == $query[0]['qname']) {

                if ($query[0]['qtype'] == self::$TYPE_A) {
                    $result[] = $server->public_net->ipv4->ip;
                }

                else if ($query[0]['qtype'] == self::$TYPE_AAAA) {
                    // first, use IPv6 rDNS config for name resolution
                    foreach ($server->public_net->ipv6->dns_ptr as $address) {
                        if ($address->dns_ptr == $query[0]['qname']) {
                            $result[] = $address->ip;
                        }
                    }

                    // if rDNS had no result, use the default /64 block and return the first address
                    if (empty($result)) {
                        $result[] = str_replace('::/64', '::1', $server->public_net->ipv6->ip);
                    }
                }
            }
        }

        $return = [];
        foreach ($result as $address) {
            $return[] = [
                'name' => $query[0]['qname'],
                'class' => $query[0]['qclass'],
                'ttl' => 0,
                'data' => [
                    'type' => $query[0]['qtype'],
                    'value' => $address
                ]
            ];


        }
        return $return;
    }

    private function apiCall($url)
    {
        $ch = curl_init();
        $headers = array('Authorization: Bearer ' . $this->apiKey);

        curl_setopt($ch, CURLOPT_URL, self::$API_BASE_URL . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function allowsRecursion()
    {
        return false;
    }

    public function isAuthority($domain)
    {
        return true;
    }
}
