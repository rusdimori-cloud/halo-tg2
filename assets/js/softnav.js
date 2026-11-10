/* halo-tg2 — navigasi lembut (soft navigation / PJAX ringan).
   Kerang halaman (header, topbar, sidebar, dock) tetap hidup; hanya
   region konten yang bertukar, dengan skeleton selama memuat —
   seperti aplikasi. Dua kerang didukung:
   - panel  : region <main id="ht-panel-main"> di halaman dashboard
   - lesson : region #tutor-single-entry-content di halaman lesson
   Pengaman: respons yang bukan kerang yang sama (mis. halaman login
   karena sesi habis), konten video/kuis bawaan Tutor yang butuh
   inisialisasi skripnya sendiri, dan kegagalan jaringan selalu
   jatuh ke muat penuh biasa. */
(function () {
  "use strict";
  var cfg = window.HT_SOFTNAV || {};
  if (!cfg.enabled) return;
  if (!("fetch" in window) || !("DOMParser" in window) || !("pushState" in history)) return;

  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  function shellOf(doc) {
    if (doc.querySelector(".ht-panel")) return "panel";
    if (doc.querySelector("#tutor-single-entry-content")) return "lesson";
    return null;
  }
  var SHELL = shellOf(document);
  if (!SHELL) return;
  if (SHELL === "panel" && !cfg.panelPath) cfg.panelPath = location.pathname;

  var REGION = { panel: "#ht-panel-main", lesson: "#tutor-single-entry-content" };
  var SYNC = {
    panel: [
      { sel: ".ht-panel__topbar h1", mode: "text" },
      { sel: ".ht-panel__nav", mode: "html" },
      { sel: ".ht-panel__top-actions", mode: "html" }
    ],
    lesson: [
      { sel: ".tutor-course-single-sidebar-wrapper", mode: "html" },
      { sel: ".tutor-spotlight-mobile-progress-complete", mode: "html" }
    ]
  };

  /* ---------- skeleton ---------- */
  function skelLine(w) { return '<div class="ht-skel ht-skel--line" style="width:' + w + '%"></div>'; }
  function skelBlock(h) { return '<div class="ht-skel ht-skel--block" style="height:' + h + 'px"></div>'; }
  function skelCards(n) {
    var s = '<div class="ht-soft-skel__grid">';
    for (var i = 0; i < n; i++) s += '<div class="ht-soft-skel__card">' + skelBlock(120) + skelLine(70) + skelLine(45) + '</div>';
    return s + "</div>";
  }
  function skelRows(n) {
    var s = "";
    for (var i = 0; i < n; i++) s += '<div class="ht-soft-skel__row"><div class="ht-skel ht-skel--circle"></div><div class="ht-soft-skel__lines">' + skelLine(55) + skelLine(30) + "</div></div>";
    return s;
  }
  function skeletonFor(url) {
    var view = "beranda";
    try { view = new URL(url).searchParams.get("tampilan") || "beranda"; } catch (e) {}
    var inner;
    if (SHELL === "lesson") {
      inner = '<div class="ht-skel ht-skel--video"></div>' + skelLine(60) + skelLine(92) + skelLine(96) + skelLine(78) + skelLine(40);
    } else if (view === "kelas" || view === "wishlist") {
      inner = skelLine(34) + skelCards(4);
    } else if (view === "statistik") {
      inner = skelLine(34) + skelBlock(180) + skelRows(3);
    } else if (view === "pengaturan") {
      inner = skelLine(30) + skelBlock(150) + skelLine(80) + skelLine(64) + skelBlock(120);
    } else if (view === "beranda") {
      inner = skelBlock(96) + skelLine(30) + skelCards(2);
    } else {
      inner = skelLine(34) + skelRows(5);
    }
    return '<div class="ht-soft-skel" aria-hidden="true">' + inner + "</div>";
  }

  /* ---------- bilah progres atas ---------- */
  var bar = document.createElement("div");
  bar.className = "ht-soft-progress";
  bar.hidden = true;
  bar.innerHTML = "<i></i>";
  document.body.appendChild(bar);
  function progress(on) {
    bar.hidden = !on;
    bar.classList.toggle("is-done", !on);
    if (on) { bar.classList.remove("is-done"); void bar.offsetWidth; }
  }

  /* ---------- cache untuk tombol kembali ---------- */
  var cache = {};
  var order = [];
  function cachePut(url, entry) {
    if (!cache[url]) order.push(url);
    cache[url] = entry;
    while (order.length > 8) delete cache[order.shift()];
  }
  function captureSync(doc) {
    var out = {};
    SYNC[SHELL].forEach(function (rule) {
      var el = doc.querySelector(rule.sel);
      if (el) out[rule.sel] = rule.mode === "text" ? el.textContent : el.innerHTML;
    });
    return out;
  }
  function currentEntry() {
    var region = $(REGION[SHELL]);
    if (!region) return null;
    return { html: region.outerHTML, title: document.title, scroll: window.scrollY || 0, sync: captureSync(document) };
  }
  function applySync(data) {
    if (!data) return;
    SYNC[SHELL].forEach(function (rule) {
      if (!(rule.sel in data)) return;
      var el = $(rule.sel);
      if (!el) return;
      if (rule.mode === "text") el.textContent = data[rule.sel]; else el.innerHTML = data[rule.sel];
    });
  }

  /* ---------- pertukaran konten ---------- */
  function runScripts(root) {
    $$("script", root).forEach(function (old) {
      var s = document.createElement("script");
      Array.prototype.forEach.call(old.attributes, function (at) { s.setAttribute(at.name, at.value); });
      s.textContent = old.textContent;
      old.parentNode.replaceChild(s, old);
    });
  }
  function closeOverlays() {
    $$("[data-dd-panel]").forEach(function (p) { p.hidden = true; });
    var drawer = $("[data-drawer]"), scrim = $("[data-scrim]");
    if (drawer) drawer.classList.remove("is-open");
    if (scrim) scrim.hidden = true;
  }
  function finishSwap(newRegion, doc, url, opts) {
    var region = $(REGION[SHELL]);
    if (!region) return full(url);
    newRegion.classList.add("ht-soft-enter");
    region.parentNode.replaceChild(newRegion, region);
    runScripts(newRegion);
    if (doc) { applySync(captureSync(doc)); document.title = doc.title; }
    else if (opts.cached) { applySync(opts.cached.sync); document.title = opts.cached.title; }
    if (opts.push) history.pushState({ htSoft: 1 }, "", url);
    window.scrollTo(0, opts.scroll || 0);
    closeOverlays();
    progress(false);
    document.dispatchEvent(new CustomEvent("ht:softnav:loaded", { detail: { url: url, shell: SHELL } }));
  }
  function full(url) { progress(false); location.href = url; }

  /* Konten yang inisialisasi skrip bawaannya tidak bisa diulang:
     video Tutor (Plyr + pelacakan tonton) dan kuis bawaan Tutor. */
  function hardContent(doc, region) {
    if (SHELL !== "lesson") return false;
    if (doc.body && /(^|\s)(tutor_quiz|tutor_assignments)(\s|$)/.test(doc.body.className)) return true;
    return !!region.querySelector("#tutor_video_tracking_information");
  }

  var seq = 0;
  var pending = null;
  function navigate(url, opts) {
    opts = opts || {};
    var my = ++seq;
    if (pending && pending.abort) pending.abort();
    pending = ("AbortController" in window) ? new AbortController() : null;
    var entry = currentEntry();
    if (entry) cachePut(location.href, entry);
    progress(true);
    var region = $(REGION[SHELL]);
    var skelTimer = setTimeout(function () {
      var r = $(REGION[SHELL]);
      if (my === seq && r) { r.setAttribute("aria-busy", "true"); r.innerHTML = skeletonFor(url); }
    }, 130);
    fetch(url, {
      credentials: "same-origin",
      cache: "no-store",
      headers: { "X-HT-Softnav": "1" },
      signal: pending ? pending.signal : undefined
    }).then(function (res) {
      if (!res.ok) throw new Error("http");
      return res.text();
    }).then(function (text) {
      clearTimeout(skelTimer);
      if (my !== seq) return;
      var doc = new DOMParser().parseFromString(text, "text/html");
      if (shellOf(doc) !== SHELL) return full(url);
      var newRegion = doc.querySelector(REGION[SHELL]);
      if (!newRegion) return full(url);
      if (hardContent(doc, newRegion)) return full(url);
      finishSwap(newRegion, doc, url, { push: opts.push !== false, scroll: 0 });
      lastUrl = url;
    }).catch(function (err) {
      clearTimeout(skelTimer);
      if (my !== seq) return;
      if (err && err.name === "AbortError") return;
      full(url);
    });
  }

  /* ---------- cegat klik tautan ---------- */
  function samePath(a, b) { return a.replace(/\/$/, "") === b.replace(/\/$/, ""); }
  document.addEventListener("click", function (e) {
    if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    var a = e.target.closest ? e.target.closest("a[href]") : null;
    if (!a) return;
    if (a.target && a.target !== "_self") return;
    if (a.hasAttribute("download") || a.hasAttribute("data-no-soft")) return;
    var url;
    try { url = new URL(a.href, location.href); } catch (err) { return; }
    if (url.origin !== location.origin) return;
    if (/wp-admin|wp-login|admin-post\.php/.test(url.pathname)) return;
    if (url.searchParams.get("action") === "logout") return;
    if (url.href.split("#")[0] === location.href.split("#")[0]) return;
    if (SHELL === "panel") {
      if (!samePath(url.pathname, cfg.panelPath || location.pathname)) return;
    } else {
      if (!a.closest(".tutor-course-single-content-wrapper")) return;
    }
    e.preventDefault();
    navigate(url.href, { push: true });
  });

  /* ---------- tombol kembali / maju ---------- */
  window.addEventListener("popstate", function () {
    var entry = currentEntry();
    if (entry) cachePut(lastUrl, entry);
    var target = cache[location.href];
    seq++;
    if (pending && pending.abort) pending.abort();
    progress(false);
    if (!target) { location.reload(); return; }
    var wrap = document.createElement("div");
    wrap.innerHTML = target.html;
    var newRegion = wrap.firstElementChild;
    if (!newRegion) { location.reload(); return; }
    lastUrl = location.href;
    finishSwap(newRegion, null, location.href, { push: false, scroll: target.scroll || 0, cached: target });
  });

  var lastUrl = location.href;
  history.replaceState({ htSoft: 1 }, "", location.href);
})();
