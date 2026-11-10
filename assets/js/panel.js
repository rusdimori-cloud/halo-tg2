/* halo-tg2 — perilaku dashboard kustom (Fase 8):
   drawer, dropdown, modal Hubungi Kami, saringan & urutan kelas,
   tab statistik, strip pembayaran, saklar mode gelap. */
(function () {
  "use strict";
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  /* Drawer seluler */
  var drawer = $("[data-drawer]"), scrim = $("[data-scrim]");
  function setDrawer(open) {
    if (!drawer) return;
    drawer.classList.toggle("is-open", open);
    if (scrim) scrim.hidden = !open;
  }
  var burger = $("[data-drawer-btn]");
  if (burger) burger.addEventListener("click", function () { setDrawer(true); });
  if (scrim) scrim.addEventListener("click", function () { setDrawer(false); });

  /* Dropdown: satu terbuka pada satu waktu, klik luar menutup */
  var openPair = null;
  function closeDropdowns(except) {
    $$("[data-dd-panel]").forEach(function (p) { if (p !== except) p.hidden = true; });
    if (!except) openPair = null;
  }
  /* Jaga dropdown tetap di dalam layar: balik sisi mendatar /
     tegak dan geser seperlunya sesuai sisa ruang viewport. */
  function fitDropdown(panel, btn) {
    panel.classList.remove("is-left", "is-upx");
    panel.style.marginLeft = "";
    if ("fixed" === getComputedStyle(panel).position) return; // panel notif seluler: sudah dibatasi CSS
    var M = 8, vw = window.innerWidth, vh = window.innerHeight;
    var r = panel.getBoundingClientRect();
    if (r.left < M) {
      panel.classList.add("is-left");
      r = panel.getBoundingClientRect();
    }
    if (r.right > vw - M) {
      panel.style.marginLeft = (vw - M - r.right) + "px";
      r = panel.getBoundingClientRect();
    }
    if (r.left < M) {
      var cur = parseFloat(panel.style.marginLeft || "0") || 0;
      panel.style.marginLeft = (cur + (M - r.left)) + "px";
      r = panel.getBoundingClientRect();
    }
    if (r.bottom > vh - M && !panel.classList.contains("is-up")) {
      var btnR = btn.getBoundingClientRect();
      if (btnR.top > r.height + 16) {
        panel.classList.add("is-upx");
      }
    }
  }
  /* Delegasi klik: konten panel bertukar lewat navigasi lembut,
     jadi pengendali widget tidak boleh diikat per node. */
  document.addEventListener("click", function (e) {
    var btn = e.target.closest ? e.target.closest("[data-dd-btn]") : null;
    if (!btn) return;
    var wrap = btn.closest(".ht-panel__ddwrap") || btn.parentElement;
    var panel = wrap ? $("[data-dd-panel]", wrap) : null;
    if (!panel) return;
    var willOpen = panel.hidden;
    closeDropdowns(panel);
    panel.hidden = !willOpen;
    if (willOpen) {
      openPair = { panel: panel, btn: btn };
      fitDropdown(panel, btn);
    } else {
      openPair = null;
    }
  });
  window.addEventListener("resize", function () {
    if (openPair && !openPair.panel.hidden) fitDropdown(openPair.panel, openPair.btn);
  });
  document.addEventListener("click", function (e) {
    if (e.target.closest("[data-dd-panel]") || e.target.closest("[data-dd-btn]")) return;
    closeDropdowns(null);
  });
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") { closeDropdowns(null); setDrawer(false); closeModal(); }
  });

  /* Dock Profil → buka dropdown avatar topbar */
  var dockProfil = $("[data-dock-profil]");
  if (dockProfil) dockProfil.addEventListener("click", function () {
    var btn = $(".ht-panel__top-actions [data-dd-btn].ht-panel__avatarbtn") || $(".ht-panel__avatarbtn");
    if (btn) btn.click();
  });

  /* Modal Hubungi Kami (delegasi; modal dicari saat kejadian) */
  function contactModal() { return $("[data-contact-modal]"); }
  function closeModal() { var m = contactModal(); if (m) m.hidden = true; }
  document.addEventListener("click", function (e) {
    if (!e.target.closest) return;
    if (e.target.closest("[data-contact-open]")) {
      var m = contactModal();
      if (m) { m.hidden = false; setDrawer(false); }
      return;
    }
    if (e.target.closest("[data-contact-close]")) { closeModal(); return; }
    var m2 = contactModal();
    if (m2 && e.target === m2) closeModal();
  });

  /* Saringan kelas (delegasi) */
  document.addEventListener("click", function (e) {
    var tab = e.target.closest ? e.target.closest("[data-filter]") : null;
    if (!tab) return;
    $$("[data-filter]").forEach(function (t) { t.classList.toggle("is-active", t === tab); });
    var grid = $("[data-course-grid]");
    if (!grid) return;
    var f = tab.getAttribute("data-filter"), visible = 0;
    $$(".ht-panel__course", grid).forEach(function (card) {
      var show = f === "semua" || card.getAttribute("data-status") === f;
      card.style.display = show ? "" : "none";
      if (show) visible++;
    });
    var empty = $("[data-filter-empty]");
    if (empty) empty.hidden = visible > 0;
  });

  /* Urutan kelas (delegasi; urutan awal dicatat malas per grid) */
  document.addEventListener("click", function (e) {
    var opt = e.target.closest ? e.target.closest("[data-sort]") : null;
    if (!opt) return;
    var grid = $("[data-course-grid]");
    if (!grid) return;
    if (!grid.__htOrig) grid.__htOrig = $$(".ht-panel__course", grid);
    var mode = opt.getAttribute("data-sort");
    var cards = $$(".ht-panel__course", grid);
    if (mode === "progres") cards.sort(function (a, b) { return (+b.dataset.percent) - (+a.dataset.percent); });
    else if (mode === "az") cards.sort(function (a, b) { return a.dataset.title < b.dataset.title ? -1 : 1; });
    else cards = grid.__htOrig.slice();
    cards.forEach(function (c) { grid.appendChild(c); });
    var label = $("[data-sort-label]");
    if (label) label.textContent = opt.textContent.trim();
    closeDropdowns(null);
  });

  /* Tab statistik (delegasi) */
  document.addEventListener("click", function (e) {
    var tab = e.target.closest ? e.target.closest("[data-stab]") : null;
    if (!tab) return;
    $$("[data-stab]").forEach(function (t) { t.classList.toggle("is-active", t === tab); });
    $$("[data-spanel]").forEach(function (p) { p.hidden = p.getAttribute("data-spanel") !== tab.getAttribute("data-stab"); });
  });

  /* Strip pembayaran: tutup per order, diingat di perangkat ini */
  function initStrips(root) {
    $$("[data-strip]", root || document).forEach(function (strip) {
      var key = "ht-strip-" + strip.getAttribute("data-strip");
      try { if (localStorage.getItem(key)) strip.style.display = "none"; } catch (e) {}
    });
  }
  document.addEventListener("click", function (e) {
    var x = e.target.closest ? e.target.closest("[data-strip-dismiss]") : null;
    if (!x) return;
    var strip = x.closest("[data-strip]");
    if (!strip) return;
    strip.style.display = "none";
    try { localStorage.setItem("ht-strip-" + strip.getAttribute("data-strip"), "1"); } catch (err) {}
  });
  initStrips(document);

  /* Saklar mode gelap (delegasi; kunci penyimpanan sama dengan tema) */
  document.addEventListener("click", function (e) {
    var sw = e.target.closest ? e.target.closest("[data-theme-switch]") : null;
    if (!sw) return;
    var html = document.documentElement;
    var next = html.getAttribute("data-halo-tg2-theme") === "dark" ? "light" : "dark";
    html.setAttribute("data-halo-tg2-theme", next);
    try { localStorage.setItem("halo-tg2-theme", next); } catch (err) {}
  });

  /* Tab geser di layar kecil + tips "Geser" otomatis (tour sekali tampil).
     Tab yang muat tidak disentuh; yang meluber dibungkus .ht-panel__tabsbox,
     tab aktif digeser masuk pandangan, tepi kanan memudar selama masih
     bisa digeser, dan pil tips muncul sekali per perangkat. */
  function initSwipeTabs() {
    if (!window.matchMedia("(max-width: 768px)").matches) return;
    $$(".ht-panel__tabs").forEach(function (tabs) {
      if (tabs.closest(".ht-panel__toolbar")) return;
      if (tabs.parentElement && tabs.parentElement.classList.contains("ht-panel__tabsbox")) return;
      if (tabs.scrollWidth <= tabs.clientWidth + 2) return;
      var box = document.createElement("div");
      box.className = "ht-panel__tabsbox is-scrollable";
      tabs.parentNode.insertBefore(box, tabs);
      box.appendChild(tabs);
      var hint = null, hintTimer = null;
      function hideHint() {
        if (hint) hint.classList.remove("is-show");
        if (hintTimer) { clearTimeout(hintTimer); hintTimer = null; }
        try { localStorage.setItem("ht_swipe_hint_seen", "1"); } catch (e) {}
      }
      var active = tabs.querySelector(".is-active");
      if (active) {
        tabs.scrollLeft = Math.max(0, active.offsetLeft - (tabs.clientWidth - active.offsetWidth) / 2);
      }
      function syncEnd() {
        box.classList.toggle("is-end", tabs.scrollLeft >= tabs.scrollWidth - tabs.clientWidth - 2);
      }
      syncEnd();
      tabs.addEventListener("scroll", function () { syncEnd(); hideHint(); }, { passive: true });
      var seen = false;
      try { seen = "1" === localStorage.getItem("ht_swipe_hint_seen"); } catch (e) {}
      if (!seen) {
        hint = document.createElement("span");
        hint.className = "ht-panel__swipehint";
        hint.innerHTML = 'Geser <span class="ht-panel__swipe-arrows" aria-hidden="true">\u00bb</span>';
        box.appendChild(hint);
        requestAnimationFrame(function () {
          requestAnimationFrame(function () { if (hint) hint.classList.add("is-show"); });
        });
        hintTimer = setTimeout(hideHint, 4200);
        tabs.addEventListener("touchstart", hideHint, { passive: true, once: true });
      }
    });
  }
  if ("loading" === document.readyState) {
    document.addEventListener("DOMContentLoaded", function () { requestAnimationFrame(initSwipeTabs); });
  } else {
    requestAnimationFrame(initSwipeTabs);
  }

  /* Setelah navigasi lembut menukar konten: hidupkan lagi widget
     yang bergantung pada node konten (tab geser, strip pembayaran). */
  document.addEventListener("ht:softnav:loaded", function () {
    requestAnimationFrame(function () { initSwipeTabs(); initStrips(document); });
  });
})();
