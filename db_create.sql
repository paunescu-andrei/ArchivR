CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    admin char(1) DEFAULT "N"
);

CREATE TABLE folder_path (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    INDEX par_ind (user_id),
    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE log (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    username VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX log_ind (user_id),
    FOREIGN KEY (user_id)
    	REFERENCES users(id)
    	ON DELETE CASCADE
);
