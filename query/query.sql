-- users table

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  username VARCHAR(100) UNIQUE,
  password VARCHAR(255)
);


-- outfits table
CREATE TABLE `firefit_db`.`outfits` (
  `id` INT NOT NULL AUTO_INCREMENT,                
  `user_id` INT NOT NULL,                        
  `image_path` VARCHAR(255) NOT NULL,              
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  PRIMARY KEY (`id`),                            
  INDEX (`user_id`)                               
) ENGINE = InnoDB;


