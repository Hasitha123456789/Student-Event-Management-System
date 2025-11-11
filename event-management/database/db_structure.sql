-- Create database
CREATE DATABASE IF NOT EXISTS event_management;
USE event_management;

-- Users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    contact_number VARCHAR(15),
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE events (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(150) NOT NULL,
    date DATE NOT NULL,
    venue VARCHAR(200) NOT NULL,
    description TEXT,
    organizer VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Registrations table
CREATE TABLE registrations (
    reg_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (user_id, event_id)
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (name, email, password, contact_number, is_admin)
VALUES ('Admin User', 'admin@example.com', '$2y$10$YourHashedPasswordHere', '1234567890', TRUE);

-- Insert sample events
INSERT INTO events (title, date, venue, description, organizer) VALUES
('Tech Workshop 2025', '2025-11-20', 'Computer Lab A', 'Learn web development basics', 'CS Department'),
('Hackathon 2025', '2025-12-05', 'Innovation Center', '24-hour coding competition', 'Tech Club'),
('Career Fair', '2025-11-25', 'Main Auditorium', 'Meet potential employers', 'Career Services');
