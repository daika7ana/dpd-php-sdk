<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Resources;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Printing\Responses\ExtendedPrintResponse;
use Daika7ana\Dpd\DTOs\Printing\Responses\LabelInfoResponse;
use Daika7ana\Dpd\DTOs\Printing\Documents\PrintDocument;
use Daika7ana\Dpd\DTOs\Printing\Requests\LabelInfoRequest;
use Daika7ana\Dpd\DTOs\Printing\Requests\PrintRequest;
use Daika7ana\Dpd\DTOs\Printing\Requests\PrintVoucherRequest;
use Daika7ana\Dpd\Http\ApiRequest;

final readonly class PrintingResource
{
    public function __construct(
        private Client $client,
    ) {}

    public function document(PrintRequest $request): PrintDocument
    {
        return PrintDocument::fromBinary(
            $this->client->sendBinary(ApiRequest::json('POST', '/print', $request->toArray())),
        );
    }

    public function extended(PrintRequest $request): ExtendedPrintResponse
    {
        return ExtendedPrintResponse::fromArray(
            $this->client->sendJson(ApiRequest::json('POST', '/print/extended', $request->toArray())),
        );
    }

    /**
     * @param list<ShipmentParcelRef>|LabelInfoRequest $request
     */
    public function labelInfo(LabelInfoRequest|array $request): LabelInfoResponse
    {
        $resolvedRequest = $request instanceof LabelInfoRequest
            ? $request
            : new LabelInfoRequest($request);

        return LabelInfoResponse::fromArray(
            $this->client->sendJson(ApiRequest::json('POST', '/print/labelInfo', $resolvedRequest->toArray())),
        );
    }

    /**
     * @param list<string>|PrintVoucherRequest $request
     */
    public function voucher(PrintVoucherRequest|array $request): PrintDocument
    {
        $resolvedRequest = $request instanceof PrintVoucherRequest
            ? $request
            : new PrintVoucherRequest($request);

        return PrintDocument::fromBinary(
            $this->client->sendBinary(ApiRequest::json('POST', '/print/voucher', $resolvedRequest->toArray())),
        );
    }
}
