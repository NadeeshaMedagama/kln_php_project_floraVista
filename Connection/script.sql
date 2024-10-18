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

CREATE TABLE flowers_category(category_id INT(5) AUTO_INCREMENT,
                              category_name VARCHAR(20) NOT NULL,
                              PRIMARY KEY (category_id));

CREATE TABLE flowers(flower_id VARCHAR(100),
                     flower_name VARCHAR(20),
                     quantity INT(5),
                     category_id INT(5),
                     description VARCHAR(255),
                     other_info JSON,
                     PRIMARY KEY (flower_id),
                     Foreign Key (category_id) REFERENCES flowers_category(category_id),
                     INDEX idx_flower_name(flower_name));


CREATE TABLE flowers_prices(flower_id VARCHAR(100) UNIQUE,
                            sale_price INT(5),
                            
                            today_dicount INT(3),
                            loyalty_discount INT(3),
                            price_off INT(5),
                            Foreign Key (flower_id) REFERENCES flowers(flower_id),
                            INDEX idx_flower_id(flower_id));

CREATE TABLE images_links(flower_id VARCHAR(100) UNIQUE,
                          file_path VARCHAR(200),
                          image_name VARCHAR(200) not null,
                          Foreign Key (flower_id) REFERENCES flowers(flower_id),
                          INDEX idx_flower_id(flower_id),
                          INDEX idx_image_name(image_name));




