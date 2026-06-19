-- রক্তবন্ধন (RoktoBondhon) — সম্পূর্ণ ডাটাবেস স্কিমা (Phase 1)
-- InfinityFree-এর phpMyAdmin-এ গিয়ে এই পুরো ফাইলটা SQL ট্যাবে পেস্ট করে Go চাপলেই হবে।
-- আগের ছোট donors টেবিল থাকলে এটা DROP করে নতুন করে বানাবে (আগের ডেটা থাকলে হারিয়ে যাবে)।

DROP TABLE IF EXISTS emergency_requests;
DROP TABLE IF EXISTS donors;
DROP TABLE IF EXISTS hospitals;
DROP TABLE IF EXISTS ambulances;
DROP TABLE IF EXISTS admins;

CREATE TABLE donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    photo_path VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,
    whatsapp VARCHAR(20) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','O+','O-','AB+','AB-') NOT NULL,
    division VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    upazila VARCHAR(50) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    date_of_birth DATE DEFAULT NULL,
    gender ENUM('male','female') NOT NULL,
    occupation VARCHAR(100) DEFAULT NULL,
    weight_kg DECIMAL(5,2) DEFAULT NULL,
    emergency_contact VARCHAR(20) DEFAULT NULL,
    last_donation_date DATE DEFAULT NULL,
    platelet_last_date DATE DEFAULT NULL,
    plasma_last_date DATE DEFAULT NULL,
    total_donations INT DEFAULT 0,
    is_blocked TINYINT(1) DEFAULT 0,
    is_verified TINYINT(1) DEFAULT 0,
    public_id VARCHAR(20) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_search ON donors (blood_group, district);
CREATE INDEX idx_division ON donors (division, district, upazila);

CREATE TABLE emergency_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(100) NOT NULL,
    hospital_name VARCHAR(150) NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','O+','O-','AB+','AB-') NOT NULL,
    bags_needed INT NOT NULL DEFAULT 1,
    contact_number VARCHAR(20) NOT NULL,
    division VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    upazila VARCHAR(50) DEFAULT NULL,
    urgency_level ENUM('normal','urgent','critical') NOT NULL DEFAULT 'normal',
    status ENUM('pending','active','fulfilled','expired') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_req_status ON emergency_requests (status, blood_group);

CREATE TABLE hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    division VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    has_blood_bank TINYINT(1) DEFAULT 0,
    map_link VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ambulances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    division VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    address VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
