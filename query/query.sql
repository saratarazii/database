-- users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  username VARCHAR(100) UNIQUE,
  password VARCHAR(255)
) ENGINE = InnoDB;

-- outfits table
CREATE TABLE outfits (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- fire_fits table
CREATE TABLE fire_fits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- fire_fit_items table
CREATE TABLE fire_fit_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fire_fit_id INT NOT NULL,
  outfit_id INT NOT NULL,
  FOREIGN KEY (fire_fit_id) REFERENCES fire_fits(id) ON DELETE CASCADE,
  FOREIGN KEY (outfit_id) REFERENCES outfits(id) ON DELETE CASCADE
) ENGINE = InnoDB;


