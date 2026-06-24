CREATE DATABASE IF NOT EXISTS student_records
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE student_records;

DROP TABLE IF EXISTS students;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricNo VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    faculty VARCHAR(50) NOT NULL,
    gpa DECIMAL(3,2) NOT NULL CHECK (gpa >= 0 AND gpa <= 4),
    email VARCHAR(100) NOT NULL UNIQUE,
    year TINYINT NOT NULL CHECK (year BETWEEN 1 AND 6),
    active TINYINT NOT NULL DEFAULT 1
);

INSERT INTO students (matricNo, name, course, faculty, gpa, email, year, active) VALUES
    ('A21CS0001', 'Ahmad Zulkarnain bin Hassan', 'Bachelor of Computer Science (Software Engineering)', 'FSKSM', 3.75, 'ahmad.zulkarnain@graduate.utm.my', 3, 1),
    ('A21CS0002', 'Nur Aisyah binti Abdullah', 'Bachelor of Computer Science (Data Engineering)', 'FSKSM', 3.92, 'nur.aisyah@graduate.utm.my', 3, 1),
    ('A21CS0003', 'Muhammad Faris bin Razak', 'Bachelor of Computer Science (Cybersecurity)', 'FSKSM', 3.45, 'm.faris@graduate.utm.my', 2, 1),
    ('A21IT0004', 'Siti Khadijah binti Ibrahim', 'Bachelor of Information Technology', 'FSKSM', 3.68, 'siti.khadijah@graduate.utm.my', 4, 1);