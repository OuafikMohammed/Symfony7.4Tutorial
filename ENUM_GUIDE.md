# Symfony Enum Guide - Minimal Code Setup

## What is an Enum?

An **Enum** (Enumeration) is a PHP data type that represents a fixed set of named constant values. In Symfony, enums can be automatically converted from URL parameters to type-safe enum instances through the **ParamConverter** system.

**Benefits:**
- Type safety (no invalid values)
- Cleaner code (no magic strings)
- Automatic validation (404 if invalid)
- IDE autocomplete support

---

## Quick Setup (Minimal Code)

### Step 1: Create the Enum Class (1 file, 11 lines)

Create `src/Enum/OrderStatusEnum.php`:

```php
<?php

namespace App\Enum;

enum OrderStatusEnum: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}
```

**That's it!** No need to register anything. Symfony auto-discovers it.

---

### Step 2: Create the Controller (1 file, 20 lines)

Create `src/Controller/OrderController.php`:

```php
<?php

namespace App\Controller;

use App\Enum\OrderStatusEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/orders/list/{status}', name: 'list_orders')]
    public function list(OrderStatusEnum $status): Response
    {
        return new Response('Status: ' . $status->value);
    }
}
```

**That's all you need!** 31 lines total to have a working enum parameter converter.

---

## How to Use (Commands)

### Test the Routes

```bash
# Show all routes
php bin/console debug:router

# Filter for order routes
php bin/console debug:router | grep orders
```

### Test in Browser

**Valid URLs** (auto-converts enum):
- `http://localhost:8000/orders/list/pending` → ✅ Works
- `http://localhost:8000/orders/list/paid` → ✅ Works
- `http://localhost:8000/orders/list/shipped` → ✅ Works

**Invalid URL** (no matching case):
- `http://localhost:8000/orders/list/invalid` → ❌ 404 Not Found (automatic)

---

## Complete Execution Flow

```
URL Request: /orders/list/paid
        ↓
Symfony Router extracts: status = "paid" (string)
        ↓
Type Hint Check: parameter expects OrderStatusEnum
        ↓
ParamConverter: Match "paid" to enum case
        ↓
Enum Lookup in OrderStatusEnum.php:
  ✓ case Paid = 'paid'  ← MATCH!
        ↓
Create enum instance: OrderStatusEnum::Paid
        ↓
Dependency Injection: Inject into method parameter
        ↓
Method receives: $status = OrderStatusEnum::Paid
        ↓
Access values:
  - $status->name = "Paid"
  - $status->value = "paid"
        ↓
Response returned to browser
```

---

## Key Points

| Aspect | Details |
|--------|---------|
| **Minimum Files** | 2 (Enum + Controller) |
| **Auto-Discovery** | ✅ Yes, no config needed |
| **Invalid Values** | ❌ Automatic 404 error |
| **Type Safety** | ✅ Method receives only valid enums |
| **Default Values** | ✅ Supported with `= OrderStatusEnum::Paid` |
| **Multiple Cases** | ✅ Route only accepts case values |

---

## Advanced: Multiple Methods (Same Controller)

```php
#[Route('/orders/{status}', name: 'orders_index')]
public function index(OrderStatusEnum $status): Response {}

#[Route('/orders/{status}/edit', name: 'orders_edit')]
public function edit(OrderStatusEnum $status): Response {}

#[Route('/orders/{status}/delete', name: 'orders_delete')]
public function delete(OrderStatusEnum $status): Response {}
```

All routes automatically validate against your enum cases!

---

## Common Use Cases

### 1. Default Value (if no parameter provided)
```php
#[Route('/orders/{status?}', name: 'orders')]
public function show(OrderStatusEnum $status = OrderStatusEnum::Paid): Response
```

### 2. Optional Enum
```php
public function filter(?OrderStatusEnum $status = null): Response
{
    if ($status === null) {
        // No status provided
    }
}
```

### 3. Multiple Enums (Different Routes)
Create separate enums and use in different routes:
```php
// UserRoleEnum for /users/{role}
// PaymentStatusEnum for /payments/{status}
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| 404 on valid enum value | Check the enum case VALUE matches URL parameter (case-sensitive) |
| ParamConverter not working | Verify type hint is exactly the enum class name |
| IDE not recognizing enum | Run `composer dump-autoload` |
| Route not in debug:router | Check namespace and use statement in controller |

---

## File Summary

```
src/
├── Enum/
│   └── OrderStatusEnum.php      (11 lines - Define enum cases)
├── Controller/
│   └── OrderController.php      (20 lines - Use enum in routes)
```

**Total: 31 lines of code for a fully working enum system!**

---

## Testing Your Setup

```bash
# Start the server
symfony serve

# In another terminal, test the route
curl http://localhost:8000/orders/list/paid

# Check router debug info
php bin/console debug:router list_orders
```

---

## Why Use Enums Over Strings?

**Without Enum (Old Way):**
```php
$status = $request->query->get('status'); // Could be anything
if ($status === 'paid') { /* ... */ }     // Fragile, prone to typos
```

**With Enum (New Way):**
```php
public function show(OrderStatusEnum $status): Response
{
    // $status is guaranteed to be a valid enum case
    // IDE knows all possible values
    // No manual validation needed
}
```

Enums = **Type safety + Less code + Better maintainability**
