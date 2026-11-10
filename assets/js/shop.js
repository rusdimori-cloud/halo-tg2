/* halo-tg2 — perilaku toko (Fase 4).
   Setelah add-to-cart AJAX berhasil, buka cart drawer untuk konfirmasi
   (alur resmi Bagian 9: tidak ada lemparan ke halaman cart / popup lain). */
(function ($) {
  "use strict";
  $(document.body).on("added_to_cart", function () {
    var btn = document.querySelector("[data-cart-open]");
    if (btn) {
      btn.click();
    }
  });
})(jQuery);
