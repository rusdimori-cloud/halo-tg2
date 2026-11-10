<?php
/**
 * Halaman depan halo-tg2 (Fase 2) — pindahan landing yang disetujui
 * (halo-tg2-landing.html, final 7 Nov 2026). Header/footer dari tema;
 * gaya section dari assets/css/landing.css; skrip dari assets/js/landing.js.
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
get_header();
?>
<main class="halo-tg2__front">
<!-- ============ HERO ============ -->
<section class="halo-tg2__hero">
  <div class="halo-tg2__glow" style="width:560px;height:560px;top:-240px;right:-120px"></div>
  <div class="halo-tg2__wrap">
    <div class="halo-tg2__hero-grid">
      <div class="halo-tg2__hero-copy">
        <span class="halo-tg2__eyebrow halo-tg2__t-label-sm">Persiapan Ujian Tokutei Ginou 2</span>
        <h1 class="halo-tg2__t-h-lg" aria-label="Belajar TG2 Kapan Saja dan Di Mana Saja, Semudah Buka Ponsel, Tablet, atau Laptop">Belajar TG2 Kapan Saja dan Di Mana Saja, Semudah Buka <span class="halo-tg2__rot" aria-hidden="true"><span class="halo-tg2__rot-word">Ponsel</span><span class="halo-tg2__rot-word">Tablet</span><span class="halo-tg2__rot-word">Laptop</span></span></h1>
        <p class="halo-tg2__t-body-md">Tanpa buku tebal, tanpa bingung mulai dari mana, belajar lebih terarah.</p>
        <div class="halo-tg2__hero-cta">
          <a class="halo-tg2__btn halo-tg2__btn--primary" href="/masuk"><i class="fa-regular fa-user-plus" aria-hidden="true"></i> Mulai Gratis</a>
          <a class="halo-tg2__btn halo-tg2__btn--secondary" href="#simulasi"><i class="fa-regular fa-layer-group" aria-hidden="true"></i> Lihat Simulasi CBT</a>
        </div>
        <span class="halo-tg2__t-mono-sm" style="color:var(--ht-text-3)">Tanpa kartu kredit · Materi awal gratis</span>
        <div class="halo-tg2__proof">
          <span class="halo-tg2__avatars" aria-hidden="true"><span>IM</span><span>AR</span><span>HS</span></span>
          <p class="halo-tg2__t-body-sm" style="color:var(--ht-text-2)"><b>312+ peserta</b> sedang belajar — <b>87%</b> dari yang menuntaskan simulasi berhasil lulus.</p>
        </div>
        <p class="halo-tg2__t-mono-sm" style="color:var(--ht-text-3)">Khusus jalur manufaktur · <span lang="ja">生産管理オペレーション 3級</span></p>
      </div>
      <div class="halo-tg2__hero-visual" id="simulasi"><span class="halo-tg2__float halo-tg2__float--1 halo-tg2__t-label-md"><i class="fa-regular fa-circle-check" aria-hidden="true"></i> Skor 82 · LULUS</span><span class="halo-tg2__float halo-tg2__float--2 halo-tg2__t-label-md"><i class="fa-regular fa-cards-blank" aria-hidden="true"></i> 600+ Flashcard</span>
      <figure class="halo-tg2__card halo-tg2__hero-shot" data-halo-tg2-reveal>
        <!-- GAMBAR STOK SEMENTARA — ganti src di bawah ini dengan screenshot CBT / dashboard (HP atau desktop) -->
        <img class="halo-tg2__hero-shot-img" src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600&auto=format&fit=crop" alt="Peserta belajar persiapan ujian TG2 lewat laptop" width="1600" height="1000" decoding="async">
      </figure>
    </div>
    </div>
    <div class="halo-tg2__hero-stats">
      <div class="halo-tg2__stat"><b><span data-count="312">0</span>+</b><span class="halo-tg2__t-mono-sm">Peserta Aktif</span></div>
      <div class="halo-tg2__stat"><b><span data-count="87">0</span>%</b><span class="halo-tg2__t-mono-sm">Tingkat Kelulusan</span></div>
      <div class="halo-tg2__stat"><b><span data-count="320">0</span>+</b><span class="halo-tg2__t-mono-sm">Soal CBT</span></div>
      <div class="halo-tg2__stat"><b><span data-count="600">0</span>+</b><span class="halo-tg2__t-mono-sm">Flashcard &amp; Kosakata</span></div>
    </div>
  </div>
</section>

<!-- ============ FITUR ============ -->
<div class="halo-tg2__marquee" aria-hidden="true"><div class="halo-tg2__marquee-track"><div class="halo-tg2__marquee-set"><span>Simulasi CBT</span><i>✦</i><span>600+ Flashcard</span><i>✦</i><span>Kosakata JA → ID</span><i>✦</i><span>Timer Persis Ujian Asli</span><i>✦</i><span>8× Simulasi Penuh</span><i>✦</i><span>Progress Terukur</span><i>✦</i><span>15–20 Menit Sehari</span><i>✦</i><span>Tanpa Kartu Kredit</span><i>✦</i></div><div class="halo-tg2__marquee-set"><span>Simulasi CBT</span><i>✦</i><span>600+ Flashcard</span><i>✦</i><span>Kosakata JA → ID</span><i>✦</i><span>Timer Persis Ujian Asli</span><i>✦</i><span>8× Simulasi Penuh</span><i>✦</i><span>Progress Terukur</span><i>✦</i><span>15–20 Menit Sehari</span><i>✦</i><span>Tanpa Kartu Kredit</span><i>✦</i></div></div></div>
<section class="halo-tg2__section" id="fitur">
  <div class="halo-tg2__wrap">
    <div class="halo-tg2__sec-head" data-halo-tg2-reveal>
      <span class="halo-tg2__eyebrow halo-tg2__t-label-sm">Fitur</span>
      <h2 class="halo-tg2__t-h-md">Semua yang Anda butuhkan untuk lulus</h2>
      <p class="halo-tg2__t-body-md">Pilih fitur di kiri — panel di kanan menunjukkan cara kerjanya di dashboard Anda.</p>
    </div>
    <div class="halo-tg2__feat-grid">
      <div class="halo-tg2__notes" data-halo-tg2-reveal>
        <button class="halo-tg2__note is-on" type="button" data-note="cbt" aria-pressed="true">
          <h3><i class="fa-regular fa-laptop" aria-hidden="true"></i> Simulasi CBT</h3>
          <p class="halo-tg2__t-body-sm">Computer Based Test autentik dengan timer dan interface seperti ujian sesungguhnya.</p>
        </button>
        <button class="halo-tg2__note" type="button" data-note="vocab" aria-pressed="false">
          <h3><i class="fa-regular fa-book-open" aria-hidden="true"></i> Kosakata Lengkap</h3>
          <p class="halo-tg2__t-body-sm">Kosakata teknis manajemen produksi bilingual Jepang–Indonesia, tersusun per bab.</p>
        </button>
        <button class="halo-tg2__note" type="button" data-note="flash" aria-pressed="false">
          <h3><i class="fa-regular fa-cards-blank" aria-hidden="true"></i> Flashcard Digital</h3>
          <p class="halo-tg2__t-body-sm">Pengulangan terstruktur yang membuat istilah produksi menempel tanpa terasa menghafal.</p>
        </button>
        <button class="halo-tg2__note" type="button" data-note="prog" aria-pressed="false">
          <h3><i class="fa-regular fa-chart-line" aria-hidden="true"></i> Dashboard Progress</h3>
          <p class="halo-tg2__t-body-sm">Skor, bab yang dikuasai, dan fokus berikutnya — semua terlihat dalam satu layar.</p>
        </button>
      </div>
      <div class="halo-tg2__card halo-tg2__panel" data-halo-tg2-reveal aria-hidden="true">
        <div class="halo-tg2__zone is-on" data-zone="cbt">
          <div class="halo-tg2__zone-head"><span class="halo-tg2__t-mono-sm">Simulasi CBT</span><span class="halo-tg2__t-mono-sm">12:45</span></div>
          <div class="halo-tg2__zone-row"><span lang="ja">工程管理の目的はどれですか。</span><b class="halo-tg2__t-label-md">Soal 7/20</b></div>
          <div class="halo-tg2__progress"><span class="halo-tg2__progress-bar" style="width:35%"></span></div>
        </div>
        <div class="halo-tg2__zone" data-zone="vocab">
          <div class="halo-tg2__zone-head"><span class="halo-tg2__t-mono-sm">Kosakata · Bab 2</span><span class="halo-tg2__t-mono-sm">JA → ID</span></div>
          <div class="halo-tg2__zone-row"><b lang="ja">段取り</b><span>Persiapan kerja</span></div>
          <div class="halo-tg2__zone-row"><b lang="ja">歩留まり</b><span>Tingkat hasil baik</span></div>
        </div>
        <div class="halo-tg2__zone" data-zone="flash">
          <div class="halo-tg2__zone-head"><span class="halo-tg2__t-mono-sm">Flashcard</span><span class="halo-tg2__t-mono-sm">128 / 600</span></div>
          <div class="halo-tg2__zone-row"><b lang="ja">品質管理</b><span>Tekan untuk membalik</span></div>
        </div>
        <div class="halo-tg2__zone" data-zone="prog">
          <div class="halo-tg2__zone-head"><span class="halo-tg2__t-mono-sm">Progress Mingguan</span><span class="halo-tg2__t-mono-sm">82%</span></div>
          <div class="halo-tg2__zone-bars">
            <div class="halo-tg2__progress"><span class="halo-tg2__progress-bar" style="width:82%"></span></div>
            <div class="halo-tg2__progress"><span class="halo-tg2__progress-bar" style="width:64%"></span></div>
            <div class="halo-tg2__progress"><span class="halo-tg2__progress-bar" style="width:45%"></span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ FLASHCARD ============ -->
<section class="halo-tg2__section halo-tg2__fc-sec halo-tg2__band--light" id="flashcard">
  <div class="halo-tg2__wrap">
    <div class="halo-tg2__fc-grid">
      <div class="halo-tg2__fc-copy" data-halo-tg2-reveal>
        <span class="halo-tg2__eyebrow halo-tg2__t-label-sm">Flashcard</span>
        <h2 class="halo-tg2__t-h-md">Hafalkan kosakata teknis, tanpa terasa menghafal</h2>
        <p class="halo-tg2__t-body-md">Balik kartunya, cek artinya, tandai yang sudah hafal. Ulangi sedikit demi sedikit — sistemnya yang mengatur ritmenya untuk Anda.</p>
        <div class="halo-tg2__fc-index" data-fc-dots></div>
        <div class="halo-tg2__fc-prog">
          <div class="halo-tg2__fc-prog-top halo-tg2__t-label-md"><span>Sudah hafal</span><b data-fc-known>0 / 5</b></div>
          <div class="halo-tg2__progress"><span class="halo-tg2__progress-bar" data-fc-bar style="width:0%"></span></div>
        </div>
        <a class="halo-tg2__btn halo-tg2__btn--secondary" href="https://pvent.jp/kelas/600-kosakata-flashcard-bisnis-karir/"><i class="fa-regular fa-cards-blank" aria-hidden="true"></i> Buka 600+ Flashcard</a>
      </div>
      <div class="halo-tg2__fc-stage" data-halo-tg2-reveal>
        <div class="halo-tg2__deck" data-fc-deck tabindex="0" aria-label="Dek flashcard. Spasi untuk membalik, panah kiri dan kanan untuk berpindah kartu.">
          <div class="halo-tg2__ghost halo-tg2__ghost--2"></div>
          <div class="halo-tg2__ghost halo-tg2__ghost--1"></div>
        </div>
        <div class="halo-tg2__fc-ctrl">
          <button class="halo-tg2__btn halo-tg2__btn--secondary halo-tg2__btn--icon" type="button" data-fc-prev aria-label="Kartu sebelumnya"><i class="fa-regular fa-arrow-left" aria-hidden="true"></i></button>
          <button class="halo-tg2__btn halo-tg2__btn--secondary" type="button" data-fc-again><i class="fa-regular fa-rotate-left" aria-hidden="true"></i> Ulangi Nanti</button>
          <button class="halo-tg2__btn halo-tg2__btn--primary" type="button" data-fc-know><i class="fa-regular fa-check" aria-hidden="true"></i> Sudah Hafal</button>
          <button class="halo-tg2__btn halo-tg2__btn--secondary halo-tg2__btn--icon" type="button" data-fc-next aria-label="Kartu berikutnya"><i class="fa-regular fa-arrow-right" aria-hidden="true"></i></button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ KELAS ============ -->
<section class="halo-tg2__section" id="kelas">
  <div class="halo-tg2__wrap">
    <div class="halo-tg2__sec-head" data-halo-tg2-reveal>
      <span class="halo-tg2__eyebrow halo-tg2__t-label-sm">Kelas</span>
      <h2 class="halo-tg2__t-h-md">Pilih jalur belajar Anda</h2>
      <p class="halo-tg2__t-body-md">Mulai dari yang gratis, atau langsung ke paket lengkap dengan simulasi CBT.</p>
    </div>
    <div class="halo-tg2__filters" data-halo-tg2-reveal>
      <button class="halo-tg2__chip" type="button" data-filter="all" aria-pressed="true">Semua</button>
      <button class="halo-tg2__chip" type="button" data-filter="manufaktur" aria-pressed="false">Manufaktur</button>
      <button class="halo-tg2__chip" type="button" data-filter="bisnis" aria-pressed="false">Bisnis Karir</button>
    </div>
    <div class="halo-tg2__courses">
      <article class="halo-tg2__card halo-tg2__course" data-cat="manufaktur" data-halo-tg2-reveal>
        <div class="halo-tg2__course-cover halo-tg2__course-cover--1">
          <span class="halo-tg2__course-badge">Paket Lengkap</span>
          <button class="halo-tg2__fav" type="button" data-fav aria-pressed="false" aria-label="Simpan ke favorit"><i class="fa-regular fa-heart" aria-hidden="true"></i></button>
          <span class="halo-tg2__course-tile"><i class="fa-regular fa-industry" aria-hidden="true"></i></span>
          <div class="halo-tg2__course-cover-text"><span>Manufaktur · CBT + Flashcard</span><b>Manajemen Produksi</b></div>
        </div>
        <div class="halo-tg2__course-body">
          <h3 class="halo-tg2__course-title"><a href="https://pvent.jp/kelas/cbt-operasi-manajemen-produksi-tingkat-3/"><span lang="ja">生産管理オペレーション 3級</span> &amp; 600+ Kosakata Flashcard</a></h3>
          <div class="halo-tg2__course-meta2">
            <span class="halo-tg2__format"><i class="fa-regular fa-laptop" aria-hidden="true"></i> Simulasi CBT</span>
            <span class="halo-tg2__t-body-sm">10 modul · 24 jam materi · 8 simulasi</span>
          </div>
          <div class="halo-tg2__course-foot">
            <div class="halo-tg2__price2"><s>¥7.500</s><span class="halo-tg2__price2-row"><b>¥3.750</b><span>/ 6 bulan</span><em>50% OFF</em></span></div>
            <span class="halo-tg2__level">Pemula</span>
          </div>
        </div>
      </article>
      <article class="halo-tg2__card halo-tg2__course" data-cat="bisnis" data-halo-tg2-reveal style="--d:.08s">
        <div class="halo-tg2__course-cover halo-tg2__course-cover--2">
          <span class="halo-tg2__course-badge">Hemat 72%</span>
          <button class="halo-tg2__fav" type="button" data-fav aria-pressed="false" aria-label="Simpan ke favorit"><i class="fa-regular fa-heart" aria-hidden="true"></i></button>
          <span class="halo-tg2__course-tile"><i class="fa-regular fa-cards-blank" aria-hidden="true"></i></span>
          <div class="halo-tg2__course-cover-text"><span>Bisnis Karir · Flashcard</span><b>Flashcard Bisnis</b></div>
        </div>
        <div class="halo-tg2__course-body">
          <h3 class="halo-tg2__course-title"><a href="https://pvent.jp/kelas/600-kosakata-flashcard-bisnis-karir/">600+ Kosakata Flashcard Bisnis Karir</a></h3>
          <div class="halo-tg2__course-meta2">
            <span class="halo-tg2__format"><i class="fa-regular fa-cards-blank" aria-hidden="true"></i> Flashcard Digital</span>
            <span class="halo-tg2__t-body-sm">600+ kartu · Akses 1 tahun · Dari HP</span>
          </div>
          <div class="halo-tg2__course-foot">
            <div class="halo-tg2__price2"><s>¥3.500</s><span class="halo-tg2__price2-row"><b>¥990</b><span>/ 1 tahun</span><em>72% OFF</em></span></div>
            <span class="halo-tg2__level">Semua Level</span>
          </div>
        </div>
      </article>
      <article class="halo-tg2__card halo-tg2__course" data-cat="manufaktur bisnis" data-halo-tg2-reveal style="--d:.16s">
        <div class="halo-tg2__course-cover halo-tg2__course-cover--3">
          <span class="halo-tg2__course-badge">Gratis</span>
          <button class="halo-tg2__fav" type="button" data-fav aria-pressed="false" aria-label="Simpan ke favorit"><i class="fa-regular fa-heart" aria-hidden="true"></i></button>
          <span class="halo-tg2__course-tile"><i class="fa-regular fa-book-open" aria-hidden="true"></i></span>
          <div class="halo-tg2__course-cover-text"><span>Manufaktur · Pengantar</span><b>Kelas Pengantar</b></div>
        </div>
        <div class="halo-tg2__course-body">
          <h3 class="halo-tg2__course-title"><a href="https://pvent.jp/kelas/koasakata-flashcard-operasi-manajemen-produksi-tingkat-3/">Kosakata &amp; Flashcard Operasi Manajemen Produksi Tingkat 3</a></h3>
          <div class="halo-tg2__course-meta2">
            <span class="halo-tg2__format"><i class="fa-regular fa-book-open" aria-hidden="true"></i> Materi Pengantar</span>
            <span class="halo-tg2__t-body-sm">Materi pengantar · Flashcard dasar</span>
          </div>
          <div class="halo-tg2__course-foot">
            <div class="halo-tg2__price2"><span class="halo-tg2__price2-row"><b>Gratis</b><span>/ selamanya</span></span></div>
            <span class="halo-tg2__level">Pemula</span>
          </div>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ============ TELEMETRI ============ -->
<section class="halo-tg2__section">
  <div class="halo-tg2__wrap">
    <div class="halo-tg2__telemetry">
      <div class="halo-tg2__telemetry-big" data-halo-tg2-reveal>
        <span class="halo-tg2__t-display" data-rotate-value>87%</span>
        <span class="halo-tg2__t-mono-sm" data-rotate-label>Tingkat kelulusan peserta</span>
        <p class="halo-tg2__t-body-md" style="color:var(--ht-text-2)" data-rotate-desc>Hasil nyata dari peserta yang menyelesaikan simulasi sampai tuntas sebelum hari ujian.</p>
      </div>
      <div class="halo-tg2__telemetry-list" data-halo-tg2-reveal>
        <div class="halo-tg2__telemetry-row"><span class="halo-tg2__t-body-sm">Bank soal CBT terstruktur per bab</span><b><span data-count="320">0</span>+</b></div>
        <div class="halo-tg2__telemetry-row"><span class="halo-tg2__t-body-sm">Flashcard kosakata teknis bilingual</span><b><span data-count="600">0</span>+</b></div>
        <div class="halo-tg2__telemetry-row"><span class="halo-tg2__t-body-sm">Jam materi video &amp; latihan</span><b><span data-count="24">0</span></b></div>
        <div class="halo-tg2__telemetry-row"><span class="halo-tg2__t-body-sm">Menit belajar cukup per hari</span><b>15–20</b></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ LANGKAH ============ -->
<section class="halo-tg2__section" id="cara">
  <div class="halo-tg2__wrap">
    <div class="halo-tg2__steps-layout">
    <div class="halo-tg2__steps-side">
    <div class="halo-tg2__sec-head halo-tg2__steps-head" data-halo-tg2-reveal>
      <span class="halo-tg2__eyebrow halo-tg2__t-label-sm">Cara Kerja</span>
      <h2 class="halo-tg2__t-h-md">Tiga langkah, langsung belajar</h2>
      <p class="halo-tg2__t-body-md">Gulir ke bawah — garisnya bergerak mengikuti perjalananmu, sama seperti progres belajarmu nanti.</p>
    </div>
    </div>
    <div class="halo-tg2__timeline" data-timeline>
      <div class="halo-tg2__tl-track" aria-hidden="true"><span class="halo-tg2__tl-fill" data-tl-fill></span></div>
      <div class="halo-tg2__tl-step">
        <span class="halo-tg2__tl-node" data-tl-node>01</span>
        <div class="halo-tg2__tl-body">
          <h3>Daftar dengan Mudah</h3>
          <p class="halo-tg2__t-body-md">Buat akun gratis dalam hitungan detik — tanpa biaya pendaftaran, tanpa kartu kredit. Miniaturnya berjalan sendiri, persis semudah aslinya.</p>
          <div class="halo-tg2__card halo-tg2__tl-card halo-tg2__demo halo-tg2__demo--signup" data-demo-signup aria-hidden="true">
            <div class="halo-tg2__demo-form" data-demo-form>
              <span class="halo-tg2__t-mono-sm" style="color:var(--ht-text-3)">Daftar · Langkah 1</span>
              <div class="halo-tg2__demo-group">
                <span class="halo-tg2__t-label-md" style="color:var(--ht-text-2)">USERNAME</span>
                <div class="halo-tg2__demo-field" data-demo-field="user"><span class="halo-tg2__demo-typed" data-typed-user></span><span class="halo-tg2__demo-caret"></span><span class="halo-tg2__demo-ph">pilih username</span></div>
              </div>
              <div class="halo-tg2__demo-group">
                <span class="halo-tg2__t-label-md" style="color:var(--ht-text-2)">EMAIL</span>
                <div class="halo-tg2__demo-field" data-demo-field="email"><span class="halo-tg2__demo-typed" data-typed-email></span><span class="halo-tg2__demo-caret"></span><span class="halo-tg2__demo-ph">nama@email.com</span></div>
              </div>
              <span class="halo-tg2__demo-btn" data-demo-btn><span class="halo-tg2__demo-spinner"></span><span data-demo-btn-label>Daftar Gratis</span></span>
              <span class="halo-tg2__t-mono-sm" style="color:var(--ht-text-3)">Tanpa kartu kredit</span>
              <span class="halo-tg2__demo-cursor"><i class="fa-solid fa-arrow-pointer" aria-hidden="true"></i></span>
            </div>
            <div class="halo-tg2__demo-success">
              <span class="halo-tg2__demo-check"><i class="fa-regular fa-check" aria-hidden="true"></i></span>
              <span class="halo-tg2__demo-success-title">Pendaftaran Berhasil!</span>
              <span class="halo-tg2__t-body-sm" style="color:var(--ht-text-2)">Akun alex_tg2 sudah aktif — selamat belajar.</span>
            </div>
          </div>
        </div>
      </div>
      <div class="halo-tg2__tl-step">
        <span class="halo-tg2__tl-node" data-tl-node>02</span>
        <div class="halo-tg2__tl-body">
          <h3>Pilih Materi Belajar</h3>
          <p class="halo-tg2__t-body-md">Katalog materinya bergeser sendiri — pilih sesuai target ujianmu, dari 生産管理 sampai simulasi CBT. Semua tersusun bertahap per bab, cukup 15–20 menit per hari.</p>
          <div class="halo-tg2__card halo-tg2__tl-card halo-tg2__demo halo-tg2__demo--catalog" aria-hidden="true">
            <span class="halo-tg2__t-mono-sm" style="color:var(--ht-text-3)">Belajar · Langkah 2</span>
            <div class="halo-tg2__demo-marquee">
              <div class="halo-tg2__demo-track">
                <div class="halo-tg2__demo-set">
                  <div class="halo-tg2__demo-course"><span class="halo-tg2__demo-thumb"><i class="fa-regular fa-industry"></i></span><b lang="ja">生産管理オペレーション3級</b><span>Kursus Utama</span></div>
                  <div class="halo-tg2__demo-course"><span class="halo-tg2__demo-thumb"><i class="fa-regular fa-briefcase"></i></span><b lang="ja">ビジネス・キャリア検定</b><span>Ujian Karier</span></div>
                  <div class="halo-tg2__demo-course"><span class="halo-tg2__demo-thumb"><i class="fa-regular fa-id-card"></i></span><b lang="ja">特定技能2号</b><span>Jalur TG2</span></div>
                  <div class="halo-tg2__demo-course"><span class="halo-tg2__demo-thumb"><i class="fa-regular fa-laptop"></i></span><b lang="ja">CBT 模擬試験</b><span>Simulasi Ujian</span></div>
                  <div class="halo-tg2__demo-course"><span class="halo-tg2__demo-thumb"><i class="fa-regular fa-book-open"></i></span><b lang="ja">学習教材</b><span>Materi Belajar</span></div>
                </div>
                <div class="halo-tg2__demo-set">
                  <div class="halo-tg2__demo-course"><span class="halo-tg2__demo-thumb"><i class="fa-regular fa-industry"></i></span><b lang="ja">生産管理オペレーション3級</b><span>Kursus Utama</span></div>
                  <div class="halo-tg2__demo-course"><span class="halo-tg2__demo-thumb"><i class="fa-regular fa-briefcase"></i></span><b lang="ja">ビジネス・キャリア検定</b><span>Ujian Karier</span></div>
                  <div class="halo-tg2__demo-course"><span class="halo-tg2__demo-thumb"><i class="fa-regular fa-id-card"></i></span><b lang="ja">特定技能2号</b><span>Jalur TG2</span></div>
                  <div class="halo-tg2__demo-course"><span class="halo-tg2__demo-thumb"><i class="fa-regular fa-laptop"></i></span><b lang="ja">CBT 模擬試験</b><span>Simulasi Ujian</span></div>
                  <div class="halo-tg2__demo-course"><span class="halo-tg2__demo-thumb"><i class="fa-regular fa-book-open"></i></span><b lang="ja">学習教材</b><span>Materi Belajar</span></div>
                </div>
              </div>
            </div>
            <span class="halo-tg2__t-body-sm" style="color:var(--ht-text-2)">5 katalog materi · bergeser otomatis</span>
          </div>
        </div>
      </div>
      <div class="halo-tg2__tl-step">
        <span class="halo-tg2__tl-node" data-tl-node>03</span>
        <div class="halo-tg2__tl-body">
          <h3>Pantau Hasil Belajar</h3>
          <p class="halo-tg2__t-body-md">Semua aktivitasmu terangkum otomatis di dashboard — jumlah soal, progres bab, sampai prediksi kelulusan yang naik seiring konsistensimu belajar.</p>
          <div class="halo-tg2__card halo-tg2__tl-card halo-tg2__demo halo-tg2__demo--dash" data-demo-dash aria-hidden="true">
            <span class="halo-tg2__t-mono-sm" style="color:var(--ht-text-3)">Dashboard · Langkah 3</span>
            <div class="halo-tg2__demo-dash-top">
              <div class="halo-tg2__demo-pass">
                <span class="halo-tg2__demo-pass-num"><span data-demo-count="83">0</span>%</span>
                <span class="halo-tg2__t-label-md" style="color:var(--ht-demo-blue)">TINGKAT KELULUSAN</span>
                <p class="halo-tg2__t-body-sm" style="color:var(--ht-text-2)">Prediksi tingkat kelulusan berdasarkan perkembangan belajar.</p>
              </div>
              <svg class="halo-tg2__demo-chart" viewBox="0 0 260 96" fill="none" aria-hidden="true">
                <path d="M0 24H260M0 48H260M0 72H260" stroke="var(--ht-line)" stroke-width="1"/>
                <path class="halo-tg2__demo-chart-area" d="M0 78 C30 74 45 60 70 62 C95 64 115 44 140 40 C165 36 195 26 220 18 C235 13 248 11 260 9 L260 96 L0 96 Z" fill="rgba(43,127,255,.10)"/>
                <path class="halo-tg2__demo-chart-line" data-demo-chart-line d="M0 78 C30 74 45 60 70 62 C95 64 115 44 140 40 C165 36 195 26 220 18 C235 13 248 11 260 9" stroke="#2B7FFF" stroke-width="2.5" stroke-linecap="round"/>
                <circle class="halo-tg2__demo-chart-dot" cx="260" cy="9" r="4" fill="#2B7FFF"/>
              </svg>
            </div>
            <div class="halo-tg2__demo-stats">
              <div class="halo-tg2__demo-stat"><span class="halo-tg2__t-mono-sm" style="color:var(--ht-text-3)">SOAL DIKERJAKAN</span><b><span data-demo-count="1248">0</span> soal</b></div>
              <div class="halo-tg2__demo-stat"><span class="halo-tg2__t-mono-sm" style="color:var(--ht-text-3)">SKOR SIMULASI CBT</span><b><span data-demo-count="82">0</span> / 100</b></div>
            </div>
            <div class="halo-tg2__demo-prog">
              <div class="halo-tg2__demo-prog-top"><span class="halo-tg2__t-label-md" style="color:var(--ht-text-2)">PROGRES BELAJAR</span><b><span data-demo-count="76">0</span>%</b></div>
              <div class="halo-tg2__demo-bar"><span data-demo-bar></span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONI ============ -->
<section class="halo-tg2__section halo-tg2__band--dark" id="testimoni">
  <div class="halo-tg2__wrap">
    <div class="halo-tg2__sec-head" data-halo-tg2-reveal>
      <span class="halo-tg2__eyebrow halo-tg2__t-label-sm">Testimoni</span>
      <h2 class="halo-tg2__t-h-md">Kata mereka yang sudah lulus</h2>
      <p class="halo-tg2__t-body-md">Cerita nyata dari mereka yang sudah membuktikannya langsung di ruang ujian sesungguhnya.</p>
    </div>
    <div class="halo-tg2__testis">
      <figure class="halo-tg2__card halo-tg2__testi" data-halo-tg2-reveal>
        <i class="fa-regular fa-quote-left" aria-hidden="true"></i>
        <p>Awalnya udah beli bukunya, tapi bingung mulai dari mana. Pas nemu platform ini, langsung kebantu banget. Simulasi CBT-nya mirip banget sama ujian asli. Pas ujian beneran jadi nggak gugup sama sekali.</p>
        <figcaption class="halo-tg2__testi-who"><span class="halo-tg2__avatar">IM</span><div><b class="halo-tg2__t-label-lg">Ikhsan Maulana F.</b><span class="halo-tg2__t-mono-sm">Peserta</span></div></figcaption>
      </figure>
      <figure class="halo-tg2__card halo-tg2__testi" data-halo-tg2-reveal>
        <i class="fa-regular fa-quote-left" aria-hidden="true"></i>
        <p>Ini beneran ngebantu banget. Flashcard, simulasi CBT, dan kumpulan kosakata bikin belajar jadi lebih kebayang dan ga mumet. Progress belajarnya keliatan jelas, bisa dibuka di semua device.</p>
        <figcaption class="halo-tg2__testi-who"><span class="halo-tg2__avatar">AR</span><div><b class="halo-tg2__t-label-lg">Arif Rahman</b><span class="halo-tg2__t-mono-sm">Peserta</span></div></figcaption>
      </figure>
      <figure class="halo-tg2__card halo-tg2__testi" data-halo-tg2-reveal>
        <i class="fa-regular fa-quote-left" aria-hidden="true"></i>
        <p>Flashcard di sini jadi penyelamat. Istilah produksi yang biasanya susah dihafalin jadi lebih nempel karena disajikan singkat dan jelas. Belajarnya fleksibel, bisa dicicil, tapi progresnya tetap kerasa.</p>
        <figcaption class="halo-tg2__testi-who"><span class="halo-tg2__avatar">HS</span><div><b class="halo-tg2__t-label-lg">Hadi Susanto</b><span class="halo-tg2__t-mono-sm">Peserta</span></div></figcaption>
      </figure>
    </div>
  </div>
</section>

<!-- ============ FAQ ============ -->
<section class="halo-tg2__section" id="faq">
  <div class="halo-tg2__wrap">
    <div class="halo-tg2__sec-head" data-halo-tg2-reveal>
      <span class="halo-tg2__eyebrow halo-tg2__t-label-sm">FAQ</span>
      <h2 class="halo-tg2__t-h-md">Pertanyaan yang sering ditanyakan</h2>
      <p class="halo-tg2__t-body-md">Klik pertanyaannya — jawabannya langsung tampil.</p>
    </div>
    <div class="halo-tg2__faq2">
      <div class="halo-tg2__faq-list" data-halo-tg2-reveal>
        <button class="halo-tg2__faq-item-btn is-on" type="button" data-faq="1" aria-pressed="true">Bagaimana cara mendaftar dan mulai belajar? <i class="fa-regular fa-arrow-right" aria-hidden="true"></i></button>
        <button class="halo-tg2__faq-item-btn" type="button" data-faq="2" aria-pressed="false">Metode pembayaran apa yang tersedia? <i class="fa-regular fa-arrow-right" aria-hidden="true"></i></button>
        <button class="halo-tg2__faq-item-btn" type="button" data-faq="3" aria-pressed="false">Apakah langganan saya diperpanjang otomatis? <i class="fa-regular fa-arrow-right" aria-hidden="true"></i></button>
        <button class="halo-tg2__faq-item-btn" type="button" data-faq="4" aria-pressed="false">Bisakah saya mendapatkan pengembalian uang? <i class="fa-regular fa-arrow-right" aria-hidden="true"></i></button>
        <button class="halo-tg2__faq-item-btn" type="button" data-faq="5" aria-pressed="false">Perlu install aplikasi untuk mengaksesnya? <i class="fa-regular fa-arrow-right" aria-hidden="true"></i></button>
      </div>
      <div class="halo-tg2__card halo-tg2__faq-pane" data-halo-tg2-reveal>
        <div class="halo-tg2__faq-a2 is-on" data-faq-pane="1">
          <span class="halo-tg2__t-mono-sm" style="color:var(--ht-primary-70)">Pertanyaan 01</span>
          <h3>Bagaimana cara mendaftar dan mulai belajar?</h3>
          <p class="halo-tg2__t-body-sm">Klik tombol Daftar, isi nama dan email, lalu verifikasi melalui tautan yang dikirim ke kotak masuk Anda. Setelah masuk, Anda sudah bisa langsung mengakses materi gratis. Untuk materi berbayar, cukup pilih paket yang sesuai dan lakukan pembayaran.</p>
        </div>
        <div class="halo-tg2__faq-a2" data-faq-pane="2">
          <span class="halo-tg2__t-mono-sm" style="color:var(--ht-primary-70)">Pertanyaan 02</span>
          <h3>Metode pembayaran apa yang tersedia?</h3>
          <p class="halo-tg2__t-body-sm">Saat ini kami hanya menerima pembayaran melalui kartu kredit. Transaksi diproses secara aman melalui payment gateway terenkripsi. Kami berencana menambah metode pembayaran lain ke depannya.</p>
        </div>
        <div class="halo-tg2__faq-a2" data-faq-pane="3">
          <span class="halo-tg2__t-mono-sm" style="color:var(--ht-primary-70)">Pertanyaan 03</span>
          <h3>Apakah langganan saya diperpanjang otomatis?</h3>
          <p class="halo-tg2__t-body-sm">Tidak. Kami tidak menerapkan perpanjangan otomatis. Setiap perpanjangan dilakukan secara manual sesuai kebutuhan. Anda tidak akan dikenakan biaya apa pun tanpa persetujuan eksplisit.</p>
        </div>
        <div class="halo-tg2__faq-a2" data-faq-pane="4">
          <span class="halo-tg2__t-mono-sm" style="color:var(--ht-primary-70)">Pertanyaan 04</span>
          <h3>Bisakah saya mendapatkan pengembalian uang?</h3>
          <p class="halo-tg2__t-body-sm">Pembayaran yang sudah dilakukan tidak dapat dikembalikan, mengingat akses ke seluruh konten diberikan secara langsung setelah transaksi berhasil. Apabila Anda mengalami kendala teknis atau situasi khusus, silakan hubungi kami melalui email untuk didiskusikan lebih lanjut.</p>
        </div>
        <div class="halo-tg2__faq-a2" data-faq-pane="5">
          <span class="halo-tg2__t-mono-sm" style="color:var(--ht-primary-70)">Pertanyaan 05</span>
          <h3>Perlu install aplikasi untuk mengaksesnya?</h3>
          <p class="halo-tg2__t-body-sm">Tidak perlu. Platform ini sepenuhnya berbasis website — cukup buka browser di HP, tablet, atau komputer, lalu login dan mulai belajar. Tampilan sudah dioptimasi untuk layar kecil, sehingga nyaman digunakan bahkan saat jam istirahat kerja.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ CTA ============ -->
<section class="halo-tg2__section halo-tg2__cta halo-tg2__band--primary" id="daftar">
  <div class="halo-tg2__glow" style="width:480px;height:480px;bottom:-260px;left:50%;margin-left:-240px"></div>
  <div class="halo-tg2__wrap">
    <div class="halo-tg2__cta-in" data-halo-tg2-reveal>
      <span class="halo-tg2__eyebrow halo-tg2__t-label-sm">Mulai Hari Ini</span>
      <h2 class="halo-tg2__t-h-lg">Mulai Sekarang Juga!</h2>
      <p class="halo-tg2__t-body-md">Daftar gratis dan buka materi pertama Anda dalam waktu kurang dari satu menit.</p>
      <a class="halo-tg2__btn halo-tg2__btn--primary halo-tg2__btn--glow" href="#daftar">Daftar Sekarang <i class="fa-regular fa-arrow-right" aria-hidden="true"></i></a>
      <span class="halo-tg2__cta-note halo-tg2__t-mono-sm">Tanpa kartu kredit · Tanpa perpanjangan otomatis</span>
      <p class="halo-tg2__t-body-sm" style="color:var(--ht-text-2)">Masih ragu atau mau tanya dulu?</p>
      <a class="halo-tg2__btn halo-tg2__btn--wa-outline" href="#" data-wa-link><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Tanya via WhatsApp</a>
    </div>
  </div>
</section>
</main>
<?php
get_footer();
