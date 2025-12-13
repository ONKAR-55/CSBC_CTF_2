CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(50),
    setting_value TEXT
);

INSERT INTO users (username, password) VALUES ('admin', 'secure_admin_123');
INSERT INTO config (setting_name, setting_value) VALUES ('announcement', 'Welcome to the secure portal.');