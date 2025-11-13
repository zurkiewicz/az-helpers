<?php
namespace AZ\Helpers\Network;

use RuntimeException;

class Network
{

    /**
     * 
     * @return string
     */
    public function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // może zawierać wiele adresów, bierz pierwszy
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipList[0]);
        }

        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    }


    /**
     * 
     * @param string $ip
     * @return int|null
     */
    public function getIpVersion(string $ip): ?int
    {
        // Check IPv4
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return 4;
        }

        // Check IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return 6;
        }

        return null; // not a valid IP
    }


    /**
     * 
     * @param string $cidr
     * @return bool
     */
    public function isValidCidr(string $cidr): bool
    {
        $cidr = trim($cidr);

        // Check if there is exactly one slash
        if (substr_count($cidr, '/') !== 1) {
            return false;
        }

        list($ip, $prefix) = explode('/', $cidr, 2);

        $ip = trim($ip);
        $prefix = trim($prefix);

        // Prefix must be a non-empty integer string
        if ($prefix === '' || !ctype_digit($prefix)) {
            return false;
        }

        $prefixInt = (int) $prefix;

        // Check IPv4 CIDR
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            if ($prefixInt < 0 || $prefixInt > 32) {
                return false;
            }
            return true;
        }

        // Check IPv6 CIDR
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            if ($prefixInt < 0 || $prefixInt > 128) {
                return false;
            }
            return true;
        }

        // Neither valid IPv4 nor IPv6
        return false;
    }


    /**
     * 
     * 
     * @param string $ip
     * @param string $cidr
     * @throws \RuntimeException
     * @return bool
     */
    public function ipInCidr(string $ip, string $cidr): bool
    {

        if (count(explode('/', $cidr)) != 2) {

            $version = $this->getIpVersion($cidr);

            switch ($version) {
                case 4:
                    $cidr .= '/32';
                    break;

                case 6:
                    $cidr .= '/128';
                    break;

                default:
                    throw new RuntimeException("The address is not in CIDR format", 1);

            }

        }

        list($network, $prefix) = explode('/', $cidr);

        // Convert both IPs to binary strings
        $ipBin = inet_pton($ip);
        $networkBin = inet_pton($network);

        if ($ipBin === false || $networkBin === false) {
            return false; // invalid IP format
        }

        $ipLen = strlen($ipBin); // 4 bytes for IPv4, 16 for IPv6
        $bits = $ipLen * 8;

        // Build binary mask
        $mask = str_repeat("\xff", intdiv((int) $prefix, 8));
        $remaining = (int) $prefix % 8;

        if ($remaining > 0) {
            $mask .= chr((0xff << (8 - $remaining)) & 0xff);
        }

        $mask = str_pad($mask, $ipLen, "\0");

        // Compare masked values
        return ($ipBin & $mask) === ($networkBin & $mask);
    }


}