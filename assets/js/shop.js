/* halo-tg2: perilaku toko (Fase 4).
   1) Setelah add-to-cart AJAX berhasil, buka cart drawer untuk konfirmasi
      (alur resmi Bagian 9: tidak ada lemparan ke halaman cart / popup lain),
      plus animasi keranjang masuk: tombol keranjang di header berdenyut
      dan item yang baru masuk di drawer tersorot sebentar.
   2) Form add-to-cart bawaan Tutor di halaman course adalah form POST
      klasik yang me-refresh halaman. Form itu dicegat dan dikirim lewat
      AJAX Woo (wc-ajax=add_to_cart) agar halaman diam di tempat; fragment
      Woo (badge jumlah + isi mini cart) diterapkan langsung ke DOM.
      Bila AJAX gagal, form dikirim dengan cara klasik agar pembelian
      tidak pernah macet. */
(function ($) {
  "use strict";

  $(document.body).on("added_to_cart", function () {
    var openBtn = document.querySelector("[data-cart-open]");
    if (openBtn) {
      openBtn.classList.remove("is-bump");
      void openBtn.offsetWidth;
      openBtn.classList.add("is-bump");
      openBtn.click();
    }
    var fresh = document.querySelector(
      ".halo-tg2__drawer-body .widget_shopping_cart_content"
    );
    if (fresh) {
      fresh.classList.remove("is-fresh");
      void fresh.offsetWidth;
      fresh.classList.add("is-fresh");
    }
  });

  document.addEventListener("submit", function (e) {
    var form = e.target;
    if (!form || form.__htAjaxSent || !form.querySelector) {
      return;
    }
    var btn = form.querySelector('button[name="add-to-cart"]');
    if (!btn) {
      return;
    }
    // Tamu yang wajib masuk dulu: biarkan modal masuk Tutor yang bekerja.
    if (btn.classList.contains("tutor-open-login-modal")) {
      return;
    }
    if (!window.HT_SHOP || !HT_SHOP.ajaxUrl || btn.disabled) {
      return;
    }
    e.preventDefault();

    var textSpan = btn.querySelector("span:last-of-type");
    var oldText = textSpan ? textSpan.textContent : "";
    btn.disabled = true;
    btn.classList.add("is-loading");
    if (textSpan) {
      textSpan.textContent = "Menambahkan…";
    }

    var qtyInput = form.querySelector('input[name="quantity"]');
    var body = new FormData();
    // Sengaja TANPA field "add-to-cart": field itu juga dibaca pemroses
    // form klasik Woo pada request yang sama, membuat item masuk 2x.
    body.append("product_id", btn.value);
    body.append("quantity", qtyInput ? qtyInput.value : "1");

    function fallback() {
      form.__htAjaxSent = true;
      btn.disabled = false;
      btn.classList.remove("is-loading");
      if (textSpan) {
        textSpan.textContent = oldText;
      }
      if (form.requestSubmit) {
        form.requestSubmit(btn);
      } else {
        form.submit();
      }
    }

    fetch(HT_SHOP.ajaxUrl, {
      method: "POST",
      body: body,
      credentials: "same-origin"
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (!data || data.error || !data.fragments) {
          fallback();
          return;
        }
        $.each(data.fragments, function (selector, html) {
          $(selector).replaceWith(html);
        });
        btn.classList.remove("is-loading");
        btn.classList.add("is-added");
        if (textSpan) {
          textSpan.textContent = "Ditambahkan";
        }
        $(document.body).trigger("added_to_cart", [
          data.fragments,
          data.cart_hash,
          $(btn)
        ]);
        // Konfirmasi resmi di situs ini adalah cart drawer, bukan tautan
        // "Lihat keranjang": Woo menyisipkan tautan a.added_to_cart di
        // samping tombol pada event yang sama, dan handler Tutor menempelkan
        // ikon keranjang ke tautan itu setiap event berbunyi (ikon menumpuk
        // sebanyak jumlah klik). Singkirkan tautannya dari kartu beli.
        setTimeout(function () {
          var scope = form.closest(".pva-buy-card") || form;
          scope
            .querySelectorAll("a.added_to_cart")
            .forEach(function (el) {
              el.remove();
            });
        }, 60);
        setTimeout(function () {
          btn.disabled = false;
          btn.classList.remove("is-added");
          if (textSpan) {
            textSpan.textContent = oldText;
          }
        }, 1600);
      })
      .catch(fallback);
  });
})(jQuery);
