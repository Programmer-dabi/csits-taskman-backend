CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_name VARCHAR(150) NOT NULL,
    sub_organization VARCHAR(100) NOT NULL,
    due_date DATETIME NOT NULL,
    status ENUM(
        'Pending',
        'Due Tommorrow',
        'Accomplished',
        'Unaccomplished'
    ) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);