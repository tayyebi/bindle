CREATE TABLE IF NOT EXISTS system_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    level VARCHAR(20) NOT NULL DEFAULT 'info',
    message TEXT NOT NULL,
    file VARCHAR(500) NOT NULL DEFAULT '',
    line INTEGER NOT NULL DEFAULT 0,
    trace TEXT NOT NULL DEFAULT '',
    user_type VARCHAR(20) NOT NULL DEFAULT '',
    user_id VARCHAR(64) NOT NULL DEFAULT '',
    request_method VARCHAR(10) NOT NULL DEFAULT '',
    request_url VARCHAR(500) NOT NULL DEFAULT '',
    request_ip VARCHAR(45) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_system_logs_level ON system_logs(level);
CREATE INDEX IF NOT EXISTS idx_system_logs_created_at ON system_logs(created_at);
