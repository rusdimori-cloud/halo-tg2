/* halo-tg2 — skrip utama tema
   Toggle tema (tombol header + switch Mode Gelap di dropdown akun),
   menu mobile fullscreen, dropdown akun header, dan cart drawer.
   Tanpa dependensi. */
(function () {
  "use strict";
  var d = document;
  var reduced = false;
  try { reduced = matchMedia("(prefers-reduced-motion: reduce)").matches; } catch (e) {}

  function $(s, c) { return (c || d).querySelector(s); }
  function $$(s, c) { return [].slice.call((c || d).querySelectorAll(s)); }

  /* ===== Tema (gelap/terang) ===== */
  var rootEl = d.documentElement;
  var themeBtn = $("[data-theme-toggle]");
  function syncThemeBtn() {
    if (!themeBtn) return;
    var dark = rootEl.getAttribute("data-halo-tg2-theme") === "dark";
    themeBtn.setAttribute("aria-label", dark ? "Aktifkan tema terang" : "Aktifkan tema gelap");
  }
  function toggleTheme() {
    var next = rootEl.getAttribute("data-halo-tg2-theme") === "dark" ? "light" : "dark";
    rootEl.setAttribute("data-halo-tg2-theme", next);
    try { localStorage.setItem("halo-tg2-theme", next); } catch (e) {}
    syncThemeBtn();
  }
  if (themeBtn) themeBtn.addEventListener("click", toggleTheme);
  $$("[data-theme-switch]").forEach(function (b) { b.addEventListener("click", toggleTheme); });
  syncThemeBtn();

  /* ===== Menu mobile (fullscreen) ===== */
  var burger = $("[data-burger]");
  var menu = $("[data-mobile-menu]");
  function setMenuLock(open) { d.body.classList.toggle("halo-tg2__menu-open", open); }
  function closeMenu() {
    if (!menu || !menu.classList.contains("is-open")) return;
    menu.classList.remove("is-open");
    burger.setAttribute("aria-expanded", "false");
    burger.setAttribute("aria-label", "Buka menu");
    setMenuLock(false);
  }
  if (burger && menu) {
    burger.addEventListener("click", function () {
      if (menu.classList.contains("is-open")) { closeMenu(); return; }
      menu.classList.add("is-open");
      burger.setAttribute("aria-expanded", "true");
      burger.setAttribute("aria-label", "Tutup menu");
      setMenuLock(true);
    });
    $$("a", menu).forEach(function (a) { a.addEventListener("click", closeMenu); });
    d.addEventListener("keydown", function (e) { if (e.key === "Escape") closeMenu(); });
  }

  /* ===== Dropdown akun header ===== */
  $$("[data-user-dd]").forEach(function (wrap) {
    var btn = $("[data-user-dd-btn]", wrap);
    var panel = $("[data-user-dd-panel]", wrap);
    if (!btn || !panel) return;
    var timer = null;
    function closeDd() {
      if (panel.hidden) return;
      btn.setAttribute("aria-expanded", "false");
      if (reduced) { panel.hidden = true; return; }
      panel.classList.add("is-closing");
      clearTimeout(timer);
      timer = setTimeout(function () { panel.hidden = true; panel.classList.remove("is-closing"); }, 160);
    }
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      if (!panel.hidden) { closeDd(); return; }
      clearTimeout(timer);
      panel.classList.remove("is-closing");
      panel.hidden = false;
      btn.setAttribute("aria-expanded", "true");
    });
    panel.addEventListener("click", function (e) { e.stopPropagation(); });
    d.addEventListener("click", function () { closeDd(); });
    d.addEventListener("keydown", function (e) { if (e.key === "Escape") closeDd(); });
    wrap._htCloseDd = closeDd;
  });

  /* ===== Cart drawer ===== */
  var drawer = $("[data-cart-drawer]");
  var scrim = $("[data-cart-scrim]");
  function setDrawer(open) {
    if (!drawer) return;
    drawer.classList.toggle("is-open", open);
    drawer.setAttribute("aria-hidden", open ? "false" : "true");
    if (scrim) {
      if (open) { scrim.hidden = false; }
      scrim.classList.toggle("is-open", open);
      if (!open) { setTimeout(function () { scrim.hidden = true; }, reduced ? 0 : 210); }
    }
    d.body.classList.toggle("halo-tg2__locked", open);
  }
  $$("[data-cart-open]").forEach(function (b) { b.addEventListener("click", function () { setDrawer(true); }); });
  $$("[data-cart-close]").forEach(function (b) { b.addEventListener("click", function () { setDrawer(false); }); });
  if (scrim) scrim.addEventListener("click", function () { setDrawer(false); });
  d.addEventListener("keydown", function (e) { if (e.key === "Escape") setDrawer(false); });
  // Woo menyegarkan isi mini-cart lewat AJAX: drawer mengambil markup terbaru
  // dari fragment bawaan, jadi tidak ada pekerjaan tambahan di sini.
})();
