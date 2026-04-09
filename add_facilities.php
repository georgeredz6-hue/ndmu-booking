<?php
/**
 * Migration script: deduplicate facilities and add all missing ones.
 * Safe to run multiple times. Visit once, then delete.
 */
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';

// ---------- Complete facility list ----------
$allFacilities = [
    ['NDMU Auditorium',         'Main Building, Ground Floor',           800],
    ['AVP Conference Room',     'Administration Building, 2nd Floor',     40],
    ['College Gymnasium',       'Sports Complex',                       1000],
    ['Computer Laboratory 1',   'IT Building, 1st Floor',                 30],
    ['Library Study Room A',    'Library Building, 3rd Floor',            20],
    ['Multi-Purpose Hall',      'Student Center',                        200],
    ['Covered Court',           'Campus Grounds',                        500],
    ['BRC Dining Hall',         'BRC Building',                          300],
    ['BRC Convention Hall',     'BRC Building',                          500],
    ['SMC Hall',                'Student Medical Center',                200],
    ['BRC Lobby',               'BRC Building',                          100],
    ['Dance Studio 1',          'Arts Building, 1st Floor',               50],
    ['Dance Studio 2',          'Arts Building, 2nd Floor',               50],
    ['Quadrangle',              'Campus Grounds',                       1000],
    ['Reviewing Stand',         'Campus Grounds',                        300],
    ['Soccer Field',            'Sports Complex',                       2000],
    ['Function Hall',           'Event Center',                          400],
    ['Gymnasium',               'Sports Complex',                        800],
    ['Tennis Court',            'Sports Complex',                        100],
    ['Badminton Court',         'Sports Complex',                        150],
    ['Dumont',                  'Classroom Building',                    100],
    ['Doherty',                 'Classroom Building',                    100],
    ['Teston',                  'Classroom Building',                    100],
    ['Omer',                    'Classroom Building',                    100],
    ['Creegan',                 'Classroom Building',                    100],
    ['SLR',                     'Student Learning Resource Center',      150],
];

$results = [];

try {
    $pdo->beginTransaction();

    // ----- Step 1: Remove duplicate facilities (keep lowest id per name) -----
    $dupeStmt = $pdo->query(
        'SELECT name, MIN(id) AS keep_id, COUNT(*) AS cnt FROM facilities GROUP BY name HAVING cnt > 1'
    );
    $dupes = $dupeStmt->fetchAll();
    $dupesRemoved = 0;

    foreach ($dupes as $d) {
        $del = $pdo->prepare('DELETE FROM facilities WHERE name = ? AND id != ?');
        $del->execute([$d['name'], $d['keep_id']]);
        $dupesRemoved += $del->rowCount();
    }
    $results[] = "Removed {$dupesRemoved} duplicate facility rows.";

    // ----- Step 2: Insert missing facilities -----
    $existStmt = $pdo->prepare('SELECT COUNT(*) FROM facilities WHERE name = ?');
    $insStmt   = $pdo->prepare(
        'INSERT INTO facilities (name, location, capacity, is_active) VALUES (?, ?, ?, 1)'
    );
    $inserted = 0;

    foreach ($allFacilities as [$name, $location, $capacity]) {
        $existStmt->execute([$name]);
        if ((int) $existStmt->fetchColumn() === 0) {
            $insStmt->execute([$name, $location, $capacity]);
            $inserted++;
        }
    }
    $results[] = "Inserted {$inserted} missing facilities.";

    // ----- Step 3: Insert missing items -----
    $itemExist = $pdo->prepare('SELECT COUNT(*) FROM items WHERE name = ?');
    $itemIns   = $pdo->prepare(
        'INSERT INTO items (name, category, quantity_available, is_active) VALUES (?, ?, ?, 1)'
    );
    $itemsInserted = 0;
    $extraItems = [
        ['Extension Cord (10m)',   'Equipment', 15],
        ['Whiteboard Markers Set', 'Supplies',  20],
    ];

    foreach ($extraItems as [$iName, $iCat, $iQty]) {
        $itemExist->execute([$iName]);
        if ((int) $itemExist->fetchColumn() === 0) {
            $itemIns->execute([$iName, $iCat, $iQty]);
            $itemsInserted++;
        }
    }
    $results[] = "Inserted {$itemsInserted} missing items.";

    $pdo->commit();

    // ----- Show results -----
    $total = $pdo->query('SELECT COUNT(*) FROM facilities WHERE is_active = 1')->fetchColumn();
    echo '<h1>Migration completed successfully!</h1>';
    echo '<ul>';
    foreach ($results as $r) {
        echo '<li>' . htmlspecialchars($r) . '</li>';
    }
    echo '<li>Total active facilities now: ' . (int) $total . '</li>';
    echo '</ul>';
    echo '<p><a href="book_facility.php">Go to Book Facility</a> | <a href="student_dashboard.php">Dashboard</a></p>';
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo '<h1>Error</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
