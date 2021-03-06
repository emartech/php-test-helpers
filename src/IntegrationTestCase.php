<?php

namespace Emartech\TestHelper;

use GuzzleHttp\Client;
use Escher\Provider as EscherProvider;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Emartech\Jwt\Jwt;

abstract class IntegrationTestCase extends BaseTestCase
{
    /**
     * @var string
     */
    protected $serviceHost;

    /**
     * @var SpyLogger
     */
    protected $spyLogger;

    /**
     * @var Client
     */
    protected $client;

    protected function setUp()
    {
        parent::setUp();
        $this->serviceHost = getenv('INTEGRATION_TEST_HOST');
        $this->spyLogger = new SpyLogger();
        $this->client = new Client(['cookies' => true]);
    }

    protected function assertOK(ResponseInterface $response, $message = '')
    {
        $this->assertEquals(200, $response->getStatusCode(), "HTTP status code is not OK" . $message);
    }

    protected function get($uri)
    {
        return $this->client->get($this->serviceHost . $uri);
    }

    protected function post($uri, $postValues = [])
    {
        $options = [];
        if (!empty($postValues)) {
            $options['form_params'] = $postValues;
        }
        return $this->client->post($this->serviceHost . $uri, $options);
    }

    protected function getWithEscher(string $uri)
    {
        $url = $this->serviceHost . $uri;
        $escherSignedHeaders = $this->escherSignHeaders($url, 'GET');
        return $this->client->get($url, ['headers' => $escherSignedHeaders]);
    }

    protected function postWithEscher(string $uri, string $body = '')
    {
        $url = $this->serviceHost . $uri;
        $escherSignedHeaders = $this->escherSignHeaders($url, 'POST', $body);
        return $this->client->post($url, ['headers' => $escherSignedHeaders, 'body' => $body]);
    }

    protected function putWithEscher(string $uri, string $body = '')
    {
        $url = $this->serviceHost . $uri;
        $escherSignedHeaders = $this->escherSignHeaders($url, 'PUT');
        return $this->client->put($url, ['headers' => $escherSignedHeaders, 'body' => $body]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $body
     * @param array $formParams
     * @return mixed|ResponseInterface
     * @throws GuzzleException
     */
    protected function requestWithJwt(string $method, string $uri, string $body = '', array $formParams = [])
    {
        $url = $this->serviceHost . $uri;
        $jwt = new Jwt(getenv('JWT_SECRET'));
        $token = $jwt->generateToken($body);
        $options = ['headers' => ['Authorization' => 'Bearer ' . $token], 'body' => $body];
        if (!empty($formParams)) {
            $options['form_params'] = $formParams;
        }
        return $this->client->request($method, $url, $options);
    }

    /**
     * @param string $uri
     * @param string $body
     * @return mixed|ResponseInterface
     * @throws GuzzleException
     */
    protected function deleteWithJwt(string $uri, string $body = '')
    {
        return $this->requestWithJwt('DELETE', $uri, $body);
    }

    /**
     * @param string $uri
     * @param string $body
     * @return mixed|ResponseInterface
     * @throws GuzzleException
     */
    protected function getWithJwt(string $uri, string $body = '')
    {
        return $this->requestWithJwt('GET', $uri, $body);
    }

    /**
     * @param string $uri
     * @param array $formParams
     * @param string $body
     * @return mixed|ResponseInterface
     * @throws GuzzleException
     */
    protected function postWithJwt(string $uri, array $formParams = [], string $body = '')
    {
        return $this->requestWithJwt('POST', $uri, $body, $formParams);
    }

    protected function assertJsonSuccess(ResponseInterface $response, bool $expected)
    {
        $json = json_decode($response->getBody(), true);
        $this->assertTrue(isset($json['success']));
        $this->assertEquals($expected, $json['success']);
    }

    private function createEscherProvider(): EscherProvider
    {
        $escherCredentials = json_decode(getenv('ESCHER_SUITE_KEY_DB'), true);
        $escherKey = array_keys($escherCredentials)[0];
        $escherProvider = new EscherProvider(getenv('ESCHER_SUITE_CREDENTIAL_SCOPE'), $escherKey, $escherCredentials[$escherKey], []);
        return $escherProvider;
    }

    private function escherSignHeaders(string $url, string $method, string $body = '')
    {
        $escherProvider = $this->createEscherProvider();
        $escher = $escherProvider->createEscher();
        return $escher->signRequest(
            $escherProvider->getEscherKey(),
            $escherProvider->getEscherSecret(),
            $method,
            $url,
            $body
        );
    }
}
