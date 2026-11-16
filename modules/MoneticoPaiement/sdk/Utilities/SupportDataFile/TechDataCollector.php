<?php

declare(strict_types=1);

namespace MoneticoPaiement\Sdk\Utilities\SupportDataFile;

final class TechDataCollector implements DataCollectorInterface
{
    private array $hostingProviderSourceStrings;

    /**
     * @param array<string> $hostingProviderSourceStrings String from which we will try guess which is the hosting provider, like the database connexion string
     */
    public function __construct(array $hostingProviderSourceStrings = [])
    {
        $this->hostingProviderSourceStrings = $hostingProviderSourceStrings;
    }

    public function collect(): array
    {
        return [
            'tech' => [
                'hosting_provider' => $this->getHostingProvider(),
            ],
        ];
    }

    /**
     * This is really more a wild guess than anything else...
     *
     * Try to find the provider from the operating system information (it may work for OVH at least) and from the database connexion string.
     *
     * There are probably more ways to detect the provider more reliably,
     * but we must create a test account on each to check what env vars or other info are available.
     */
    private function getHostingProvider(): ?string
    {
        $searchStringsPerHostingProvider = [
            // the key will automatically be added to the searched values
            'localhost' => ['127.0.0.1'],
            // wordpress-specific
            'wordpress.com' => ['wordpress'],
            'wpserveur' => [],
            'wpengine' => [],
            // prestashop-specific
            'prestashop.com' => ['prestashop'],
            // generalist
            'digitalocean' => [],
            'infomaniak' => [],
            'hostinger' => [],
            'o2switch' => [],
            'hetzner' => [],
            'gandi' => [],
            'ionos' => ['1and1', 'hosting-data.io'],
            'lws' => [],
            // PaaS
            'scalingo' => [],
            'clever-cloud' => [],
            // big clouds
            'ovh' => ['ovhcloud', '-ovh-'],
            'azure' => ['azure.com', 'microsoft.com'],
            'gcp' => ['google.com'],
            'aws' => ['aws.com'],
        ];

        /** @var array<string, true> $hostingProviderMatches */
        $hostingProviderMatches = [];

        $haystacks = array_merge($this->hostingProviderSourceStrings, [php_uname()]); // php_uname = full OS version

        foreach ($searchStringsPerHostingProvider as $hostingProvider => $searchStrings) {
            $searchStrings[] = $hostingProvider;

            foreach ($haystacks as $haystack) {
                foreach ($searchStrings as $needle) {
                    if (is_int(strpos($haystack, $needle))) {
                        $hostingProviderMatches[$hostingProvider] = true;
                    }
                }
            }
        }

        if (empty($hostingProviderMatches)) {
            return null;
        }

        return implode(', ', array_keys($hostingProviderMatches));
    }
}