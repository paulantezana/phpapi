CREATE DATABASE api_pa3;

USE api_pa3;

CREATE TABLE customers(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(128) NOT NULL,
    email VARCHAR(64) DEFAULT '',
    phone VARCHAR(32) DEFAULT ''
);

CREATE TABLE products(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    bar_code VARCHAR(64) NOT NULL,
    description VARCHAR(128) DEFAULT '',
    purchase_price double(11,2) DEFAULT 0.00,
    sale_price double(11,2) DEFAULT 0.00
);

INSERT INTO customers(full_name, email, phone)
VALUES ('paul yoel', 'yoel.antezana@gmail.com', '977898412132');

INSERT INTO products (bar_code, description, purchase_price, sale_price)
VALUES ('PE12165165', 'Gaseosa Cocacola', 2,4);