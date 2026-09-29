# Web UI/UX — tüm sayfa aileleri

Bu aşama Brand Kit v1’in sayfa ve etkileşim sistemidir. Mevcut ana sayfadan keşfedilen URL’ler ile istenen yeni araçlar ayrı işaretlenmiştir. Tam CMS envanteri alınmadığı için “tüm sayfalar”, aşağıdaki kapsamlı sayfa ailelerini ifade eder; keşfedilmemiş içerikler canlıya geçişte aynı şablonlara eşlenmelidir. `index.html` ana sayfa ve `?page=…` rotalarıyla görsel önizleme sunar. Örnek içerikler kurgu/yer tutucudur, gerçek harita hesaplaması yoktur.

## Bilgi mimarisi ve navigasyon

Desktop: orijinal logo + Skyra Astro → Bugün → Burçlar → Araçlar → Dergi → Rehberler → primary “Doğum haritam”. Tema kontrolü ayrı icon button. Header 80px, sticky, opak surface; agresif blur yok. Menü alanı yetmediğinde 1024px altında açılır mobil navigasyon.

Mobile: logo, tema, menü; 72px header. Menü sayfa içinde açılan dikey panel, `aria-expanded`, Escape ile kapanır ve odak tetikleyiciye döner; modal olmadığı için focus trap yok. Harita CTA’sı menüde ve hero’da. Otomatik açılan popup veya yatay sürükleme zorunluluğu yok. İçeriğe atla linki ve sticky yüksekliğini telafi eden scroll-margin.

Footer: Keşfet / Rehberler / Skyra grupları; hakkımızda, yöntem, iletişim, gizlilik, KVKK, çerezler ve erişilebilirlik. Danışmanlık ayrı bağlantı olarak korunur. Gelecekte dil seçimi yalnız gerçek çeviriler yayımlandığında eklenir.

## Ana sayfa — dokuz bölüm

| Bölüm | Tasarım / içerik | Aksiyon ve responsive davranış |
| --- | --- | --- |
| 1 Hero | Sol 7 kolon: slogan, 2 cümle; sağ 5 kolon: orbital kompozisyon | Haritamı keşfet primary; günlük yorum secondary. Mobil dekor küçülür ve metinden sonra gelir. |
| 2 Bugün gökyüzünde | Tarih + Europe/Istanbul; Ay burcu, fazı, transit, tema | Dört kart desktop; 2×2 tablet/mobile. Veri ve yorum etiketleri ayrı. |
| 3 Günlük burçlar | 12 kart, sembol + isim + tarih aralığı | 6/4/2 kolon; her burç tek bağlantıyla günlük detaya. Haftalık arşiv linki. |
| 4 Haritanı keşfet | Gece moru sol metin + açık form sağ | Tarih, saat, yer; saat bilinmiyor seçeneği. 1 kolon mobile. |
| 5 Araçlar | 7 araç; kısa açıklama + indeks | 3/2/1 kolon; en çok iki satır başlık. |
| 6 Dergi | Bir büyük editorial kart + iki küçük | 2:1:1 oran / mobil alt alta. Kategori + süre + yazar. |
| 7 Skyra deneyimi | Açık dil, kişisel keşif, özenli deneyim | Ölçülmemiş başarı veya sahte yorum/sayı kullanılmaz. Yöntem linki. |
| 8 Newsletter | Sakin lavanta panel, email, açık kayıt açıklaması | Tek alan + buton; mobil stacked. Double opt-in yayında. |
| 9 Footer | Az metin, düzenli gruplar, haklar | 4/2/1 kolon; linklerde yeterli touch hedefi. |

## Sayfa envanteri ve şablonlar

