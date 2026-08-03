-- Run this once in phpMyAdmin (or via mysql CLI) against your BMIS database.
-- Backs the login lockout in classes/security.php with persistent storage
-- (session-based counters can be bypassed by simply dropping cookies between
-- attempts, so failed-login counts must survive across sessions).

CREATE TABLE IF NOT EXISTS `tbl_login_attempts` (
    `identity_hash` VARCHAR(64) NOT NULL PRIMARY KEY,
    `attempt_count` INT NOT NULL DEFAULT 0,
    `first_attempt` DATETIME NOT NULL,
    `last_attempt`  DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
