<?php
/**
 * Database Setup Script
 * Visit this page ONCE to create all tables.
 * DELETE this file after setup for security.
 */
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';

$sql = <<<'SQL'
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(255)  NOT NULL,
    email         VARCHAR(255)  NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    role          ENUM('student','adviser','staff','dsa_director','ppss_director','dean','avp_admin','vp_admin','president','admin','janitor','security') NOT NULL DEFAULT 'student',
    phone         VARCHAR(20),
    department    VARCHAR(255),
    student_id    VARCHAR(50),
    profile_photo VARCHAR(255),
    is_active     TINYINT(1)    DEFAULT 1,
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS facilities (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    location    VARCHAR(255) NOT NULL,
    capacity    INT          NOT NULL,
    photo_path  VARCHAR(255),
    description TEXT,
    is_active   TINYINT(1)   DEFAULT 1,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS items (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    name               VARCHAR(255) NOT NULL,
    category           VARCHAR(100) NOT NULL,
    quantity_available INT          NOT NULL DEFAULT 0,
    photo_path         VARCHAR(255),
    description        TEXT,
    is_active          TINYINT(1)   DEFAULT 1,
    created_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS facility_bookings (
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    user_id               INT          NOT NULL,
    facility_id           INT          NOT NULL,
    title                 VARCHAR(255) NOT NULL DEFAULT '',
    date_start            DATE         NOT NULL,
    date_end              DATE         NOT NULL,
    time_start            TIME         NOT NULL DEFAULT '08:00:00',
    time_end              TIME         NOT NULL DEFAULT '17:00:00',
    purpose               TEXT,
    participants          INT          DEFAULT 0,
    status                ENUM('pending','approved','rejected','fully_approved','cancelled') DEFAULT 'pending',
    current_approval_role ENUM('adviser','staff','dsa_director','ppss_director','dean','avp_admin','vp_admin','president') DEFAULT 'adviser',
    rejection_reason      TEXT,
    created_at            TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    approved_at           TIMESTAMP    NULL,
    FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS facility_booking_approvals (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    role       VARCHAR(50) NOT NULL,
    user_id    INT NOT NULL,
    action     ENUM('approve','reject') NOT NULL,
    remarks    TEXT,
    action_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES facility_bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)              ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS item_bookings (
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    user_id               INT NOT NULL,
    item_id               INT NOT NULL,
    quantity_needed       INT NOT NULL DEFAULT 1,
    quantity_requested    INT NOT NULL DEFAULT 1,
    borrow_date           DATE,
    return_date           DATE,
    borrow_time           TIME DEFAULT '08:00:00',
    return_time           TIME DEFAULT '17:00:00',
    date_start            DATETIME,
    date_end              DATETIME,
    purpose               TEXT,
    status                ENUM('pending','approved','rejected','fully_approved','cancelled') DEFAULT 'pending',
    current_approval_role ENUM('adviser','staff','dsa_director','ppss_director','dean','avp_admin','vp_admin','president') DEFAULT 'adviser',
    rejection_reason      TEXT,
    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at           TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS maintenance_alerts (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    facility_id         INT NOT NULL,
    title               VARCHAR(255) NOT NULL,
    description         TEXT,
    severity            ENUM('low','medium','high','critical') DEFAULT 'medium',
    date_start          DATETIME NOT NULL,
    date_end            DATETIME NOT NULL,
    assigned_to_role    ENUM('janitor','security','staff','admin'),
    status              ENUM('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
    created_by_user_id  INT NOT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id)        REFERENCES facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS faq_entries (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    category   VARCHAR(100) NOT NULL,
    question   TEXT         NOT NULL,
    answer     TEXT         NOT NULL,
    sort_order INT          DEFAULT 0,
    is_active  TINYINT(1)   DEFAULT 1,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS system_settings (
    `key`   VARCHAR(100) PRIMARY KEY,
    `value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    title      VARCHAR(255) NOT NULL,
    message    TEXT         NOT NULL,
    type       VARCHAR(50)  DEFAULT 'info',
    booking_id INT          DEFAULT NULL,
    is_read    TINYINT(1)   DEFAULT 0,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    subject    VARCHAR(255) NOT NULL,
    message    TEXT         NOT NULL,
    is_read    TINYINT(1)   DEFAULT 0,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    subject    VARCHAR(255) NOT NULL,
    message    TEXT         NOT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
SQL;

$seedSql = <<<'SQL'
INSERT IGNORE INTO users (full_name, email, password_hash, role, is_active) VALUES
('System Administrator', 'admin@ndmu.edu.ph', '$2b$12$JBdcE.z4YOsVXHwudM05FuPnw0mau37SEMb1lUtX.fekjr/SYyz16', 'admin', 1);

INSERT IGNORE INTO system_settings (`key`, `value`) VALUES
('school_name',             'Notre Dame of Marbel University'),
('address',                 'Marbel, Koronadal City, South Cotabato, Philippines'),
('site_description',        'Facility and Item Booking System'),
('contact_email',           'admin@ndmu.edu.ph'),
('timezone',                'Asia/Manila'),
('max_booking_days_ahead',  '30'),
('require_approval',        '1'),
('allow_weekend_bookings',  '0');

INSERT IGNORE INTO faq_entries (category, question, answer, sort_order, is_active) VALUES
('General', 'How do I book a facility?', 'Log in to your account, go to Facilities, select a facility, pick your date and time, and submit.', 1, 1),
('General', 'How do I borrow equipment?', 'Go to Items, choose the equipment you need, specify quantity and dates, and submit for approval.', 2, 1),
('General', 'What is the approval process?', 'Bookings follow a multi-level approval chain: Adviser > Staff > Dean > PPSS Director > DSA Director > AVP Admin > VP Admin > President.', 3, 1),
('General', 'What role will I have after registering?', 'All new accounts are Student by default. An administrator can change your role.', 4, 1),
('Technical', 'I forgot my password. What do I do?', 'Click Forgot Password on the login page and follow the instructions.', 7, 1);

INSERT IGNORE INTO facilities (name, location, capacity, is_active) VALUES
('NDMU Auditorium', 'Main Building, Ground Floor', 800, 1),
('AVP Conference Room', 'Administration Building, 2nd Floor', 40, 1),
('College Gymnasium', 'Sports Complex', 1000, 1),
('Computer Laboratory 1', 'IT Building, 1st Floor', 30, 1),
('Library Study Room A', 'Library Building, 3rd Floor', 20, 1),
('Multi-Purpose Hall', 'Student Center', 200, 1);

INSERT IGNORE INTO items (name, category, quantity_available, is_active) VALUES
('LCD Projector', 'Equipment', 5, 1),
('Laptop', 'Equipment', 10, 1),
('Wireless Microphone', 'Equipment', 8, 1),
('Portable Speaker System', 'Equipment', 3, 1),
('Folding Tables', 'Furniture', 30, 1),
('Monobloc Chairs', 'Furniture', 200, 1);
SQL;

echo '<h1>NDMU Database Setup</h1>';
echo '<pre style="font-family:monospace; background:#f4f4f4; padding:1rem; border-radius:8px;">';

$errors = 0;

// Run schema
$statements = array_filter(array_map('trim', explode(';', $sql)));
foreach ($statements as $stmt) {
    if ($stmt === '' || str_starts_with($stmt, '--')) continue;
    try {
        $pdo->exec($stmt);
        echo "OK: " . substr($stmt, 0, 60) . "...\n";
    } catch (Throwable $e) {
        echo "ERR: " . $e->getMessage() . "\n";
        $errors++;
    }
}

// Run seed data
$seedStatements = array_filter(array_map('trim', explode(';', $seedSql)));
foreach ($seedStatements as $stmt) {
    if ($stmt === '' || str_starts_with($stmt, '--')) continue;
    try {
        $pdo->exec($stmt);
        echo "SEED OK: " . substr($stmt, 0, 60) . "...\n";
    } catch (Throwable $e) {
        echo "SEED SKIP: " . $e->getMessage() . "\n";
    }
}

echo '</pre>';

if ($errors === 0) {
    echo '<h2 style="color:green;">Setup complete! All tables created.</h2>';
    echo '<p>Default admin: <strong>admin@ndmu.edu.ph</strong> / <strong>Super@admin123</strong></p>';
    echo '<p><strong>DELETE this file (setup_db.php) for security.</strong></p>';
    echo '<p><a href="login.php">Go to Login</a></p>';
} else {
    echo '<h2 style="color:orange;">Setup completed with ' . $errors . ' error(s). Check above.</h2>';
}
