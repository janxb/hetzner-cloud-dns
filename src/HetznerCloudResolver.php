<?php

class HetznerCloudResolver implements \yswery\DNS\ResolverInterface
{
    const TYPE_A = 1;

    const TYPE_AAAA = 28;

    private $hCloudServerEndpoint;

    public function __construct(string $apiKey)
    {
        new \LKDev\HetznerCloud\HetznerAPIClient($apiKey);
        $this->hCloudServerEndpoint = new LKDev\HetznerCloud\Models\Servers\Servers();
    }

    public function getAnswer(array $query)
    {
        $query[0]['qname'] = rtrim($query[0]['qname'], '.');

        $result = [];
        try {
            foreach ($this->hCloudServerEndpoint->all() as $server) {
                if ($server->name == $query[0]['qname']) {

                    if ($query[0]['qtype'] == self::TYPE_A) {
                        $result[] = $server->public_net->ipv4->ip;
                    } else {
                        if ($query[0]['qtype'] == self::TYPE_AAAA) {
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
            }
        } catch (\LKDev\HetznerCloud\APIException $exception) {
            echo $exception->getMessage();

            return 0;
        }
        $return = [];
        foreach ($result as $address) {
            $return[] = [
                'name' => $query[0]['qname'],
                'class' => $query[0]['qclass'],
                'ttl' => 0,
                'data' => [
                    'type' => $query[0]['qtype'],
                    'value' => $address,
                ],
            ];
        }

        return $return;
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