| Sayfa ailesi | Kaynak / önerilen URL | UI/UX yapısı |
| --- | --- | --- |
| Ana sayfa | mevcut `/` | Yukarıdaki dokuz bölüm |
| Bugün | yeni `/bugun/` | H1 + tarih/saat dilimi → veri kartları → günlük tema → olaylar → kaynak/yöntem. Veri yokken bekleme değil açıklamalı empty state. |
| Günlük yorum arşivi | yeni `/gunluk-burc-yorumlari/` | Tarih → 12 burç → yayın zamanı → günlük/haftalık link. |
| Burç günlük detayı ×12 | yeni `/burclar/{burc}/gunluk/` | Burç başlığı + tarih → tema → ilişki/iş/kişisel alan metni → yükselen okuma rehberi → diğer burçlar. Önceki/sonraki gün yalnız gerçek yayın varsa. |
| Burç profil detayı ×12 | yeni `/burclar/{burc}/` | Sembol + tarih aralığı → element/nitelik/yönetici → dengeli profil metni → günlük yorum. Katı kişilik teşhisi yok. |
| Haftalık yorum arşivi | mevcut `/haftalik-astroloji-yorumu/` | Hafta aralığı → 12 burç → önceki haftalar. Mevcut URL korunur. |
| Haftalık yorum detayı | mevcut link hedefleri CMS’den teyit | Haftanın teması → alanlara göre metin → tarih aralığı → ilgili olaylar. |
| Doğum haritası formu | yeni `/dogum-haritasi/` | Açıklama → tarih/saat/yer → gizlilik notu → “Haritamı oluştur”. Form desktop 640px, mobil tam genişlik. |
| Harita sonucu | yeni sonuç şablonu; özel veri URL’ye yazılmaz | Girdi özeti → harita ve erişilebilir yerleşim tablosu → Güneş/Ay/yükselen → evler/açılar → yöntem → bilgileri düzenle. Saat bilinmiyorsa yükselen/evler yok. |
| Araçlar indeksi | yeni `/araclar/` | 7 araç kartı + yöntem notu; kullanıcı niyetine göre kısa açıklama. |
| Yükselen hesaplama | yeni `/yukselen-burc/` | Tarih+saat+yer zorunlu → sonuç+hangi bilgi neyi etkiler → haritaya git. |
| Ay burcu hesaplama | yeni `/ay-burcu/` | Tarih+saat+yer → sonuç; saat yoksa olası aralık, burç değişimi varsa belirsizlik. |
| Burç uyumu | yeni `/burc-uyumu/` | İki burç select → sembolik ortak temalar → iletişim önerileri; kaderci yüzde veya ilişki garantisi yok. |
| Transitler | yeni `/transitler/` | Genel transit liste → gezegen/açı/tarih filtreleri → detaya git. Natal veriyle eşleşirse açık “Kişisel transit” etiketi. |
| Transit detayı | yeni `/transitler/{olay}/` | Gezegen çifti → başlangıç/kesinleşme/bitiş → yöntem/orb → yorum → ilgili içerik. |
| Retro takvimi | yeni `/retro-takvimi/` | Yıl/gezegen filtresi → erişilebilir liste öncelikli → opsiyonel takvim. |
| Astrolojik takvim | yeni `/astrolojik-takvim/` | Ay navigasyonu → olay listesi → olay detayı. Renk dışında tür etiketi. |
| Astrolojik olay detayı | yeni `/astrolojik-takvim/{olay}/` | Tarih+saat+saat dilimi → olay tanımı → yorum → kaynak → ilgili olay. |
| Astroloji rehberi | mevcut `/astroloji/` | Başlangıç/gezegenler/evler/açılar/retro konu grupları → rehber kartları. |
| Rehber yazısı | mevcut slug’lar korunur | Breadcrumb → H1+özet+yazar → içindekiler → 65ch yazı → kaynaklar → ilgili içerik. |
| Doğal taşlar indeksi | mevcut `/dogal-taslar/` | Taş adı arama → alfabetik/simge filtreleri → sade kartlar; şifa iddiaları yeniden edit edilir. |
| Taş detayı | mevcut CMS slug’ı | Mineral bilgisi → kültürel sembolizm → bakım → kaynaklar. Tıbbi etki vaatleri yok. |
| Tarot indeksi | mevcut `/tarot/` | Büyük/küçük arkana filtre → kart grid → sayı/isim. |
| Tarot detayı | mevcut `/tarot/{slug}/` teyit | Sade kart görseli → semboller → yorum → okuma önerisi → ilgili kartlar. |
| Dergi / Blog | mevcut `/blog/` | Featured içerik → kategori filtreleri → sayfalı arşiv → boş sonuç. “Dergi” UI etiketi, URL korunur. |
| Yazı detayı | mevcut içerik URL’si | H1 → gerçek yazar/tarih/süre → görsel → okunur gövde → kaynak → ilgili yazılar. |
| Danışmanlık | mevcut `/tarot-danismanligi/` | Kapsam, süreç, gerçek ücret/süre editör tarafından doldurulur → SSS → mevcut Instagram’a ulaş. Uydurma fiyat/sertifika yok. |
| Hakkımızda | mevcut `/hakkinda/` | Marka hikâyesi → gerçek ekip profilleri → yaklaşım → iletişim. |
| Yöntem | yeni `/yontem/` | Hesaplama sistemi, ev sistemi, efemeris sürümü, saat dilimi, yorum editörü ve belirsizlik açıklaması. |
| İletişim | yeni `/iletisim/` | Konu+email+mesaj → açık geri dönüş bilgisi; mevcut Instagram linki. |
| Arama | WordPress `/?s=` | Arama terimi → içerik türü → sonuç listesi → no-results önerisi. |
| Newsletter sonucu | yeni kayıt durum şablonu | Email onay isteği / tamamlandı / expired link / servis hatası. |
| KVKK, gizlilik, çerez, erişilebilirlik | mevcut `/kvkk/`, `/cerez-politikasi/`; diğerleri yeni | Son güncelleme → içindekiler → okunur metin. Mevcut hukuki metinlerin yerine örnek metin yayımlanmaz. |
| 404 | WordPress `404.html` | Açık mesaj → arama → bugün / burçlar / harita linkleri. |

