<?php

namespace zFramework\Core\Facades;

class cURL
{

    /**
     * target url
     */
    static $cURL;

    /**
     * set url and some options
     * @param string $url
     * @return self
     */
    public static function set(string $url): self
    {
        self::$cURL = curl_init($url);
        curl_setopt(self::$cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$cURL, CURLOPT_HEADER, true);
        return new self();
    }

    /**
     * Post parameters
     * @param array $fiels
     * @return self
     */
    public static function post(array $fields = []): self
    {
        curl_setopt(self::$cURL, CURLOPT_POST, 1);
        curl_setopt(self::$cURL, CURLOPT_POSTFIELDS, http_build_query($fields));
        return new self();
    }

    /**
     * Set Options
     * @param array $options
     * @return self
     */
    public static function options(array $options = []): self
    {
        curl_setopt_array(self::$cURL, $options);
        return new self();
    }

    /**
     * Send request to target with all settings.
     * @param \Closure $callback
     */
    public static function send(\Closure $callback = null)
    {
        $response = curl_exec(self::$cURL);
        if ($callback != null) {
            $err     = curl_errno(self::$cURL);
            $errmsg  = curl_error(self::$cURL);
            $header  = curl_getinfo(self::$cURL);
            $callback($response, $header, ['error_no' => $err, 'error_message' => $errmsg]);
        }

        curl_close(self::$cURL);
    }
}
