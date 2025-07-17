<?php

namespace App;

class UrlValidator
{
    public function isValid(string $url): bool
    {
        if ('' === $url) {
            return false;
        }

        $urlParts = parse_url($url);

        // Check if parse_url succeeded and has a host
        if ($urlParts === false || ! isset($urlParts['host'])) {
            return false;
        }

        // Avoid localhost and private IP addresses
        if (stripos($urlParts['host'], 'localhost') !== false ||
            preg_match('/^(127\.0\.0\.1|::1|0x[0-9a-f]+|0177[0-9]+|192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/i', $urlParts['host'])) {
            return false;
        }

        // Disallow JavaScript, file, and other dangerous protocols
        if (isset($urlParts['scheme']) && preg_match('/^(javascript|vbscript|data|file|gopher|ldap)$/i', $urlParts['scheme'])) {
            return false;
        }

        // Only allow http and https schemes
        if (! isset($urlParts['scheme']) || ! in_array(strtolower($urlParts['scheme']), ['http', 'https'], true)) {
            return false;
        }

        // Convert IDN to ASCII for validation
        $urlParts['host'] = idn_to_ascii($urlParts['host'], 0, defined('INTL_IDNA_VARIANT_UTS46') ? INTL_IDNA_VARIANT_UTS46 : 0);

        // Reconstruct URL with ASCII host for validation
        $reconstructedUrl = $this->reconstructUrl($urlParts);

        return false !== filter_var($reconstructedUrl, FILTER_VALIDATE_URL);
    }

    /**
     * @param array<string, int<0, 65535>|string> $urlParts
     */
    private function reconstructUrl(array $urlParts): string
    {
        $url = '';

        if (isset($urlParts['scheme'])) {
            $url .= (string) $urlParts['scheme'] . '://';
        }

        if (isset($urlParts['user'])) {
            $url .= (string) $urlParts['user'];
            if (isset($urlParts['pass'])) {
                $url .= ':' . (string) $urlParts['pass'];
            }
            $url .= '@';
        }

        if (isset($urlParts['host'])) {
            $url .= (string) $urlParts['host'];
        }

        if (isset($urlParts['port'])) {
            $url .= ':' . (string) $urlParts['port'];
        }

        if (isset($urlParts['path'])) {
            $url .= (string) $urlParts['path'];
        }

        if (isset($urlParts['query'])) {
            $url .= '?' . (string) $urlParts['query'];
        }

        if (isset($urlParts['fragment'])) {
            $url .= '#' . (string) $urlParts['fragment'];
        }

        return $url;
    }
}
