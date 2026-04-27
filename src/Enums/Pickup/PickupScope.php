<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Pickup;

enum PickupScope: string
{
    case ExplicitShipmentIdList = 'EXPLICIT_SHIPMENT_ID_LIST';
    case AllCreatedByLoggedUser = 'ALL_CREATED_BY_LOGGED_USER';
    case AllCreatedBySameClient = 'ALL_CREATED_BY_SAME_CLIENT';
    case AllCreatedBySameContractUsers = 'ALL_CREATED_BY_SAME_CONTRACT_USERS';
}
