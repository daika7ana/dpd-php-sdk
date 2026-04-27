<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Resources;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Locations\Geography\Block;
use Daika7ana\Dpd\DTOs\Locations\Geography\Complex;
use Daika7ana\Dpd\DTOs\Locations\Geography\Country;
use Daika7ana\Dpd\DTOs\Locations\Offices\Office;
use Daika7ana\Dpd\DTOs\Locations\Geography\PointOfInterest;
use Daika7ana\Dpd\DTOs\Locations\Geography\Postcode;
use Daika7ana\Dpd\DTOs\Locations\Responses\NearestOfficesResponse;
use Daika7ana\Dpd\DTOs\Locations\Geography\Site;
use Daika7ana\Dpd\DTOs\Locations\Geography\State;
use Daika7ana\Dpd\DTOs\Locations\Geography\Street;
use Daika7ana\Dpd\DTOs\Locations\Requests\BlockSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\ComplexSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\CountrySearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\NearestOfficesRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\OfficeSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\PointOfInterestSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\SiteSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\StateSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\StreetSearchRequest;
use Daika7ana\Dpd\Exceptions\UnsupportedResponseException;
use Daika7ana\Dpd\Http\ApiRequest;
use Daika7ana\Dpd\Support\CsvHelper;

final class LocationsResource
{
    public function __construct(
        private Client $client,
    ) {}

    public function getCountry(int $id): ?Country
    {
        return $this->fetchOptional('/location/country/{id}', 'country', [Country::class, 'fromArray'], ['id' => $id]);
    }

    /**
     * @return Collection<int, Country>
     */
    public function findCountries(CountrySearchRequest $request): Collection
    {
        return $this->fetchCollection('/location/country', $request->toArray(), 'countries', [Country::class, 'fromArray']);
    }

    /**
     * @return Collection<int, Country>
     */
    public function allCountries(?int $ttlSeconds = null): Collection
    {
        return $this->fetchCachedCsvCollection('location:countries:all', '/location/country/csv', [Country::class, 'fromCsvRow'], $ttlSeconds);
    }

    public function getState(string $id): ?State
    {
        return $this->fetchOptional('/location/state/{id}', 'state', [State::class, 'fromArray'], ['id' => $id]);
    }

    /**
     * @return Collection<int, State>
     */
    public function findStates(StateSearchRequest $request): Collection
    {
        return $this->fetchCollection('/location/state', $request->toArray(), 'states', [State::class, 'fromArray']);
    }

    /**
     * @return Collection<int, State>
     */
    public function allStates(int $countryId, ?int $ttlSeconds = null): Collection
    {
        return $this->fetchCachedCsvCollection(
            cacheKey: sprintf('location:states:%d:all', $countryId),
            path: '/location/state/csv/{countryId}',
            mapper: [State::class, 'fromCsvRow'],
            ttlSeconds: $ttlSeconds,
            pathParameters: ['countryId' => $countryId],
        );
    }

    public function getSite(int $id): ?Site
    {
        return $this->fetchOptional('/location/site/{id}', 'site', [Site::class, 'fromArray'], ['id' => $id]);
    }

    /**
     * @return Collection<int, Site>
     */
    public function findSites(SiteSearchRequest $request): Collection
    {
        return $this->fetchCollection('/location/site', $request->toArray(), 'sites', [Site::class, 'fromArray']);
    }

    /**
     * @return Collection<int, Site>
     */
    public function allSites(int $countryId, ?int $ttlSeconds = null): Collection
    {
        return $this->fetchCachedCsvCollection(
            cacheKey: sprintf('location:sites:%d:all', $countryId),
            path: '/location/site/csv/{countryId}',
            mapper: [Site::class, 'fromCsvRow'],
            ttlSeconds: $ttlSeconds,
            pathParameters: ['countryId' => $countryId],
        );
    }

    public function getStreet(int $id): ?Street
    {
        return $this->fetchOptional('/location/street/{id}', 'street', [Street::class, 'fromArray'], ['id' => $id]);
    }

    /**
     * @return Collection<int, Street>
     */
    public function findStreets(StreetSearchRequest $request): Collection
    {
        return $this->fetchCollection('/location/street', $request->toArray(), 'streets', [Street::class, 'fromArray']);
    }

    /**
     * @return Collection<int, Street>
     */
    public function allStreets(int $countryId, ?int $ttlSeconds = null): Collection
    {
        return $this->fetchCachedCsvCollection(
            cacheKey: sprintf('location:streets:%d:all', $countryId),
            path: '/location/street/csv/{countryId}',
            mapper: [Street::class, 'fromCsvRow'],
            ttlSeconds: $ttlSeconds,
            pathParameters: ['countryId' => $countryId],
        );
    }

    public function getComplex(int $id): ?Complex
    {
        return $this->fetchOptional('/location/complex/{id}', 'complex', [Complex::class, 'fromArray'], ['id' => $id]);
    }

    /**
     * @return Collection<int, Complex>
     */
    public function findComplexes(ComplexSearchRequest $request): Collection
    {
        return $this->fetchCollection('/location/complex', $request->toArray(), 'complexes', [Complex::class, 'fromArray']);
    }

    /**
     * @return Collection<int, Complex>
     */
    public function allComplexes(int $countryId, ?int $ttlSeconds = null): Collection
    {
        return $this->fetchCachedCsvCollection(
            cacheKey: sprintf('location:complexes:%d:all', $countryId),
            path: '/location/complex/csv/{countryId}',
            mapper: [Complex::class, 'fromCsvRow'],
            ttlSeconds: $ttlSeconds,
            pathParameters: ['countryId' => $countryId],
        );
    }

