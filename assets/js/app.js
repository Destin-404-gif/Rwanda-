/* =====================================================================
   RWANDA EXPLORER - app.js (public site behaviour, no dependencies)
   ===================================================================== */
(function () {
  'use strict';

  var BASE = window.__BASE__ || '';
  var CSRF = window.__CSRF__ || '';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var url = function (p) { return BASE + p; };

  /* ------------------------------------------------------------ toast */
  function showToast(message, type) {
    var root = $('#toast-root');
    if (!root) return;
    var el = document.createElement('div');
    el.className = 'toast ' + (type === 'error' ? 'is-error' : 'is-ok');
    el.textContent = message;
    root.appendChild(el);
    setTimeout(function () {
      el.style.transition = 'opacity .4s, transform .4s';
      el.style.opacity = '0';
      el.style.transform = 'translateY(10px)';
      setTimeout(function () { el.remove(); }, 450);
    }, 3600);
  }
  window.showToast = showToast;

  /* -------------------------------------------------- header on scroll */
  var header = $('#site-header');
  function onScroll() {
    if (!header) return;
    header.classList.toggle('is-solid', window.scrollY > 40);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ------------------------------------------------------- overlay state
     One overlay at a time. Stacking: navbar < drawer < search < modal. */
  var drawer = $('#mobile-drawer');
  var drawerBackdrop = $('.drawer-backdrop');
  var openModalEl = null;
  var lastFocus = null;

  function anyOverlayOpen() {
    if (openModalEl) return true;
    if (searchOverlay && !searchOverlay.hidden) return true;
    if (drawer && drawer.classList.contains('is-open')) return true;
    return false;
  }
  function syncScrollLock() {
    document.body.style.overflow = anyOverlayOpen() ? 'hidden' : '';
  }
  function setDrawer(open) {
    if (!drawer) return;
    if (open) { closeAllModals(); closeSearch(); }
    drawer.classList.toggle('is-open', open);
    drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
    if (drawerBackdrop) {
      if (open) drawerBackdrop.hidden = false;
      drawerBackdrop.classList.toggle('is-open', open);
      if (!open) setTimeout(function () { drawerBackdrop.hidden = true; syncScrollLock(); }, 320);
    }
    syncScrollLock();
    $$('[data-drawer-open]').forEach(function (b) { b.setAttribute('aria-expanded', open ? 'true' : 'false'); });
  }
  $$('[data-drawer-open]').forEach(function (b) { b.addEventListener('click', function () { setDrawer(true); }); });
  $$('[data-drawer-close]').forEach(function (b) { b.addEventListener('click', function () { setDrawer(false); }); });

  /* drawer accordions */
  $$('[data-accordion]').forEach(function (group) {
    var toggle = $('[data-accordion-toggle]', group);
    var panel = $('[data-accordion-panel]', group);
    if (!toggle || !panel) return;
    toggle.addEventListener('click', function () {
      var open = panel.hidden;
      panel.hidden = !open;
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.classList.toggle('is-open', open);
    });
  });

  /* --------------------------------------------------------- dropdowns */
  $$('[data-dropdown]').forEach(function (menu) {
    var trigger = $('[data-dropdown-toggle]', menu);
    if (!trigger) return;
    trigger.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = !menu.classList.contains('is-open');
      $$('[data-dropdown]').forEach(function (m) { m.classList.remove('is-open'); });
      menu.classList.toggle('is-open', open);
      trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    menu.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { menu.classList.remove('is-open'); trigger.setAttribute('aria-expanded', 'false'); }
    });
  });
  document.addEventListener('click', function (e) {
    if (!e.target.closest('[data-dropdown]')) {
      $$('[data-dropdown]').forEach(function (m) { m.classList.remove('is-open'); });
    }
  });

  /* ------------------------------------------------------ search overlay */
  var searchOverlay = $('#search-overlay');
  var searchInput = $('#overlay-search');
  var searchResults = $('#search-results');
  var searchTimer = null;

  function openSearch() {
    if (!searchOverlay) return;
    closeAllModals();
    setDrawer(false);
    searchOverlay.hidden = false;
    searchOverlay.setAttribute('aria-hidden', 'false');
    syncScrollLock();
    setTimeout(function () { if (searchInput) searchInput.focus(); }, 30);
  }
  function closeSearch() {
    if (!searchOverlay || searchOverlay.hidden) return;
    searchOverlay.hidden = true;
    searchOverlay.setAttribute('aria-hidden', 'true');
    syncScrollLock();
  }
  $$('[data-search-open]').forEach(function (b) { b.addEventListener('click', openSearch); });
  $$('[data-search-close]').forEach(function (b) { b.addEventListener('click', closeSearch); });
  if (searchOverlay) {
    searchOverlay.addEventListener('click', function (e) {
      if (e.target === searchOverlay) closeSearch();
    });
  }

  function renderSearchResults(data) {
    if (!searchResults) return;
    var total = data.total || 0;
    if (!data.q || total === 0) { searchResults.innerHTML = data.q ? '<p class="quick-empty">No matches found.</p>' : ''; return; }
    var html = '';
    var groups = [
      ['destinations', 'Destinations', '/destinations/'],
      ['tours', 'Tours', '/tours/'],
      ['hotels', 'Hotels', '/hotels/']
    ];
    groups.forEach(function (g) {
      var list = (data.results && data.results[g[0]]) || [];
      if (!list.length) return;
      html += '<div><p class="search-group-title">' + g[1] + '</p><div class="search-list">';
      list.forEach(function (r) {
        html += '<a class="search-result glass" href="' + url(g[2] + encodeURIComponent(r.slug)) + '">' +
          '<img src="' + r.image + '" alt="" loading="lazy">' +
          '<span><strong></strong><small></small></span></a>';
      });
      html += '</div></div>';
    });
    searchResults.innerHTML = html;
    // fill texts safely (avoid HTML injection from API data)
    var anchors = $$('.search-result', searchResults);
    var i = 0;
    groups.forEach(function (g) {
      var list = (data.results && data.results[g[0]]) || [];
      list.forEach(function (r) {
        var a = anchors[i++];
        if (!a) return;
        $('strong', a).textContent = r.name || '';
        $('small', a).textContent = r.meta || '';
      });
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      clearTimeout(searchTimer);
      var q = searchInput.value.trim();
      if (q.length < 2) { if (searchResults) searchResults.innerHTML = ''; return; }
      searchTimer = setTimeout(function () {
        fetch(url('/api/search') + '?q=' + encodeURIComponent(q), {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'fetch' }
        })
          .then(function (r) { return r.json(); })
          .then(renderSearchResults)
          .catch(function () { if (searchResults) searchResults.innerHTML = '<p class="quick-empty">Search is unavailable right now.</p>'; });
      }, 260);
    });
  }
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (openModalEl) { hideModal(openModalEl); return; }
      if (searchOverlay && !searchOverlay.hidden) { closeSearch(); return; }
      setDrawer(false);
      return;
    }
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); openSearch(); }
  });

  /* -------------------------------------------------------- hero slider */
  var hero = $('[data-slider]');
  if (hero) {
    var slides = $$('.hero-slide', hero);
    var dots = $$('.hero-dot', hero);
    var strips = $$('.strip-card[data-goto]', hero);
    var counter = $('[data-counter-current]', hero);
    var count = parseInt(hero.getAttribute('data-slide-count'), 10) || slides.length || 1;
    var current = 0;
    var timer = null;
    var paused = false;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function go(index) {
      if (!count) return;
      current = ((index % count) + count) % count;
      slides.forEach(function (s, i) {
        s.classList.toggle('is-active', i === current);
        s.setAttribute('aria-hidden', i === current ? 'false' : 'true');
      });
      dots.forEach(function (d, i) {
        d.classList.toggle('is-active', i === current);
        d.setAttribute('aria-selected', i === current ? 'true' : 'false');
      });
      strips.forEach(function (s, i) { s.classList.toggle('is-active', i === current); });
      if (counter) counter.textContent = ('0' + (current + 1)).slice(-2);
    }

    function play() {
      if (reduceMotion) return;
      stop();
      timer = setInterval(function () {
        if (!paused && !document.hidden) go(current + 1);
      }, 6000);
    }
    function stop() { if (timer) { clearInterval(timer); timer = null; } }

    dots.concat(strips).forEach(function (el) {
      el.addEventListener('click', function () {
        go(parseInt(el.getAttribute('data-goto'), 10) || 0);
        play();
      });
    });

    hero.addEventListener('mouseenter', function () { paused = true; });
    hero.addEventListener('mouseleave', function () { paused = false; });
    hero.addEventListener('focusin', function () { paused = true; });
    hero.addEventListener('focusout', function () { paused = false; });
    document.addEventListener('visibilitychange', function () { paused = document.hidden; });

    hero.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { go(current + 1); play(); }
      if (e.key === 'ArrowLeft') { go(current - 1); play(); }
    });

    /* swipe */
    var touchX = null;
    hero.addEventListener('touchstart', function (e) { touchX = e.touches[0].clientX; }, { passive: true });
    hero.addEventListener('touchend', function (e) {
      if (touchX === null) return;
      var dx = e.changedTouches[0].clientX - touchX;
      if (Math.abs(dx) > 55) { go(current + (dx < 0 ? 1 : -1)); play(); }
      touchX = null;
    }, { passive: true });

    go(0);
    play();
  }

  /* ------------------------------------------------------------- modals
     One portal at the end of <body>, one backdrop and one close button per
     modal. Opening any overlay closes the others. */
  function unloadVideo(root) {
    var v = $('video', root);
    if (!v) return;
    try { v.pause(); } catch (err) {}
    v.removeAttribute('src');
    $$('source', v).forEach(function (s) { s.remove(); });
    try { v.load(); } catch (err) {}
  }
  function hideModal(m) {
    if (!m || m.hidden) return;
    unloadVideo(m);
    m.hidden = true;
    m.setAttribute('aria-hidden', 'true');
    if (openModalEl === m) openModalEl = null;
    syncScrollLock();
    if (lastFocus && document.contains(lastFocus)) {
      var target = lastFocus;
      lastFocus = null;
      try { target.focus({ preventScroll: true }); } catch (err) { target.focus(); }
    }
  }
  function closeAllModals() {
    $$('.modal').forEach(hideModal);
    openModalEl = null;
    syncScrollLock();
  }
  function showModal(modal, trigger) {
    if (!modal) return;
    var returnTo = trigger || document.activeElement;
    closeAllModals();
    closeSearch();
    setDrawer(false);
    lastFocus = returnTo;
    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');
    openModalEl = modal;
    syncScrollLock();
    var focusTarget = $('.modal-close', modal) || $('.modal-dialog', modal) || modal;
    setTimeout(function () {
      if (modal.hidden) return;
      try { focusTarget.focus({ preventScroll: true }); } catch (err) { focusTarget.focus(); }
    }, 40);
  }
  function trapFocus(e, modal) {
    var items = $$('a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), ' +
      'select:not([disabled]), textarea:not([disabled]), video[controls], [tabindex]:not([tabindex="-1"])', modal)
      .filter(function (el) { return !el.hidden && el.offsetWidth + el.offsetHeight > 0; });
    if (!items.length) return;
    var first = items[0];
    var last = items[items.length - 1];
    if (e.shiftKey && (document.activeElement === first || document.activeElement === modal)) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  }

  $$('[data-modal-close]').forEach(function (b) {
    b.addEventListener('click', function () { hideModal(b.closest('.modal')); });
  });
  $$('.modal').forEach(function (m) {
    m.addEventListener('click', function (e) {
      if (e.target.classList.contains('modal-backdrop')) hideModal(m);
    });
    m.addEventListener('keydown', function (e) { if (e.key === 'Tab') trapFocus(e, m); });
  });

  /* promo video - the video element only exists when the file is present */
  $$('[data-video-open]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var modal = document.getElementById('video-modal');
      if (!modal) return;
      showModal(modal, btn);
      var video = $('#promo-video');
      var error = $('#video-error');
      if (!video) return;
      var fail = function () {
        video.hidden = true;
        if (error) error.hidden = false;
      };
      video.onerror = fail;
      var src = video.getAttribute('data-src');
      if (!src) { fail(); return; }
      if (!video.getAttribute('src')) {
        video.setAttribute('src', src);
        try { video.load(); } catch (err) {}
      }
      var p = video.play();
      if (p && p.catch) p.catch(function () { /* autoplay blocked - controls available */ });
    });
  });

  /* lightbox */
  $$('[data-lightbox]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var modal = document.getElementById('lightbox');
      var img = $('#lightbox-img');
      var cap = $('#lightbox-caption');
      if (img) { img.src = btn.getAttribute('data-lightbox'); img.alt = btn.getAttribute('data-caption') || ''; }
      if (cap) cap.textContent = btn.getAttribute('data-caption') || '';
      showModal(modal, btn);
    });
  });

  /* --------------------------------------------------- password toggles */
  $$('[data-password-toggle]').forEach(function (btn) {
    var target = document.getElementById(btn.getAttribute('data-password-toggle'));
    if (!target) return;
    btn.addEventListener('click', function () {
      var show = target.type === 'password';
      target.type = show ? 'text' : 'password';
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  });

  /* ------------------------------------------- register password rules */
  var pwHint = $('[data-pw-hint]');
  if (pwHint) {
    var form = pwHint.closest('form');
    var pw = form ? form.querySelector('input[name="password"]') : null;
    var pw2 = form ? form.querySelector('input[name="password_confirm"]') : null;
    var update = function () {
      if (!pw) return;
      var v = pw.value;
      var len = v.length >= 8;
      var mix = /[A-Za-z]/.test(v) && /[0-9]/.test(v);
      var match = !!(pw2 && pw2.value && pw2.value === v);
      var set = function (rule, ok) {
        var li = pwHint.querySelector('[data-rule="' + rule + '"]');
        if (li) li.classList.toggle('is-ok', ok);
      };
      set('len', len); set('mix', mix); set('match', match);
    };
    if (pw) pw.addEventListener('input', update);
    if (pw2) pw2.addEventListener('input', update);
    update();
  }

  /* ------------------------------------------------- favourite buttons */
  $$('[data-fav-type]').forEach(function (btn) {
    var pressed = btn.getAttribute('aria-pressed') === 'true';
    var setLabel = function (active) {
      var span = $('span', btn);
      if (span) span.textContent = active ? 'Saved to favourites' : 'Save to favourites';
      btn.setAttribute('aria-pressed', active ? 'true' : 'false');
      btn.classList.toggle('is-active', active);
    };
    setLabel(pressed);
    btn.addEventListener('click', function () {
      if (!window.__USER__) {
        showToast('Sign in to save favourites.', 'error');
        var here = window.location.pathname.replace(BASE, '');
        setTimeout(function () { window.location.href = url('/login?next=' + encodeURIComponent(here)); }, 500);
        return;
      }
      var type = btn.getAttribute('data-fav-type');
      var id = btn.getAttribute('data-fav-id');
      var data = new FormData();
      data.append('target_type', type);
      data.append('target_id', id);
      data.append('csrf_token', CSRF);
      fetch(url('/api/favorites/toggle'), {
        method: 'POST',
        headers: { 'X-CSRF-Token': CSRF, 'X-Requested-With': 'fetch', 'Accept': 'application/json' },
        body: data
      })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res.ok) { setLabel(res.active); showToast(res.active ? 'Saved to favourites.' : 'Removed from favourites.'); }
          else showToast(res.error || 'Could not update favourites.', 'error');
        })
        .catch(function () { showToast('Could not update favourites - are you logged in?', 'error'); });
    });
  });
  /* favourites page: remove forms -> api handles redirect */

  /* ------------------------------------------------------ booking modal */
  var bookingModal = $('#booking-modal');
  $$('[data-book-tour], [data-book-hotel]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (!bookingModal) {
        var next = window.location.pathname.replace(BASE, '');
        window.location.href = url('/login?next=' + encodeURIComponent(next));
        return;
      }
      var hotelBtn = btn.hasAttribute('data-book-hotel');
      var title = btn.getAttribute('data-book-title') || 'this trip';
      var roomId = btn.getAttribute('data-book-room');
      var roomInput = $('#bk-room-id');
      if (roomInput) roomInput.value = roomId || '';
      var hotelInput = $('#bk-hotel-id');
      if (hotelBtn && hotelInput) hotelInput.value = btn.getAttribute('data-book-hotel');
      var t = $('#booking-title');
      if (t) t.textContent = 'Book ' + title;
      showModal(document.getElementById('booking-modal'), btn);
    });
  });
  $$('[data-goto-login]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var next = window.location.pathname.replace(BASE, '');
      window.location.href = url('/login?next=' + encodeURIComponent(next));
    });
  });

  /* ------------------------------------------------------- confirm forms */
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    var msg = form.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) { e.preventDefault(); return; }
    var btn = e.submitter;
    if (btn && btn.hasAttribute('data-submit-confirm')) {
      if (!window.confirm(btn.getAttribute('data-submit-confirm'))) e.preventDefault();
    }
  });

  /* -------------------------------------------------------- count-ups */
  var counters = $$('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        io.unobserve(el);
        var target = parseInt(el.getAttribute('data-count'), 10) || 0;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { el.textContent = target; return; }
        var start = null;
        var dur = 1400;
        var step = function (ts) {
          if (!start) start = ts;
          var p = Math.min(1, (ts - start) / dur);
          el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
          if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
      });
    }, { threshold: 0.4 });
    counters.forEach(function (c) { io.observe(c); });
  } else {
    counters.forEach(function (c) { c.textContent = c.getAttribute('data-count'); });
  }

  /* ---------------------------------------- server flash -> toast style */
  /* (alerts are rendered server-side; nothing to do here) */
})();
