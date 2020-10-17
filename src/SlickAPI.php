<?php

namespace SlickLabs\Laravel\SlickAPI;

use GuzzleHttp;
use GuzzleHttp\Client;
use SlickLabs\Laravel\SlickAPI\Util\Strings;
use Illuminate\Support\Facades\Cache;

/**
 * SlickAPI
 *
 * Contains the functionality to connect with the SlickLabs API.
 *
 * @author Leo Flapper
 * @author Nathan Jansen
 * @version 1.0.0
 */
class SlickAPI
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    /**
     * Default method is GET
     */
    const DEFAULT_METHOD = self::METHOD_GET;

    const CACHE_KEY = 'slickapi';

    const CACHE_EXPIRATION = '+1 hour';

    /**
     * The SlickLabs API url.
     * @var string
     */
    protected $url;

    /**
     * @var SlickAPIConfig
     */
    protected $config;

    /**
     * The SlickLabs API version
     *
     * @var string
     */
    protected $version;

    /**
     * The SlickLabs API access token.
     * @var string
     */
    protected $accessToken;

    /**
     * The response body.
     * @var string
     */
    protected $body;

    /**
     * The connection client.
     * @var Client
     */
    protected $client;

    /**
     * Retrieves the settings and sets up the connection client.
     * @param Client $client the request client.
     * @throws \Exception
     */
    public function __construct(SlickAPIConfig $config, $client = null)
    {
        $this->config = $config;

        $this->setAccessToken($this->config->get('token'));
        $this->setUrl($this->config->get('url'));
        $this->setVersion($this->config->get('version'));
        $this->setClient($client);
    }

    /**
     * Sets the request client.
     * @param Client|null $client the request client.
     * @return void
     */
    private function setClient(Client $client = null)
    {
        if (!$client) {
            $this->client = new Client([
                'base_uri' => $this->getUrl()
            ]);
        } else {
            $this->client = $client;
        }
    }

    /**
     * Function for making a call to the SlickLabs API
     * @param  string $route the route from the SlickLabs API
     * @param  array $args options array.
     * @return Response $this psr-7 complient response object.
     */
    public function request($route, $args = [])
    {
        $defaults = [
            'method' => self::DEFAULT_METHOD,
            'headers' => $this->config->get('headers', []),
            'body' => $this->config->get('body', []),
            'query' => $this->config->get('query', []),
            'verify' => $this->config->get('verify', true)
        ];

        $args = array_replace_recursive($defaults, $args);

        $url = $this->getRouteUrl($route);

        //ShopAPI::getAdminAjax() / 'new url'-bugfix
        if (filter_var($route, FILTER_VALIDATE_URL)) {
            $url = $route;
        }

        $response = null;

        if ($response = $this->doRequest($url, $args['method'], $args)) {
            $response = new Response(
                $response->getStatusCode(),
                $response->getHeaders(),
                $this->decodeBody($response->getBody()),
                $response->getProtocolVersion()
            );
        }

        return $response;
    }

    /**
     * Generates the rout url by combing the route with the API url and version.
     * @param  string $route the route to call.
     * @return string the route url.
     */
    public function getRouteUrl($route)
    {
        return $this->getUrl() . Strings::addLeading($this->getVersion(), '/') . $route;
    }

    /**
     * Returns the SlickLabs API url.
     * @return string the SlickLabs API url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the SlickLabs API url.
     * @param string $url the SlickLabs API url.
     * @return void
     */
    protected function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Returns the SlickLabs API version.
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the SlickLabs API version.
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Executes the request.
     * @param  string $url the request url.
     * @param  string $method the request method.
     * @param  array $headers the request headers.
     * @param  array $body the request body (POST).
     * @param  array $query the request query parameters (GET).
     * @return Response the request response object
     */
    protected function doRequest($url, $method, array $args = [])
    {
        $headers = $args['headers'] ?? [];
        $query = $args['query'] ?? [];

        $args = [
            'verify' => false,
            'headers' => array_replace_recursive($headers, $this->getDefaultHeaders()),
            'query' => array_replace_recursive($query, $this->getDefaultQueryArgs()),
            'json' => $args['body'] ?? null
        ];

        $response = null;
        try {
            $response = $this->getClient()->request($method, $url, $args);
        } catch (GuzzleHttp\Exception\ServerException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }



    public function get($url, array $args = [], $cached = false)
    {
        $args['method'] = self::METHOD_GET;
        if($cached) {

            $cacheKey = $url;

            if(isset($args['query'])) {
                $cacheKey .= http_build_query($args['query']);
            }

            if($result = $this->getCache($cacheKey)) {
                $response = new Response(
                    200,
                    [
                        'laravel_cache' => true
                    ],
                    $result,
                    '1.1'
                );

                return $response;
            }

            $cacheExpiration = '';
            if(isset($args['cacheExpiration']) && $args['cacheExpiration']) {
                $cacheExpiration = $args['cacheExpiration'];
            }

            $response = $this->request($url, $args);

            if($response->getBody()) {
                $this->putCache($cacheKey, $response, $cacheExpiration);
            }

            return $response;

        }
        
        return $this->request($url, $args);
    }

    public function getCached($url, array $args = [])
    {
        return $this->get($url, $args, true);
    }

    public function post($url, array $args = [])
    {
        $args['method'] = self::METHOD_POST;

        return $this->request($url, $args);
    }

    public function put($url, array $args = [])
    {
        $args['method'] = self::METHOD_PUT;

        return $this->request($url, $args);
    }

    public function patch($url, array $args = [])
    {
        $args['method'] = self::METHOD_PATCH;

        return $this->request($url, $args);
    }

    public function delete($url, array $args = [])
    {
        $args['method'] = self::METHOD_DELETE;

        return $this->request($url, $args);
    }

    /**
     * Returns the default request headers.
     * @return array the default request headers.
     */
    public function getDefaultHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAccessToken()
        ];
    }

    /**
     * Returns the SlickLabs API access token.
     * @return string the SlickLabs API access token.
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Sets the SlickLabs API access token.
     * @param string $accessToken the SlickLabs API access token
     * @return void
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * The default query (GET) arguments.
     * @return array the default query arguments.
     */
    public function getDefaultQueryArgs()
    {
        return [];
    }

    /**
     * Returns the request client.
     * @return Client the request client.
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Decodes the json response body.
     * @param  string $body the response body
     * @return mixed a decoded object or string.
     */
    private function decodeBody($body)
    {
        if ($object = json_decode($body)) {

            if (json_last_error()) {
                return 'JSON Error: ' . json_last_error();
            }

        } else {
            $object = (string)$body;
        }

        return $object;
    }

    public function getCache($key)
    {
        return Cache::get($this->getCacheKey($key));
    }

    public function getCacheKey($key)
    {
        return self::CACHE_KEY . '_' . $this->config->get('cache_key') . '_' . str_replace('/', '_', $key);
    }

    public function putCache($key, $response, $cacheExpiration = '')
    {
        if('' === $cacheExpiration) {
            $cacheExpiration = self::CACHE_EXPIRATION;
        }

        $expiration = $this->config->get('cache_expiration', $cacheExpiration);
        
        Cache::put($this->getCacheKey($key), $response->getBody(), (new \DateTime())->modify($expiration));
    }
}
