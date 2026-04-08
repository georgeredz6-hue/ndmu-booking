<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

enforceCorrectDashboard('student');
$user = getCurrentUser();
$flash = getFlash();

$facilityBookings = [];
$itemBookings = [];

try {
    $stmt = $pdo->prepare(
        "SELECT fb.*, f.name AS facility_name
         FROM facility_bookings fb
         JOIN facilities f ON f.id = fb.facility_id
         WHERE fb.user_id = ?
         ORDER BY fb.created_at DESC, fb.id DESC"
    );
    $stmt->execute([(int)$user['id']]);
    $facilityBookings = $stmt->fetchAll();
} catch (Throwable) {}

try {
    $stmt = $pdo->prepare(
        "SELECT ib.*, i.name AS item_name, i.category
         FROM item_bookings ib
         JOIN items i ON i.id = ib.item_id
         WHERE ib.user_id = ?
         ORDER BY ib.created_at DESC, ib.id DESC"
    );
    $stmt->execute([(int)$user['id']]);
    $itemBookings = $stmt->fetchAll();
} catch (Throwable) {}

function progressForFacilityBooking(PDO $pdo, array $booking): array
{
    $chain = approvalChain();
    $doneRoles = [];
    try {
        $stmt = $pdo->prepare("SELECT role FROM facility_booking_approvals WHERE booking_id = ? AND action = 'approve' ORDER BY action_at ASC, id ASC");
        $stmt->execute([(int)$booking['id']]);
        foreach ($stmt->fetchAll() as $r) $doneRoles[] = (string)$r['role'];
    } catch (Throwable) {}

    $completed = [];
    foreach ($chain as $role) {
        $completed[$role] = in_array($role, $doneRoles, true);
    }

    if ((string)$booking['status'] === 'fully_approved') {
        foreach ($chain as $role) $completed[$role] = true;
    }
    return $completed;
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<!-- Dashboard Banner -->
<div class="page-banner">
  <div class="container">
    <div class="d-flex align-items-center gap-3">
      <img src="assets/images/ndmulogo.svg" alt="NDMU" width="48" height="48" style="object-fit:contain;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
      <div>
        <h1 class="h3 fw-bold mb-0" style="color:#fff;">Welcome, <?= e((string)$user['name']) ?></h1>
        <div class="text-white-50 small">NDMU Facility Booking System Dashboard</div>
      </div>
    </div>
  </div>
</div>

<div class="container pb-5">
  <?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?> fade-up"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <!-- Quick Actions -->
  <div class="row g-3 mb-4 fade-up">
    <div class="col-sm-6 col-md-3">
      <a href="book_facility.php" class="card card-lift text-decoration-none h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="card-icon icon-emerald"><i class="fa-regular fa-calendar-days"></i></div>
          <div>
            <div class="fw-bold small" style="color:var(--ndmu-navy);">Book Facility</div>
            <div class="text-muted" style="font-size:0.75rem;">Reserve a venue</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-md-3">
      <a href="book_item.php" class="card card-lift text-decoration-none h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="card-icon icon-info"><i class="fa-solid fa-box-open"></i></div>
          <div>
            <div class="fw-bold small" style="color:var(--ndmu-navy);">Borrow Item</div>
            <div class="text-muted" style="font-size:0.75rem;">Request equipment</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-md-3">
      <a href="notifications.php" class="card card-lift text-decoration-none h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="card-icon icon-gold"><i class="fa-solid fa-bell"></i></div>
          <div>
            <div class="fw-bold small" style="color:var(--ndmu-navy);">Notifications</div>
            <div class="text-muted" style="font-size:0.75rem;">View updates</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6 col-md-3">
      <a href="profile.php" class="card card-lift text-decoration-none h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="card-icon icon-navy"><i class="fa-solid fa-user"></i></div>
          <div>
            <div class="fw-bold small" style="color:var(--ndmu-navy);">My Profile</div>
            <div class="text-muted" style="font-size:0.75rem;">Account settings</div>
          </div>
        </div>
      </a>
    </div>
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs fade-up" id="dashTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="fac-tab" data-bs-toggle="tab" data-bs-target="#fac" type="button" role="tab">
        <i class="fa-regular fa-calendar-days me-1"></i>Facility Bookings
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="item-tab" data-bs-toggle="tab" data-bs-target="#item" type="button" role="tab">
        <i class="fa-solid fa-box-open me-1"></i>Item Bookings
      </button>
    </li>
  </ul>

  <div class="tab-content fade-up" id="dashTabsContent">
    <!-- Facility Bookings Tab -->
    <div class="tab-pane fade show active" id="fac" role="tabpanel" aria-labelledby="fac-tab">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted small">Your facility booking requests</div>
        <a class="btn btn-sm btn-warning" href="book_facility.php"><i class="fa-solid fa-plus me-1"></i>New Booking</a>
      </div>

      <?php if (!$facilityBookings): ?>
        <div class="alert alert-info">No facility bookings yet. Start by creating your first reservation.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Facility</th>
                <th>Dates</th>
                <th>Status</th>
                <th>Reviewing</th>
                <th style="min-width:320px;">Progress</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($facilityBookings as $b): ?>
                <?php $progress = progressForFacilityBooking($pdo, $b); ?>
                <tr>
                  <td class="fw-semibold"><?= e((string)$b['title']) ?></td>
                  <td><?= e((string)$b['facility_name']) ?></td>
                  <td>
                    <div class="small"><?= e((string)$b['date_start']) ?> &rarr; <?= e((string)$b['date_end']) ?></div>
                    <div class="text-muted small"><?= e((string)$b['time_start']) ?> &rarr; <?= e((string)$b['time_end']) ?></div>
                  </td>
                  <td><?= statusBadge((string)$b['status']) ?></td>
                  <td><?= approvalRoleBadge((string)($b['current_approval_role'] ?? '')) ?></td>
                  <td>
                    <div class="d-flex flex-wrap gap-1">
                      <?php foreach (approvalChain() as $role): ?>
                        <?php $done = !empty($progress[$role]); ?>
                        <span class="badge <?= $done ? 'bg-success' : 'bg-light' ?>">
                          <?= $done ? '<i class="fa-solid fa-check" style="font-size:0.625rem;"></i>' : '<i class="fa-solid fa-circle" style="font-size:0.375rem;opacity:0.4;"></i>' ?>
                          <?= e(ucwords(str_replace('_',' ', $role))) ?>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  </td>
                  <td class="text-end">
                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                      <?php if ((string)$b['status'] === 'pending'): ?>
                        <a class="btn btn-sm btn-outline-primary" href="edit_facility_booking.php?id=<?= (int)$b['id'] ?>">Edit</a>
                      <?php endif; ?>
                      <?php if (!in_array((string)$b['status'], ['fully_approved','rejected'], true)): ?>
                        <form method="post" action="cancel_booking.php" class="d-inline">
                          <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
                          <input type="hidden" name="type" value="facility">
                          <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                          <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this booking?')">Cancel</button>
                        </form>
                      <?php endif; ?>
                      <?php if ((string)$b['status'] === 'fully_approved'): ?>
                        <a class="btn btn-sm btn-success" target="_blank" href="download_receipt.php?id=<?= (int)$b['id'] ?>">
                          <i class="fa-solid fa-download me-1"></i>Receipt
                        </a>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-semibold small">Availability Calendar</div>
          <div class="text-muted" style="font-size:0.75rem;">Auto-refreshes every 30s</div>
        </div>
        <div class="card">
          <div class="card-body p-2">
            <div id="miniFacilityCalendar" class="mini-calendar"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Item Bookings Tab -->
    <div class="tab-pane fade" id="item" role="tabpanel" aria-labelledby="item-tab">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted small">Your item borrowing requests</div>
        <a class="btn btn-sm btn-warning" href="book_item.php"><i class="fa-solid fa-plus me-1"></i>New Request</a>
      </div>

      <?php if (!$itemBookings): ?>
        <div class="alert alert-info">No item bookings yet. Start by requesting equipment.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Item</th>
                <th>Category</th>
                <th>Dates</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Reviewing</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($itemBookings as $b): ?>
                <tr>
                  <td class="fw-semibold"><?= e((string)$b['item_name']) ?></td>
                  <td><?= e((string)($b['category'] ?? '')) ?></td>
                  <td>
                    <div class="small"><?= e((string)$b['borrow_date']) ?> &rarr; <?= e((string)$b['return_date']) ?></div>
                    <div class="text-muted small"><?= e((string)$b['borrow_time']) ?> &rarr; <?= e((string)$b['return_time']) ?></div>
                  </td>
                  <td><?= (int)$b['quantity_needed'] ?></td>
                  <td><?= statusBadge((string)$b['status']) ?></td>
                  <td><?= approvalRoleBadge((string)($b['current_approval_role'] ?? '')) ?></td>
                  <td class="text-end">
                    <?php if (!in_array((string)$b['status'], ['fully_approved','rejected'], true)): ?>
                      <form method="post" action="cancel_booking.php" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
                        <input type="hidden" name="type" value="item">
                        <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this request?')">Cancel</button>
                      </form>
                    <?php else: ?>
                      <span class="text-muted small">&mdash;</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-semibold small">Availability Calendar</div>
          <div class="text-muted" style="font-size:0.75rem;">Auto-refreshes every 30s</div>
        </div>
        <div class="card">
          <div class="card-body p-2">
            <div id="miniItemCalendar" class="mini-calendar"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
  (function(){
    function buildMiniCalendar(elId, feedUrl){
      const el = document.getElementById(elId);
      if(!el) return null;

      const cal = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        height: 310,
        headerToolbar: { left: 'prev,next', center: 'title', right: '' },
        dayMaxEvents: 2,
        fixedWeekCount: false,
        events: (info, success, failure) => {
          fetch(feedUrl, {headers:{'Accept':'application/json'}})
            .then(r => r.json())
            .then(success)
            .catch(failure);
        }
      });
      cal.render();
      return cal;
    }

    const facCal = buildMiniCalendar('miniFacilityCalendar', 'get_facility_events.php');
    const itemCal = buildMiniCalendar('miniItemCalendar', 'get_item_events.php');

    setInterval(() => {
      facCal?.refetchEvents();
      itemCal?.refetchEvents();
    }, 30000);
  })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
