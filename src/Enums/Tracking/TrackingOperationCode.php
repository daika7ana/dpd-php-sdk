<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Enums\Tracking;

enum TrackingOperationCode: int
{
    case ArrivalScan = 1;
    case DepartureScan = 2;
    case ReceivedInOffice = 11;
    case OutForDelivery = 12;
    case Delivered = -14;
    case ProcessedInOffice = 21;
    case ReturnedToOffice = 38;
    case CourierPickup = 39;
    case UnsuccessfulDelivery = 44;
    case DeferredDelivery = 69;
    case ReturnToSender = 111;
    case ProcessedToInsuranceDepartment = 112;
    case ProcessedForDestruction = 114;
    case Redirected = 115;
    case Forwarded = 116;
    case StoppedBySender = 121;
    case RefusedByRecipient = 123;
    case DeliveredBackToSender = 124;
    case Destroyed = 125;
    case TheftOrBurglary = 127;
    case Canceled = 128;
    case AdministrativeClosure = 129;
    case PreparedForSelfCollectingConsignee = 134;
    case ClarifyShipmentDelivery = 136;
    case HandoverForContentsCheckTest = 144;
    case ShipmentDataReceived = 148;
    case UnsuccessfulShipmentPickup = 164;
    case DeliveryCapacityReached = 169;
    case Predict = 175;
    case ExportToForeignProvider = 176;
    case UnexpectedDelay = 181;
    case PostponedDeliveryDueToIncompleteAddress = 190;
    case RefuseContentsCheckTest = 195;
    case HandoverToMidwayCarrier = 217;
    case NotificationSentForParcelInOfficeLocker = 1134;

    public function description(): string
    {
        return match ($this) {
            self::ArrivalScan => 'Arrival Scan',
            self::DepartureScan => 'Departure Scan',
            self::ReceivedInOffice => 'Received in Office',
            self::OutForDelivery => 'Out for Delivery',
            self::Delivered => 'Delivered',
            self::ProcessedInOffice => 'Processed in Office',
            self::ReturnedToOffice => 'Returned to Office',
            self::CourierPickup => 'Courier Pick-up',
            self::UnsuccessfulDelivery => 'Unsuccessful Delivery',
            self::DeferredDelivery => 'Deferred delivery',
            self::ReturnToSender => 'Return to Sender',
            self::ProcessedToInsuranceDepartment => 'Processed in the Insurance Department',
            self::ProcessedForDestruction => 'Processed for Destruction',
            self::Redirected => 'Redirected',
            self::Forwarded => 'Forwarded',
            self::StoppedBySender => 'Stopped by Sender',
            self::RefusedByRecipient => 'Refused by recipient',
            self::DeliveredBackToSender => 'Delivered Back to Sender',
            self::Destroyed => 'Destroyed',
            self::TheftOrBurglary => 'Theft/Burglary',
            self::Canceled => 'Canceled',
            self::AdministrativeClosure => 'Administrative Closure',
            self::PreparedForSelfCollectingConsignee => 'Prepared for Self-collecting Consignee',
            self::ClarifyShipmentDelivery => 'Clarify shipment delivery',
            self::HandoverForContentsCheckTest => 'Handover for contents check/test',
            self::ShipmentDataReceived => 'Shipment data received',
            self::UnsuccessfulShipmentPickup => 'Unsuccessful shipment pickup',
            self::DeliveryCapacityReached => 'Delivery capacity reached',
            self::Predict => 'Predict',
            self::ExportToForeignProvider => 'Export to foreign provider',
            self::UnexpectedDelay => 'Unexpected delay',
            self::PostponedDeliveryDueToIncompleteAddress => 'Postponed delivery due to inaccurate/incomplete address',
            self::RefuseContentsCheckTest => 'Refuse contents check/test',
            self::HandoverToMidwayCarrier => 'Handover to midway carrier',
            self::NotificationSentForParcelInOfficeLocker => 'Notification sent for parcel in office/locker',
        };
    }
}
