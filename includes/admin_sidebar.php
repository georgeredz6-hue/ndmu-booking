<?php
declare(strict_types=1);
$sidebarPage = basename($_SERVER['PHP_SELF'] ?? '');
?>
<div class="admin-layout">
  <aside class="admin-sidebar" id="adminSidebar">
    <ul class="sidebar-nav">
      <li>
        <a class="nav-link <?= $sidebarPage === 'admin_panel.php' ? 'active' : '' ?>" href="admin_panel.php">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
      </li>
      <li>
        <a class="nav-link <?= $sidebarPage === 'admin_users.php' ? 'active' : '' ?>" href="admin_users.php">
          <i class="fa-solid fa-users"></i> Users
        </a>
      </li>
      <li>
        <a class="nav-link <?= $sidebarPage === 'admin_facilities.php' ? 'active' : '' ?>" href="admin_facilities.php">
          <i class="fa-solid fa-building"></i> Facilities
        </a>
      </li>
      <li>
        <a class="nav-link <?= $sidebarPage === 'admin_items.php' ? 'active' : '' ?>" href="admin_items.php">
          <i class="fa-solid fa-box-open"></i> Items
        </a>
      </li>
      <li>
        <a class="nav-link <?= $sidebarPage === 'admin_bookings.php' ? 'active' : '' ?>" href="admin_bookings.php">
          <i class="fa-regular fa-calendar-days"></i> Bookings
        </a>
      </li>
      <li>
        <a class="nav-link <?= $sidebarPage === 'admin_maintenance.php' ? 'active' : '' ?>" href="admin_maintenance.php">
          <i class="fa-solid fa-wrench"></i> Maintenance
        </a>
      </li>
      <li>
        <a class="nav-link <?= $sidebarPage === 'admin_reports.php' ? 'active' : '' ?>" href="admin_reports.php">
          <i class="fa-solid fa-chart-bar"></i> Reports
        </a>
      </li>
      <li>
        <a class="nav-link <?= $sidebarPage === 'admin_faq.php' ? 'active' : '' ?>" href="admin_faq.php">
          <i class="fa-solid fa-circle-question"></i> FAQ
        </a>
      </li>
      <li>
        <a class="nav-link <?= $sidebarPage === 'admin_messages.php' ? 'active' : '' ?>" href="admin_messages.php">
          <i class="fa-solid fa-envelope"></i> Messages
        </a>
      </li>
      <li>
        <a class="nav-link <?= $sidebarPage === 'admin_settings.php' ? 'active' : '' ?>" href="admin_settings.php">
          <i class="fa-solid fa-gear"></i> Settings
        </a>
      </li>
    </ul>
  </aside>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <main class="admin-main">
