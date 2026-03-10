-- create database password_manager_v2;

create table users (id int not null auto_increment,
                                firstname_hash varchar(255),
                                lastname_hash varchar(255),
                                username_hash varchar(255) unique,
                                password_hash varchar(255),
                                device_terminal varchar(255) default null,
                                ip varchar(255) default null,
                                dateRegistered DATETIME default null,
                                firstname varchar(255),
                                lastname VARCHAR(255),
                                username VARCHAR(255),
                                primary key(id));

-- create table master_key (id int not null auto_increment primary key,
--                            key_hash VARCHAR(255) DEFAULT NULL,
--                            user_id int not null,
--                            FOREIGN KEY(user_id) REFERENCES users.id);

create table master_key (id int not null auto_increment primary key,
                            key_hash VARCHAR(255) DEFAULT NULL,
                            user_id int not null,
                            FOREIGN KEY(user_id) REFERENCES users(id));

create table accounts (id int not null auto_increment primary key,
                        username varchar(255) default null,
                        username_hash varchar(255) default null,
                        password varchar(255) default null,
                        note varchar(255) default null,
                        addedBy varchar(255) default null,
                        dateAdded DATETIME default null,
                        account_for varchar(255) default null,
                        title_hash varchar(255) default null,
                        FOREIGN KEY(addedBy) REFERENCES users(username));

CREATE TABLE password_reset_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);