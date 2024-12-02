<?php

namespace App\Services;

class CookieHandler
{
    /**
     * Get cookie value
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return void
     */
    public function setCookie(string $name, string $value, array $options = []): void
    {
        setcookie($name, $value, $options);
    }
}