    /**
     * @return Collection<int, Block>
     */
    public function findBlocks(BlockSearchRequest $request): Collection
    {
        return $this->fetchCollection('/location/block', $request->toArray(), 'blocks', [Block::class, 'fromArray']);
    }

    /**
     * @return Collection<int, Block>
     */
    public function allBlocks(int $countryId, ?int $ttlSeconds = null): Collection
    {
        return $this->fetchCachedCsvCollection(
            cacheKey: sprintf('location:blocks:%d:all', $countryId),
            path: '/location/block/csv/{countryId}',
            mapper: [Block::class, 'fromCsvRow'],
            ttlSeconds: $ttlSeconds,
            pathParameters: ['countryId' => $countryId],
        );
    }

    public function getPointOfInterest(int $id): ?PointOfInterest
    {
        return $this->fetchOptional('/location/poi/{id}', 'poi', [PointOfInterest::class, 'fromArray'], ['id' => $id]);
    }

    /**
     * @return Collection<int, PointOfInterest>
     */
    public function findPointsOfInterest(PointOfInterestSearchRequest $request): Collection
    {
        return $this->fetchCollection('/location/poi', $request->toArray(), 'pois', [PointOfInterest::class, 'fromArray']);
    }

    /**
     * @return Collection<int, PointOfInterest>
     */
    public function allPointsOfInterest(int $countryId, ?int $ttlSeconds = null): Collection
    {
        return $this->fetchCachedCsvCollection(
            cacheKey: sprintf('location:pois:%d:all', $countryId),
            path: '/location/poi/csv/{countryId}',
            mapper: [PointOfInterest::class, 'fromCsvRow'],
            ttlSeconds: $ttlSeconds,
            pathParameters: ['countryId' => $countryId],
        );
    }

    /**
     * @return Collection<int, Postcode>
     */
    public function allPostcodes(int $countryId, ?int $ttlSeconds = null): Collection
    {
        return $this->fetchCachedCsvCollection(
            cacheKey: sprintf('location:postcodes:%d:all', $countryId),
            path: '/location/postcode/csv/{countryId}',
            mapper: [Postcode::class, 'fromCsvRow'],
            ttlSeconds: $ttlSeconds,
            pathParameters: ['countryId' => $countryId],
        );
    }

    public function getOffice(int $id): ?Office
    {
        return $this->fetchOptional('/location/office/{id}', 'office', [Office::class, 'fromArray'], ['id' => $id]);
    }

    /**
     * @return Collection<int, Office>
     */
    public function findOffices(OfficeSearchRequest $request): Collection
    {
        return $this->fetchCollection('/location/office', $request->toArray(), 'offices', [Office::class, 'fromArray']);
    }

    public function findNearestOffices(NearestOfficesRequest $request): NearestOfficesResponse
    {
        return NearestOfficesResponse::fromArray(
            $this->client->sendJson(ApiRequest::json('POST', '/location/office/nearest-offices', $request->toArray())),
        );
    }

    /**
     * @template TEntity
     *
     * @param callable(array<string, mixed>): TEntity $mapper
     * @param array<string, string|int> $pathParameters
     *
     * @return TEntity|null
     */
    private function fetchOptional(string $path, string $resultKey, callable $mapper, array $pathParameters = []): mixed
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', $path, [], $pathParameters));
        $entity = $data[$resultKey] ?? null;

        if ($entity === null) {
            return null;
        }

        if (!is_array($entity)) {
            throw new UnsupportedResponseException(
                message: sprintf('Expected a %s object in the DPD response.', $resultKey),
                responseDetails: ['payload' => $data],
            );
        }

        return $mapper($entity);
    }

    /**
     * @template TEntity
     *
     * @param array<string, mixed> $payload
     * @param callable(array<string, mixed>): TEntity $mapper
     *
     * @return Collection<int, TEntity>
     */
    private function fetchCollection(string $path, array $payload, string $resultKey, callable $mapper): Collection
    {
        $data = $this->client->sendJson(ApiRequest::json('POST', $path, $payload));
        $entities = $data[$resultKey] ?? null;

        if (!is_array($entities)) {
            throw new UnsupportedResponseException(
                message: sprintf('Expected a %s array in the DPD response.', $resultKey),
                responseDetails: ['payload' => $data],
            );
        }

        $items = [];

        foreach ($entities as $entity) {
            if (!is_array($entity)) {
                continue;
            }

            $items[] = $mapper($entity);
        }

        return new Collection($items);
    }

    /**
     * @template TEntity
     *
     * @param callable(array<string, string>): TEntity $mapper
     * @param array<string, string|int> $pathParameters
     *
     * @return Collection<int, TEntity>
     */
    private function fetchCachedCsvCollection(
        string $cacheKey,
        string $path,
        callable $mapper,
        ?int $ttlSeconds = null,
        array $pathParameters = [],
    ): Collection {
        /** @var Collection<int, TEntity> */
        return $this->client->rememberReferenceData(
            $cacheKey,
            function () use ($path, $mapper, $pathParameters): Collection {
                $csv = $this->client->sendCsv(ApiRequest::csv('POST', $path, [], $pathParameters));
                $rows = CsvHelper::readAssociativeRows($csv->asString());
                $items = [];

                foreach ($rows as $row) {
                    $items[] = $mapper($row);
                }

                return new Collection($items);
            },
            $ttlSeconds,
        );
    }
}
