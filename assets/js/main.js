/* halo-tg2 — skrip utama tema (Fase 1)
   Toggle tema, menu mobile (animasi dari Customizer via kelas body),
   dan cart drawer. Tanpa dependensi. */
(function () {
  "use strict";
  var d = document;
  var reduced = false;
  try { reduced = matchMedia("(prefers-reduced-motion: reduce)").matches; } catch (e) {}

  function $(s, c) { return (c || d).querySelector(s); }
  function $$(s, c) { return [].slice.call((c || d).querySelectorAll(s)); }

  /* ===== Toggle tema (gelap/terang) ===== */
  var rootEl = d.documentElement;
  var themeBtn = $("[data-theme-toggle]");
  function syncThemeBtn() {
    if (!themeBtn) return;
    var dark = rootEl.getAttribute("data-halo-tg2-theme") === "dark";
    themeBtn.setAttribute("aria-label", dark ? "Aktifkan tema terang" : "Aktifkan tema gelap");
  }
  if (themeBtn) {
    themeBtn.addEventListener("click", function () {
      var next = rootEl.getAttribute("data-halo-tg2-theme") === "dark" ? "light" : "dark";
      rootEl.setAttribute("data-halo-tg2-theme", next);
      try { localStorage.setItem("halo-tg2-theme", next); } catch (e) {}
      syncThemeBtn();
    });
  }
  syncThemeBtn();

  /* ===== Menu mobile ===== */
  var burger = $("[data-burger]");
  var menu = $("[data-mobile-menu]");
  var menuTimer = null;
  function closeMenu(force) {
    if (!menu || !menu.classList.contains("is-open")) return;
    burger.setAttribute("aria-expanded", "false");
    burger.setAttribute("aria-label", "Buka menu");
    if (force || reduced) {
      menu.classList.remove("is-open", "is-closing");
      return;
    }
    menu.classList.add("is-closing");
    menu.classList.remove("is-open");
    clearTimeout(menuTimer);
    menuTimer = setTimeout(function () { menu.classList.remove("is-closing"); }, 170);
  }
  if (burger && menu) {
    burger.addEventListener("click", function () {
      if (menu.classList.contains("is-open")) { closeMenu(false); return; }
      clearTimeout(menuTimer);
      menu.classList.remove("is-closing");
      menu.classList.add("is-open");
      burger.setAttribute("aria-expanded", "true");
      burger.setAttribute("aria-label", "Tutup menu");
    });
    $$("a", menu).forEach(function (a) { a.addEventListener("click", function () { closeMenu(true); }); });
    d.addEventListener("keydown", function (e) { if (e.key === "Escape") closeMenu(false); });
  }

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
