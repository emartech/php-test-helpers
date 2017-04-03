<?php

namespace Emartech\TestHelper;

use GuzzleHttp\Client;
use Escher\Provider as EscherProvider;
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


    protected function setUp()
    {
        parent::setUp();
        $this->serviceHost = getenv('INTEGRATION_TEST_HOST');
        $this->spyLogger = new SpyLogger();
    }

    /**
     * @param $response
     */
    protected function assertOK(ResponseInterface $response, $message = '')
    {
        $this->assertEquals(200, $response->getStatusCode(), "HTTP status code is not OK" . $message);
    }

    protected function get($uri)
    {
        return (new Client())->get($this->serviceHost.$uri);
    }

    protected function getWithEscher(string $uri)
    {
        $url = $this->serviceHost.$uri;
        $escherCredentials = json_decode(getenv('ESCHER_SUITE_KEY_DB'), true);
        $escherKey = array_keys($escherCredentials)[0];
        $escherProvider = new EscherProvider(getenv('ESCHER_SUITE_CREDENTIAL_SCOPE'), $escherKey, $escherCredentials[$escherKey], []);

        $escher = $escherProvider->createEscher();
        $escherSignedHeaders = $escher->signRequest(
            $escherProvider->getEscherKey(),
            $escherProvider->getEscherSecret(),
            'GET',
            $url,
            ''
        );

        return (new Client())->get($url, ['headers' => $escherSignedHeaders]);
    }

    protected function putWithEscher(string $uri, string $body = '')
    {
        $url = $this->serviceHost.$uri;
        $escherCredentials = json_decode(getenv('ESCHER_SUITE_KEY_DB'), true);
        $escherKey = array_keys($escherCredentials)[0];
        $escherProvider = new EscherProvider(getenv('ESCHER_SUITE_CREDENTIAL_SCOPE'), $escherKey, $escherCredentials[$escherKey], []);

        $escher = $escherProvider->createEscher();
        $escherSignedHeaders = $escher->signRequest(
            $escherProvider->getEscherKey(),
            $escherProvider->getEscherSecret(),
            'PUT',
            $url,
            ''
        );

        return (new Client())->put($url, ['headers' => $escherSignedHeaders, 'body' => $body]);
    }

    protected function requestWithJwt(string $method, string $uri, string $body = '', array $formParams = [])
    {
        $url = $this->serviceHost.$uri;
        $jwt = new Jwt(getenv('JWT_SECRET'));
        $token = $jwt->generateToken($body);
        $options = ['headers' => ['Authorization' => 'Bearer ' . $token], 'body' => $body];
        if (!empty($formParams)) {
            $options['form_params'] = $formParams;
        }
        return (new Client())->request($method, $url, $options);
    }

    protected function deleteWithJwt(string $uri, string $body = '')
    {
        return $this->requestWithJwt('DELETE', $uri, $body);
    }

    protected function getWithJwt(string $uri, string $body = '')
    {
        return $this->requestWithJwt('GET', $uri, $body);
    }

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
}
