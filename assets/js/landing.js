/* halo-tg2: skrip halaman depan (Fase 2): reveal, counters, flashcard,
   filter kursus, fitur interaktif, timeline scroll, hero rotating text.
   Toggle tema & menu mobile dimiliki assets/js/main.js tema. */
(function(){
  "use strict";
  var d=document;
  var reduced=false; try{ reduced=matchMedia('(prefers-reduced-motion: reduce)').matches; }catch(e){}
  function $(s,c){ return (c||d).querySelector(s); }
  function $$(s,c){ return [].slice.call((c||d).querySelectorAll(s)); }

  /* ===== Reveal ===== */
  var revealEls=$$('[data-halo-tg2-reveal]');
  if('IntersectionObserver' in window && !reduced){
    var ro=new IntersectionObserver(function(es){ es.forEach(function(en){ if(en.isIntersecting){ en.target.classList.add('is-in'); ro.unobserve(en.target); } }); },{threshold:.12});
    revealEls.forEach(function(el){ ro.observe(el); });
  } else { revealEls.forEach(function(el){ el.classList.add('is-in'); }); }

  /* ===== Counters ===== */
  function animateCount(el){
    var target=+el.dataset.count, t0=null;
    if(reduced){ el.textContent=target; return; }
    (function step(ts){ if(!t0) t0=ts; var p=Math.min((ts-t0)/1200,1), k=1-Math.pow(1-p,3);
      el.textContent=Math.round(k*target); if(p<1) requestAnimationFrame(step); })(performance.now());
  }
  if('IntersectionObserver' in window){
    var co=new IntersectionObserver(function(es){ es.forEach(function(en){ if(en.isIntersecting){ animateCount(en.target); co.unobserve(en.target); } }); },{threshold:.5});
    $$('[data-count]').forEach(function(el){ co.observe(el); });
  } else { $$('[data-count]').forEach(animateCount); }

  /* ===== Flashcard deck ===== */
  var CARDS=[
    {w:'納期',ruby:'<ruby>納<rt>のう</rt></ruby><ruby>期<rt>き</rt></ruby>',ro:'nōki',mean:'Tenggat pengiriman',ex:'製品の納期を確認してください。',exr:'<ruby>製品<rt>せいひん</rt></ruby>の<ruby>納期<rt>のうき</rt></ruby>を<ruby>確認<rt>かくにん</rt></ruby>してください。',exid:'Tolong periksa tenggat pengiriman produk ini.'},
    {w:'歩留まり',ruby:'<ruby>歩留<rt>ぶど</rt></ruby>まり',ro:'budomari',mean:'Tingkat hasil baik produksi',ex:'歩留まりを改善する。',exr:'<ruby>歩留<rt>ぶど</rt></ruby>まりを<ruby>改善<rt>かいぜん</rt></ruby>する。',exid:'Memperbaiki tingkat hasil baik produksi.'},
    {w:'段取り',ruby:'<ruby>段取<rt>だんど</rt></ruby>り',ro:'dandori',mean:'Persiapan & pengaturan kerja',ex:'段取りが大切です。',exr:'<ruby>段取<rt>だんど</rt></ruby>りが<ruby>大切<rt>たいせつ</rt></ruby>です。',exid:'Persiapan sebelum bekerja itu penting.'},
    {w:'工程管理',ruby:'<ruby>工程<rt>こうてい</rt></ruby><ruby>管理<rt>かんり</rt></ruby>',ro:'kōtei kanri',mean:'Pengendalian proses',ex:'工程管理を徹底する。',exr:'<ruby>工程管理<rt>こうていかんり</rt></ruby>を<ruby>徹底<rt>てってい</rt></ruby>する。',exid:'Menjalankan pengendalian proses secara menyeluruh.'},
    {w:'品質管理',ruby:'<ruby>品質<rt>ひんしつ</rt></ruby><ruby>管理<rt>かんり</rt></ruby>',ro:'hinshitsu kanri',mean:'Pengendalian mutu',ex:'品質管理が重要です。',exr:'<ruby>品質管理<rt>ひんしつかんり</rt></ruby>が<ruby>重要<rt>じゅうよう</rt></ruby>です。',exid:'Pengendalian mutu itu penting.'}
  ];
  var deck=$('[data-fc-deck]'), dotsWrap=$('[data-fc-dots]'), fi=0, known={};
  CARDS.forEach(function(c,i){
    var card=d.createElement('div');
    card.className='halo-tg2__fc'+(i===0?' is-on':'');
    card.setAttribute('role','group'); card.setAttribute('aria-roledescription','kartu');
    card.setAttribute('aria-label','Kartu '+(i+1)+' dari '+CARDS.length+': '+c.w);
    card.innerHTML=
      '<button class="halo-tg2__fc-flip" type="button" aria-pressed="false" aria-label="Balik kartu '+c.w+'">'+
        '<span class="halo-tg2__fc-in">'+
          '<span class="halo-tg2__fc-face halo-tg2__fc-front">'+
            '<span class="halo-tg2__fc-top"><span class="halo-tg2__t-mono-sm">生産管理</span><span class="halo-tg2__t-mono-sm">'+(i+1)+' / '+CARDS.length+'</span></span>'+
            '<span class="halo-tg2__fc-word" lang="ja">'+c.ruby+'</span>'+
            '<span class="halo-tg2__fc-hint halo-tg2__t-label-md"><i class="fa-regular fa-rotate" aria-hidden="true"></i> Tekan untuk melihat arti</span>'+
          '</span>'+
          '<span class="halo-tg2__fc-face halo-tg2__fc-back">'+
            '<span class="halo-tg2__fc-top"><span class="halo-tg2__t-mono-sm" lang="ja">'+c.w+'</span><span class="halo-tg2__t-mono-sm">'+(i+1)+' / '+CARDS.length+'</span></span>'+
            '<span><span class="halo-tg2__fc-ro halo-tg2__t-label-lg">'+c.ro+'</span><br><span class="halo-tg2__fc-mean">'+c.mean+'</span></span>'+
            '<span class="halo-tg2__fc-ex"><span class="halo-tg2__fc-ex-jp" lang="ja">'+c.exr+'</span><span class="halo-tg2__fc-ex-id halo-tg2__t-body-sm">'+c.exid+'</span></span>'+
          '</span>'+
        '</span>'+
      '</button>';
    deck.appendChild(card);
    var dot=d.createElement('button');
    dot.type='button'; dot.className='halo-tg2__chip'; dot.dataset.go=i;
    dot.setAttribute('aria-pressed', i===0?'true':'false');
    dot.innerHTML='<span lang="ja"></span>';
    dot.firstChild.textContent=c.w;
    dot.addEventListener('click',function(){ fshow(i, i<fi?-1:1); });
    dotsWrap.appendChild(dot);
  });
  var fcs=$$('.halo-tg2__fc',deck), fdots=$$('.halo-tg2__chip',dotsWrap);
  function fshow(n,dir){ n=(n+fcs.length)%fcs.length;
    fcs.forEach(function(c,j){ var fl=$('.halo-tg2__fc-flip',c);
      if(j!==n) fl.setAttribute('aria-pressed','false');
      c.style.setProperty('--ht-fc-from',(dir<0?-40:40)+'px');
      c.style.setProperty('--ht-fc-rot',(dir<0?-2:2)+'deg');
      c.classList.toggle('is-on',j===n); });
    fdots.forEach(function(dd,j){ dd.setAttribute('aria-pressed', j===n?'true':'false'); });
    fi=n; }
  function fflip(){ var fl=$('.halo-tg2__fc-flip',fcs[fi]); fl.setAttribute('aria-pressed', fl.getAttribute('aria-pressed')!=='true'); }
  function fcount(){ var k=Object.keys(known).length;
    $('[data-fc-known]').textContent=k+' / '+fcs.length;
    $('[data-fc-bar]').style.width=(k/fcs.length*100)+'%'; }
  fcs.forEach(function(c){ $('.halo-tg2__fc-flip',c).addEventListener('click',fflip); });
  $('[data-fc-prev]').addEventListener('click',function(){ fshow(fi-1,-1); });
  $('[data-fc-next]').addEventListener('click',function(){ fshow(fi+1,1); });
  $('[data-fc-know]').addEventListener('click',function(){ known[fi]=1; fcount(); fshow(fi+1,1); });
  $('[data-fc-again]').addEventListener('click',function(){ delete known[fi]; fcount(); fshow(fi+1,1); });
  deck.addEventListener('keydown',function(e){
    if(e.key==='ArrowRight'){ e.preventDefault(); fshow(fi+1,1); }
    else if(e.key==='ArrowLeft'){ e.preventDefault(); fshow(fi-1,-1); }
    else if((e.key===' '||e.key==='Enter') && e.target===e.currentTarget){ e.preventDefault(); fflip(); } });
  var tx=null;
  deck.addEventListener('touchstart',function(e){ tx=e.touches[0].clientX; },{passive:true});
  deck.addEventListener('touchend',function(e){ if(tx===null) return; var dx=e.changedTouches[0].clientX-tx; tx=null;
    if(Math.abs(dx)>50){ fshow(fi+(dx<0?1:-1), dx<0?1:-1); } });

  /* ===== Course filter ===== */
  var chips=$$('[data-filter]'), courses=$$('.halo-tg2__course');
  chips.forEach(function(ch){ ch.addEventListener('click',function(){
    chips.forEach(function(x){ x.setAttribute('aria-pressed', x===ch?'true':'false'); });
    var cat=ch.dataset.filter;
    courses.forEach(function(k){ var show = cat==='all' || (' '+k.dataset.cat+' ').indexOf(' '+cat+' ')>-1; k.hidden=!show; });
  }); });

  /* ===== Fitur interaktif: catatan menyalakan zona panel ===== */
  var notes=$$('[data-note]'), zones=$$('[data-zone]');
  function setZone(key){
    notes.forEach(function(n){ var on=n.dataset.note===key; n.classList.toggle('is-on',on); n.setAttribute('aria-pressed',on?'true':'false'); });
    zones.forEach(function(z){ z.classList.toggle('is-on', z.dataset.zone===key); });
  }
  notes.forEach(function(n){
    n.addEventListener('click',function(){ setZone(n.dataset.note); });
    n.addEventListener('mouseenter',function(){ setZone(n.dataset.note); });
    n.addEventListener('focus',function(){ setZone(n.dataset.note); });
  });

  /* ===== Timeline: garis mengikuti scroll (picu di 42% layar); satu unit (judul+subjudul+card) muncul saat garis menyentuh nomornya ===== */
  var tl=$('[data-timeline]'), tlFill=$('[data-tl-fill]'), tlNodes=$$('[data-tl-node]');
  if(tl && tlFill){
    if(reduced){ tlFill.style.height='100%'; tlNodes.forEach(function(n){ n.classList.add('is-on'); }); }
    else{
      var tlSteps=$$('.halo-tg2__tl-step', tl),
          tlBodies=tlSteps.map(function(s){ return $('.halo-tg2__tl-body', s); });
      tl.classList.add('is-armed');
      var tlTick=false, tlPeak=0, tlDone=false;
      var tlUpdate=function(){
        tlTick=false;
        if(tlDone) return;
        var r=tl.getBoundingClientRect(), vh=window.innerHeight;
        var anchor = window.innerWidth<=760 ? 0.40 : 0.42;
        var p=(vh*anchor - r.top)/r.height; p=Math.max(0,Math.min(1,p));
        if(p>tlPeak) tlPeak=p; /* hanya maju: scroll ke atas tidak memundurkan garis */
        tlFill.style.height=(tlPeak*100)+'%';
        var lineY=r.top + tlPeak*r.height;
        tlNodes.forEach(function(n, i){
          var nr=n.getBoundingClientRect();
          if(lineY >= nr.top + nr.height/2){
            n.classList.add('is-on');
            if(tlBodies[i]) tlBodies[i].classList.add('is-shown');
          }
        });
        if(tlPeak>=1){
          tlDone=true;
          window.removeEventListener('scroll', tlReq);
          window.removeEventListener('resize', tlReq);
        }
      };
      var tlReq=function(){ if(!tlTick){ tlTick=true; requestAnimationFrame(tlUpdate); } };
      window.addEventListener('scroll', tlReq, {passive:true});
      window.addEventListener('resize', tlReq);
      tlUpdate();
    }
  }

  /* ===== FAQ: dua panel (desktop) / accordion (tablet & mobile) ===== */
  var faqBtns=$$('[data-faq]'), faqPanes=$$('[data-faq-pane]'),
      faqBox=$('.halo-tg2__faq2'), faqList=$('.halo-tg2__faq-list'), faqPaneCard=$('.halo-tg2__faq-pane'),
      faqActive='1', faqOpen='1', faqMQ=null;
  try{ faqMQ=window.matchMedia('(max-width:1024px)'); }catch(e){}
  faqPanes.forEach(function(p){
    var w=document.createElement('div'); w.className='halo-tg2__faq-a2-in';
    while(p.firstChild) w.appendChild(p.firstChild);
    p.appendChild(w);
  });
  function faqRender(){
    var acc = faqMQ && faqMQ.matches;
    faqBtns.forEach(function(b){
      var k=b.dataset.faq, on = acc ? (k===faqOpen) : (k===faqActive);
      b.classList.toggle('is-on', !!on); b.setAttribute('aria-pressed', on?'true':'false');
      b.setAttribute('aria-expanded', acc ? (on?'true':'false') : 'false');
    });
    faqPanes.forEach(function(p){
      var k=p.dataset.faqPane;
      p.classList.toggle('is-on', !acc && k===faqActive);
      p.classList.toggle('is-open', acc && k===faqOpen);
    });
  }
  function faqPlace(){
    var acc = faqMQ && faqMQ.matches;
    if(!faqBox) return;
    faqBox.classList.toggle('is-acc', !!acc);
    if(acc){
      faqBtns.forEach(function(b){
        var p=faqPanes.filter(function(x){ return x.dataset.faqPane===b.dataset.faq; })[0];
        if(p && p.parentNode!==faqList) faqList.insertBefore(p, b.nextSibling);
      });
    } else {
      faqPanes.forEach(function(p){ if(p.parentNode!==faqPaneCard) faqPaneCard.appendChild(p); });
    }
    faqRender();
  }
  faqBtns.forEach(function(b){ b.addEventListener('click',function(){
    var k=b.dataset.faq, acc = faqMQ && faqMQ.matches;
    if(acc){ faqOpen = (faqOpen===k ? '' : k); }
    else { faqActive=k; }
    faqRender();
  }); });
  if(faqMQ){ if(faqMQ.addEventListener) faqMQ.addEventListener('change', faqPlace); else if(faqMQ.addListener) faqMQ.addListener(faqPlace); }
  faqPlace();

  /* ===== Favorit kartu kursus ===== */
  $$('[data-fav]').forEach(function(btn){
    btn.addEventListener('click',function(){
      var on=btn.classList.toggle('is-on');
      btn.setAttribute('aria-pressed', on?'true':'false');
      var ic=btn.querySelector('i');
      if(ic){ ic.classList.toggle('fa-solid',on); ic.classList.toggle('fa-regular',!on); }
    });
  });

  /* ===== Telemetri berputar ===== */
  var ROT=[
    {v:'87%',l:'Tingkat kelulusan peserta',t:'Hasil nyata dari peserta yang menyelesaikan simulasi sampai tuntas sebelum hari ujian.'},
    {v:'312+',l:'Peserta aktif belajar',t:'Ratusan pejuang TG1 yang sedang menyiapkan diri naik ke Tokutei Ginou 2.'},
    {v:'320+',l:'Soal CBT di bank soal',t:'Soal terstruktur per bab dengan penjelasan di setiap jawaban.'},
    {v:'600+',l:'Flashcard & kosakata',t:'Istilah teknis produksi bilingual yang siap dicicil setiap hari.'}
  ];
  var rotI=0, rotVal=$('[data-rotate-value]'), rotLab=$('[data-rotate-label]'), rotDesc=$('[data-rotate-desc]'), rotBox=$('.halo-tg2__telemetry-big');
  if(rotVal && !reduced){
    var rotTimer=setInterval(function(){
      rotBox.classList.add('is-fade');
      setTimeout(function(){
        rotI=(rotI+1)%ROT.length;
        rotVal.textContent=ROT[rotI].v; rotLab.textContent=ROT[rotI].l; rotDesc.textContent=ROT[rotI].t;
        rotBox.classList.remove('is-fade');
      },360);
    },4200);
    rotBox.addEventListener('mouseenter',function(){ clearInterval(rotTimer); });
  }

  /* ===== Demo 3 langkah: gerbang viewport untuk marquee (Step 2) ===== */
  var caraSec=$('#cara');
  if(caraSec && 'IntersectionObserver' in window && !reduced){
    caraSec.classList.add('is-gated');
    var caraIO=new IntersectionObserver(function(es){ es.forEach(function(en){ caraSec.classList.toggle('is-live', en.isIntersecting); }); },{threshold:.05});
    caraIO.observe(caraSec);
  }

  /* ===== Demo Step 1: form daftar mengetik sendiri ===== */
  var su=$('[data-demo-signup]');
  if(su){
    var suForm=$('[data-demo-form]',su), suUser=$('[data-typed-user]',su), suEmail=$('[data-typed-email]',su),
        suFUser=$('[data-demo-field="user"]',su), suFEmail=$('[data-demo-field="email"]',su),
        suBtn=$('[data-demo-btn]',su), suBtnLabel=$('[data-demo-btn-label]',su),
        suCursor=$('.halo-tg2__demo-cursor',su),
        suTimers=[], suRunning=false;
    function suLater(fn,ms){ suTimers.push(setTimeout(fn,ms)); }
    function suType(el,text,start,speed){ for(var i=1;i<=text.length;i++){ (function(n){ suLater(function(){ el.textContent=text.slice(0,n); }, start+n*speed); })(i); } return start+text.length*speed; }
    function suMoveTo(el){ if(!el || !suCursor) return; var fr=suForm.getBoundingClientRect(), r=el.getBoundingClientRect(); suCursor.style.transform='translate('+Math.round(r.left-fr.left+r.width*.55)+'px,'+Math.round(r.top-fr.top+r.height*.62)+'px)'; suCursor.style.opacity='1'; }
    function suReset(){ su.classList.remove('is-success'); suBtn.classList.remove('is-loading','is-press'); suBtnLabel.textContent='Daftar Gratis'; suUser.textContent=''; suEmail.textContent=''; suFUser.classList.remove('is-active','has-text'); suFEmail.classList.remove('is-active','has-text'); if(suCursor) suCursor.style.opacity='0'; suForm.classList.remove('is-in'); }
    function suCycle(){
      suReset();
      suLater(function(){ suForm.classList.add('is-in'); },60);
      suLater(function(){ suMoveTo(suFUser); },650);
      suLater(function(){ suFUser.classList.add('is-active','has-text'); },1200);
      var t1=suType(suUser,'alex_tg2',1350,95);
      suLater(function(){ suMoveTo(suFEmail); }, t1+350);
      suLater(function(){ suFUser.classList.remove('is-active'); suFEmail.classList.add('is-active','has-text'); }, t1+900);
      var t2=suType(suEmail,'alex@mail.com', t1+1050, 85);
      suLater(function(){ suMoveTo(suBtn); }, t2+300);
      suLater(function(){ suFEmail.classList.remove('is-active'); suBtn.classList.add('is-press'); }, t2+850);
      suLater(function(){ suBtn.classList.remove('is-press'); suBtn.classList.add('is-loading'); suBtnLabel.textContent='Memproses...'; }, t2+1050);
      suLater(function(){ su.classList.add('is-success'); suRunning=false; }, t2+2600);
    }
    function suStart(){ if(suRunning) return; suRunning=true; suCycle(); }
    if(reduced){
      suUser.textContent='alex_tg2'; suEmail.textContent='alex@mail.com';
      suFUser.classList.add('has-text'); suFEmail.classList.add('has-text'); suForm.classList.add('is-in');
    } else if('IntersectionObserver' in window){
      var suIO=new IntersectionObserver(function(es){ es.forEach(function(en){ if(en.isIntersecting){ suStart(); suIO.unobserve(su); } }); },{threshold:.35});
      suIO.observe(su);
    } else { suStart(); }
  }

  /* ===== Demo Step 3: dashboard count-up + grafik ===== */
  var dash=$('[data-demo-dash]');
  if(dash){
    var dashNums=$$('[data-demo-count]',dash), dashLine=$('[data-demo-chart-line]',dash), dashRafs=[];
    function dashFmt(v){ return Math.round(v).toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); }
    function dashSetupLine(){ if(!dashLine) return 0; var len=0; try{ len=dashLine.getTotalLength(); }catch(e){} if(len){ dashLine.style.transition='none'; dashLine.style.strokeDasharray=len; dashLine.style.strokeDashoffset=len; } return len; }
    function dashPlay(){
      var len=dashSetupLine();
      if(dashLine && len){ void dashLine.getBoundingClientRect(); dashLine.style.transition='stroke-dashoffset 1.8s cubic-bezier(.16,1,.3,1) .15s'; dashLine.style.strokeDashoffset='0'; }
      dash.classList.add('is-playing');
      dashNums.forEach(function(el){
        var target=parseFloat(el.getAttribute('data-demo-count'))||0, t0=null;
        function step(ts){ if(!t0) t0=ts; var p=Math.min((ts-t0)/1600,1), k=1-Math.pow(1-p,3); el.textContent=dashFmt(k*target); if(p<1) dashRafs.push(requestAnimationFrame(step)); }
        dashRafs.push(requestAnimationFrame(step));
      });
    }
    function dashReset(){
      dashRafs.forEach(cancelAnimationFrame); dashRafs=[];
      dash.classList.remove('is-playing');
      dashNums.forEach(function(el){ el.textContent='0'; });
      dashSetupLine();
    }
    function dashFinal(){
      dashNums.forEach(function(el){ el.textContent=dashFmt(parseFloat(el.getAttribute('data-demo-count'))||0); });
      dash.classList.add('is-playing');
      if(dashLine){ dashLine.style.transition='none'; dashLine.style.strokeDasharray='none'; dashLine.style.strokeDashoffset='0'; }
      var bar=$('[data-demo-bar]',dash); if(bar) bar.style.width='76%';
    }
    if(reduced){ dashFinal(); }
    else if('IntersectionObserver' in window){
      dashReset();
      var dashIO=new IntersectionObserver(function(es){ es.forEach(function(en){ if(en.isIntersecting){ dashPlay(); dashIO.unobserve(dash); } }); },{threshold:.35});
      dashIO.observe(dash);
    } else { dashPlay(); }
  }
})();
