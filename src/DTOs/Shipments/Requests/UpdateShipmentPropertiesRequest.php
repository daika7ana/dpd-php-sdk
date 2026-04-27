<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

use InvalidArgumentException;

final readonly class UpdateShipmentPropertiesRequest
{
    /**
     * @var array<string, array<string, mixed>|bool>
     */
    private const PROPERTY_TREE = [
        'id' => true,
        'sender' => [
            'clientId' => true,
            'phone1' => ['number' => true, 'extension' => true],
            'phone2' => ['number' => true, 'extension' => true],
            'phone3' => ['number' => true, 'extension' => true],
            'clientName' => true,
            'contactName' => true,
            'objectName' => true,
            'email' => true,
            'privatePerson' => true,
            'address' => [
                'countryId' => true,
                'stateId' => true,
                'siteId' => true,
                'siteType' => true,
                'siteName' => true,
                'postCode' => true,
                'x' => true,
                'y' => true,
                'streetId' => true,
                'streetType' => true,
                'streetName' => true,
                'streetNo' => true,
                'complexId' => true,
                'complexType' => true,
                'complexName' => true,
                'blockNo' => true,
                'entranceNo' => true,
                'floorNo' => true,
                'apartmentNo' => true,
                'poiId' => true,
                'addressNote' => true,
                'addressLine1' => true,
                'addressLine2' => true,
            ],
            'dropoffOfficeId' => true,
            'dropoffGeoPUDOId' => true,
            'dropoff' => true,
        ],
        'recipient' => [
            'clientId' => true,
            'phone1' => ['number' => true, 'extension' => true],
            'phone2' => ['number' => true, 'extension' => true],
            'phone3' => ['number' => true, 'extension' => true],
            'clientName' => true,
            'objectName' => true,
            'contactName' => true,
            'email' => true,
            'privatePerson' => true,
            'address' => [
                'countryId' => true,
                'stateId' => true,
                'siteId' => true,
                'siteType' => true,
                'siteName' => true,
                'postCode' => true,
                'x' => true,
                'y' => true,
                'streetId' => true,
                'streetType' => true,
                'streetName' => true,
                'streetNo' => true,
                'complexId' => true,
                'complexType' => true,
                'complexName' => true,
                'blockNo' => true,
                'entranceNo' => true,
                'floorNo' => true,
                'apartmentNo' => true,
                'poiId' => true,
                'addressNote' => true,
                'addressLine1' => true,
                'addressLine2' => true,
            ],
            'pickupOfficeId' => true,
            'pickupGeoPUDOId' => true,
            'autoSelectNearestOffice' => true,
            'autoSelectNearestOfficePolicy' => [
                'unavailableNearestOfficeAction' => true,
                'officeType' => true,
            ],
        ],
        'service' => [
            'pickupDate' => true,
            'serviceId' => true,
            'additionalServices' => [
                'cod' => [
                    'amount' => true,
                    'currencyCode' => true,
                    'processingType' => true,
                    'payoutToThirdParty' => true,
                    'payoutToLoggedClient' => true,
                    'includeShippingPrice' => true,
                    'cardPaymentForbidden' => true,
                    'fiscalReceiptItems' => [
                        '*' => [
                            'description' => true,
                            'vatGroup' => true,
                            'amount' => true,
                            'amountWithVat' => true,
                        ],
                    ],
                ],
                'obpd' => [
                    'option' => true,
                    'returnShipmentServiceId' => true,
                    'returnShipmentPayer' => true,
                ],
                'declaredValue' => [
                    'amount' => true,
                    'fragile' => true,
                    'ignoreIfNotApplicable' => true,
                ],
                'fixedTimeDelivery' => true,
                'returns' => [
                    'rod' => [
                        'enabled' => true,
                        'comment' => true,
                        'returnToClientId' => true,
                        'returnToOfficeId' => true,
                        'thirdPartyPayer' => true,
                    ],
                    'returnReceipt' => [
                        'enabled' => true,
                        'returnToClientId' => true,
                        'returnToOfficeId' => true,
                        'thirdPartyPayer' => true,
                    ],
                    'electronicReturnReceipt' => [
                        'recipientEmails' => ['*' => true],
                        'thirdPartyPayer' => true,
                    ],
                    'swap' => [
                        'serviceId' => true,
                        'parcelsCount' => true,
                        'declaredValue' => true,
                        'fragile' => true,
                        'returnToOfficeId' => true,
                        'thirdPartyPayer' => true,
                    ],
                    'rop' => [
                        'pallets' => [
                            '*' => [
                                'serviceId' => true,
                                'parcelsCount' => true,
                            ],
                        ],
                        'thirdPartyPayer' => true,
                    ],
                    'returnVoucher' => [
                        'serviceId' => true,
                        'payer' => true,
                        'validityPeriod' => true,
                    ],
                    'sendBackClientId' => true,
                ],
                'specialDeliveryId' => true,
                'deliveryToFloor' => true,
            ],
            'deferredDays' => true,
            'saturdayDelivery' => true,
            'autoAdjustPickupDate' => true,
            'deliveryLimitViolationAutoAdjustment' => true,
        ],
        'content' => [
            'parcelsCount' => true,
            'totalWeight' => true,
            'contents' => true,
            'package' => true,
            'documents' => true,
            'palletized' => true,
            'parcels' => [
                '*' => [
                    'id' => true,
                    'seqNo' => true,
                    'packageUniqueNumber' => true,
                    'size' => [
                        'width' => true,
                        'depth' => true,
                        'height' => true,
                    ],
                    'weight' => true,
                    'ref1' => true,
                    'ref2' => true,
                    'pickupExternalCarrierParcelNumber' => [
                        'externalCarrier' => true,
                        'parcelNumber' => true,
                    ],
                    'deliveryExternalCarrierParcelNumber' => [
                        'externalCarrier' => true,
                        'parcelNumber' => true,
                    ],
                    'goods' => [
                        '*' => [
                            'description' => true,
                            'hsCode' => true,
                            'quantity' => true,
                            'valuePerItem' => true,
                            'weightPerItem' => true,
                            'originCountryCode' => true,
                        ],
                    ],
                    'printAdditionalBarcode' => [
                        'value' => true,
                        'label' => true,
                        'format' => true,
                        'valueType' => true,
                    ],
                ],
            ],
            'pendingParcels' => true,
            'pendingShipment' => true,
            'exciseGoods' => true,
            'lq' => true,
            'goodsValue' => true,
            'goodsValueCurrencyCode' => true,
            'uitCode' => true,
            'printAdditionalBarcode' => [
                'value' => true,
                'label' => true,
                'format' => true,
                'valueType' => true,
            ],
        ],
        'payment' => [
            'courierServicePayer' => true,
            'declaredValuePayer' => true,
            'packagePayer' => true,
            'thirdPartyClientId' => true,
            'discountCardId' => [
                'contractId' => true,
                'cardId' => true,
            ],
            'senderBankAccount' => [
                'iban' => true,
                'accountHolder' => true,
            ],
            'administrativeFee' => true,
        ],
        'pendingShipment' => true,
        'shipmentNote' => true,
        'ref1' => true,
        'ref2' => true,
        'consolidationRef' => true,
        'requireUnsuccessfulDeliveryStickerImage' => true,
    ];

    public string $id;

    /**
     * @var array<string, string>
     */
    public array $properties;

    /**
     * @param array<string, string> $properties
     */
    public function __construct(
        string $id,
        array $properties,
    ) {
        $normalizedProperties = [];

        foreach ($properties as $property => $value) {
            $normalizedProperty = self::normalizePropertyPath($property);

            if (!self::isSupportedPropertyPath($normalizedProperty)) {
                throw new InvalidArgumentException(sprintf('Unsupported shipment property path "%s".', $property));
            }

            $normalizedProperties[$normalizedProperty] = $value;
        }

        $this->id = $id;
        $this->properties = $normalizedProperties;
    }

    /**
     * @return array{id: string, properties: array<string, string>}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'properties' => $this->properties,
        ];
    }

    private static function normalizePropertyPath(string $property): string
    {
        $normalizedProperty = trim($property);
        $normalizedProperty = preg_replace('/\[(\d+)\]/', '.$1', $normalizedProperty) ?? $normalizedProperty;

        return trim($normalizedProperty, '.');
    }

    private static function isSupportedPropertyPath(string $property): bool
    {
        if ($property === '' || preg_match('/(^\.|\.\.|\.$)/', $property) === 1) {
            return false;
        }

        $segments = explode('.', $property);

        return self::matchesTree($segments, self::PROPERTY_TREE);
    }

    /**
     * @param list<string> $segments
     * @param array<string, array<string, mixed>|bool> $tree
     */
    private static function matchesTree(array $segments, array $tree): bool
    {
        if ($segments === []) {
            return false;
        }

        $segment = array_shift($segments);
        if ($segment === null) {
            return false;
        }

        if (ctype_digit($segment)) {
            if (!isset($tree['*'])) {
                return false;
            }

            $branch = $tree['*'];

            return $segments === []
                ? $branch === true
                : is_array($branch) && self::matchesTree($segments, $branch);
        }

        if (!preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $segment)) {
            return false;
        }

        if (!array_key_exists($segment, $tree)) {
            return false;
        }

        $branch = $tree[$segment];

        if ($segments === []) {
            return true;
        }

        return is_array($branch) && self::matchesTree($segments, $branch);
    }
}
