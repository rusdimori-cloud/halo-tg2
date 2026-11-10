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
  function closeDropdowns(except) {
    $$("[data-dd-panel]").forEach(function (p) { if (p !== except) p.hidden = true; });
  }
  $$("[data-dd-btn]").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      var wrap = btn.closest(".ht-panel__ddwrap") || btn.parentElement;
      var panel = wrap ? $("[data-dd-panel]", wrap) : null;
      if (!panel) return;
      var willOpen = panel.hidden;
      closeDropdowns(panel);
      panel.hidden = !willOpen;
    });
  });
  document.addEventListener("click", function (e) {
    if (!e.target.closest("[data-dd-panel]")) closeDropdowns(null);
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

  /* Modal Hubungi Kami */
  var modal = $("[data-contact-modal]");
  function closeModal() { if (modal) modal.hidden = true; }
  $$("[data-contact-open]").forEach(function (b) {
    b.addEventListener("click", function () { if (modal) { modal.hidden = false; setDrawer(false); } });
  });
  $$("[data-contact-close]").forEach(function (b) { b.addEventListener("click", closeModal); });
  if (modal) modal.addEventListener("click", function (e) { if (e.target === modal) closeModal(); });

  /* Saringan kelas */
  var grid = $("[data-course-grid]");
  $$("[data-filter]").forEach(function (tab) {
    tab.addEventListener("click", function () {
      $$("[data-filter]").forEach(function (t) { t.classList.toggle("is-active", t === tab); });
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
  });

  /* Urutan kelas */
  var originalOrder = grid ? $$(".ht-panel__course", grid) : [];
  $$("[data-sort]").forEach(function (opt) {
    opt.addEventListener("click", function () {
      if (!grid) return;
      var mode = opt.getAttribute("data-sort");
      var cards = $$(".ht-panel__course", grid);
      if (mode === "progres") cards.sort(function (a, b) { return (+b.dataset.percent) - (+a.dataset.percent); });
      else if (mode === "az") cards.sort(function (a, b) { return a.dataset.title < b.dataset.title ? -1 : 1; });
      else cards = originalOrder.slice();
      cards.forEach(function (c) { grid.appendChild(c); });
      var label = $("[data-sort-label]");
      if (label) label.textContent = opt.textContent.trim();
      closeDropdowns(null);
    });
  });

  /* Tab statistik */
  $$("[data-stab]").forEach(function (tab) {
    tab.addEventListener("click", function () {
      $$("[data-stab]").forEach(function (t) { t.classList.toggle("is-active", t === tab); });
      $$("[data-spanel]").forEach(function (p) { p.hidden = p.getAttribute("data-spanel") !== tab.getAttribute("data-stab"); });
    });
  });

  /* Strip pembayaran: tutup per order, diingat di perangkat ini */
  $$("[data-strip]").forEach(function (strip) {
    var key = "ht-strip-" + strip.getAttribute("data-strip");
    try { if (localStorage.getItem(key)) strip.style.display = "none"; } catch (e) {}
    var x = $("[data-strip-dismiss]", strip);
    if (x) x.addEventListener("click", function () {
      strip.style.display = "none";
      try { localStorage.setItem(key, "1"); } catch (e) {}
    });
  });

  /* Saklar mode gelap (kunci penyimpanan sama dengan tema) */
  $$("[data-theme-switch]").forEach(function (sw) {
    sw.addEventListener("click", function () {
      var html = document.documentElement;
      var next = html.getAttribute("data-halo-tg2-theme") === "dark" ? "light" : "dark";
      html.setAttribute("data-halo-tg2-theme", next);
      try { localStorage.setItem("halo-tg2-theme", next); } catch (e) {}
    });
  });
})();
