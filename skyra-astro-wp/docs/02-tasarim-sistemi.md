# 02 · Tasarım sistemi

Kaynak: *Skyra Astro Brand Kit v1.1* (28 sayfa). Brand Kit yeniden yazılmadı; değerleri olduğu gibi token'a dönüştürüldü. Yön: **quiet observatory** — ışıklı krem bir yayın alanı, az sayıda gece moru odak, ince yörünge çizgileri, işlevsel veri kutuları.

## Tokenlar

`theme.json` preset'leri tek kaynak; `skyra.css` bunları kararlı adlarla takma adlandırır. Blok CSS'i yalnızca bu adları kullanır; bölüm içinde rastgele değer yoktur.

| Token | Açık | Koyu | Brand Kit rolü |
| --- | --- | --- | --- |
| `--color-primary` | `#665080` | `#CDB8E7` | Ana aksiyon |
| `--color-secondary` | `#E6DDED` | `#332A43` | Yardımcı yüzey |
| `--color-accent` | `#A45C74` | `#DFA6BC` | Küçük editoryal vurgu (dekor, çizgi, nokta) |
| `--color-accent-text` | `#934A63` | `#DFA6BC` | Vurgunun küçük metin tonu (tüm açık yüzeylerde ≥ 4.5:1) |
| `--color-background` | `#F8F5EF` | `#17131F` | Zemin |
| `--color-surface` / `--color-surface-raised` | `#FFFDF9` / `#EEE7F3` | `#241E2E` / `#2D2539` | Kart / yükseltilmiş yüzey |
| `--color-text` / `--color-text-muted` | `#292333` / `#695E71` | `#F5F0F8` / `#C0B4CB` | Metin |
| `--color-border` / `--color-control` | `#DAD1DF` / `#887790` | `#4B4058` / `#9D8CA9` | Dekoratif ayırıcı / form sınırı |
| `--color-hover`, `--color-on-primary`, `--color-night` | `#523E6B`, `#FFFDF9`, `#211B30` | `#DDCAF2`, `#241E2E`, `#100D17` | |
| `--color-blush`, `--color-sand` | `#F1E1E7`, `#EEE6DA` | koyu karşılıkları | Yalnız dekoratif yüzey |
| `--font-display` / `--font-body` | Quicksand 500–600 / DM Sans 400–600 | | Başlık & marka / metin & arayüz |
| `--radius-xs…pill` | 6 · 10 · 14 · 24 · 32 · 999 px | | Rozet · input · buton · kart · büyük CTA · chip |
| `--shadow-sm/md/lg` | Brand Kit değerleri | koyuda daha derin | Hover'da md |
| `--space-2xs…4xl` | 4 · 8 · 12 · 16 · 24 · 32 · 48 · 64 · 96 | | 4 px taban |
| `--gutter`, `--section` | 20/28/40, 48/64/96 | | mobil/tablet/masaüstü |
| Gradyanlar | dawn · lunar · night · dark-aura | | Sayfada en çok 2 büyük gradyan alanı (hero dawn, harita night) |

`--color-accent-text` tek eklemedir: Brand Kit pembesi `#A45C74`, kremde 4.42:1 verdiği için 12 px etiketlerde WCAG AA'yı karşılamıyordu. Renk ailesi aynı, yalnız küçük metin için bir ton koyu.

## Tipografi

| Rol | Tanım |
| --- | --- |
| H1 | Quicksand 500, `clamp(38px → 64px)`, 1.08, −0.045em |
| H2 | Quicksand 600, 28 → 40 px, −0.035em |
| H3 / kart başlığı | Quicksand 600 22–24 px / DM Sans 600 18–20 px |
| Gövde | DM Sans 400, 16 → 17 px, 1.65 · blog 17 → 19 px, 1.8 |
| Eyebrow | DM Sans 600, 12 px, 0.12em, büyük harf |
| Veri | DM Sans 500, `tabular-nums` |

