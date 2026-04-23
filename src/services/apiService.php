<?php

class ApiService
{
    /**
     * Get the API base URL from environment variable or use default
     *
     * @return string
     */
    protected static function getBaseURL()
    {
        return getenv('YG_API_BASE_URL') ?? 'https://app.tuyglu.com/api/rest/';
    }

    /**
     * Get the API key from WordPress options
     *
     * @return string|null
     */
    protected static function getApiKey()
    {
        return get_option('ygwp_api_key');
    }

    /**
     * Add API key header to request headers if available
     *
     * @param array $headers
     * @return array
     */
    protected static function addApiKeyHeader($headers = [])
    {
        $api_key = self::getApiKey();
        if (!empty($api_key)) {
            $headers['Apikey'] = $api_key;
        }
        return $headers;
    }

    /**
     * Make a GET request to the API
     *
     * @param string $endpoint
     * @param array $headers
     * @return array
     */
    public static function get($endpoint, $headers = [])
    {
        $headers = self::addApiKeyHeader($headers);
        $url = self::getBaseURL() . $endpoint;

        $args = [
            'method' => 'GET',
            'headers' => $headers,
        ];

        return self::makeRequest($url, $args);
    }

    /**
     * Make a POST request to the API
     *
     * @param string $endpoint
     * @param mixed $body
     * @param array $headers
     * @return array
     */
    public static function post($endpoint, $body = [], $headers = [])
    {
        $headers = self::addApiKeyHeader($headers);
        $url = self::getBaseURL() . $endpoint;

        $args = [
            'method' => 'POST',
            'headers' => $headers,
            'body' => $body,
        ];

        return self::makeRequest($url, $args);
    }

    /**
     * Make a POST request with file upload
     *
     * @param string $endpoint
     * @param array $data - Regular form data
     * @param array $files - Array of files in format ['field_name' => '/path/to/file']
     * @param array $headers
     * @return array
     */
    public static function postWithFiles($endpoint, $data = [], $files = [], $headers = [])
    {
        $headers = self::addApiKeyHeader($headers);
        $url = self::getBaseURL() . $endpoint;

        // Create multipart body for file upload
        $boundary = '----YGLUFormBoundary' . md5(time());

        $body = '';

        // Add JSON-encoded data as a single field named 'data'
        $json_data = json_encode($data);
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"data\"\r\n";
        $body .= "Content-Type: application/json\r\n";
        $body .= "\r\n";
        $body .= $json_data . "\r\n";

        // Add file fields
        foreach ($files as $field_name => $file_path) {
            if (file_exists($file_path)) {
                $filename = basename($file_path);
                $mimetype = mime_content_type($file_path) ?: 'application/octet-stream';

                $body .= "--{$boundary}\r\n";
                $body .= "Content-Disposition: form-data; name=\"{$field_name}\"; filename=\"{$filename}\"\r\n";
                $body .= "Content-Type: {$mimetype}\r\n";
                $body .= "\r\n";
                $body .= file_get_contents($file_path) . "\r\n";
            }
        }

        $body .= "--{$boundary}--\r\n";

        $args = [
            'method' => 'POST',
            'timeout' => 30,
            'headers' => [
                'Content-Type' => "multipart/form-data; boundary={$boundary}",
                'Content-Length' => strlen($body),
            ] + $headers, // Merge with additional headers including Apikey
            'body' => $body,
        ];

        return self::makeRequest($url, $args);
    }

    /**
     * Make a PUT request to the API
     * 
     * @param string $endpoint
     * @param mixed $body
     * @param array $headers
     * @return array
     */
    public static function put($endpoint, $body = [], $headers = [])
    {
        $headers = self::addApiKeyHeader($headers);
        $url = self::getBaseURL() . $endpoint;

        $args = [
            'method' => 'PUT',
            'headers' => $headers,
            'body' => $body,
        ];

        return self::makeRequest($url, $args);
    }

    /**
     * Make a DELETE request to the API
     *
     * @param string $endpoint
     * @param array $headers
     * @return array
     */
    public static function delete($endpoint, $headers = [])
    {
        $headers = self::addApiKeyHeader($headers);
        $url = self::getBaseURL() . $endpoint;

        $args = [
            'method' => 'DELETE',
            'headers' => $headers,
        ];

        return self::makeRequest($url, $args);
    }

    /**
     * Make an HTTP request using WordPress HTTP API
     *
     * @param string $url
     * @param array $args
     * @return array
     */
    protected static function makeRequest($url, $args)
    {
        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'error' => $response->get_error_message(),
                'data' => null
            ];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        return [
            'success' => $status_code >= 200 && $status_code < 300,
            'status_code' => $status_code,
            'data' => json_decode($body, true),
            'raw_response' => $response
        ];
    }
}
