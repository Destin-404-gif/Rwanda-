/* =====================================================================
   RWANDA EXPLORER - admin.js (dashboard shell, charts, live widgets)
   ===================================================================== */
(function () {
  'use strict';

  var BASE = window.__BASE__ || '';
  var CSRF = window.__CSRF__ || '';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var url = function (p) { return BASE + p; };
  var jsonHeaders = { 'Accept': 'application/json', 'X-Requested-With': 'fetch', 'X-CSRF-Token': CSRF };
  var isMobile = function () { return window.matchMedia('(max-width: 860px)').matches; };

  /* ---------------------------------------------------------- sidebar */
  var shell = $('#admin-shell');
  var sidebar = $('#admin-sidebar');
  var backdrop = $('.admin-backdrop');

  try {
    if (shell && !isMobile() && localStorage.getItem('ae-collapsed') === '1') {
      shell.classList.add('is-collapsed');
    }
  } catch (e) { /* storage blocked */ }

  function setBackdrop(show) {
    if (!backdrop) return;
    backdrop.hidden = !show;
    backdrop.classList.toggle('is-open', show);
  }
  function openSidebar() {
    if (!sidebar) return;
    if (isMobile()) {
      sidebar.classList.add('is-open');
      setBackdrop(true);
    } else if (shell) {
      shell.classList.remove('is-collapsed');
      try { localStorage.removeItem('ae-collapsed'); } catch (e) {}
    }
    document.body.style.overflow = isMobile() ? 'hidden' : '';
  }
  function closeSidebar() {
    if (!sidebar) return;
    if (isMobile()) {
      sidebar.classList.remove('is-open');
      setBackdrop(false);
      document.body.style.overflow = '';
    } else if (shell) {
      shell.classList.add('is-collapsed');
      try { localStorage.setItem('ae-collapsed', '1'); } catch (e) {}
    }
  }
  $$('[data-sidebar-open]').forEach(function (b) { b.addEventListener('click', openSidebar); });
  $$('[data-sidebar-close]').forEach(function (b) { b.addEventListener('click', closeSidebar); });
  $$('.admin-nav-link').forEach(function (a) {
    a.addEventListener('click', function () { if (isMobile()) closeSidebar(); });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebar && sidebar.classList.contains('is-open')) closeSidebar();
  });
  window.addEventListener('resize', function () {
    if (!isMobile() && sidebar) { sidebar.classList.remove('is-open'); setBackdrop(false); document.body.style.overflow = ''; }
  });

  /* ------------------------------------------------------ quick search */
  var quick = $('#admin-quick-search');
  var quickBox = $('#quick-results');
  var quickTimer = null;

  function fillResults(box, rows, groups) {
    var i = 0;
    $$('[data-fill]', box).forEach(function (el) {
      var row = rows[i++];
      if (!row) return;
      $('strong', el).textContent = row.name || '';
      $('small', el).textContent = row.meta || '';
    });
    void groups;
  }
  function renderQuick(data) {
    if (!quickBox) return;
    if (!data.q || !data.total) {
      quickBox.hidden = false;
      quickBox.innerHTML = '<p class="quick-empty">' + (data.q ? 'No destinations, tours or hotels match that.' : 'Type to search the catalogue…') + '</p>';
      return;
    }
    var rows = [];
    var html = '';
    var groups = [
      ['destinations', 'Destinations', '/admin/destinations/', '/destinations/'],
      ['tours', 'Tours', '/admin/tours/', '/tours/'],
      ['hotels', 'Hotels', '/admin/hotels/', '/hotels/']
    ];
    groups.forEach(function (g) {
      var list = (data.results && data.results[g[0]]) || [];
      list.forEach(function (r) {
        rows.push(r);
        html += '<a href="' + url(g[2] + encodeURIComponent(r.slug)) + '" data-fill>' +
          '<strong></strong><small>' + g[1] + '</small></a>';
      });
    });
    html += '<a href="' + url('/admin/destinations?q=' + encodeURIComponent(data.q)) + '" data-fill>' +
      '<strong>Filter destinations</strong><small>All records</small></a>';
    quickBox.hidden = false;
    quickBox.innerHTML = html;
    fillResults(quickBox, rows, groups);
  }
  if (quick) {
    quick.addEventListener('input', function () {
      clearTimeout(quickTimer);
      var q = quick.value.trim();
      if (q.length < 2) { if (quickBox) quickBox.hidden = true; return; }
      quickTimer = setTimeout(function () {
        fetch(url('/api/search') + '?q=' + encodeURIComponent(q), { headers: jsonHeaders })
          .then(function (r) { return r.json(); })
          .then(renderQuick)
          .catch(function () {
            if (quickBox) { quickBox.hidden = false; quickBox.innerHTML = '<p class="quick-empty">Search is unavailable.</p>'; }
          });
      }, 240);
    });
    document.addEventListener('click', function (e) {
      if (quickBox && !e.target.closest('.topbar-search')) quickBox.hidden = true;
    });
    quick.addEventListener('keydown', function (e) { if (e.key === 'Escape') quickBox.hidden = true; });
  }

  /* ------------------------------------------------- topbar notifications */
  var notifBox = $('#topbar-notifs');
  var notifsLoaded = false;

  function timeAgo(iso) {
    if (!iso) return '';
    var t = new Date(String(iso).replace(' ', 'T')).getTime();
    if (isNaN(t)) return String(iso);
    var s = Math.floor((Date.now() - t) / 1000);
    if (s < 60) return 'just now';
    if (s < 3600) return Math.floor(s / 60) + 'm ago';
    if (s < 86400) return Math.floor(s / 3600) + 'h ago';
    return Math.floor(s / 86400) + 'd ago';
  }
  function loadNotifs() {
    if (!notifBox || notifsLoaded) return;
    notifsLoaded = true;
    fetch(url('/api/notifications'), { headers: jsonHeaders })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!notifBox) return;
        if (!data.ok) {
          notifBox.innerHTML = '<p class="notif-loading">' + (data.error || 'Could not load notifications.') + '</p>';
          return;
        }
        var list = data.notifications || [];
        if (!list.length) { notifBox.innerHTML = '<p class="notif-loading">You are all caught up.</p>'; return; }
        notifBox.innerHTML = list.slice(0, 8).map(function (n) {
          return '<a href="' + url('/admin/notifications') + '"' + ((parseInt(n.is_read, 10) || 0) ? '' : ' style="color:var(--text)"') + '>' +
            '<strong></strong><small></small></a>';
        }).join('');
        $$('a', notifBox).forEach(function (a, i) {
          var n = list[i];
          if (!n) return;
          $('strong', a).textContent = n.title || 'Notification';
          $('small', a).textContent = timeAgo(n.created_at) + ' · ' + (n.message || '');
        });
        var badge = $('.icon-badge');
        if (badge && data.unread) badge.textContent = data.unread > 99 ? '99+' : data.unread;
      })
      .catch(function () {
        notifsLoaded = false;
        if (notifBox) notifBox.innerHTML = '<p class="notif-loading">Notifications unavailable.</p>';
      });
  }
  var bell = $('.admin-topbar [data-dropdown-toggle]');
  if (bell) bell.addEventListener('click', function () { setTimeout(loadNotifs, 60); });

  /* ------------------------------------------------------ bookings chart */
  var canvas = $('#bookings-chart');
  if (canvas && canvas.getContext) {
    var data = [];
    try { data = JSON.parse(canvas.getAttribute('data-chart') || '[]'); } catch (e) { data = []; }

    function drawChart() {
      var parent = canvas.parentElement;
      var cssW = Math.max(280, (parent ? parent.clientWidth : 600));
      var cssH = parseInt(canvas.getAttribute('height'), 10) || 260;
      var dpr = window.devicePixelRatio || 1;
      canvas.width = cssW * dpr;
      canvas.height = cssH * dpr;
      canvas.style.width = cssW + 'px';
      canvas.style.height = cssH + 'px';

      var ctx = canvas.getContext('2d');
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      ctx.clearRect(0, 0, cssW, cssH);

      if (!data.length) return;

      var padL = 44, padR = 14, padT = 26, padB = 34;
      var w = cssW - padL - padR;
      var h = cssH - padT - padB;
      var maxCnt = 1;
      var maxAmt = 1;
      data.forEach(function (d) {
        maxCnt = Math.max(maxCnt, Number(d.cnt) || 0);
        maxAmt = Math.max(maxAmt, Number(d.amt) || 0);
      });

      /* grid + y labels (bookings) */
      ctx.font = '11px Poppins, sans-serif';
      ctx.strokeStyle = 'rgba(255,255,255,0.08)';
      ctx.fillStyle = 'rgba(234,240,234,0.45)';
      ctx.lineWidth = 1;
      var steps = 4;
      for (var g = 0; g <= steps; g++) {
        var y = padT + h - (h * g / steps);
        ctx.beginPath();
        ctx.moveTo(padL, y);
        ctx.lineTo(padL + w, y);
        ctx.stroke();
        ctx.textAlign = 'right';
        ctx.fillText(String(Math.round(maxCnt * g / steps)), padL - 10, y + 4);
      }

      var slot = w / data.length;
      var barW = Math.max(8, Math.min(42, slot * 0.5));

      /* booking bars */
      data.forEach(function (d, i) {
        var cx = padL + slot * i + slot / 2;
        var bh = (h * (Number(d.cnt) || 0)) / maxCnt;
        var x = cx - barW / 2;
        var y2 = padT + h - bh;

        var grad = ctx.createLinearGradient(0, y2, 0, padT + h);
        grad.addColorStop(0, '#D4F53C');
        grad.addColorStop(1, 'rgba(139,186,15,0.35)');
        ctx.fillStyle = grad;
        roundRect(ctx, x, y2, barW, bh, 5);
        ctx.fill();

        /* value on top */
        if (Number(d.cnt) > 0) {
          ctx.fillStyle = 'rgba(234,240,234,0.75)';
          ctx.textAlign = 'center';
          ctx.font = '600 11px Poppins, sans-serif';
          ctx.fillText(String(d.cnt), cx, y2 - 7);
        }

        /* x label */
        ctx.fillStyle = 'rgba(234,240,234,0.5)';
        ctx.font = '11px Poppins, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(shortMonth(d.ym), cx, padT + h + 20);
      });

      /* revenue line (dim) */
      ctx.beginPath();
      data.forEach(function (d, i) {
        var cx = padL + slot * i + slot / 2;
        var ly = padT + h - (h * (Number(d.amt) || 0)) / maxAmt;
        if (i === 0) ctx.moveTo(cx, ly); else ctx.lineTo(cx, ly);
      });
      ctx.strokeStyle = 'rgba(234,240,234,0.45)';
      ctx.lineWidth = 2;
      ctx.setLineDash([5, 5]);
      ctx.stroke();
      ctx.setLineDash([]);
      data.forEach(function (d, i) {
        var cx = padL + slot * i + slot / 2;
        var ly = padT + h - (h * (Number(d.amt) || 0)) / maxAmt;
        ctx.beginPath();
        ctx.arc(cx, ly, 3.5, 0, Math.PI * 2);
        ctx.fillStyle = '#0B0F0A';
        ctx.fill();
        ctx.strokeStyle = 'rgba(234,240,234,0.7)';
        ctx.lineWidth = 2;
        ctx.stroke();
      });
    }

    function roundRect(c, x, y, w2, h2, r) {
      var rr = Math.min(r, w2 / 2, Math.max(0, h2) / 2);
      c.beginPath();
      c.moveTo(x + rr, y);
      c.arcTo(x + w2, y, x + w2, y + h2, rr);
      c.arcTo(x + w2, y + h2, x, y + h2, rr);
      c.arcTo(x, y + h2, x, y, rr);
      c.arcTo(x, y, x + w2, y, rr);
      c.closePath();
    }
    function shortMonth(ym) {
      var parts = String(ym || '').split('-');
      if (parts.length < 2) return String(ym || '');
      var names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      var mi = parseInt(parts[1], 10) - 1;
      return (names[mi] || parts[1]) + ' ' + parts[0].slice(2);
    }

    drawChart();
    var redraw;
    window.addEventListener('resize', function () {
      clearTimeout(redraw);
      redraw = setTimeout(drawChart, 180);
    });
  }

  /* ------------------------------------------- inline list quick filter */
  $$('[data-list-filter]').forEach(function (input) {
    var listId = input.getAttribute('data-list-filter');
    var list = document.getElementById(listId);
    if (!list) return;
    input.addEventListener('input', function () {
      var q = input.value.trim().toLowerCase();
      var shown = 0;
      $$('tbody tr', list).forEach(function (tr) {
        var hit = !q || tr.textContent.toLowerCase().indexOf(q) !== -1;
        tr.hidden = !hit;
        if (hit) shown++;
      });
      var empty = $('.table-empty', list);
      if (empty) empty.hidden = shown > 0;
    });
  });
})();
