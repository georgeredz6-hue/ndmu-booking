<?php
/**
 * Add all missing facilities from the original schema.
 * Visit once, then delete.
 */
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';

$sql = <<<'SQL'
INSERT IGNORE INTO facilities (name, location, capacity, is_active) VALUES
('Covered Court',           'Campus Grounds',                        500,  1),
('BRC Dining Hall',         'BRC Building',                          300,  1),
('BRC Convention Hall',     'BRC Building',                          500,  1),
('SMC Hall',                'Student Medical Center',                200,  1),
('BRC Lobby',               'BRC Building',                          100,  1),
('Dance Studio 1',          'Arts Building, 1st Floor',               50,  1),
('Dance Studio 2',          'Arts Building, 2nd Floor',               50,  1),
('Quadrangle',              'Campus Grounds',                       1000,  1),
('Reviewing Stand',         'Campus Grounds',                        300,  1),
('Soccer Field',            'Sports Complex',                       2000,  1),
('Function Hall',           'Event Center',                          400,  1),
('Gymnasium',               'Sports Complex',                        800,  1),
('Tennis Court',            'Sports Complex',                        100,  1),
('Badminton Court',         'Sports Complex',                        150,  1),
('Dumont',                  'Classroom Building',                    100,  1),
('Doherty',                 'Classroom Building',                    100,  1),
('Teston',                  'Classroom Building',                    100,  1),
('Omer',                    'Classroom Building',                    100,  1),
('Creegan',                 'Classroom Building',                    100,  1),
('SLR',                     'Student Learning Resource Center',      150,  1);
SQL;

$sqlItems = <<<'SQL'
INSERT IGNORE INTO items (name, category, quantity_available, is_active) VALUES
('Extension Cord (10m)',    'Equipment',  15,  1),
('Whiteboard Markers Set',  'Supplies',   20,  1);
SQL;

try {
    $pdo->exec($sql);
    $pdo->exec($sqlItems);
    echo '<h1>Data added successfully!</h1>';
    echo '<p>20 additional facilities and 2 items have been inserted.</p>';
    echo '<p><a href="book_facility.php">Go to Book Facility</a> | <a href="student_dashboard.php">Dashboard</a></p>';
} catch (Throwable $e) {
    echo '<h1>Error</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
