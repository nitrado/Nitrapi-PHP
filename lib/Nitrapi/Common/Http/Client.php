<?php

namespace Nitrapi\Common\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Nitrapi\Common\Exceptions\NitrapiException;
use Nitrapi\Common\Exceptions\NitrapiHttpErrorException;
use Nitrapi\Common\Exceptions\NitrapiMaintenanceException;

class Client extends GuzzleClient
{
    const MINIMUM_PHP_VERSION = '5.3.0';

    public function __construct($baseUrl = '', $config = null) {
        if (PHP_VERSION < self::MINIMUM_PHP_VERSION) {
            throw new NitrapiException(sprintf(
                'You must have PHP version >= %s installed.',
                self::MINIMUM_PHP_VERSION
            ));
        }

        $config['base_uri'] = $baseUrl;
        parent::__construct($config);
    }

    /**
     * @param $url
     * @param array $headers
     * @param array $options
     * @return mixed
     */
    public function dataGet($url, $headers = null, $options = array()) {
        try {
            if (is_array($headers)) {
                $options['headers'] = $headers;
            }

            $response = $this->request('GET', $url, $options);
            $this->checkErrors($response);
            $json = json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody(), true);
                $msg = isset($response['message']) ? $response['message'] : 'Unknown error';
                if ($e->getResponse()->getStatusCode() == 503) {
                    throw new NitrapiMaintenanceException();
                }
                if ($e->getResponse()->getStatusCode() == 428) {
                    throw new NitrapiMaintenanceException();
                }
                throw new NitrapiHttpErrorException($msg);
            }
            throw new NitrapiHttpErrorException($e->getMessage());
        }

        return (isset($json['data'])) ? $json['data'] : $json['message'];
    }

    /**
     * @param $url
     * @param array $body
     * @param array $headers
     * @param array $options
     * @return mixed
     */
    public function dataPost($url, $body = null, $headers = null, $options = array()) {
        try {
            if (is_array($body)) {
                $options['form_params'] = $body;
            }
            if (is_array($headers)) {
                $options['headers'] = $headers;
            }

            $response = $this->request('POST', $url, $options);
            $this->checkErrors($response);
            $json = json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody(), true);
                $msg = isset($response['message']) ? $response['message'] : 'Unknown error';
                if ($e->getResponse()->getStatusCode() == 503) {
                    throw new NitrapiMaintenanceException();
                }
                if ($e->getResponse()->getStatusCode() == 428) {
                    throw new NitrapiMaintenanceException();
                }
                throw new NitrapiHttpErrorException($msg);
            }
            throw new NitrapiHttpErrorException($e->getMessage());
        }

        if (isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }

        if (!empty($json['message'])) {
            return $json['message'];
        }

        return true;
    }

    /**
     * @param $url
     * @param array $body
     * @param array $headers
     * @param array $options
     * @return bool
     */
    public function dataDelete($url, $body = null, $headers = null, $options = array()) {
        try {
            if (is_array($body)) {
                $options['form_params'] = $body;
            }
            if (is_array($headers)) {
                $options['headers'] = $headers;
            }
            $response = $this->request('DELETE', $url, $options);
            $this->checkErrors($response);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody(), true);
                $msg = isset($response['message']) ? $response['message'] : 'Unknown error';
                if ($e->getResponse()->getStatusCode() == 503) {
                    throw new NitrapiMaintenanceException();
                }
                if ($e->getResponse()->getStatusCode() == 428) {
                    throw new NitrapiMaintenanceException();
                }
                throw new NitrapiHttpErrorException($msg);
            }
            throw new NitrapiHttpErrorException($e->getMessage());
        }

        return true;
    }

    protected function checkErrors(Response $response, $responseCode = 200) {
        $json = json_decode($response->getBody(), true);

        $allowedPorts = array();
        $allowedPorts[] = $responseCode;
        if ($responseCode = 200) {
            $allowedPorts[] = 201;
        }

        if (!in_array($response->getStatusCode(), $allowedPorts)) {
            throw new NitrapiHttpErrorException("Invalid http status code " . $response->getStatusCode());
        }

        if (isset($json['status']) && $json['status'] == "error") {
            throw new NitrapiHttpErrorException("Got Error from API " . $json["message"]);
        }
    }
}