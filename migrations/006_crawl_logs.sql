CREATE TABLE IF NOT EXISTS crawl_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    url TEXT NOT NULL,
    shop_id UUID REFERENCES shops(id) ON DELETE SET NULL,
    http_code INTEGER NOT NULL DEFAULT 0,
    duration_ms INTEGER NOT NULL DEFAULT 0,
    success BOOLEAN NOT NULL DEFAULT false,
    parser_used VARCHAR(20) NOT NULL DEFAULT '',
    error_message TEXT NOT NULL DEFAULT '',
    product_name VARCHAR(255) NOT NULL DEFAULT '',
    product_price DECIMAL(12,2) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_crawl_logs_created_at ON crawl_logs(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_crawl_logs_success ON crawl_logs(success);
CREATE INDEX IF NOT EXISTS idx_crawl_logs_http_code ON crawl_logs(http_code);
CREATE INDEX IF NOT EXISTS idx_crawl_logs_shop ON crawl_logs(shop_id);