Fontlar yerelde (WOFF2, latin + latin-ext, toplam 116 KB), `font-display: swap`. İlk ekran için Quicksand latin, DM Sans latin ve latin-ext önceden yüklenir (Türkçe gövde metni ş/ğ/İ için latin-ext'e hemen ihtiyaç duyar).

## Logo

Kedi amblemi değiştirilmedi. Yapılanlar yalnızca Brand Kit'in izin verdiği optik düzeltmeler: saydam kenarlar kırpılıp amblem kare bir tuvalde ortalandı (`skyra-mark.png`), her zaman krem bir yüzey üzerinde (koyu modda da, invert yok) 44–48 px kutuda gösterilir; yanında canlı Quicksand "skyra / ASTRO" yazısı. Retina/baskı için yetkili SVG master hâlâ gerekiyor.

## İkonlar

`class-icons.php`: 24 × 24 ızgara, 1.5 px çizgi, yuvarlak uç, `currentColor`. 12 burç ve 11 gök cismi aynı optik ağırlıkta, elle çizilmiş SVG (emoji ve Unicode glif yok). Burç adları her zaman metin olarak yanında. Dekoratif ikonlar `aria-hidden`, yalnız ikon içeren düğmelerin erişilebilir adı var.

## Bileşenler

- **Butonlar:** primary / secondary / ghost / icon; 48 px (lg 56 px); hover'da −2 px ve ok +3 px (yalnızca hover destekleyen cihazda); focus 3 px + 3 px offset; loading'de `aria-busy` ve "Hesaplanıyor…".
- **Kartlar:** surface + dekoratif border + 24 px radius; hover'da gölge + 2 px yükselme; kart içinde tek bağlantı (stretched link), iç içe link yok.
- **Veri/Yorum etiketleri:** "Veri", "Yorum", "Gökyüzü okuması", "Editör yorumu" — Brand Kit'in veri/yorum ayrımı.
- **Formlar:** görünür label, "gerekli / isteğe bağlı" metni, alan altı yardım ve hata (`aria-invalid` + `aria-describedby`), 16 px metin (iOS zoom yok), yerel tarih/saat kontrolleri, erişilebilir combobox (oklar, Enter, Escape, "sonuç bulunamadı").
- **Harita (SVG):** tek çizici; halka, 12 burç dilimi, 5° çentikler, ev çizgileri, ASC/MC, gezegenler (üst üste binmeyi önleyen yayılım), uyumlu (mor) / gerilimli (pembe) açılar. Her haritanın yanında metin tablosu var.

## Bölüm ritmi (ana sayfa)

Hero (dawn gradyan) → Bugün (krem, dashboard + editoryal) → Günlük burçlar (surface bandı) → Doğum haritası (tek gece moru odak, açık form paneli) → Araçlar (bento, farklı boyutlar) → Günün enerjisi (tipografik, surface) → Ay fazı (lavanta bandı) → Yaklaşan olaylar (takvim + zaman çizgisi) → Burçlar (element sütunları, surface) → Blog (1 + 3 editoryal) → Skyra nedir (surface) → Bülten (lavanta panel) → Topluluk → Footer (gece, takımyıldız çizgisi).

## Hareket

- İlk yükleme: başlık ve CTA'lar yalnızca `transform` ile 14 px yükselir (opaklık yok → LCP etkilenmez), toplam < 1 sn.
- Hero: yörüngeler ve takımyıldız çizgileri stroke-reveal ile çizilir; iki yörünge 160/240 sn'de bir döner, ekrandan çıkınca durur. Destekleyen tarayıcıda hero, kaydırdıkça hafif küçülüp döner (`animation-timeline: view()`, scroll hijacking yok).
- Harita çizimleri görünür olduklarında halkadan açılara doğru sırayla belirir; Ay fazı yeni aydan bugünkü faza büyür; bülten panelindeki yörüngeler çizilir.
- Kart hover 160–220 ms, en çok 2 px. `prefers-reduced-motion: reduce` tüm animasyonları kapatır; içerik hiçbir zaman animasyona bağlı değildir (yalnızca ekranın altındaki öğeler gizlenip belirir).

## Koyu mod

Koyu mod preset renklerini yeniden tanımlar; her blok ve takma ad otomatik uyar. Sistem tercihi varsayılan, header'daki düğme kalıcı olarak (`localStorage`) değiştirir; tema, ilk boyamadan önce satır içi betikle uygulanır (flaş yok). Yüzey katmanları (background/surface/raised) gölgeye değil ton farkına dayanır; harita, Ay ve element renkleri koyu zemin için ayrıca ayarlandı. Editörler site genelinde koyu görünüm isterse **Görünüm → Editör → Stiller → Gece** varyasyonu var.

Skyra Astro teması aktif değilken (ör. Skyra Editorial) koyu modu Skyra Core üstlenir (`includes/class-color-mode.php`): header'daki ilk gezinme bloğunun yanına aynı anahtar eklenir (gezinme yoksa sağ altta sabit durur), bloklar ve Skyra Editorial renkleri `assets/css/color-mode.css` ile koyuya geçer. Skyra teması aktifken bu katman devre dışıdır; `skyra_color_mode` filtresiyle kapatılabilir.

## Responsive

Mobile-first; 360, 390, 430, 768, 1024, 1440 px'de yatay taşma yok (otomatik test). Mobilde: tek kolon, büyük tam genişlik CTA'lar, burç kartları kaydırmalı (düğmeli), araçlar kompakt satır, burçlar 2 kolon, blog yatay kart, footer menüleri 2 kolon. Masaüstü mobilin büyütülmüşü değildir: hero 7/5 kolon ve taşan gökyüzü kompozisyonu, Bugün paneli 4 kolon + büyük Ay kartı, araçlar 12 kolon bento, takvimde yapışkan ay başlığı ve mini ay görünümü. Tam ekran yükseklikleri `svh`/`dvh` ile.
