CREATE DATABASE IF NOT EXISTS skillsyncpro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE skillsyncpro;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  
  password VARCHAR(255),
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE resumes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  filename VARCHAR(255),
  extracted_text LONGTEXT,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  company VARCHAR(255),
  location VARCHAR(255),
  description TEXT,
  required_skills TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO jobs (title, company, location, description, required_skills) VALUES
('Frontend Developer', 'Tech Company', 'Remote', 'Build responsive frontends', 'html,css,javascript,react'),
('PHP Backend Developer', 'Another Tech Company', 'On-site', 'Build APIs and web apps', 'php,mysql,api,rest');
