CREATE TABLE IF NOT EXISTS password_reset_tokens (
    token_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    nic VARCHAR(16) NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    used_at DATETIME DEFAULT NULL,
    FOREIGN KEY (nic) REFERENCES users(nic) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_password_reset_tokens_nic (nic),
    INDEX idx_password_reset_tokens_expires_at (expires_at)
);
