# Bindle - Product Requirements Document

## 1. Executive Summary

Bindle is a hosted SaaS cart management and card payment platform for online shops. Shop owners point a CNAME subdomain to Bindle, embed a simple link on their product pages, and Bindle handles the rest — cart, checkout, and payment approval.

## 2. Product Type

Hosted SaaS multi-tenant platform. Shop tenants are identified by their request hostname (CNAME). Bindle routes requests based on the `Host` header to the correct shop context.

## 3. Target Users

Individual creators and solopreneurs selling physical or digital products. Non-technical shop owners who want a drop-in cart/payment solution without integrating a full e-commerce platform.

## 4. Tech Stack

| Component     | Technology                 |
|---------------|----------------------------|
| Backend       | Pure PHP (MVC architecture)|
| Database      | PostgreSQL                 |
| Container     | Docker Compose             |
| Frontend      | Server-rendered HTML + CSS |
| Hosting       | Single server / VPS        |

## 5. Core Features

### 5.1 Multi-Tenant Architecture

- Shop owners configure a CNAME record pointing their subdomain (e.g., `shop.example.com`) to Bindle's server.
- Bindle inspects the `Host` header to identify the shop and loads its configuration.
- All requests to the shop's domain are served by Bindle.
- System admin has a separate admin area for global management.

### 5.2 Product Detection (Add to Cart)

- Shops place a link on their product page pointing to `https://<shop-domain>/add/<current-page-url>`.
- When clicked, Bindle fetches the product page URL, parses **schema.org/Product** JSON-LD/microdata, and extracts:
  - Product name
  - Price
  - Currency
  - Description
  - Image URL
  - Product type (physical vs digital)
  - Availability
- The product is stored in the customer's cart.

### 5.3 Cart Management

- Cart is persisted to PostgreSQL keyed by an anonymous cart ID stored in a cookie.
- Supports multiple items from the same shop in a single cart.
- Customers can view cart, update quantities, and remove items.
- Cart cookie is long-lived but can be cleared by the customer.

### 5.4 Hosted Checkout

- Checkout is a server-rendered page on the **shop's domain** (via CNAME).
- Customer fills in:
  - Name
  - Email
  - Shipping address (if physical product)
- Order summary is displayed before submission.

### 5.5 Payment - Manual Approval

- Currently supports only manual payment approval.
- Customer submits their order and receives instructions (defined by shop owner) for payment proof submission.
- Payment proof options:
  - Screenshot upload
  - Transaction ID / reference text
- Shop owner reviews proof in their dashboard and approves/rejects the order.
- On approval, customer receives a confirmation (optional: shop receives webhook).

### 5.6 Order Management

- Customers can view their order status via a hosted page on the shop domain.
- Orders have states: `pending`, `approved`, `rejected`, `cancelled`.
- Basic email notifications for order confirmation and status changes.

### 5.7 Webhooks

- Optional webhook callbacks to shop owner's endpoint on:
  - Order created
  - Order approved
  - Order rejected
- Webhook payload includes order details and status.

### 5.8 Shop Owner Dashboard

- Separate Bindle login for shop owners (at `https://bindle.app` or admin subdomain).
- Shop owners can:
  - View all orders for their shop
  - Approve/reject pending payments
  - View order details (customer info, product, proof)
  - Configure payment instructions shown to customers
  - Configure webhook URL
  - View basic order analytics/stats

### 5.9 System Admin Area

- Minimal admin panel for platform management.
- Admins can:
  - List all registered shops
  - Create/suspend shops
  - Impersonate any shop or customer view (support use case)
  - View basic platform stats

### 5.10 Digital Product Delivery

- For digital products, after payment approval, customer receives a download link.
- Download links can be time-limited or permanent (configurable per product).

## 6. User Flows

### 6.1 Shop Registration Flow

1. Shop owner signs up at Bindle website.
2. Owner configures CNAME record with their DNS provider.
3. Owner configures shop settings (payment instructions, webhooks).
4. Owner adds add-to-cart links to their product pages.

### 6.2 Customer Purchase Flow

1. Customer visits product page on shop's website.
2. Customer clicks "Add to Cart" link (goes to Bindle).
3. Bindle parses product page, adds item to cart, redirects to cart page.
4. Customer views cart on shop domain, clicks "Checkout".
5. Customer fills in details, submits order.
6. Customer sees payment instructions, submits proof.
7. Order is marked `pending`.
8. Shop owner reviews proof in dashboard, approves/rejects.
9. Customer receives notification of approval + download link (digital) or shipping notification (physical).

## 7. Data Model (High-Level)

### Shop
- id, domain, name, email, payment_instructions, webhook_url, is_active, created_at

### Customer
- id, email, name, cart_id (cookie-based)

### Product
- id, shop_id, name, price, currency, type (physical/digital), description, image_url, is_active, created_at

### Cart
- id (cookie value), shop_id, created_at, updated_at

### CartItem
- id, cart_id, product_id, quantity, price_at_add

### Order
- id, shop_id, customer_id, cart_id, status (pending/approved/rejected/cancelled), total, currency, shipping_address, customer_email, customer_name, notes, created_at, approved_at

### PaymentProof
- id, order_id, type (screenshot/text), value (file path or text), submitted_at

### WebhookLog
- id, shop_id, event, url, request_body, response_status, created_at

### DownloadToken
- id, order_id, product_id, token, expires_at, used_at

## 8. Constraints & Limitations (MVP)

- Manual payment only (no Stripe, PayPal, crypto integration planned).
- No inventory management (products are assumed in-stock).
- No tax calculation.
- No coupon/discount codes.
- No multi-currency (shop has one base currency).
- No refund/dispute management (shop owner handles off-platform).
- No cart abandonment recovery.
- Single language (English).
- Admin area is minimal.
- No public API for third-party integration (webhooks only).

## 9. Security & Compliance

- Standard web security: HTTPS-only, CSRF protection, input validation.
- No PCI DSS scope (manual payments, no card data handling).
- GDPR: store minimal PII, provide data export/deletion on request.
- Shop owner authentication: password + session.
- Download tokens: signed, time-limited for digital products.
- Rate limiting on add-to-cart and checkout endpoints.

## 10. Future Considerations (Post-MVP)

- Stripe/PayPal integration for automatic payment processing.
- Inventory tracking.
- Tax calculation (e.g., TaxJar API).
- Coupon/discount engine.
- Abandoned cart emails.
- Refund/dispute management in dashboard.
- Multi-language support.
- Public REST API for shop owners.
- Analytics dashboard (revenue, conversion, popular products).
- Plugin/embed widget for inline checkout.
- Customer accounts (login, order history).

## 11. Glossary

| Term | Definition |
|------|------------|
| Shop Owner | Person who registers their online shop with Bindle |
| Customer | End-user buying products from the shop |
| CNAME | DNS record type used to map shop's subdomain to Bindle |
| schema.org | Standard structured data format for product information |
| Cart ID | Anonymous identifier stored in cookie mapping to DB cart |
