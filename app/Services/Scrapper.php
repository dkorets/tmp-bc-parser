<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Config\Repository as Config;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Scrapper
{
    private const BEST_CHANGE_RATES_URL = 'https://www.bestchange.ru/action.php';

    private array $proxyIpList;
    private array $proxyConfig;

    public function __construct(
        private readonly Client $client,
        private readonly Config $config,
    ) {
        $this->setupProxyIpList();
    }

    private function setupProxyIpList(): void
    {
        $this->proxyConfig = $this->config->get('bestchange_parser.proxy');
        $ipList = array_flip($this->proxyConfig['ip_list']);
        $keys = array_keys($ipList);

        $this->proxyIpList = array_fill_keys($keys, 0);
    }

    /**
     * @throws GuzzleException
     */
    public function fetchRates(int $from, int $to, ?int $city): ResponseInterface
    {
        $data = [
            RequestOptions::FORM_PARAMS => [
                'action' => 'getrates',
                'page' => 'rates',
                'from' => $from,
                'to' => $to,
                'city' => $city,
            ],
            RequestOptions::HEADERS => [
                'Authority' => 'www.bestchange.com',
                'Accept' => '*/*',
                'Accept-Language' => 'en-GB,en-US;q=0.9,en;q=0.8',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Cookie' => $this->getHardcodedSessionIdCookie(),
                'Origin' => 'https://www.bestchange.com',
                'Referer' => 'https://www.bestchange.com/tether-trc20-to-dollar-cash-in-kiev.html',
                'Sec-Ch-Ua' => '"Chromium\";v=\"118\", \"Google Chrome\";v=\"118\", \"Not=A?Brand\";v=\"99\"',
                'Sec-Ch-Ua-Mobile' => '?0',
                'Sec-Ch-Ua-Platform' => '"macOS\"',
                'Sec-Fetch-Dest' => 'empty',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Site' => 'same-origin',
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
            ],
        ];

        if (true === $this->proxyConfig['enabled']) {
            $data['curl'] = [
                CURLOPT_PROXY => $this->proxyConfig['host'],
                CURLOPT_PROXYUSERPWD => $this->getProxyAuth(),
            ];
        }

        return $this->client
            ->post(
                self::BEST_CHANGE_RATES_URL,
                $data,
            );
    }

    private function getProxyAuth(): string
    {
        $username = $this->proxyConfig['username'];
        $password = $this->proxyConfig['password'];
        $proxyIp = $this->resolveProxyIp();

        return "$username-zone-data_center-ip-$proxyIp:$password";
    }

    private function resolveProxyIp(): string
    {
        foreach ($this->proxyIpList as $ip => $usageCount) {
            if ($usageCount <= $this->proxyConfig['requests_per_ip']) {
                $this->proxyIpList[$ip] += 1;
                return $ip;
            }
        }

        throw new RuntimeException("Proxy ip list exceeded!");
    }

    private function getHardcodedSessionIdCookie(): string
    {
        return 'PHPSESSID=2h2a1dnahsl7cg02gspm3a1p7p; userid=3985fb2df30cd57af91b72834babf7e5; pixel=1; time_offset=-60; _ga=GA1.1.173094820.1698667676; _ym_uid=1698667676320965330; _ym_d=1698667676; city=3; last_ci=3; source=N%16%D8s%E7De%23%3D%91%01%AE%08P%26%B5%7B%BD%13%AE%1C0%60%D2%13v%12%83M%3C%24%EB%E2%09%9F%21%E2%C6%12%AC%A1%12v%F8%F3%FF%3E%0A%D2%E4%17%F5%A1; history=1698667868-1019-1-10-89-0.98405810-6366170.36a1698667977-1019-1-10-89-0.98405810-6366170.36a1698673284-585-1-10-89-0.98371944-1733080.78a1698677856-585-1-10-89-0.98362268-1733080.78a1698691873-1019-1-10-89-0.98355480-6366170.36a1698744084-1019-1-10-89-0.98453283-6366170.36; _ym_isad=1; _ga_FJL01FHVHH=GS1.1.1698849120.10.1.1698849243.55.0.0; PHPSESSID=tks9133bjdo7g3o5l22fsbk4p1; userid=6e066d0f25b63df2641b93332f0700a9';
    }
}
