<?php

namespace OCA\NCDownloader\Http;

use Symfony\Component\HttpClient\HttpClient;

final class Client
{
    private $client;

    public function __construct(?array $options = [])
    {
        $this->client = HttpClient::create($this->configure($options));
    }

    public static function create(?array $options = [])
    {
        return new self($options);
    }

    private function defaultUserAgent(): string
    {
        return "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36";
    }

    private function defaultOptions(): array
    {
        return [
            'headers' => [],
            'extra' => ['curl' => []],
        ];
    }

    private function configure(array $options): array
    {
        $settings = $this->defaultOptions();

        // Safely extract options
        $curl = $options['curl'] ?? [];
        $headers = $options['headers'] ?? [];
        $ipv4 = $options['ipv4'] ?? false;
        $force_ipv4 = $options['force_ipv4'] ?? false;
        $useragent = $options['useragent'] ?? $this->defaultUserAgent();

        // Merge provided curl options with defaults
        $settings['extra']['curl'] = array_merge($settings['extra']['curl'], $curl);

        // Configure IPv4 resolution if requested
        if ($ipv4 || $force_ipv4) {
            $settings['extra']['curl'][CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
        }

        // Set headers and user agent
        $settings['headers'] = array_merge($settings['headers'], $headers);
        $settings['headers']['User-Agent'] = $useragent;

        return $settings;
    }

    public function request(string $url, $method, ?array $options = [])
    {
        return $this->client->request($url, $method, $options);
    }
}
