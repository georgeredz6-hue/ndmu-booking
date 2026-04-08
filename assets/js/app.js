/**
 * NDMU Booking System — Micro-Interactions & Animations
 * Provides scroll-triggered animations, navbar scroll behavior,
 * button ripple effects, and smooth UX enhancements.
 */
(function () {
  'use strict';

  /* ---------- Utility Helpers ---------- */
  const qs = (sel, root) => (root || document).querySelector(sel);
  const qsa = (sel, root) => Array.from((root || document).querySelectorAll(sel));

  /* ---------- Navbar Scroll Effect ---------- */
  function initNavbarScroll() {
    const nav = qs('.ndmu-navbar');
    if (!nav) return;

    let ticking = false;
    function onScroll() {
      if (!ticking) {
        window.requestAnimationFrame(function () {
          nav.classList.toggle('scrolled', window.scrollY > 24);
          ticking = false;
        });
        ticking = true;
      }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---------- Scroll-Triggered Fade-Up ---------- */
  function initFadeUp() {
    const els = qsa('.fade-up');
    if (!els.length) return;

    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('visible');
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
      );
      els.forEach(function (el) { observer.observe(el); });
    } else {
      els.forEach(function (el) { el.classList.add('visible'); });
    }
  }

  /* ---------- Button Ripple Effect ---------- */
  function initRipple() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.btn');
      if (!btn) return;

      var rect = btn.getBoundingClientRect();
      var size = Math.max(rect.width, rect.height);
      var x = e.clientX - rect.left - size / 2;
      var y = e.clientY - rect.top - size / 2;

      var circle = document.createElement('span');
      circle.className = 'ripple-circle';
      circle.style.width = circle.style.height = size + 'px';
      circle.style.left = x + 'px';
      circle.style.top = y + 'px';
      btn.style.position = 'relative';
      btn.style.overflow = 'hidden';
      btn.appendChild(circle);

      circle.addEventListener('animationend', function () {
        circle.remove();
      });
    });
  }

  /* ---------- Password Toggle ---------- */
  function initPasswordToggles() {
    qsa('[data-toggle-password]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var target = qs(btn.getAttribute('data-toggle-password'));
        if (!target) return;
        var isPwd = target.getAttribute('type') === 'password';
        target.setAttribute('type', isPwd ? 'text' : 'password');
        var icon = btn.querySelector('i');
        if (icon) {
          icon.classList.toggle('fa-eye', !isPwd);
          icon.classList.toggle('fa-eye-slash', isPwd);
        }
      });
    });
  }

  /* ---------- Smooth Page Enter ---------- */
  function initPageEnter() {
    var main = qs('main') || qs('.container:not(.ndmu-navbar .container)');
    if (main) {
      main.classList.add('page-enter');
    }
  }

  /* ---------- Mobile Sidebar Toggle ---------- */
  function initSidebarToggle() {
    var toggle = qs('[data-toggle-sidebar]');
    var sidebar = qs('.admin-sidebar');
    var overlay = qs('.sidebar-overlay');

    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('show');
      if (overlay) overlay.classList.toggle('show');
    });

    if (overlay) {
      overlay.addEventListener('click', function () {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
      });
    }
  }

  /* ---------- Counter Animation for Stat Cards ---------- */
  function initCounterAnimation() {
    var statValues = qsa('.stat-value[data-count]');
    if (!statValues.length) return;

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              animateCounter(entry.target);
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.5 }
      );
      statValues.forEach(function (el) { observer.observe(el); });
    }
  }

  function animateCounter(el) {
    var target = parseInt(el.getAttribute('data-count'), 10) || 0;
    var duration = 800;
    var start = performance.now();

    function step(now) {
      var progress = Math.min((now - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target).toLocaleString();
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  /* ---------- Notification Refresh ---------- */
  function initNotificationRefresh() {
    var badge = qs('#notifBadge');
    if (!badge) return;

    async function refreshCount() {
      try {
        var res = await fetch('get_notification_count.php', {
          headers: { 'Accept': 'application/json' },
        });
        if (!res.ok) return;
        var data = await res.json();
        var c = Number(data.count || 0);
        if (c > 0) {
          badge.style.display = '';
          badge.textContent = String(c);
          badge.classList.add('notif-badge');
        } else {
          badge.style.display = 'none';
        }
      } catch (_) { /* silent */ }
    }
    setInterval(refreshCount, 30000);
  }

  /* ---------- Init All ---------- */
  function init() {
    initNavbarScroll();
    initFadeUp();
    initRipple();
    initPasswordToggles();
    initPageEnter();
    initSidebarToggle();
    initCounterAnimation();
    initNotificationRefresh();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
