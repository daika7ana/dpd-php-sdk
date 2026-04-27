<?php

declare(strict_types=1);

namespace Daika7ana\Dpd;

use Daika7ana\Dpd\Auth\Credentials;
use Daika7ana\Dpd\Auth\CredentialsProviderInterface;
use Daika7ana\Dpd\Auth\InMemoryCredentialsProvider;
use Daika7ana\Dpd\Auth\MutableCredentialsProviderInterface;
use Daika7ana\Dpd\Cache\NullReferenceDataCache;
use Daika7ana\Dpd\Cache\ReferenceDataCacheInterface;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\DTOs\Common\Documents\BinaryContent;
use Daika7ana\Dpd\Http\ApiRequest;
use Daika7ana\Dpd\Http\GuzzleHttpClient;
use Daika7ana\Dpd\Http\HttpClientInterface;
use Daika7ana\Dpd\Resources\CalculationsResource;
use Daika7ana\Dpd\Resources\ClientsResource;
use Daika7ana\Dpd\Resources\LocationsResource;
use Daika7ana\Dpd\Resources\PaymentsResource;
use Daika7ana\Dpd\Resources\PickupResource;
use Daika7ana\Dpd\Resources\PrintingResource;
use Daika7ana\Dpd\Resources\ServicesResource;
use Daika7ana\Dpd\Resources\ShipmentsResource;
use Daika7ana\Dpd\Resources\TrackingResource;
use Daika7ana\Dpd\Resources\ValidationResource;
use Daika7ana\Dpd\Support\ApiResponseMapper;
use Daika7ana\Dpd\Support\JsonHelper;
use GuzzleHttp\Psr7\HttpFactory;
use LogicException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class Client
{
    private ClientConfig $config;

    private CredentialsProviderInterface $credentialsProvider;

    private ReferenceDataCacheInterface $referenceDataCache;

    private ?ServicesResource $servicesResource = null;

    private ?ShipmentsResource $shipmentsResource = null;

    private ?LocationsResource $locationsResource = null;

    private ?PrintingResource $printingResource = null;

    private ?TrackingResource $trackingResource = null;

    private ?PickupResource $pickupResource = null;

    private ?ValidationResource $validationResource = null;

    private ?CalculationsResource $calculationsResource = null;

    private ?ClientsResource $clientsResource = null;

    private ?PaymentsResource $paymentsResource = null;

    public function __construct(
        private HttpClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        ?ClientConfig $config = null,
        ?CredentialsProviderInterface $credentialsProvider = null,
        ?ReferenceDataCacheInterface $referenceDataCache = null,
    ) {
        $this->config = $config ?? ClientConfig::fromEnvironment();
        $this->credentialsProvider = $credentialsProvider ?? InMemoryCredentialsProvider::fromConfig($this->config);
        $this->referenceDataCache = $referenceDataCache ?? new NullReferenceDataCache();
    }

    public static function create(
        ?HttpClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?ClientConfig $config = null,
        ?CredentialsProviderInterface $credentialsProvider = null,
        ?ReferenceDataCacheInterface $referenceDataCache = null,
    ): self {
        $config ??= ClientConfig::fromEnvironment();

        $httpClient ??= new GuzzleHttpClient(
            timeout: $config->timeout,
            connectTimeout: $config->connectTimeout,
        );

        if ($requestFactory === null || $streamFactory === null) {
            $psr17Factory = new HttpFactory();
            $requestFactory ??= $psr17Factory;
            $streamFactory ??= $psr17Factory;
        }

        return new self(
            $httpClient,
            $requestFactory,
            $streamFactory,
            $config,
            $credentialsProvider,
            $referenceDataCache,
        );
    }

    public function getConfig(): ClientConfig
    {
        return $this->config;
    }

    public function getCredentials(): Credentials
    {
        return $this->credentialsProvider->getCredentials();
    }

    public function credentialsProvider(): CredentialsProviderInterface
    {
        return $this->credentialsProvider;
    }

    public function setCredentials(Credentials $credentials): self
    {
        if (!$this->credentialsProvider instanceof MutableCredentialsProviderInterface) {
            throw new LogicException('The configured credentials provider is not mutable.');
        }

        $this->credentialsProvider->setCredentials($credentials);

        return $this;
    }

    public function referenceDataCache(): ReferenceDataCacheInterface
    {
        return $this->referenceDataCache;
    }

    /**
     * @template TResolved
     *
     * @param callable(): TResolved $resolver
     *
     * @return TResolved
     */
    public function rememberReferenceData(string $key, callable $resolver, ?int $ttlSeconds = null): mixed
    {
        return $this->referenceDataCache->remember($key, $resolver, $ttlSeconds);
    }

    public function createRequest(ApiRequest $request): RequestInterface
    {
        $uri = $this->resolveUri($request->path, $request->pathParameters);
        $payload = $this->withCredentials($request->payload);

        $psrRequest = $this->requestFactory
            ->createRequest($request->method, $uri)
            ->withHeader('Accept', $request->accept)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');

        return $psrRequest->withBody(
            $this->streamFactory->createStream(JsonHelper::encode($payload)),
        );
    }

    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->httpClient->sendRequest($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function sendJson(ApiRequest $request): array
    {
        return ApiResponseMapper::decodeJson($this->send($this->createRequest($request)));
    }

    /**
     * @param list<string> $acceptedContentTypes
     */
    public function sendBinary(ApiRequest $request, array $acceptedContentTypes = ['application/pdf', 'text/plain']): BinaryContent
    {
        return ApiResponseMapper::extractBinary(
            $this->send($this->createRequest($request)),
            $acceptedContentTypes,
        );
    }

    public function sendCsv(ApiRequest $request): BinaryContent
    {
        return ApiResponseMapper::extractCsv($this->send($this->createRequest($request)));
    }

    public function services(): ServicesResource
    {
        $this->servicesResource ??= new ServicesResource($this);

        return $this->servicesResource;
    }

    public function shipments(): ShipmentsResource
    {
        $this->shipmentsResource ??= new ShipmentsResource($this);

        return $this->shipmentsResource;
    }

    public function locations(): LocationsResource
    {
        $this->locationsResource ??= new LocationsResource($this);

        return $this->locationsResource;
    }

    public function printing(): PrintingResource
    {
        $this->printingResource ??= new PrintingResource($this);

        return $this->printingResource;
    }

    public function tracking(): TrackingResource
    {
        $this->trackingResource ??= new TrackingResource($this);

        return $this->trackingResource;
    }

    public function pickup(): PickupResource
    {
        $this->pickupResource ??= new PickupResource($this);

        return $this->pickupResource;
    }

    public function validation(): ValidationResource
    {
        $this->validationResource ??= new ValidationResource($this);

        return $this->validationResource;
    }

    public function calculations(): CalculationsResource
    {
        $this->calculationsResource ??= new CalculationsResource($this);

        return $this->calculationsResource;
    }

    public function clients(): ClientsResource
    {
        $this->clientsResource ??= new ClientsResource($this);

        return $this->clientsResource;
    }

    public function payments(): PaymentsResource
    {
        $this->paymentsResource ??= new PaymentsResource($this);

        return $this->paymentsResource;
    }

    /**
     * @param array<string, string|int> $pathParameters
     */
    private function resolveUri(string $path, array $pathParameters): string
    {
        $resolvedPath = $path;

        foreach ($pathParameters as $key => $value) {
            $resolvedPath = str_replace('{' . $key . '}', rawurlencode((string) $value), $resolvedPath);
        }

        return $this->config->baseUrl . '/' . ltrim($resolvedPath, '/');
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function withCredentials(array $payload): array
    {
        $credentials = $this->credentialsProvider->getCredentials();
        $resolvedPayload = [
            'userName' => $credentials->username,
            'password' => $credentials->password,
            'language' => $credentials->language,
            ...$payload,
        ];

        if ($credentials->clientSystemId !== null) {
            $resolvedPayload['clientSystemId'] = $credentials->clientSystemId;
        }

        return $resolvedPayload;
    }
}
