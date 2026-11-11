/* halo-tg2: perilaku halaman auth (Satu Pintu).
   Tab Masuk/Daftar/Lupa Sandi dengan animasi fade + geser mengikuti
   arah, tinggi panel bertransisi halus, show/hide kata sandi, meter
   kekuatan sandi, validasi inline, dan cek ketersediaan nama
   pengguna via AJAX (debounce). Pemrosesan tetap di server:
   login/lupa sandi oleh WordPress, daftar oleh Tutor LMS. */
(function () {
  "use strict";
  var root = document.querySelector("[data-auth-root]");
  if (!root) return;

  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var reduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var ORDER = { login: 0, register: 1, lostpassword: 2 };
  var DUR = reduced ? 0 : 400;

  /* ---------- Perpindahan panel ---------- */
  var viewport = $("[data-auth-viewport]", root);
  var tabsWrap = $("[data-auth-tabs]", root);
  var tabInk = $("[data-auth-tabink]", root);
  var panels = {};
  $$("[data-auth-panel]", root).forEach(function (p) { panels[p.getAttribute("data-auth-panel")] = p; });
  var current = null;
  Object.keys(panels).forEach(function (k) { if (!panels[k].hidden) current = k; });
  if (!current) current = "login";
  var switchTimer = null;

  function placeInk() {
    if (!tabInk || !tabsWrap || tabsWrap.hidden) return;
    var active = $("[data-auth-tab].is-active", tabsWrap);
    if (!active) return;
    tabInk.style.left = active.offsetLeft + "px";
    tabInk.style.width = active.offsetWidth + "px";
  }

  function setTabs(mode) {
    $$("[data-auth-tab]", tabsWrap).forEach(function (b) {
      var on = b.getAttribute("data-auth-tab") === mode;
      b.classList.toggle("is-active", on);
      b.setAttribute("aria-selected", on ? "true" : "false");
    });
    if (tabsWrap) tabsWrap.hidden = mode === "lostpassword";
    placeInk();
  }

  function showPanel(mode, focus) {
    var target = panels[mode];
    if (!target || mode === current) { setTabs(mode); return; }
    var from = panels[current];
    var forward = ORDER[mode] > ORDER[current];
    current = mode;
    setTabs(mode);
    try {
      var url = new URL(window.location.href);
      url.searchParams.set("mode", mode);
      window.history.replaceState(null, "", url.toString());
    } catch (err) { /* abaikan */ }

    if (switchTimer) { clearTimeout(switchTimer); switchTimer = null; }
    $$("[data-auth-panel]", root).forEach(function (p) {
      if (p !== from && p !== target) { p.hidden = true; p.classList.remove("is-leaving", "is-leaving-r", "is-leaving-l", "is-entering-r", "is-entering-l"); }
    });

    if (reduced || !viewport) {
      if (from) from.hidden = true;
      target.hidden = false;
      if (focus) focusFirst(target);
      return;
    }

    viewport.classList.add("is-animating");
    viewport.style.height = from.offsetHeight + "px";
    target.hidden = false;
    target.classList.add(forward ? "is-entering-r" : "is-entering-l");
    from.classList.add("is-leaving", forward ? "is-leaving-l" : "is-leaving-r");
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        viewport.style.height = target.offsetHeight + "px";
      });
    });
    switchTimer = setTimeout(function () {
      from.hidden = true;
      from.classList.remove("is-leaving", "is-leaving-r", "is-leaving-l");
      target.classList.remove("is-entering-r", "is-entering-l");
      viewport.style.height = "";
      viewport.classList.remove("is-animating");
      switchTimer = null;
      if (focus) focusFirst(target);
    }, DUR);
  }

  function focusFirst(panel) {
    var input = panel.querySelector("input:not([type=hidden]):not([type=checkbox])");
    if (input) { try { input.focus({ preventScroll: true }); } catch (err) { input.focus(); } }
  }

  $$("[data-auth-tab]", root).forEach(function (b) {
    b.addEventListener("click", function (e) {
      e.preventDefault();
      showPanel(b.getAttribute("data-auth-tab"), true);
    });
  });
  $$("[data-auth-goto]", root).forEach(function (a) {
    a.addEventListener("click", function (e) {
      e.preventDefault();
      showPanel(a.getAttribute("data-auth-goto"), true);
    });
  });
  if (tabsWrap) {
    tabsWrap.addEventListener("keydown", function (e) {
      if (e.key !== "ArrowRight" && e.key !== "ArrowLeft") return;
      var modes = ["login", "register"];
      var next = modes[(modes.indexOf(current) + (e.key === "ArrowRight" ? 1 : modes.length - 1)) % modes.length];
      showPanel(next, true);
    });
  }
  window.addEventListener("resize", placeInk);
  requestAnimationFrame(placeInk);
  window.addEventListener("load", placeInk);

  /* ---------- Show/hide kata sandi ---------- */
  var EYE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>';
  var EYE_OFF = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 4l16 16M9.9 5.9A9.4 9.4 0 0 1 12 5.5c6 0 9.5 6.5 9.5 6.5a17.6 17.6 0 0 1-3.3 3.7M6 8A16.9 16.9 0 0 0 2.5 12S6 18.5 12 18.5c1.2 0 2.3-.3 3.3-.7M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';
  $$("[data-pwd-toggle]", root).forEach(function (btn) {
    btn.addEventListener("click", function () {
      var input = btn.parentElement.querySelector("input");
      if (!input) return;
      var show = input.type === "password";
      input.type = show ? "text" : "password";
      btn.innerHTML = show ? EYE_OFF : EYE;
      btn.setAttribute("aria-label", show ? "Sembunyikan kata sandi" : "Tampilkan kata sandi");
      btn.setAttribute("aria-pressed", show ? "true" : "false");
      input.focus();
    });
  });

  /* ---------- Utilitas galat field ---------- */
  function setErr(form, name, msg) {
    var slot = form.querySelector('[data-err-for="' + name + '"]');
    var input = form.querySelector('[name="' + name + '"]');
    if (slot) {
      slot.textContent = msg || "";
      slot.hidden = !msg;
    }
    if (input) {
      if (msg) input.setAttribute("aria-invalid", "true");
      else input.removeAttribute("aria-invalid");
    }
    return !msg;
  }
  function fieldVal(form, name) {
    var el = form.querySelector('[name="' + name + '"]');
    return el ? el.value.trim() : "";
  }

  /* ---------- Cek nama pengguna (AJAX) ---------- */
  var userInput = $("[data-username-input]", root);
  var userStatus = $("[data-user-status]", root);
  var userState = "idle"; // idle | checking | available | taken | invalid
  var userTimer = null, userAbort = null, userPromise = null, checkedValue = "";
  var ICON_OK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8.5 12.2 2.4 2.4 4.6-5"/></svg>';
  var ICON_BAD = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7.5V13M12 16.2v.3"/></svg>';
  var USER_PATTERN = /^[a-z0-9_-]+$/;

  function setUserStatus(kind, msg) {
    if (!userStatus) return;
    userStatus.className = "ht-auth__status" + (kind ? " is-" + kind : "");
    if (!msg) { userStatus.hidden = true; userStatus.innerHTML = ""; return; }
    userStatus.hidden = false;
    if (kind === "checking") userStatus.innerHTML = '<span class="ht-auth__spin" aria-hidden="true"></span>' + msg;
    else if (kind === "ok") userStatus.innerHTML = ICON_OK + msg;
    else userStatus.innerHTML = ICON_BAD + msg;
  }

  function checkUsername(value) {
    if (!window.HT_AUTH || !window.fetch) return Promise.resolve("idle");
    if (userAbort) userAbort.abort();
    userAbort = window.AbortController ? new AbortController() : null;
    userState = "checking";
    setUserStatus("checking", "Memeriksa nama pengguna…");
    var fd = new FormData();
    fd.append("action", "halo_tg2_check_username");
    fd.append("nonce", window.HT_AUTH.userNonce);
    fd.append("username", value);
    var opts = { method: "POST", body: fd, credentials: "same-origin" };
    if (userAbort) opts.signal = userAbort.signal;
    userPromise = fetch(window.HT_AUTH.ajaxUrl, opts)
      .then(function (r) { return r.json(); })
      .then(function (j) {
        var d = (j && j.data) || {};
        checkedValue = value;
        if (d.status === "available") { userState = "available"; setUserStatus("ok", d.message || "Nama pengguna tersedia."); }
        else if (d.status === "taken") { userState = "taken"; setUserStatus("bad", d.message || "Nama pengguna sudah digunakan."); }
        else if (d.status === "invalid") { userState = "invalid"; setUserStatus("bad", d.message || "Format nama pengguna belum benar."); }
        else { userState = "idle"; setUserStatus("", ""); if (d.message) setUserStatus("bad", d.message); }
        return userState;
      })
      .catch(function () {
        // Jaringan bermasalah: jangan blokir pendaftaran, server akan
        // memeriksa ulang saat submit.
        userState = "idle";
        setUserStatus("", "");
        return "idle";
      });
    return userPromise;
  }

  if (userInput) {
    userInput.addEventListener("input", function () {
      var v = userInput.value.toLowerCase();
      if (v !== userInput.value) userInput.value = v;
      if (userTimer) clearTimeout(userTimer);
      var form = userInput.closest("form");
      if (!v) { userState = "idle"; setUserStatus("", ""); if (form) setErr(form, "user_login", null); return; }
      if (v.length < 3) { userState = "idle"; setUserStatus("", ""); return; }
      if (!USER_PATTERN.test(v)) {
        userState = "invalid";
        setUserStatus("bad", "Hanya huruf kecil, angka, garis bawah (_), dan tanda hubung (-).");
        return;
      }
      userTimer = setTimeout(function () { checkUsername(v); }, 450);
    });
  }

  /* ---------- Meter kekuatan sandi ---------- */
  var passInput = $("[data-password-input]", root);
  var strengthBox = $("[data-strength]", root);
  var strengthText = $("[data-strength-text]", root);
  function strengthLevel(v) {
    var score = 0;
    if (v.length >= 8) score++;
    if (v.length >= 12) score++;
    if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score++;
    if (/\d/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    if (score <= 2) return 1;
    if (score === 3) return 2;
    return 3;
  }
  if (passInput && strengthBox) {
    passInput.addEventListener("input", function () {
      var v = passInput.value;
      strengthBox.hidden = !v;
      if (!v) return;
      var lv = strengthLevel(v);
      strengthBox.setAttribute("data-level", String(lv));
      strengthText.textContent = "Kekuatan kata sandi: " + (lv === 1 ? "Lemah" : lv === 2 ? "Sedang" : "Kuat");
    });
  }

  /* ---------- Validasi + kirim ---------- */
  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function validateLogin(form) {
    var ok = true;
    if (!fieldVal(form, "log")) { setErr(form, "log", "Isi dulu nama pengguna atau email-nya."); ok = false; } else setErr(form, "log", null);
    if (!fieldVal(form, "pwd")) { setErr(form, "pwd", "Isi dulu kata sandinya."); ok = false; } else setErr(form, "pwd", null);
    return ok;
  }
  function validateLost(form) {
    var ok = true;
    if (!fieldVal(form, "user_login")) { setErr(form, "user_login", "Isi dulu nama pengguna atau email-nya."); ok = false; } else setErr(form, "user_login", null);
    return ok;
  }
  function validateRegisterSync(form) {
    var ok = true;
    if (!fieldVal(form, "first_name")) { setErr(form, "first_name", "Nama depan wajib diisi."); ok = false; } else setErr(form, "first_name", null);
    if (!fieldVal(form, "last_name")) { setErr(form, "last_name", "Nama belakang wajib diisi."); ok = false; } else setErr(form, "last_name", null);
    var u = fieldVal(form, "user_login");
    if (!u) { setErr(form, "user_login", "Nama pengguna wajib diisi."); ok = false; }
    else if (u.length < 3 || !USER_PATTERN.test(u)) { setErr(form, "user_login", "Hanya huruf kecil, angka, garis bawah (_), dan tanda hubung (-), minimal 3 karakter."); ok = false; }
    else setErr(form, "user_login", null);
    var em = fieldVal(form, "email");
    if (!em) { setErr(form, "email", "Alamat email wajib diisi."); ok = false; }
    else if (!EMAIL_RE.test(em)) { setErr(form, "email", "Format alamat email belum benar."); ok = false; }
    else setErr(form, "email", null);
    var p = form.querySelector('[name="password"]').value;
    if (!p) { setErr(form, "password", "Kata sandi wajib diisi."); ok = false; }
    else if (p.length < 8) { setErr(form, "password", "Kata sandi minimal 8 karakter."); ok = false; }
    else setErr(form, "password", null);
    var p2 = form.querySelector('[name="password_confirmation"]').value;
    if (!p2) { setErr(form, "password_confirmation", "Konfirmasi kata sandi wajib diisi."); ok = false; }
    else if (p2 !== p) { setErr(form, "password_confirmation", "Konfirmasi kata sandi belum sama."); ok = false; }
    else setErr(form, "password_confirmation", null);
    var terms = form.querySelector('[name="terms_conditions"]');
    if (terms && !terms.checked) { setErr(form, "terms_conditions", "Centang dulu persetujuan Syarat & Ketentuan."); ok = false; }
    else if (terms) setErr(form, "terms_conditions", null);
    return ok;
  }

  function focusFirstError(form) {
    var bad = form.querySelector('[aria-invalid="true"]:not([type=hidden])');
    if (bad) { try { bad.focus({ preventScroll: false }); } catch (err) { bad.focus(); } }
  }

  function setLoading(form, on) {
    var btn = form.querySelector("[data-submit-btn]");
    if (!btn) return;
    if (on) {
      btn.disabled = true;
      btn.classList.add("is-loading");
      btn.setAttribute("aria-busy", "true");
      if (!btn.dataset.label) btn.dataset.label = btn.textContent;
      btn.textContent = "Memproses…";
    } else {
      btn.disabled = false;
      btn.classList.remove("is-loading");
      btn.removeAttribute("aria-busy");
      if (btn.dataset.label) btn.textContent = btn.dataset.label;
    }
  }

  $$("[data-auth-form]", root).forEach(function (form) {
    var kind = form.getAttribute("data-auth-form");
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (form.dataset.submitting === "1") return;
      var ok = kind === "login" ? validateLogin(form) : kind === "lostpassword" ? validateLost(form) : validateRegisterSync(form);
      if (!ok) { focusFirstError(form); return; }
      form.dataset.submitting = "1";
      setLoading(form, true);
      if (kind !== "register") { form.submit(); return; }
      // Daftar: pastikan status username terakhir adalah tersedia.
      var u = fieldVal(form, "user_login");
      var finish = function (state) {
        if (state === "taken") {
          form.dataset.submitting = "";
          setLoading(form, false);
          setErr(form, "user_login", "Nama pengguna sudah digunakan. Pilih yang lain ya.");
          focusFirstError(form);
          return;
        }
        if (state === "invalid") {
          form.dataset.submitting = "";
          setLoading(form, false);
          focusFirstError(form);
          return;
        }
        form.submit();
      };
      if (userState === "available" && checkedValue === u) finish("available");
      else if (userState === "checking" && userPromise) userPromise.then(finish);
      else checkUsername(u).then(finish);
    });
    // Bersihkan galat saat mengetik.
    form.addEventListener("input", function (e) {
      var name = e.target && e.target.name;
      if (!name) return;
      var slot = form.querySelector('[data-err-for="' + name + '"]');
      if (slot && !slot.hidden && name !== "user_login") setErr(form, name, null);
      if (name === "password_confirmation" || name === "password") {
        var p = form.querySelector('[name="password"]');
        var p2 = form.querySelector('[name="password_confirmation"]');
        if (p && p2 && p2.value && p2.value === p.value) setErr(form, "password_confirmation", null);
      }
    });
  });
})();
