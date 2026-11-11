/* halo-tg2: perilaku dashboard kustom (Fase 8):
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

  /* Pil penanda aktif dock + bilah biru di tepi atas tab aktif:
     keduanya meluncur dengan pantulan ringan. Pil bergerak
     OPTIMISTIS sejak tautan diklik (tidak menunggu server), lalu
     dikonfirmasi oleh posisi server saat navigasi lembut selesai.
     Posisi terakhir disimpan di luar node karena isi dock bisa
     bertukar saat sinkronisasi navigasi lembut. */
  var dockEl = $(".ht-panel__dock");
  var dockPill = null, dockBar = null, pillPos = null;
  function dockEls() {
    if (dockPill && dockEl.contains(dockPill)) return;
    /* Pengaman: buang pil/bilah asing yang mungkin sudah ada di dalam
       dock (misalnya terbawa HTML pulihan lama), supaya isinya selalu
       tepat satu pasang yang terlacak di sini. */
    Array.prototype.forEach.call(dockEl.querySelectorAll(".ht-panel__dockpill, .ht-panel__dockbar"), function (n) { n.remove(); });
    dockPill = document.createElement("span");
    dockPill.className = "ht-panel__dockpill";
    dockPill.setAttribute("aria-hidden", "true");
    dockEl.appendChild(dockPill);
    dockBar = document.createElement("span");
    dockBar.className = "ht-panel__dockbar";
    dockBar.setAttribute("aria-hidden", "true");
    dockEl.appendChild(dockBar);
    dockEl.classList.add("has-pill");
    if (pillPos) applyDockPos(pillPos, false);
  }
  function applyDockPos(pos, animate) {
    dockEls();
    [dockPill, dockBar].forEach(function (el) { el.style.transition = animate ? "" : "none"; });
    dockPill.style.opacity = pos.visible ? "1" : "0";
    dockPill.style.transform = "translate(" + pos.x + "px," + pos.y + "px)";
    dockPill.style.width = pos.w + "px";
    dockPill.style.height = pos.h + "px";
    var bw = Math.max(34, Math.round(pos.w * 0.44));
    dockBar.style.opacity = pos.visible ? "1" : "0";
    dockBar.style.transform = "translateX(" + Math.round(pos.x + (pos.w - bw) / 2) + "px)";
    dockBar.style.width = bw + "px";
    if (!animate) { void dockPill.offsetWidth; dockPill.style.transition = ""; dockBar.style.transition = ""; }
    pillPos = pos;
  }
  function dockPosFor(item) {
    var dr = dockEl.getBoundingClientRect(), ar = item.getBoundingClientRect();
    return { x: ar.left - dr.left, y: ar.top - dr.top, w: ar.width, h: ar.height, visible: true };
  }
  function dockPlace(animate) {
    if (!dockEl) return;
    var active = dockEl.querySelector("a.is-active");
    if (!active) {
      dockEls();
      if (pillPos) applyDockPos(Object.assign({}, pillPos, { visible: false }), animate);
      return;
    }
    applyDockPos(dockPosFor(active), animate);
  }
  function dockViewItem(view) {
    var found = null;
    $$(".ht-panel__dock a[href]").forEach(function (l) {
      var v = "beranda";
      try { v = new URL(l.href, location.href).searchParams.get("tampilan") || "beranda"; } catch (e) {}
      if (v === view) found = l;
    });
    return found;
  }
  if (dockEl) {
    requestAnimationFrame(function () { dockPlace(false); });
    window.addEventListener("resize", function () { dockPlace(false); });
    document.addEventListener("ht:softnav:loaded", function () {
      dockPlace(true);
      /* Koreksi senyap setelah luncuran selesai: lebar dock bisa
         bergeser seukuran scrollbar saat tinggi konten berubah. */
      setTimeout(function () { dockPlace(false); }, 560);
    });
    /* Gerak optimistis: pil + bilah meluncur begitu tautan panel
       diklik, sebelum jawaban server tiba. */
    document.addEventListener("click", function (e) {
      if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      var a = e.target.closest ? e.target.closest("a[href]") : null;
      if (!a) return;
      var u;
      try { u = new URL(a.href, location.href); } catch (err) { return; }
      if (u.pathname.replace(/\/$/, "") !== location.pathname.replace(/\/$/, "")) return;
      var view = u.searchParams.get("tampilan") || "beranda";
      var item = dockViewItem(view);
      if (item) applyDockPos(dockPosFor(item), true);
      else if (pillPos) applyDockPos(Object.assign({}, pillPos, { visible: false }), true);
    });
  }

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
    $$("[data-spanel]").forEach(function (p) {
      var show = p.getAttribute("data-spanel") === tab.getAttribute("data-stab");
      p.hidden = !show;
      if (show) { p.classList.remove("ht-stab-enter"); void p.offsetWidth; p.classList.add("ht-stab-enter"); }
    });
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

  /* Saklar tema tersegmentasi matahari | bulan (delegasi; klik segmen
     menetapkan tema secara eksplisit; kunci penyimpanan sama dengan tema).
     Keadaan aktif tombol murni dari atribut tema <html> lewat CSS;
     aria-pressed disinkronkan di sini. */
  function syncThemeSegs() {
    var cur = document.documentElement.getAttribute("data-halo-tg2-theme") === "dark" ? "dark" : "light";
    $$("[data-theme-set]").forEach(function (b) {
      b.setAttribute("aria-pressed", b.getAttribute("data-theme-set") === cur ? "true" : "false");
    });
  }
  document.addEventListener("click", function (e) {
    var btn = e.target.closest ? e.target.closest("[data-theme-set]") : null;
    if (!btn) return;
    var html = document.documentElement;
    var val = btn.getAttribute("data-theme-set") === "dark" ? "dark" : "light";
    if (html.getAttribute("data-halo-tg2-theme") !== val) {
      html.setAttribute("data-halo-tg2-theme", val);
      try { localStorage.setItem("halo-tg2-theme", val); } catch (err) {}
    }
    syncThemeSegs();
  });
  syncThemeSegs();
  document.addEventListener("ht:softnav:loaded", syncThemeSegs);

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

  /* ---------- Katalog course (view Course) ----------
     Pencarian aktif mulai 3 huruf, saringan kategori, dan alih
     grid/list: semua di sisi klien dari kartu yang dirender
     server; skeleton singkat menandai setiap perubahan saringan. */
  var shopQ = "", shopCat = "semua", shopTimer = null, shopSkelTimer = null;
  function shopApply() {
    var grid = $("[data-shop-grid]");
    if (!grid) return;
    var q = shopQ.trim().toLowerCase();
    var searching = q.length >= 3;
    var n = 0;
    $$(".ht-shop__card", grid).forEach(function (card) {
      var okCat = shopCat === "semua" || (" " + (card.getAttribute("data-cats") || "") + " ").indexOf(" " + shopCat + " ") >= 0;
      var okQ = !searching || (card.getAttribute("data-search") || "").indexOf(q) >= 0;
      var show = okCat && okQ;
      card.style.display = show ? "" : "none";
      if (show) n++;
    });
    var count = $("[data-shop-count]");
    if (count) count.textContent = n + " course";
    var empty = $("[data-shop-empty]");
    if (empty) empty.hidden = n > 0;
    var hint = $("[data-shop-hint]");
    if (hint) hint.hidden = !(q.length > 0 && q.length < 3);
  }
  function shopRefresh(withSkel) {
    var skel = $("[data-shop-skel]"), grid = $("[data-shop-grid]");
    if (withSkel && skel && grid) {
      skel.hidden = false;
      clearTimeout(shopSkelTimer);
      shopSkelTimer = setTimeout(function () { skel.hidden = true; shopApply(); }, 380);
    } else {
      shopApply();
    }
  }
  document.addEventListener("input", function (e) {
    if (!e.target.matches || !e.target.matches("[data-shop-search]")) return;
    shopQ = e.target.value || "";
    clearTimeout(shopTimer);
    if (shopQ.trim().length >= 3) {
      shopTimer = setTimeout(function () { shopRefresh(true); }, 260);
    } else {
      shopTimer = setTimeout(function () { shopRefresh(false); }, 180);
    }
  });
  document.addEventListener("click", function (e) {
    if (!e.target.closest) return;
    var vt = e.target.closest("[data-shop-view]");
    if (vt) {
      var mode = vt.getAttribute("data-shop-view") || "grid";
      var grid = $("[data-shop-grid]");
      if (grid) grid.classList.toggle("is-list", mode === "list");
      $$("[data-shop-view]").forEach(function (b) { b.classList.toggle("is-active", b === vt); });
      try { localStorage.setItem("ht-shop-view", mode); } catch (err) {}
    }
  });
  /* Tombol hati wishlist di sampul kartu katalog: alih status
     secara optimistis (dengan animasi pop), lalu sinkronkan ke
     server lewat AJAX; gagal = kembalikan state semula. */
  document.addEventListener("click", function (e) {
    var btn = e.target.closest ? e.target.closest("[data-wish-btn]") : null;
    if (!btn) return;
    e.preventDefault();
    var cid = btn.getAttribute("data-course-id");
    var on = btn.classList.toggle("is-on");
    btn.setAttribute("aria-pressed", on ? "true" : "false");
    btn.classList.remove("is-anim");
    void btn.offsetWidth;
    btn.classList.add("is-anim");
    if (!window.HT_PANEL || !window.fetch || !cid) return;
    var fd = new FormData();
    fd.append("action", "halo_tg2_wishlist_toggle");
    fd.append("nonce", window.HT_PANEL.wishNonce);
    fd.append("course_id", cid);
    fetch(window.HT_PANEL.ajaxUrl, { method: "POST", body: fd, credentials: "same-origin" })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        if (!j || !j.success) throw new Error("bad");
        var serverOn = j.data && j.data.state === "added";
        if (serverOn !== on) {
          btn.classList.toggle("is-on", serverOn);
          btn.setAttribute("aria-pressed", serverOn ? "true" : "false");
        }
      })
      .catch(function () {
        btn.classList.toggle("is-on", !on);
        btn.setAttribute("aria-pressed", !on ? "true" : "false");
      });
  });
  function shopInit() {
    if (!$("[data-shop-grid]")) return;
    shopQ = ""; shopCat = "semua";
    var mode = "grid";
    try { mode = localStorage.getItem("ht-shop-view") || "grid"; } catch (e) {}
    var grid = $("[data-shop-grid]");
    grid.classList.toggle("is-list", mode === "list");
    $$("[data-shop-view]").forEach(function (b) { b.classList.toggle("is-active", b.getAttribute("data-shop-view") === mode); });
  }
  shopInit();

  /* ---------- Editor foto profil & sampul (Pengaturan) ----------
     Modal dengan crop interaktif: pilih foto, geser + zoom,
     hasil crop diunggah lewat AJAX dan dipasang sebagai foto
     profil / sampul (meta Tutor). */
  var phTarget = "profil", phImg = null, phNat = { w: 0, h: 0 },
      phScale = 1, phMin = 1, phZoom = 1, phTx = 0, phTy = 0, phUrl = null;
  function phEls() {
    return {
      modal: $("[data-photo-modal]"), empty: $("[data-photo-empty]"), work: $("[data-photo-work]"),
      box: $("[data-crop-box]"), img: $("[data-crop-img]"), zoom: $("[data-crop-zoom]"),
      save: $("[data-photo-save]"), msg: $("[data-photo-msg]"), file: $("[data-photo-file]")
    };
  }
  function phFrame() {
    var e = phEls();
    var w = e.work ? e.work.clientWidth : 300;
    if (phTarget === "profil") { var s = Math.min(300, w); return { w: s, h: s }; }
    return { w: w, h: Math.round(w / 3) };
  }
  function phClamp() {
    var f = phFrame();
    var iw = phNat.w * phScale, ih = phNat.h * phScale;
    phTx = Math.min(0, Math.max(f.w - iw, phTx));
    phTy = Math.min(0, Math.max(f.h - ih, phTy));
  }
  function phRender() {
    var e = phEls();
    if (!e.img || !phImg) return;
    e.img.style.width = phNat.w + "px";
    e.img.style.height = phNat.h + "px";
    e.img.style.transform = "translate(" + phTx + "px," + phTy + "px) scale(" + phScale + ")";
  }
  function phLayout(refit) {
    var e = phEls();
    if (!e.box) return;
    var f = phFrame();
    e.box.style.width = f.w + "px";
    e.box.style.height = f.h + "px";
    e.box.classList.toggle("is-profil", phTarget === "profil");
    e.box.classList.toggle("is-cover", phTarget === "cover");
    if (!phImg) return;
    phMin = Math.max(f.w / phNat.w, f.h / phNat.h);
    if (refit) {
      phZoom = 1;
      if (e.zoom) e.zoom.value = "1";
      phScale = phMin;
      phTx = (f.w - phNat.w * phScale) / 2;
      phTy = (f.h - phNat.h * phScale) / 2;
    } else {
      phScale = phMin * phZoom;
      phClamp();
    }
    phRender();
  }
  function phMsg(text, ok) {
    var e = phEls();
    if (!e.msg) return;
    e.msg.hidden = !text;
    e.msg.textContent = text || "";
    e.msg.classList.toggle("is-ok", !!ok);
    e.msg.classList.toggle("is-error", !!text && !ok);
  }
  function phOpen(target) {
    var e = phEls();
    if (!e.modal) return;
    phSetTarget(target || "profil");
    e.modal.hidden = false;
    requestAnimationFrame(function () { e.modal.classList.add("is-open"); phLayout(false); });
  }
  function phClose() {
    var e = phEls();
    if (!e.modal) return;
    e.modal.classList.remove("is-open");
    e.modal.hidden = true;
  }
  function phSetTarget(t) {
    phTarget = t === "cover" ? "cover" : "profil";
    $$("[data-photo-target]").forEach(function (b) { b.classList.toggle("is-active", b.getAttribute("data-photo-target") === phTarget); });
    phLayout(true);
  }
  function phLoadFile(file) {
    if (!file) return;
    if (!/^image\//.test(file.type)) { phMsg("Berkas itu bukan gambar."); return; }
    if (file.size > 10 * 1024 * 1024) { phMsg("Ukuran foto melebihi 10MB."); return; }
    if (phUrl) URL.revokeObjectURL(phUrl);
    phUrl = URL.createObjectURL(file);
    var img = new Image();
    img.onload = function () {
      phImg = img;
      phNat = { w: img.naturalWidth, h: img.naturalHeight };
      var e = phEls();
      if (e.img) e.img.src = phUrl;
      if (e.empty) e.empty.hidden = true;
      if (e.work) e.work.hidden = false;
      if (e.save) e.save.disabled = false;
      phMsg("");
      phLayout(true);
    };
    img.onerror = function () { phMsg("Foto tidak bisa dibaca. Coba foto lain."); };
    img.src = phUrl;
  }
  document.addEventListener("click", function (e) {
    if (!e.target.closest) return;
    var open = e.target.closest("[data-photo-open]");
    if (open) { phOpen(open.getAttribute("data-target")); return; }
    if (e.target.closest("[data-photo-close]")) { phClose(); return; }
    var tab = e.target.closest("[data-photo-target]");
    if (tab) { phSetTarget(tab.getAttribute("data-photo-target")); return; }
    if (e.target.closest("[data-photo-change]")) { var f = phEls().file; if (f) f.click(); return; }
    if (e.target.closest("[data-photo-save]")) { phSave(); }
  });
  document.addEventListener("change", function (e) {
    if (e.target.matches && e.target.matches("[data-photo-file]")) {
      phLoadFile(e.target.files && e.target.files[0]);
      e.target.value = "";
    }
  });
  document.addEventListener("input", function (e) {
    if (!e.target.matches || !e.target.matches("[data-crop-zoom]") || !phImg) return;
    var f = phFrame();
    var cx = (f.w / 2 - phTx) / phScale, cy = (f.h / 2 - phTy) / phScale;
    phZoom = parseFloat(e.target.value) || 1;
    phScale = phMin * phZoom;
    phTx = f.w / 2 - cx * phScale;
    phTy = f.h / 2 - cy * phScale;
    phClamp();
    phRender();
  });
  document.addEventListener("pointerdown", function (e) {
    var box = e.target.closest ? e.target.closest("[data-crop-box]") : null;
    if (!box || !phImg) return;
    e.preventDefault();
    var sx = e.clientX, sy = e.clientY, tx0 = phTx, ty0 = phTy;
    function mv(ev) { phTx = tx0 + (ev.clientX - sx); phTy = ty0 + (ev.clientY - sy); phClamp(); phRender(); }
    function up() { window.removeEventListener("pointermove", mv); window.removeEventListener("pointerup", up); }
    window.addEventListener("pointermove", mv);
    window.addEventListener("pointerup", up);
  });
  document.addEventListener("keydown", function (e) { if (e.key === "Escape") phClose(); });
  function phSaveDone() {
    var e = phEls();
    if (!e.save) return;
    e.save.classList.remove("is-loading");
    e.save.textContent = "Upload & Simpan";
    e.save.disabled = !phImg;
  }

  /* ---------- Konfirmasi reset foto (Pengaturan) ---------- */
  var resetForm = null;
  function resetOpen(form) {
    var modal = $("[data-reset-modal]");
    if (!modal || !form) return;
    resetForm = form;
    var jenisIn = form.querySelector('[name="jenis"]');
    var isProfil = !jenisIn || jenisIn.value !== "sampul";
    var t = $("[data-reset-title]"), x = $("[data-reset-text]");
    if (t) t.textContent = isProfil ? "Reset Foto Profil?" : "Reset Cover?";
    if (x) x.textContent = isProfil
      ? "Foto profil akan dihapus dan kembali ke tampilan bawaan berupa huruf inisial namamu. Tindakan ini tidak bisa dibatalkan."
      : "Foto sampul akan dihapus dan kembali ke tampilan bawaan berupa gradasi biru. Tindakan ini tidak bisa dibatalkan.";
    modal.hidden = false;
    requestAnimationFrame(function () { modal.classList.add("is-open"); });
  }
  function resetClose() {
    var modal = $("[data-reset-modal]");
    if (!modal) return;
    modal.classList.remove("is-open");
    modal.hidden = true;
    resetForm = null;
  }
  document.addEventListener("click", function (e) {
    if (!e.target.closest) return;
    var btn = e.target.closest("[data-reset-btn]");
    if (btn) { resetOpen(btn.closest("form")); return; }
    if (e.target.closest("[data-reset-close]")) { resetClose(); return; }
    if (e.target.closest("[data-reset-confirm]")) {
      var f = resetForm;
      resetClose();
      if (f) {
        if (f.hasAttribute("data-ajax-form")) ajaxSendForm(f, f.querySelector("[data-reset-btn]"));
        else f.submit();
      }
    }
  });
  document.addEventListener("keydown", function (e) { if (e.key === "Escape") resetClose(); });

  function phSave() {
    var e = phEls();
    if (!phImg || !e.save || e.save.disabled) return;
    if (!window.HT_PANEL || !window.fetch) { phMsg("Unggahan tidak tersedia di halaman ini."); return; }
    var f = phFrame();
    var outW = phTarget === "profil" ? 512 : 1200;
    var outH = phTarget === "profil" ? 512 : 400;
    var cv = document.createElement("canvas");
    cv.width = outW; cv.height = outH;
    var ctx = cv.getContext("2d");
    ctx.fillStyle = "#fff";
    ctx.fillRect(0, 0, outW, outH);
    ctx.drawImage(phImg, -phTx / phScale, -phTy / phScale, f.w / phScale, f.h / phScale, 0, 0, outW, outH);
    e.save.disabled = true;
    e.save.classList.add("is-loading");
    e.save.textContent = "Mengunggah…";
    phMsg("");
    cv.toBlob(function (blob) {
      if (!blob) { phMsg("Gagal menyiapkan foto. Coba lagi."); phSaveDone(); return; }
      var fd = new FormData();
      fd.append("action", "halo_tg2_photo_upload");
      fd.append("nonce", window.HT_PANEL.photoNonce);
      fd.append("target", phTarget);
      fd.append("photo", blob, phTarget === "profil" ? "foto-profil.jpg" : "cover.jpg");
      fetch(window.HT_PANEL.ajaxUrl, { method: "POST", body: fd, credentials: "same-origin" })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (!j || !j.success) throw new Error((j && j.data && j.data.message) || "gagal");
          phSaveDone();
          phMsg("Foto tersimpan. Memuat ulang halaman…", true);
          setTimeout(function () { window.location.reload(); }, 800);
        })
        .catch(function (err) {
          phMsg("Foto belum tersimpan (" + (err && err.message ? err.message : "jaringan") + "). Coba lagi.");
          phSaveDone();
        });
    }, "image/jpeg", 0.9);
  }

  /* Setelah navigasi lembut menukar konten: hidupkan lagi widget
     yang bergantung pada node konten (tab geser, strip pembayaran). */
  document.addEventListener("ht:softnav:loaded", function () {
    requestAnimationFrame(function () { initSwipeTabs(); initStrips(document); shopInit(); });
  });

  /* ---------- Tab Pengaturan: panel geser ala Login & Daftar ----------
     Bilah tab tetap di tempat; hanya panel konten yang bergeser
     dengan animasi arah. Semua panel dirender server di DOM yang
     sama, perpindahan murni sisi klien, dan URL diselaraskan dengan
     replaceState agar tombol kembali tetap urusan navigasi lembut. */
  var SET_ORDER = ["akun", "keamanan", "sosial", "preferensi"];
  var setCurrent = null, setTimer = null;
  var setReduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function setNav() { return $("[data-tabnav]"); }
  function setPanelMap() {
    var map = {};
    $$("[data-tabpanel]").forEach(function (p) { map[p.getAttribute("data-tabpanel")] = p; });
    return map;
  }
  function setInk(name) {
    var nav = setNav(); if (!nav) return;
    var ink = nav.querySelector("[data-tabink]"); if (!ink) return;
    var link = null;
    $$("[data-tablink]", nav).forEach(function (l) { if (l.getAttribute("data-tablink") === name) link = l; });
    if (!link) { ink.style.width = "0px"; return; }
    ink.style.left = link.offsetLeft + "px";
    ink.style.width = link.offsetWidth + "px";
  }
  function setActiveLink(name) {
    $$("[data-tablink]").forEach(function (l) { l.classList.toggle("is-active", l.getAttribute("data-tablink") === name); });
  }
  function showSetPanel(name) {
    var panels = setPanelMap();
    var target = panels[name];
    if (!target) return;
    if (setCurrent === null) {
      Object.keys(panels).forEach(function (k) { if (!panels[k].hidden) setCurrent = k; });
      if (setCurrent === null) setCurrent = "akun";
    }
    if (name === setCurrent) { setActiveLink(name); setInk(name); return; }
    var from = panels[setCurrent];
    var forward = SET_ORDER.indexOf(name) > SET_ORDER.indexOf(setCurrent);
    setCurrent = name;
    setActiveLink(name);
    setInk(name);
    try {
      var url = new URL(window.location.href);
      if (name === "akun") url.searchParams.delete("bagian"); else url.searchParams.set("bagian", name);
      window.history.replaceState(null, "", url.toString());
    } catch (err) { /* abaikan */ }
    var activeLink = null;
    $$("[data-tablink]").forEach(function (l) { if (l.getAttribute("data-tablink") === name) activeLink = l; });
    if (activeLink && activeLink.scrollIntoView) { try { activeLink.scrollIntoView({ block: "nearest", inline: "nearest" }); } catch (err) {} }

    if (setTimer) { clearTimeout(setTimer); setTimer = null; }
    Object.keys(panels).forEach(function (k) {
      var p = panels[k];
      if (p !== from && p !== target) { p.hidden = true; p.classList.remove("is-leaving", "is-leaving-r", "is-leaving-l", "is-entering-r", "is-entering-l"); }
    });
    var viewport = $("[data-tabviewport]");
    if (setReduced || !viewport || !from) {
      if (from) from.hidden = true;
      target.hidden = false;
      return;
    }
    viewport.classList.add("is-animating");
    viewport.style.height = from.offsetHeight + "px";
    target.hidden = false;
    target.classList.add(forward ? "is-entering-r" : "is-entering-l");
    from.classList.add("is-leaving", forward ? "is-leaving-l" : "is-leaving-r");
    requestAnimationFrame(function () {
      requestAnimationFrame(function () { viewport.style.height = target.offsetHeight + "px"; });
    });
    setTimer = setTimeout(function () {
      from.hidden = true;
      from.classList.remove("is-leaving", "is-leaving-r", "is-leaving-l");
      target.classList.remove("is-entering-r", "is-entering-l");
      viewport.style.height = "";
      viewport.classList.remove("is-animating");
      setTimer = null;
    }, 400);
  }
  function initSetTabs() {
    if (!setNav()) return;
    var panels = setPanelMap();
    setCurrent = null;
    Object.keys(panels).forEach(function (k) { if (!panels[k].hidden) setCurrent = k; });
    if (!setCurrent) setCurrent = "akun";
    setActiveLink(setCurrent);
    requestAnimationFrame(function () { setInk(setCurrent); });
  }
  document.addEventListener("click", function (e) {
    if (!e.target.closest) return;
    var link = e.target.closest("[data-tablink]");
    if (!link) return;
    if (!setPanelMap()[link.getAttribute("data-tablink")]) return;
    e.preventDefault();
    showSetPanel(link.getAttribute("data-tablink"));
  });
  document.addEventListener("keydown", function (e) {
    if (e.key !== "ArrowRight" && e.key !== "ArrowLeft") return;
    if (!e.target.closest || !e.target.closest("[data-tabnav]")) return;
    e.preventDefault();
    var idx = SET_ORDER.indexOf(setCurrent || "akun");
    var next = e.key === "ArrowRight" ? Math.min(SET_ORDER.length - 1, idx + 1) : Math.max(0, idx - 1);
    showSetPanel(SET_ORDER[next]);
    var link = null;
    $$("[data-tablink]").forEach(function (l) { if (l.getAttribute("data-tablink") === SET_ORDER[next]) link = l; });
    if (link) link.focus();
  });
  window.addEventListener("resize", function () { if (setCurrent) setInk(setCurrent); });
  requestAnimationFrame(initSetTabs);
  document.addEventListener("ht:softnav:loaded", function () { requestAnimationFrame(initSetTabs); });

  /* ===== Dropdown kustom: <select data-custom-select> menjadi tombol + menu gaya Halo TG2.
     Select asli tetap di DOM sebagai pembawa nilai form (tersembunyi oleh CSS),
     jadi kirim form klasik maupun AJAX tidak berubah sama sekali. ===== */
  var HT_CHEV = '<svg width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true"><path d="M1 1.5 6 6.5 11 1.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
  var HT_CHECK = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 12.5 9.5 18 20 6.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>';
  function closeAllSelects(except) {
    $$(".ht-select.is-open").forEach(function (w) {
      if (except && w === except) return;
      w.classList.remove("is-open");
      var m = $("[data-select-menu]", w); if (m) m.hidden = true;
      var b = $("[data-select-btn]", w); if (b) b.setAttribute("aria-expanded", "false");
    });
  }
  function syncSelect(wrap) {
    var sel = $("select", wrap); if (!sel) return;
    var label = $("[data-select-label]", wrap);
    var opt = sel.options[sel.selectedIndex];
    if (label) label.textContent = opt ? opt.textContent : "";
    $$("[data-select-opt]", wrap).forEach(function (b) {
      var on = b.getAttribute("data-value") === sel.value;
      b.classList.toggle("is-selected", on);
      b.setAttribute("aria-selected", on ? "true" : "false");
    });
  }
  function openSelect(wrap) {
    closeAllSelects(wrap);
    var menu = $("[data-select-menu]", wrap); var btn = $("[data-select-btn]", wrap);
    if (!menu || !btn) return;
    menu.hidden = false;
    menu.classList.remove("is-up");
    var r = btn.getBoundingClientRect();
    var h = Math.min(menu.scrollHeight || 264, 264);
    if (r.bottom + h + 10 > window.innerHeight && r.top - h - 10 > 0) menu.classList.add("is-up");
    wrap.classList.add("is-open");
    btn.setAttribute("aria-expanded", "true");
    var sel = $(".ht-select__opt.is-selected", menu) || $(".ht-select__opt", menu);
    if (sel) {
      $$(".ht-select__opt", menu).forEach(function (b) { b.classList.remove("is-focus"); });
      sel.classList.add("is-focus");
      sel.scrollIntoView({ block: "nearest" });
    }
  }
  function closeSelect(wrap) {
    wrap.classList.remove("is-open");
    var m = $("[data-select-menu]", wrap); if (m) m.hidden = true;
    var b = $("[data-select-btn]", wrap); if (b) b.setAttribute("aria-expanded", "false");
  }
  function chooseSelect(wrap, value) {
    var sel = $("select", wrap); if (!sel) return;
    sel.value = value;
    syncSelect(wrap);
    sel.dispatchEvent(new Event("change", { bubbles: true }));
    closeSelect(wrap);
    var b = $("[data-select-btn]", wrap); if (b) b.focus();
  }
  function buildSelect(sel) {
    if (!sel || sel.getAttribute("data-built")) return;
    sel.setAttribute("data-built", "1");
    var wrap = document.createElement("span");
    wrap.className = "ht-select";
    sel.parentNode.insertBefore(wrap, sel);
    wrap.appendChild(sel);
    sel.setAttribute("tabindex", "-1");
    sel.setAttribute("aria-hidden", "true");
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "ht-select__btn";
    btn.setAttribute("data-select-btn", "");
    btn.setAttribute("aria-haspopup", "listbox");
    btn.setAttribute("aria-expanded", "false");
    btn.innerHTML = "<span data-select-label></span>" + HT_CHEV;
    wrap.appendChild(btn);
    var menu = document.createElement("span");
    menu.className = "ht-select__menu";
    menu.setAttribute("data-select-menu", "");
    menu.setAttribute("role", "listbox");
    menu.hidden = true;
    Array.prototype.forEach.call(sel.options, function (o) {
      var b = document.createElement("button");
      b.type = "button";
      b.className = "ht-select__opt";
      b.setAttribute("data-select-opt", "");
      b.setAttribute("data-value", o.value);
      b.setAttribute("role", "option");
      b.innerHTML = "<span></span>" + HT_CHECK;
      b.firstChild.textContent = o.textContent;
      menu.appendChild(b);
    });
    wrap.appendChild(menu);
    syncSelect(wrap);
  }
  function initCustomSelects() {
    $$("select[data-custom-select]").forEach(buildSelect);
  }
  document.addEventListener("click", function (e) {
    var btn = e.target.closest ? e.target.closest("[data-select-btn]") : null;
    if (btn) {
      var w = btn.closest(".ht-select");
      if (w) { w.classList.contains("is-open") ? closeSelect(w) : openSelect(w); }
      return;
    }
    var opt = e.target.closest ? e.target.closest("[data-select-opt]") : null;
    if (opt) {
      var w2 = opt.closest(".ht-select");
      if (w2) chooseSelect(w2, opt.getAttribute("data-value"));
      return;
    }
    if (!(e.target.closest && e.target.closest(".ht-select"))) closeAllSelects(null);
  });
  document.addEventListener("keydown", function (e) {
    var btn = e.target.closest ? e.target.closest("[data-select-btn]") : null;
    var opt = e.target.closest ? e.target.closest("[data-select-opt]") : null;
    if (e.key === "Escape") { closeAllSelects(null); return; }
    if (btn && (e.key === "ArrowDown" || e.key === "ArrowUp")) {
      e.preventDefault();
      var w = btn.closest(".ht-select");
      if (w) openSelect(w);
      return;
    }
    if (opt && (e.key === "ArrowDown" || e.key === "ArrowUp" || e.key === "Enter" || e.key === " ")) {
      e.preventDefault();
      var w2 = opt.closest(".ht-select");
      if (e.key === "Enter" || e.key === " ") { chooseSelect(w2, opt.getAttribute("data-value")); return; }
      var items = $$("[data-select-opt]", w2);
      var i = items.indexOf(opt);
      var n = e.key === "ArrowDown" ? Math.min(i + 1, items.length - 1) : Math.max(i - 1, 0);
      items.forEach(function (b) { b.classList.remove("is-focus"); });
      items[n].classList.add("is-focus");
      items[n].focus();
    }
  });
  document.addEventListener("change", function (e) {
    var t = e.target;
    if (t && t.matches && t.matches("select[data-custom-select]")) {
      var w = t.closest(".ht-select");
      if (w) syncSelect(w);
    }
  });
  initCustomSelects();
  document.addEventListener("ht:softnav:loaded", function () { requestAnimationFrame(initCustomSelects); });

  /* Terapkan preferensi tampilan ke atribut shell panel (efeknya di CSS panel). */
  function applyPrefAttrs(p) {
    var shell = $(".ht-panel");
    if (!shell || !p) return;
    if (typeof p.vision !== "undefined") shell.setAttribute("data-vision", p.vision || "normal");
    if (typeof p.contrast !== "undefined") shell.setAttribute("data-contrast", p.contrast === "high" ? "high" : "");
    if (typeof p.motion_effects !== "undefined") shell.setAttribute("data-motion", p.motion_effects || "auto");
  }
  function prefAttrsFromForm(form) {
    var g = function (n) { var el = form.querySelector("[name='" + n + "']"); return el ? el.value : undefined; };
    var c = form.querySelector("[name='contrast']");
    return {
      vision: g("vision"),
      motion_effects: g("motion_effects"),
      contrast: c ? (c.checked ? "high" : "") : undefined
    };
  }

  /* ---------- Simpan pengaturan via AJAX ----------
     Form bertanda data-ajax-form dikirim ke admin-ajax memakai
     action yang sama dengan jalur admin-post; hasilnya tampil
     sebagai pesan di dalam kartu tanpa memuat ulang halaman.
     Bila gerbang sesi/nonce menolak atau jaringan gagal, form
     dikirim ulang dengan cara lama agar simpanan tidak hilang.
     Form kata sandi sengaja tidak bertanda: tetap cara lama. */
  function ajaxMsg(form, ok, text) {
    var card = form.closest ? form.closest(".ht-panel__setting-card") : null;
    var slot = card ? card.querySelector("[data-ajaxmsg]") : null;
    if (!slot) return;
    slot.className = "ht-panel__ajaxmsg " + (ok ? "is-ok" : "is-error");
    slot.textContent = text;
    slot.hidden = false;
  }
  function ajaxBtnState(btn, busy) {
    if (!btn) return;
    if (busy) {
      btn.setAttribute("data-ht-label", btn.innerHTML);
      btn.classList.add("is-loading");
      btn.disabled = true;
      btn.textContent = "Memproses…";
    } else {
      btn.classList.remove("is-loading");
      btn.disabled = false;
      if (btn.getAttribute("data-ht-label")) { btn.innerHTML = btn.getAttribute("data-ht-label"); btn.removeAttribute("data-ht-label"); }
    }
  }
  function ajaxSendForm(form, btn) {
    if (form.__htBusy) return;
    if (!window.HT_PANEL || !window.fetch) { form.submit(); return; }
    form.__htBusy = true;
    ajaxBtnState(btn, true);
    var fd = new FormData(form);
    fetch(HT_PANEL.ajaxUrl, { method: "POST", body: fd, credentials: "same-origin" })
      .then(function (r) { return r.text(); })
      .then(function (t) {
        var json = null;
        try { json = JSON.parse(t); } catch (e) { json = null; }
        form.__htBusy = false;
        ajaxBtnState(btn, false);
        if (!json || !json.data || json.data.code === "ht_gate") { form.submit(); return; }
        if (json.success) {
          ajaxMsg(form, true, json.data.message || "Tersimpan.");
          ajaxSideEffects(form, json.data);
        } else {
          ajaxMsg(form, false, json.data.message || "Belum tersimpan. Coba lagi sebentar lagi.");
        }
      })
      .catch(function () {
        form.__htBusy = false;
        ajaxBtnState(btn, false);
        form.submit();
      });
  }
  function ajaxSideEffects(form, data) {
    var kind = form.getAttribute("data-ajax-form");
    if (kind === "profile") {
      if (data.display_name) $$("[data-ht-displayname]").forEach(function (el) { el.textContent = data.display_name; });
      if (data.initial) $$(".ht-panel__avatar, .ht-panel__profile-avatar").forEach(function (av) {
        if (!av.querySelector("img")) av.textContent = data.initial;
      });
    } else if (kind === "prefs") {
      applyPrefAttrs(prefAttrsFromForm(form));
    } else if (kind === "resetprefs" && data.prefs) {
      var card = form.closest(".ht-panel__setting-card");
      if (card) Object.keys(data.prefs).forEach(function (k) {
        var input = card.querySelector("[name='" + k + "']");
        if (!input) return;
        if (input.type === "checkbox") input.checked = (k === "contrast") ? data.prefs[k] === "high" : !!data.prefs[k];
        else input.value = String(data.prefs[k]);
        if (input.tagName === "SELECT") input.dispatchEvent(new Event("change", { bubbles: true }));
      });
      applyPrefAttrs({ vision: data.prefs.vision, contrast: data.prefs.contrast, motion_effects: data.prefs.motion_effects });
    } else if (kind === "removephoto") {
      ajaxPhotoReset(form, data);
    }
  }
  function ajaxPhotoReset(form, data) {
    if (data.jenis === "profil") {
      $$(".ht-panel__avatar, .ht-panel__profile-avatar").forEach(function (av) { av.textContent = data.initial || ""; });
    } else if (data.jenis === "sampul") {
      var prev = $(".ht-panel__cover-preview");
      if (prev) {
        var btn = prev.querySelector("[data-photo-open]");
        prev.innerHTML = "";
        var span = document.createElement("span");
        span.textContent = "Belum ada foto sampul";
        prev.appendChild(span);
        if (btn) prev.appendChild(btn);
      }
    }
    form.hidden = true;
    var actions = form.closest(".ht-panel__photo-actions");
    if (actions) {
      var anyVisible = false;
      $$("form", actions).forEach(function (f) { if (!f.hidden) anyVisible = true; });
      if (!anyVisible) actions.hidden = true;
    }
  }
  document.addEventListener("submit", function (e) {
    var form = e.target;
    if (!form || !form.hasAttribute || !form.hasAttribute("data-ajax-form")) return;
    e.preventDefault();
    ajaxSendForm(form, form.querySelector("button[type='submit']"));
  });

  /* ---------- Sorotan target jangkar (mis. #pengingat-ujian) ----------
     Setelah mendarat dari tautan berjangkar, kartu tujuan diberi
     denyut sorotan sesaat agar mudah disadari. Bila kartu berada di
     panel tab yang tersembunyi, tab-nya diaktifkan lebih dulu. */
  var flashTimer = null;
  function flashHashTarget() {
    if (!location.hash || location.hash.length < 2) return;
    var el = null;
    try { el = document.getElementById(decodeURIComponent(location.hash.slice(1))); } catch (e) { el = null; }
    if (!el) return;
    var go = function () {
      el.classList.remove("is-flash");
      void el.offsetWidth;
      el.classList.add("is-flash");
      if (flashTimer) clearTimeout(flashTimer);
      flashTimer = setTimeout(function () { el.classList.remove("is-flash"); }, 1950);
    };
    var panel = el.closest ? el.closest("[data-tabpanel]") : null;
    if (panel && panel.hidden) {
      showSetPanel(panel.getAttribute("data-tabpanel"));
      setTimeout(function () {
        try { el.scrollIntoView({ block: "start" }); } catch (e) { /* abaikan */ }
        go();
      }, 440);
    } else {
      go();
    }
  }
  requestAnimationFrame(flashHashTarget);
  document.addEventListener("ht:softnav:loaded", function () { requestAnimationFrame(flashHashTarget); });
})();
