<?php

namespace App\Controller;

use App\Enum\OrderStatusEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * OrderController - Demonstrating Enum Parameter Conversion
 * 
 * COMPLETE EXECUTION FLOW (from browser to response):
 * 
 * 1. USER MAKES REQUEST
 *    Browser sends: GET /orders/list/paid
 * 
 * 2. SYMFONY ROUTING SYSTEM
 *    - Matches the URL against the route pattern: '/orders/list/{status}'
 *    - Extracts the parameter: status = "paid" (as a string)
 *    - Routes to OrderController->list() method
 * 
 * 3. PARAM CONVERTER (Symfony's magic)
 *    - Inspects the method signature: public function list(OrderStatusEnum $status = ...)
 *    - Sees the type hint is OrderStatusEnum (an enum)
 *    - Attempts to convert the string "paid" to an enum instance
 * 
 * 4. ENUM MATCHING (in OrderStatusEnum.php)
 *    - Symfony iterates through all cases in OrderStatusEnum
 *    - For each case, it compares: case->value == "paid"
 *    - MATCH FOUND: case Paid = 'paid'
 *    - Creates an instance: OrderStatusEnum::Paid
 * 
 * 5. DEPENDENCY INJECTION
 *    - The OrderStatusEnum::Paid instance is injected as $status parameter
 *    - Default value (OrderStatusEnum::Paid) is NOT used because we provided a URL value
 * 
 * 6. METHOD EXECUTION
 *    - list($status) now has $status = OrderStatusEnum::Paid
 *    - $status->name = "Paid"
 *    - $status->value = "paid"
 *    - Method creates and returns Response
 * 
 * 7. BROWSER RECEIVES RESPONSE
 *    Browser displays: "Orders with status: Paid (value: paid)"
 * 
 * If URL was: GET /orders/list/unknown
 *    - Symfony would NOT find a matching case
 *    - Throws 404 NotFound error
 * 
 * If URL was: GET /orders/list/ (no parameter)
 *    - Default value OrderStatusEnum::Paid is used
 *    - Method executes with $status = OrderStatusEnum::Paid
 */
class OrderController extends AbstractController
{
    #[Route('/orders/list/{status}', name: 'list_orders_by_status')]
    public function list(OrderStatusEnum $status = OrderStatusEnum::Paid): Response
    {
        // At this point, Symfony has already converted the URL string parameter
        // into an actual enum instance through the ParamConverter system
        return new Response(sprintf(
            'Orders with status: %s (value: %s)',
            $status->name,   // "Paid" - the case name
            $status->value   // "paid" - the string value from URL
        ));
    }

    #[Route('/orders', name: 'orders_index')]
    public function index(): Response
    {
        // Get all enum cases
        $statuses = OrderStatusEnum::cases();
        
        // Extract just the values for display
        $statusList = array_map(fn($status) => $status->value, $statuses);
        
        return new Response(sprintf(
            'Available order statuses: %s',
            implode(', ', $statusList)
        ));
    }
}
