/* halo-tg2 — perilaku halaman single course (Fase 3).
   Pindahan dari child theme lama (v2.7):
   1) Kurikulum selalu terbuka ala ClassAI: header topik tidak melipat isi.
   2) "Apa yang akan kamu Pelajari" + "Penonton" digabung jadi SATU panel
      dua kolom (.pva-duo) — perubahan gaya pada satu berlaku untuk keduanya. */
(function () {
  "use strict";
  document.addEventListener("click", function (e) {
    if (e.target.closest(".pva-curriculum .tutor-accordion-item-header")) {
      e.preventDefault();
      e.stopPropagation();
    }
  }, true);

  var benefits = document.querySelector(".tutor-course-details-widget-col-2");
  var audience = null;
  document.querySelectorAll(".pva-below-grid .tutor-course-details-widget").forEach(function (w) {
    var h = w.querySelector("h3");
    if (h && /penonton|audience/i.test(h.textContent)) audience = w;
  });
  if (audience) audience.classList.add("pva-audience");
  if (benefits && audience && benefits.parentNode) {
    var duo = document.createElement("div");
    duo.className = "pva-duo";
    benefits.parentNode.insertBefore(duo, benefits);
    duo.appendChild(benefits);
    duo.appendChild(audience);
  } else if (audience && benefits) {
    benefits.insertAdjacentElement("afterend", audience);
    audience.classList.add("pva-audience-moved");
  }
})();
