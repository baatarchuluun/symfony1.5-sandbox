<?php

class CurlHttpClient implements HttpClientInterface
{
    const HTTP_CODE_OK = 200;

    protected function initCurl($url)
    {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_HEADER, false);

        return $curl_handle;
    }

    /**
     * @param $curl_handle
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function executeCurl($curl_handle)
    {
        $buffer = curl_exec($curl_handle);
        $errno = curl_errno($curl_handle);
        $httpcode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

        $result = json_decode($buffer, true);

        curl_close($curl_handle);

        if ($errno) {
            $error_message = curl_strerror($errno);
            throw new Exception($error_message);
        }

        if (self::HTTP_CODE_OK == $httpcode && array_key_exists('code', $result) && 0 != $result['code']) {
            $code = $result['code'];

            $message = $result['response'];
            throw new Exception($message, $code);
        }

        if (self::HTTP_CODE_OK != $httpcode) {
            throw new Exception(json_encode($result), $httpcode);
        }

        return $result;
    }

    /**
     * @param $url
     * @param array $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function post($url, $options = [])
    {
        $header = isset($options['header']) ? $options['header'] : [];
        $data = isset($options['data']) ? $options['data'] : [];

        $curl_handle = $this->initCurl($url);

        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_handle, CURLOPT_POST, true);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));

        return $this->executeCurl($curl_handle);
    }

    /**
     * @param $url
     * @param array $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function get($url, $options = [])
    {
        $header = isset($options['header']) ? $options['header'] : [];
        $data = isset($options['data']) ? $options['data'] : [];

        $url = $url . (count($data) ? '?' . http_build_query($data, '', '&') : '');
        $curl_handle = $this->initCurl($url);

        curl_setopt($curl_handle, CURLOPT_POST, false);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $header);

        return $this->executeCurl($curl_handle);
    }

    /**
     * @param $url
     * @param array $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function delete($url, $options = [])
    {
        $header = isset($options['header']) ? $options['header'] : [];
        $data = isset($options['data']) ? $options['data'] : [];

        $curl_handle = $this->initCurl($url);

        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));

        return $this->executeCurl($curl_handle);
    }
}