## Form sistemi

Input/select: 48px minimum yükseklik, 16px metin (mobil zoomu azaltır), 10px radius, control-border, 12px yatay boşluk. Label her zaman görünür; placeholder etiket yerine geçmez. Gerekli alan işareti metinle açıklanır. Focus 3px; error metin+ikon+`aria-invalid` ve `aria-describedby`; disabled native. Otomatik doldurma için uygun autocomplete.

Date picker: yerel kontrol ilk tercih; format yardımcı metni ve mantıklı min/max. Takvim custom olacaksa tam klavye desteği gerekir. Time picker: 24 saat, saat:dakika; “Saatimi bilmiyorum” tarih hassasiyetini açıklayan seçenek. Location input: yazılı sorgu → şehir/ülke listesi; sunucuda koordinat ve doğum tarihinde tarihsel saat dilimi çözümü; yalnız şehir adıyla kesin harita üretilmez. Combobox’ta oklar, Enter, Escape, listbox ve “sonuç bulunamadı” desteklenir. Önizlemede serbest yer alanı vardır, gerçek geocoding yoktur.

Submit: alan hatalarını alan altında göster, ilk hataya odaklan, girdi korunur. Loading etiketi “Hesaplanıyor”; çift tıklama önlenir; sonuç başarısızsa yeniden dene. Özel veriler analytics, query string veya sayfa cache’ine taşınmaz. Sonuç URL’si kişisel doğum bilgisi içermez. Hesaplama backend’i theme’den ayrı plugin/service olur.

## Responsive ve hareket

Mobile 320–767px: tek ana kolon, burçlar iki kolon, gökyüzü iki kolon, 20px kenar (320px’de 16px), tüm form aksiyonları tam genişlik. Tablet 768–1023: iki/üç kolon, 28px kenar. Desktop ≥1024: 1200px container, 40px kenar, 12 kolon. Menünün 1024px’de sığması ayrıca test edilir. 200% metin zoom ve 320 CSS px reflow kontrol edilir; geniş harita tablosu dışında yatay kaydırma gerekmez. Harita tablosunun metinsel görünümü şarttır.

Micro-interaction 160–220ms ease-out: border/background/opacity; kart hareketi en çok 2px. `prefers-reduced-motion` animasyon ve smooth scroll’u kapatır. Hover yalnız uygun cihazda; kritik içerik hover tooltip’e saklanmaz. Parallax, autoplay video, canvas yıldız yağmuru yok.

## Conversion ve doğrulama

İlk görevler: “Bugün” ve “Yorumum” tek seçim; doğum haritası formuna bir primary bağlantı. Başarı ölçüleri: hero CTA tıklaması, burç seçimi, form başlatma/tamamlama oranı, newsletter onayı; başlangıç değerleri ölçülmedi. Test planı: mobilde üç kullanıcı görevi, klavye ve ekran okuyucu akışı, form hata düzeltme, doğum saati bilinmiyor durumu. Tracking olayları yalnız aksiyon adı, sayfa türü, anonim durum; doğum verisi, şehir sorgusu veya email içermez.
