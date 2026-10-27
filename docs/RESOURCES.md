# Resources

This page is the method-level reference for the public client surface.

## Services

- `services()->list(?DateTimeInterface $date = null)`
- `services()->destination(DestinationServicesRequest $request)`

Use `list()` for the service catalog and `destination()` when sender and recipient context should narrow the result.

## Locations

Detail lookups:

- `locations()->getCountry(int $id)`
- `locations()->getState(string $id)`
- `locations()->getSite(int $id)`
- `locations()->getStreet(int $id)`
- `locations()->getComplex(int $id)`
- `locations()->getPointOfInterest(int $id)`
- `locations()->getOffice(int $id)`

Search endpoints:

- `locations()->findCountries(CountrySearchRequest $request)`
- `locations()->findStates(StateSearchRequest $request)`
- `locations()->findSites(SiteSearchRequest $request)`
- `locations()->findStreets(StreetSearchRequest $request)`
- `locations()->findComplexes(ComplexSearchRequest $request)`
- `locations()->findBlocks(BlockSearchRequest $request)`
- `locations()->findPointsOfInterest(PointOfInterestSearchRequest $request)`
- `locations()->findOffices(OfficeSearchRequest $request)`
- `locations()->findNearestOffices(NearestOfficesRequest $request)`

Bulk CSV reference data:

- `locations()->allCountries(?int $ttlSeconds = null)`
- `locations()->allStates(int $countryId, ?int $ttlSeconds = null)`
- `locations()->allSites(int $countryId, ?int $ttlSeconds = null)`
- `locations()->allStreets(int $countryId, ?int $ttlSeconds = null)`
- `locations()->allComplexes(int $countryId, ?int $ttlSeconds = null)`
- `locations()->allBlocks(int $countryId, ?int $ttlSeconds = null)`
- `locations()->allPointsOfInterest(int $countryId, ?int $ttlSeconds = null)`
- `locations()->allPostcodes(int $countryId, ?int $ttlSeconds = null)`

DPD caps search results. Use the `all*()` methods when you need a complete dataset.

## Shipments

Lifecycle and updates:

- `shipments()->create(CreateShipmentRequest $request)`
- `shipments()->cancel(CancelShipmentRequest $request)`
- `shipments()->cancelWithDelete(CancelShipmentRequest $request)`
- `shipments()->addParcel(AddParcelRequest $request)`
- `shipments()->finalizePendingShipment(FinalizePendingShipmentRequest $request)`
- `shipments()->update(UpdateShipmentRequest $request)`
- `shipments()->updateProperties(UpdateShipmentPropertiesRequest $request)`

Lookup and search:

- `shipments()->info(ShipmentInformationRequest $request)`
- `shipments()->secondaryShipments(SecondaryShipmentsRequest $request)`
- `shipments()->findParcelsByReference(FindParcelsByReferenceRequest $request)`
- `shipments()->barcodeInformation(BarcodeInformationRequest|ShipmentParcelRef $request)`

Handover:

- `shipments()->handoverToCourier(HandoverToCourierRequest $request)`
- `shipments()->handoverToMidwayCarrier(HandoverToMidwayCarrierRequest $request)`

`updateProperties()` validates supported shipment property paths before sending the request. `cancelWithDelete()` exists for accounts or integrations that rely on the documented DELETE `/shipment` cancel flow.

## Printing

- `printing()->document(PrintRequest $request)`
- `printing()->extended(PrintRequest $request)`
- `printing()->labelInfo(LabelInfoRequest|array<ShipmentParcelRef> $request)`
- `printing()->voucher(PrintVoucherRequest|array<string> $request)`

Printing can return binary document content on success and JSON errors on failure. Stay on the resource methods unless you intentionally want to handle the low-level protocol yourself.

## Tracking

- `tracking()->track(TrackRequest|array<TrackShipmentParcelRef|ShipmentParcelRef> $request, ?bool $lastOperationOnly = null)`
- `tracking()->trackBatched(TrackRequest|array<TrackShipmentParcelRef|ShipmentParcelRef> $request, ?bool $lastOperationOnly = null, int $batchSize = 10)`
- `tracking()->bulkDataFiles(BulkTrackingDataFilesRequest|int $lastProcessedFileId)`

`track()` throws `InvalidArgumentException` above 10 parcels; use `trackBatched()` for longer lists (its batch size must stay between 1 and 10).

## Pickup

- `pickup()->order(PickupRequest $request)`
- `pickup()->terms(PickupTermsRequest $request)`

## Validation

- `validation()->address(ValidateAddressRequest|ShipmentAddressPayload $request)`
- `validation()->postCode(ValidatePostCodeRequest $request)`
- `validation()->phone(ValidatePhoneRequest|string $request, ?string $ext = null)`
- `validation()->shipment(ValidateShipmentRequest|CreateShipmentRequest $request)`

## Calculations, Clients, And Payments

- `calculations()->calculate(CalculationRequest $request)`
- `clients()->get(int $clientId)`
- `clients()->contractClients()`
- `clients()->createContact(CreateContactRequest $request)`
- `clients()->contactByExternalId(string $externalId, ?string $secretKey = null)`
- `clients()->ownClientId()`
- `clients()->contractInfo()`
- `payments()->payouts(PayoutRequest $request)`

## Low-Level Helpers

These helpers remain available on `Client` for endpoint work that has not been promoted into a dedicated resource yet:

- `sendJson()`
- `sendBinary()`
- `sendCsv()`
- `rememberReferenceData()`

## Cross-Resource Helpers

- `BatchingHelper::chunk()`
- `BatchingHelper::mapChunks()`
- tracking operation and exception code resolvers in `Support`
