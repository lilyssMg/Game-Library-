-- Game Library Database Schema
-- Run this file to set up the full database

CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    student_id VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    bio TEXT NOT NULL
);

CREATE TABLE games (
    game_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    genre VARCHAR(50),
    description TEXT,
    image VARCHAR(255)
    
);

-- Sample member data
INSERT INTO members (id, name, student_id, email, bio) VALUES
(1, 'Michelle Davila', '413854752', 'mich.davila08@gmail.com', 'Guatemalan girl studying CS in Taiwan one coffee away from fixing it.'),
(2, 'Lily', '413855353', 'lilyssmg@gmail.com', 'Computer Science student at Tamkang University.'),
(3, 'Gino', '413850073', 'ppple1872@gmail.com', 'CS student.'),
(4, '徐毓宏', '413850206', '413850206@o365.tku.edu.tw', 'I am currently a sophomore student specializing in Computer Science within the all-English program. Outside of my studies, I am an enthusiast of competitive gaming and enjoy playing various online titles.'),
(5, '李睿恩', '413850222', '413850222@o365.tku.edu.tw', 'Hello I am Bruce.');
 
