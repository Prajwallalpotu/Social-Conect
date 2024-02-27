CREATE TABLE users(
    Id int PRIMARY KEY AUTO_INCREMENT,
    Username varchar(200),
    Email varchar(200),
    Age int,
    Password varchar(200)
);

CREATE TABLE user_following (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_relationship (follower_id, following_id),
    FOREIGN KEY (follower_id) REFERENCES users(Id),
    FOREIGN KEY (following_id) REFERENCES users(Id)
);

ALTER TABLE users
ADD COLUMN Gender ENUM('Male', 'Female');


CREATE TABLE user_details (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    Profile_Img VARCHAR(255),
    Bio TEXT,
    Link VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(Id)
);

