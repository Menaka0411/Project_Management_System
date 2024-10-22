CREATE TABLE projects (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(255) NOT NULL,
    status ENUM('in_progress', 'completed') DEFAULT 'in_progress',
    completion_date DATE
);
