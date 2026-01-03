<?php

namespace App\Enum;

/**
 * OrderStatusEnum - A string-backed PHP Enum
 * 
 * EXECUTION FLOW:
 * 1. When a URL like "/orders/list/paid" is requested, Symfony extracts "paid" as the {status} parameter
 * 2. Symfony's ParamConverter examines the method parameter type hint (OrderStatusEnum $status)
 * 3. The ParamConverter looks for a case in this enum where the VALUE matches "paid"
 * 4. It finds: case Paid = 'paid' - this is the match!
 * 5. The enum instance OrderStatusEnum::Paid is injected into the method
 * 6. Inside the method, we can access:
 *    - $status->name    = "Paid" (the case name)
 *    - $status->value   = "paid" (the string value in the URL)
 * 7. The method executes and returns the Response
 * 
 * If the URL parameter doesn't match any case value (e.g., "/orders/list/invalid"),
 * Symfony will throw a 404 NotFound error.
 */
enum OrderStatusEnum: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}
