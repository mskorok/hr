<?php

namespace App\Constants;

/**
 * Class Message
 * @package App\Constants
 */
class Message
{
    public const THANK_YOU = 'thankYou';
    public const FIRST_BOOKING = 'firstBooking';
    public const ON_PAYMENT = 'onPayment';
    public const VOUCHERS = 'vouchers';
    public const VOUCHER = 'voucher';
    public const VOUCHER_PARTNER = 'voucherPartner';
    public const BEFORE_START_DATES = 'beforeStartDates';
    public const BOOKING_INFO = 'bookingInfoToActivityProvider';
    public const SENT_INVOICE = 'sentInvoice';
    public const ON_COMMENT = 'onComment';
    public const ON_REVIEW = 'onReview';
    public const BEFORE_START = 'beforeStart';
    public const AFTER_END = 'afterActivityEnd';
    public const MONTH_INVOICES = 'invoicingMonthlyToActivityProviders';
    public const MARKET_PLACE_INVOICES = 'invoicesToMarketPlaces';
    public const TOUR_OPERATOR_INVOICES = 'invoicesToTourOperators';
    public const AUTO_RESPONDER = 'autoResponder';
    public const BLOG_ON_COMMENT = 'blogOnComment';
    public const PENDING_PAYMENT = 'pendingPayment';
    public const WP_USER_CREATED = 'wpUserCreated';
    public const NOTIFICATION = 'notification';

    public const REMIND_BEFORE = [15, 10, 5, 1];
    public const REMIND_AFTER = [5, 1];
    public const REMIND_START = [5, 10];

    public const STATUS_SENT = 'sent';
    public const STATUS_READ = 'read';
    public const SUPPORT_STATUS_NOT_SUPPORT = 'not support';
    public const SUPPORT_STATUS_OPEN = 'open';
    public const SUPPORT_STATUS_PROGRESS = 'progress';
    public const SUPPORT_STATUS_CLOSED = 'closed';

    public const ALL_MESSAGES = [
        self::THANK_YOU,
        self::FIRST_BOOKING,
        self::ON_PAYMENT,
        self::VOUCHER,
        self::BEFORE_START_DATES,
        self::BOOKING_INFO,
        self::SENT_INVOICE,
        self::ON_COMMENT,
        self::ON_REVIEW,
        self::BEFORE_START,
        self::AFTER_END,
        self::MONTH_INVOICES,
        self::MARKET_PLACE_INVOICES,
        self::TOUR_OPERATOR_INVOICES,
        self::AUTO_RESPONDER,
        self::BLOG_ON_COMMENT,
        self::PENDING_PAYMENT,
        self::WP_USER_CREATED,
        self::NOTIFICATION

    ];

    public const REMIND = [
        self::REMIND_START,
        self::REMIND_BEFORE,
        self::REMIND_AFTER
    ];

    public const STATUS = [
        self::STATUS_READ,
        self::STATUS_SENT,
        self::SUPPORT_STATUS_OPEN,
        self::SUPPORT_STATUS_PROGRESS,
        self::SUPPORT_STATUS_CLOSED,
        self::SUPPORT_STATUS_NOT_SUPPORT
    ];
}
