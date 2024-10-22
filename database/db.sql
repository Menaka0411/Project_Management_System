
CREATE DATABASE teams_management
Use teams_management 
    
CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(255) NOT NULL,
    team_size INT NOT NULL,
    year INT NOT NULL,
    department VARCHAR(255) NOT NULL
);

CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    member_name VARCHAR(255) NOT NULL,
    roll_no VARCHAR(50) NOT NULL,
    member_role VARCHAR(50) NOT NULL,
    member_email VARCHAR(255) NOT NULL,
    member_phone VARCHAR(15) NOT NULL,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
);

CREATE TABLE projects_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    leader VARCHAR(255) NOT NULL,
    members TEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    ppt VARCHAR(255) NOT NULL,
    abstract TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);




CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    media VARCHAR(255),
    file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    roll_number VARCHAR(20) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL, 
    login_time DATETIME
);

CREATE TABLE staff ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    email VARCHAR(100) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL, 
    role ENUM('Mentor', 'HOD', 'Principal', 'AO') NOT NULL, 
    login_time DATETIME
);
--created
CREATE TABLE IF NOT EXISTS student_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll_number VARCHAR(20) NOT NULL UNIQUE,
    univ_reg_no VARCHAR(50) NOT NULL UNIQUE,
    cgpa DECIMAL(3, 2) NOT NULL,
    degree VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    batch_year VARCHAR(9) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phno VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE academic_marks (
    mark_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    semester INT NOT NULL,
    review_type VARCHAR(50) NOT NULL,
    marks DECIMAL(5,2),
    review_date DATE,
    FOREIGN KEY (student_id) REFERENCES student_details(student_id)
);





ALTER TABLE projects_submissions 
    ADD mentor VARCHAR(255) NOT NULL,  -- Add the mentor field
    ADD submission_type ENUM('abstract', 'ppt', 'both') DEFAULT 'both', -- Add the submission type (for tracking what was submitted)
    ADD status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending', -- Add status to track mentor approval
    ADD project_id INT NOT NULL; -- Add a project ID to group submissions by project/team

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    team_name VARCHAR(255) NOT NULL,
    leader VARCHAR(255) NOT NULL,
    members TEXT NOT NULL,  -- Assuming this will store comma-separated values
    mentor VARCHAR(255) NOT NULL,
    mentor_id INT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL,
    abstract MEDIUMTEXT NOT NULL,
    ppt_path VARCHAR(255),  -- Assuming this is optional
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mentor_id) REFERENCES staff(id) ON DELETE CASCADE
);

CREATE TABLE student_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    project_title VARCHAR(255) NOT NULL,
    project_description TEXT NOT NULL,
    submission_date DATE DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    mentor_comments TEXT DEFAULT NULL,
    files VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    team_name VARCHAR(100) NOT NULL,
    team_members TEXT NOT NULL,
    technology_stack VARCHAR(255) NOT NULL
);



CREATE TABLE student_project_marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll_number VARCHAR(20),
    semester INT,
    year INT,
    review_0 INT,
    review_1 INT,
    review_2 INT,
    review_3 INT,
    final_review INT
);
--attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll_number VARCHAR(20) NOT NULL,
    week_number INT NOT NULL,
    review_number INT NOT NULL,
    status ENUM('Present', 'Absent') NOT NULL,
    attendance_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (roll_number) REFERENCES student_details(roll_number) ON DELETE CASCADE
);



CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    member_name VARCHAR(255) NOT NULL,
    roll_no VARCHAR(50) NOT NULL,
    member_role VARCHAR(100),
    member_email VARCHAR(255),
    member_phone VARCHAR(15)
);
