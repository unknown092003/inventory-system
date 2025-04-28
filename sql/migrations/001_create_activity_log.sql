CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action_type VARCHAR(20) NOT NULL,
    description TEXT NOT NULL,
    table_name VARCHAR(255) NOT NULL,
    record_id INT NOT NULL,
    old_data JSON,
    new_data JSON,
    user VARCHAR(255) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_table_record (table_name, record_id)
);