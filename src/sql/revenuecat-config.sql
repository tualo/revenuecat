create table revenuecat_environment(
    id varchar(36) not null primary key,
    val longtext not null
);
create table revenuecat_subscriptions(
    id varchar(40) not null ,
    customer_id varchar(50) not null,
    current_period_ends_at timestamp not null,
    current_period_starts_at timestamp not null,
    product_id varchar(40) not null,
primary key (id),
    key idx_revenuecat_subscriptions_customer_id (customer_id),
    key idx_revenuecat_subscriptions_time (current_period_starts_at, current_period_ends_at)
);
/*
 "auto_renewal_status": "will_not_renew",
 "country": "DE",
 "current_period_ends_at": 1738915558000,
 "current_period_starts_at": 1738829158000,
 "customer_id": "$RCAnonymousID:b7a597934ad444789fa81002c3716eff",
 "entitlements": {
 "items": [],
 "next_page": null,
 "object": "list",
 "url": "https:\/\/api.revenuecat.com\/v2\/projects\/57cf58e0\/subscriptions\/subAap5939afe941a1be6729fbb11f22b0a823\/entitlements"
 },
 "environment": "sandbox",
 "gives_access": false,
 "id": "subAap5939afe941a1be6729fbb11f22b0a823",
 "management_url": "https:\/\/apps.apple.com\/account\/subscriptions",
 "object": "subscription",
 "original_customer_id": "$RCAnonymousID:b7a597934ad444789fa81002c3716eff",
 "ownership": "purchased",
 "pending_changes": null,
 "pending_payment": false,
 "presented_offering_id": null,
 "product_id": "prod4f31bd16ab",
 "starts_at": 1737446758000,
 "status": "expired",
 "store": "app_store",
 "store_subscription_identifier": "2000000849259997",
 "total_revenue_in_usd": {
 "commission": 44.55,
 "currency": "USD",
 "gross": 176.72,
 "proceeds": 103.95,
 "tax": 28.22
 }
 */
create table revenuecat_webhook(
    id varchar(36) not null primary key default uuid(),
    createdatetime datetime DEFAULT current_timestamp,
    eventtype varchar(100) not null,
    eventdata longtext not null
);
create table revenuecat_webhook_errors(
    id varchar(36) not null primary key default uuid(),
    createdatetime datetime DEFAULT current_timestamp,
    errordata longtext not null
);