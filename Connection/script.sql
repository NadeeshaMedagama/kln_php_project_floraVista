USE kln_php;

CREATE TABLE users (user_id int(10) AUTO_INCREMENT NOT NULL,
                    user_name VARCHAR(20) NOT NULL,
                    email VARCHAR(30) NOT NULL UNIQUE,
                    password VARCHAR(80) NOT NULL,
                    mobile INT(10) NOT NULL UNIQUE,
                    gender TINYINT,
                    PRIMARY KEY (user_id),
                    INDEX idx_user_id(user_id),
                    Index idx_email(email),
                    Index idx_user_name(user_name));

ALTER Table users ADD address TEXT;

CREATE Table payments(reference_no VARCHAR(20),
                      user_id INT(10) NOT NULL,
                      amount DECIMAL NOT NULL,
                      date DATE,
                      FOREIGN KEY (user_id) REFERENCES users(user_id));

ALTER TABLE payments ADD address TEXT;
CREATE TABLE delivery_items (
                                flower_id VARCHAR(100),
                                user_id INT(10),
                                quantity INT(5),
                                reference_no VARCHAR(20));


CREATE TABLE loyalty_users(loyalty_id VARCHAR(36),
                           points_blance INT(5),
                           user_id INT(10),
                           FOREIGN KEY (user_id) REFERENCES users(user_id));

ALTER TABLE loyalty_users ADD PRIMARY KEY (user_id, loyalty_id);

CREATE TABLE shopping_cart(user_id INT(10),
                           flower_id VARCHAR(100),
                           quantity INT(5),
                           PRIMARY KEY (user_id,flower_id),
                           FOREIGN KEY (user_id) REFERENCES users(user_id),
                           FOREIGN KEY (flower_id) REFERENCES flowers(flower_id));
ALTER TABLE shopping_cart ADD is_pay BOOLEAN DEFAULT FALSE;

ALTER TABLE shopping_cart ADD shipping_date DATE;

CREATE TABLE admin (admin_id INT(10) AUTO_INCREMENT,
                    admin_name VARCHAR(20) NOT NULL UNIQUE,
                    email VARCHAR(30) NOT NULL UNIQUE,
                    password VARCHAR(80) NOT NULL,
                    PRIMARY KEY (admin_id),
                    INDEX idx_admin_id(admin_id),
                    INDEX idx_email(email));

# email : thushanmadhusanka3@gmail.com   password: thushan2001
INSERT INTO admin (admin_name, email, password) VALUES ('thush','thushanmadhusanka3@gmail.com', '67215bebe2fe2737d90bb951347c6a0852a1f537');



CREATE TABLE supliers(suplier_id INT(10) AUTO_INCREMENT,
                      suplier_username VARCHAR(20) NOT NULL,
                      email VARCHAR(30) NOT NULL UNIQUE,
                      password VARCHAR(80) NOT NULL,
                      mobile INT(10) NOT NULL UNIQUE,
                      verify TINYINT DEFAULT false,
                      PRIMARY KEY (suplier_id),
                      INDEX idx_suplier_id(suplier_id),
                      INDEX idx_email(email));

CREATE TABLE categories(category_id INT(5) AUTO_INCREMENT,
                        category_name VARCHAR(20) NOT NULL,
                        PRIMARY KEY (category_id));

CREATE TABLE flowers(flower_id VARCHAR(100),
                     flower_name VARCHAR(20),
                     quantity INT(5),
                     sale_price INT(5),
                     description VARCHAR(255),
                     other_info JSON,
                     PRIMARY KEY (flower_id),
                     INDEX idx_flower_name(flower_name));

ALTER TABLE flowers ADD purchase_price FLOAT;

CREATE TABLE flower_categories (flower_id VARCHAR(100),
                                category_id INT(5),
                                PRIMARY KEY (flower_id, category_id),
                                FOREIGN KEY (flower_id) REFERENCES flowers(flower_id) ON DELETE CASCADE,
                                FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE);


CREATE TABLE flower_images(flower_id VARCHAR(100),
                           dir_path TEXT,
                           FOREIGN KEY (flower_id) REFERENCES flowers(flower_id) ON DELETE CASCADE,
                           INDEX idx_flower_id(flower_id));


CREATE TABLE flower_discounts(flower_id VARCHAR(100) UNIQUE,
                              today_dicount INT(3),
                              today_dicount_applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              loyalty_discount INT(3),
                              layalty_discount_applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              price_off INT(5),
                              price_off_applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              Foreign Key (flower_id) REFERENCES flowers(flower_id) ON DELETE CASCADE,
                              INDEX idx_flower_id(flower_id));

CREATE TABLE comments(flower_id VARCHAR(100),
                      user_id INT(10),
                      comment TEXT,
                      FOREIGN KEY (flower_id) REFERENCES flowers(flower_id) ON DELETE CASCADE,
                      FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
                      INDEX idx_flower_id(flower_id));

CREATE TABLE orders(order_id INT(10) PRIMARY KEY AUTO_INCREMENT,
                    order_date DATE DEFAULT CURRENT_DATE,
                    flower_id VARCHAR(100),
                    quantity INT(5),
                    suplier_id INT(10),
                    FOREIGN KEY (flower_id) REFERENCES flowers(flower_id) ON DELETE CASCADE,
                    FOREIGN KEY (suplier_id) REFERENCES supliers(suplier_id) ON DELETE CASCADE
);

CREATE TABLE subscriptions (
                               id INT AUTO_INCREMENT PRIMARY KEY,
                               name VARCHAR(255) NOT NULL,
                               email VARCHAR(255) NOT NULL UNIQUE,
                               created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE orders ADD purchase_price FLOAT;

ALTER TABLE orders ADD order_sale_price FLOAT DEFAULT 0;

ALTER TABLE orders ADD isDelivered BOOLEAN DEFAULT FALSE;

ALTER TABLE orders ADD delivered_date DATE;
ALTER TABLE orders ADD isAccept_suplier BOOLEAN DEFAULT FALSE;
ALTER TABLE orders ADD accept_suplier_date DATE;


SET GLOBAL event_scheduler = ON;

CREATE EVENT IF NOT EXISTS reset_today_discount
    ON SCHEDULE EVERY 1 DAY
    DO
    UPDATE flowers_prices
    SET today_discount = NULL
    WHERE today_discount IS NOT NULL;


CREATE EVENT IF NOT EXISTS reset_today_discount
    ON SCHEDULE AT '2024-11-10 00:00:00'
    DO
    UPDATE flowers_prices
    SET today_discount = NULL
    WHERE today_discount IS NOT NULL;


CREATE EVENT IF NOT EXISTS reset_today_discount
    ON SCHEDULE EVERY 1 HOUR
        STARTS '2024-11-10 00:00:00'
    DO
    UPDATE flowers_prices
    SET today_discount = NULL
    WHERE today_discount IS NOT NULL;







