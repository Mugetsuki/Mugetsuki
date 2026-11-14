/* Pikora sitesi · davranış. İçerik urunler.js'ten gelir. */
(function () {
  "use strict";
  var AYAR = window.PIKORA_AYAR || {}, AILELER = window.PIKORA_AILELER || {}, URUNLER = window.PIKORA_URUNLER || [];
  var $ = function (s, k) { return (k || document).querySelector(s); };
  var $$ = function (s, k) { return Array.prototype.slice.call((k || document).querySelectorAll(s)); };
  var azHareket = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var bul = function (id) { return URUNLER.filter(function (u) { return u.id === id; })[0]; };

  var motif = window.PIKORA_CIZIM.motif, kolyeSVG = window.PIKORA_CIZIM.kolyeSVG, boncukSVG = window.PIKORA_CIZIM.boncukSVG;

  function gorselHTML(u, buyuk) {
    if (!u.gorsel) return kolyeSVG(u.cizim);
    var alt = (u.kedi ? u.kedi.charAt(0).toUpperCase() + u.kedi.slice(1) + " " : "") + u.ad + (u.tur === "kolye" ? " kolyesi" : " bandanası");
    if (/\.\w+$/.test(u.gorsel)) return '<img src="' + u.gorsel + '" alt="' + alt + '" loading="lazy" decoding="async">';
    return '<img src="' + u.gorsel + (buyuk ? "-960" : "-480") + '.webp" srcset="' + u.gorsel + '-480.webp 480w, ' + u.gorsel + '-960.webp 960w" sizes="' +
      (buyuk ? "(min-width:768px) 460px, 100vw" : "(min-width:1100px) 260px, (min-width:768px) 30vw, 46vw") + '" width="960" height="960" alt="' + alt + '" loading="lazy" decoding="async">';
  }
  var rozet = function (u) { return u.gercek ? "" : '<span class="rozet">' + (u.gorsel ? "tasarım görseli" : "tasarım çizimi") + "</span>"; };
  var fiyat = function (u) { return u.fiyat ? '<span class="fiyat">' + u.fiyat + " ₺</span>" : '<span class="fiyat sor">Fiyatı sor</span>'; };
  var aile = function (u) { return AILELER[u.aile] || { ad: "", renk: "#F7EEDD" }; };

  /* ================= Ürünleri diz ================= */
  var NOTLAR = {
    kolye: "Ölçü vermene gerek yok: ipler uzun bırakılır, patili dostunun boynuna göre bağlarsın.",
    bandana: "Görseller tasarım örneğidir. Her ürün elde örülür ve biraz farklı olabilir."
  };
  ["kolye", "bandana"].forEach(function (tur) {
    var liste = URUNLER.filter(function (u) { return u.tur === tur; });
    var ipEl = $('[data-boncuk="' + tur + '"]'), izgara = $('[data-izgara="' + tur + '"]');
    if (!ipEl || !izgara) return;
    ipEl.innerHTML = liste.map(function (u, i) {
      var t = liste.length > 1 ? i / (liste.length - 1) : 0.5, sark = Math.round(4 * t * (1 - t) * 26); // ip ortada sarkar
      return '<button class="boncuk" type="button" data-git="' + u.id + '" style="--i:' + i + ";--sark:" + sark + 'px" aria-label="' + u.ad + ' kartına git">' + boncukSVG(u) + '<span class="boncuk-ad">' + u.ad + "</span></button>";
    }).join("");
    izgara.innerHTML = liste.map(function (u, i) {
      return '<article class="kart gir" id="k-' + u.id + '" style="--aile:' + aile(u).renk + ";--i:" + (i % 4) + '">' +
        '<button class="kart-gorsel" type="button" data-ac="' + u.id + '" aria-label="' + u.ad + ': ayrıntıları aç">' + gorselHTML(u) + rozet(u) + "</button>" +
        '<div class="kart-govde"><p class="aile">' + aile(u).ad + " ailesi</p><h4>" + u.ad + '</h4><p class="tarif">' + u.tarif + "</p>" +
        '<div class="kart-alt">' + fiyat(u) + '<button class="diz" type="button" data-diz="' + u.id + '" aria-pressed="false">İpime diz</button></div></div></article>';
    }).join("") + '<p class="kart-not gir" style="--i:3">' + NOTLAR[tur] + "</p>";
  });

  /* ================= İpim (seçilenler) ================= */
  var ANAHTAR = "pikora-ipim", secili = [];
  try { secili = (JSON.parse(localStorage.getItem(ANAHTAR)) || []).filter(bul); } catch (e) { secili = []; }
  var panel = $("#ip-panel"), sayi = $("#ip-sayi"), form = $("#panel-form"), durum = $("#panel-durum");

  function ciz() {
    try { localStorage.setItem(ANAHTAR, JSON.stringify(secili)); } catch (e) { /* gizli sekme: sorun değil */ }
    sayi.textContent = secili.length;
    $$("[data-diz]").forEach(function (d) {
      var var_ = secili.indexOf(d.dataset.diz) > -1;
      d.setAttribute("aria-pressed", var_);
      d.textContent = var_ ? "İpinde" : "İpime diz";
    });
    $$(".boncuk").forEach(function (b) { b.classList.toggle("dizili", secili.indexOf(b.dataset.git) > -1); });
    panel.classList.toggle("bos", !secili.length);
    $("#panel-ip").innerHTML = secili.map(function (id) { return boncukSVG(bul(id)); }).join("");
    $("#panel-liste").innerHTML = secili.map(function (id) {
      var u = bul(id);
      return "<li>" + boncukSVG(u) + "<span><b>" + u.ad + "</b><small>" + (u.tur === "kolye" ? "Kolye" : "Bandana") + " · " + aile(u).ad + ' ailesi</small></span><button type="button" data-cikar="' + id + '" aria-label="' + u.ad + ': ipten çıkar">Çıkar</button></li>';
    }).join("");
    var tutar = secili.reduce(function (t, id) { return t + (bul(id).fiyat || 0); }, 0);
    $("#panel-toplam").textContent = tutar ? "Toplam: " + tutar + " ₺ (gönderim hariç, mesajda konuşuruz)" : "";
    durum.textContent = "";
  }
  function ucur(id, kaynak) {
    if (azHareket || !kaynak || !kaynak.animate) return;
    var a = kaynak.getBoundingClientRect(), b = $("#ip-dugme").getBoundingClientRect(), el = document.createElement("div");
    el.className = "ucan"; el.innerHTML = boncukSVG(bul(id)); document.body.appendChild(el);
    var x0 = a.left + a.width / 2 - 22, y0 = a.top - 30, x1 = b.left + b.width / 2 - 22, y1 = b.top + b.height / 2 - 22;
    el.animate([
      { transform: "translate(" + x0 + "px," + y0 + "px) scale(.4)", opacity: 0 },
      { transform: "translate(" + x0 + "px," + (y0 - 46) + "px) scale(1.25)", opacity: 1, offset: 0.25 },
      { transform: "translate(" + x1 + "px," + y1 + "px) scale(.45)", opacity: 1 }
    ], { duration: 720, easing: "cubic-bezier(0.65,0,0.35,1)" }).onfinish = function () { el.remove(); pit(); };
  }
  function pit() { sayi.classList.remove("pit"); void sayi.offsetWidth; sayi.classList.add("pit"); }
  function degistir(id, kaynak) {
    var i = secili.indexOf(id);
    if (i > -1) secili.splice(i, 1); else { secili.push(id); ucur(id, kaynak); if (azHareket) pit(); }
    ciz();
  }
  function mesaj() {
    var ad = form.ad.value.trim();
    var s = "Merhaba Pikora! İpime şunları dizdim:\n" + secili.map(function (id) { var u = bul(id); return "• " + u.ad + " (" + u.tur + (u.fiyat ? ", " + u.fiyat + " TL" : "") + ")"; }).join("\n");
    if (ad) s += "\nPatili dostumun adı: " + ad;
    return s + "\nTeslim için bilgi alabilir miyim?";
  }
  $("#wa-gonder").addEventListener("click", function () {
    window.open("https://wa.me/" + (AYAR.whatsapp || "") + "?text=" + encodeURIComponent(mesaj()), "_blank", "noopener");
  });
  $("#ig-gonder").addEventListener("click", function () {
    var bitti = function (tamam) {
      durum.textContent = tamam ? "Mesaj kopyalandı. Instagram'da mesaj kutusuna yapıştır." : "Kopyalanamadı; mesajı WhatsApp düğmesiyle gönderebilirsin.";
      if (tamam && AYAR.instagram) window.open("https://ig.me/m/" + AYAR.instagram, "_blank", "noopener");
    };
    if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(mesaj()).then(function () { bitti(true); }, function () { bitti(false); });
    else bitti(false);
  });

  /* ================= Ürün ayrıntısı ================= */
  var ayrinti = $("#ayrinti");
  function ac(id) {
    var u = bul(id); if (!u) return;
    ayrinti.style.setProperty("--aile", aile(u).renk);
    ayrinti.innerHTML = '<div class="ayrinti-ic"><div class="ayrinti-gorsel">' + gorselHTML(u, true) + rozet(u) + '</div><div class="ayrinti-metin">' +
      '<button class="kapat" type="button" data-kapat aria-label="Kapat">×</button><p class="aile">' + aile(u).ad + ' ailesi · ' + (u.tur === "kolye" ? "kolye" : "bandana") + '</p><h2 id="ayrinti-ad">' + u.ad + "</h2><p>" + u.tarif + "</p>" + fiyat(u) +
      "<ul><li>Elde, ilmek ilmek örülür; her parça biraz farklı çıkar.</li>" + (u.tur === "kolye" ? "<li>İpleri uzun bırakılır; patili dostunun boynuna göre bağlarsın. Ölçü vermene gerek yok.</li>" : "") + "<li>Dekoratiftir. İlk günlerde gözetim altında tak; kedin rahatsız olursa çıkar.</li><li>Ürün giderleri düşülünce kalanın %10'u barınaktaki hayvanlara ayrılır.</li></ul>" +
      '<button class="diz" type="button" data-diz="' + u.id + '" aria-pressed="false">İpime diz</button></div></div>';
    ciz();
    ayrinti.showModal();
  }

  /* ================= Tıklamalar ================= */
  document.addEventListener("click", function (e) {
    var t = e.target.closest("[data-diz],[data-ac],[data-git],[data-cikar],[data-kapat],[data-ip-ac],#ip-dugme");
    if (e.target.tagName === "DIALOG") { e.target.close(); return; } // boşluğa tıklayınca kapan
    if (!t) return;
    if (t.dataset.diz) degistir(t.dataset.diz, t);
    else if (t.dataset.ac) ac(t.dataset.ac);
    else if (t.dataset.cikar) degistir(t.dataset.cikar);
    else if (t.hasAttribute("data-kapat")) t.closest("dialog").close();
    else if (t.id === "ip-dugme" || t.hasAttribute("data-ip-ac")) panel.showModal();
    else if (t.dataset.git) {
      var k = $("#k-" + t.dataset.git);
      k.scrollIntoView({ behavior: azHareket ? "auto" : "smooth", block: "center" });
      k.classList.remove("bulundu"); void k.offsetWidth; k.classList.add("bulundu");
    }
  });
  ciz();

  /* ================= Kaydırınca giriş ================= */
  if ("IntersectionObserver" in window) {
    var goz = new IntersectionObserver(function (kayitlar) {
      kayitlar.forEach(function (k) { if (k.isIntersecting) { k.target.classList.add("gorundu"); goz.unobserve(k.target); } });
    }, { rootMargin: "0px 0px -8% 0px" });
    $$(".gir").forEach(function (el) { goz.observe(el); });
  } else $$(".gir").forEach(function (el) { el.classList.add("gorundu"); });

  /* ================= Sayfa boyunca ip ================= */
  var sayfa = $("#sayfa"), svg = $("#ip-yolu"), yol, orgu, yumak, toplam = 0, basY = 0, sonY = 0, bekleyen = false;
  var RENKLER = ["#E3A72F", "#F07A5E", "#BBA8DC", "#5F6E2E", "#E3A72F", "#1E2A4F", "#8A2335"];
  var SUSLER = [
    { motif: "cicek", yaprak: 6, renk: "#BBA8DC", gobek: "#7B5EA7" },
    { motif: "portakal", renk: "#EE7B26", yapraklar: ["#5F6E2E", "#7C8F3C"] },
    { motif: "papyon", renk: "#8A2335", gobek: "#5E1424" },
    { motif: "cicek", yaprak: 8, renk: "#E3A72F", gobek: "#EE7B26" }
  ];

  function uzunlukY(y) { // ip üzerinde y yüksekliğine denk gelen uzunluk (yol aşağı doğru tekdüze iner)
    var a = 0, b = toplam, i, o;
    for (i = 0; i < 18; i++) { o = (a + b) / 2; if (yol.getPointAtLength(o).y < y) a = o; else b = o; }
    return (a + b) / 2;
  }
  function ipKur() {
    var W = sayfa.clientWidth, H = sayfa.offsetHeight, ust = sayfa.getBoundingClientRect().top;
    var mobil = W < 768, icerik = Math.min(1180, W), kenar = mobil ? 7 : Math.max(26, (W - icerik) / 2 + 30), dalga = mobil ? 3 : 12;
    var X = function (sag) { return sag ? W - kenar : kenar; };
    var uc = $("#logo-ip-ucu").getBoundingClientRect(), son = $("#ip-son").getBoundingClientRect();
    var sx = uc.right - uc.width * 0.04, sy = uc.top - ust + uc.height * 0.45;
    var d = "M" + sx.toFixed(1) + " " + sy.toFixed(1), x = sx, y = sy, gecitler = [];
    var git = function (nx, ny, k) { // yumuşak S eğrisi; y hep artar
      var h = (ny - y) * (k || 0.5);
      d += "C" + x.toFixed(1) + " " + (y + h).toFixed(1) + " " + nx.toFixed(1) + " " + (ny - h).toFixed(1) + " " + nx.toFixed(1) + " " + ny.toFixed(1);
      x = nx; y = ny;
    };
    $$("[data-ip]").forEach(function (el, i, hepsi) {
      var r = el.getBoundingClientRect(), cs = getComputedStyle(el), sag = i % 2 === 0, sonuncu = i === hepsi.length - 1;
      var t = r.top - ust + parseFloat(cs.paddingTop), b = r.bottom - ust - parseFloat(cs.paddingBottom); // içerik kutusu
      if (sonuncu) { gecitler.push((y + son.top - ust) / 2); git(son.left + son.width / 2, son.top - ust + 4, 0.62); return; }
      var bas = Math.max(y + 60, t - 44), bit = b + 4, adim, n, j;
      if (i > 0) gecitler.push((y + bas) / 2);
      git(X(sag), bas, i === 0 ? 0.55 : 0.68);
      n = Math.max(1, Math.round((bit - bas) / 190)); adim = (bit - bas) / n;
      for (j = 1; j <= n; j++) git(X(sag) + (j % 2 ? -dalga : dalga) * (sag ? 1 : -1), bas + adim * j);
    });
    var cx = x, cy = y; // ipin ucunda küçük bir ilmek ve kalp
    svg.setAttribute("viewBox", "0 0 " + W + " " + H); svg.style.height = H + "px";
    var duraklar = RENKLER.map(function (r, i) { return '<stop offset="' + (i / (RENKLER.length - 1)).toFixed(3) + '" stop-color="' + r + '"/>'; }).join("");
    svg.innerHTML = '<defs><linearGradient id="ip-renk" gradientUnits="userSpaceOnUse" x1="0" y1="' + sy.toFixed(0) + '" x2="0" y2="' + cy.toFixed(0) + '">' + duraklar + "</linearGradient></defs>" +
      '<path class="iz" d="' + d + '"/><path class="orgu" d="' + d + '" stroke="url(#ip-renk)"/><g class="susler"></g>' +
      '<path transform="translate(' + cx.toFixed(1) + " " + (cy + 26).toFixed(1) + ') scale(1.5)" d="M0 12C-12 4-16-10-6-13C-2-14 0-11 0-9C0-11 2-14 6-13C16-10 12 4 0 12Z" fill="#F07A5E"/>' +
      '<g class="yumak"><circle r="' + (mobil ? 9 : 14) + '" fill="#F07A5E" stroke="#FFFAF2" stroke-width="2"/><path d="M-7-6C1-2 4 5 0 11M-1-11C7-6 10 3 7 11M-11 3C-3 2 6 6 12 3" transform="scale(' + (mobil ? 0.6 : 0.95) + ')" fill="none" stroke="#E3A72F" stroke-width="2.6" stroke-linecap="round"/></g>';
    yol = $(".iz", svg); orgu = $(".orgu", svg); yumak = $(".yumak", svg);
    toplam = yol.getTotalLength(); basY = sy; sonY = cy;
    orgu.style.strokeDasharray = toplam + " " + toplam;
    // Geçitlere asılı süsler
    $(".susler", svg).innerHTML = gecitler.map(function (gy, i) {
      var p = yol.getPointAtLength(uzunlukY(gy)), r = mobil ? 11 : 15;
      return '<g transform="translate(' + p.x.toFixed(1) + " " + p.y.toFixed(1) + ')"><g class="sus-ic" style="animation-delay:' + (i * -700) + 'ms"><path d="M0 0V' + (r + 4) + '" stroke="rgba(42,27,30,.4)" stroke-width="1.5"/>' + motif(SUSLER[i % SUSLER.length], 0, r * 2 + 2, r) + "</g></g>";
    }).join("");
    ipGuncelle();
  }
  function ipGuncelle() {
    bekleyen = false;
    if (!yol) return;
    var hedef = azHareket ? sonY : Math.min(sonY, Math.max(basY, window.scrollY - sayfa.offsetTop + window.innerHeight * 0.62));
    var u = hedef >= sonY ? toplam : uzunlukY(hedef), p = yol.getPointAtLength(u);
    orgu.style.strokeDashoffset = toplam - u;
    yumak.setAttribute("transform", "translate(" + p.x.toFixed(1) + " " + p.y.toFixed(1) + ") rotate(" + ((u * 1.6) % 360).toFixed(0) + ")");
    yumak.style.display = azHareket ? "none" : "";
  }
  function sor() { if (!bekleyen) { bekleyen = true; requestAnimationFrame(ipGuncelle); } }
  if (svg && sayfa && svg.getTotalLength !== null) {
    var zaman;
    var yenidenKur = function () { clearTimeout(zaman); zaman = setTimeout(ipKur, 120); };
    ipKur();
    window.addEventListener("scroll", sor, { passive: true });
    if ("ResizeObserver" in window) new ResizeObserver(yenidenKur).observe(sayfa); else window.addEventListener("resize", yenidenKur);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(yenidenKur);
    window.addEventListener("load", yenidenKur);
  }
})();
