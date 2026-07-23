<?php
declare(strict_types=1);

namespace wen202402\chain\chain;



use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use IEXBase\TronAPI\Provider\HttpProviderInterface;
use Psr\Http\Message\StreamInterface;
use IEXBase\TronAPI\Exception\NotFoundException;
use IEXBase\TronAPI\Exception\TronException;
use IEXBase\TronAPI\Support\Utils;

class TorHttpProvider implements HttpProviderInterface{
    /** @var ClientInterface */
    protected $httpClient;

    /** @var string */
    protected $host;

    /** @var int */
    protected $timeout = 80000;

    /** @var array */
    protected $headers = [];

    /** @var string */
    protected $statusPage = '/';

    /**
     * Create an HttpProvider object
     *
     * @param string $host
     * @param int $timeout
     * @param mixed $user
     * @param mixed $password
     * @param array $headers
     * @param string $statusPage
     * @param ?string $proxy  例如：socks5h://127.0.0.1:9050 或 http://127.0.0.1:8118
     * @throws TronException
     */
    public function __construct(string $host,int $timeout = 80000, $user = false, $password = false, array $headers = [], string $statusPage = '/', ?string $proxy = "socks5h://127.0.0.1:9050") {
        if (!Utils::isValidUrl($host)) throw new TronException('Invalid URL provided to HttpProvider');


        if (!is_array($headers)) throw new TronException('Invalid headers array provided');
       ;

        $this->statusPage = $statusPage;
        $this->headers = $headers;
        $clientConfig = ['base_uri' =>  $this->host = $host, 'timeout'  => $this->timeout = $timeout,];
        if ($user !== false) $clientConfig['auth'] = [$user, $password];
        if (!empty($proxy)) $clientConfig['proxy'] = $proxy;

        $this->httpClient = new Client($clientConfig);
    }


    public function setStatusPage(string $page = '/'): void{
        $this->statusPage = $page;
    }


    public function isConnected(): bool{
        $response = $this->request($this->statusPage);

        if (array_key_exists('blockID', $response)) return true;
         elseif (array_key_exists('status', $response)) return true;

        return false;
    }


    public function getHost(): string{
        return $this->host;
    }


    public function getTimeout(): int{
        return $this->timeout;
    }

    /**
     * We send requests to the server
     *
     * @param string $url
     * @param array $payload
     * @param string $method
     * @return array|mixed
     * @throws TronException
     */
    public function request($url, array $payload = [], string $method = 'get'): array{


        if (!in_array($method = strtoupper($method), ['GET', 'POST'], true)) throw new TronException('The method is not defined');


        $options = ['headers' => $this->headers, 'body'    => json_encode($payload),];

        $rawResponse = $this->httpClient->send($request = new Request($method, $url, $options['headers'], $options['body']), $options);

        return $this->decodeBody(
            $rawResponse->getBody(),
            $rawResponse->getStatusCode()
        );
    }

    /**
     * Convert the original answer to an array
     */
    protected function decodeBody(StreamInterface $stream, int $status): array{
        $decodedBody = json_decode($stream->getContents(), true);

        if ((string)$stream === 'OK') $decodedBody = ['status' => 1,];
         elseif ($decodedBody === null || !is_array($decodedBody)) $decodedBody = [];


        if ($status === 404) throw new NotFoundException('Page not found');


        return $decodedBody;
    }
}
