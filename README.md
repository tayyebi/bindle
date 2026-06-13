# Bindle

Painless cart management and card payments for onlineshops.

# Domains

- There is a system administrator that can impersonate and manage everything
- This website has an administration area
- Shop owners can register their website
- Shop owners must point a subdomain with CNAME
- Shop owners must define their payment methods for shop customers
- Currently there is only manual payment approve available
- Payments can be manually approved by at least screenshot or proof text such as transaction id

# Cart

1. Shops put a link on their product page linking to `<bindle_url>/add/<current_page_url>`
2. When the user clicks on the link, bindle will decode the page url, and fetch it
3. Bindle will automatically detect the product info, and price from product json schema
4. Bindle will store the customer cart in cookie
5. Then they will proceed to checkout

