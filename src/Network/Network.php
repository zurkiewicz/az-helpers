<?php
namespace AZ\Helpers\Network;

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

}