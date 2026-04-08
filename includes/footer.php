<?php
declare(strict_types=1);

$footerSettings = [
    'school_name' => 'Notre Dame of Marbel University',
    'address' => 'Marbel, Koronadal City, South Cotabato, Philippines',
];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT `key`, `value` FROM system_settings WHERE `key` IN ('school_name','address')");
        foreach ($stmt->fetchAll() as $row) {
            $footerSettings[(string)$row['key']] = (string)$row['value'];
        }
    } catch (Throwable) {}
}
?>

<footer class="ndmu-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="footer-brand"><?= e($footerSettings['school_name']) ?></div>
        <div class="small" style="color:rgba(255,255,255,0.45);"><?= e($footerSettings['address']) ?></div>
      </div>
      <div class="col-md-6 text-md-end">
        <div class="d-flex gap-3 justify-content-md-end">
          <a href="faq.php">FAQ</a>
          <a href="contact.php">Contact</a>
        </div>
      </div>
    </div>
    <div class="footer-divider"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 small" style="color:rgba(255,255,255,0.35);">
      <div>&copy; <?= date('Y') ?> <?= e($footerSettings['school_name']) ?>. All rights reserved.</div>
      <div>Facility Booking System</div>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- NDMU Micro-Interactions -->
<script src="assets/js/app.js"></script>

</body>
</html>
