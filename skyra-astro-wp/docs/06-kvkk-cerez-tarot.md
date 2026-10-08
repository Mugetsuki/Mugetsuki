# KVKK, çerez onayı, bültenin kapatılması ve Skyra Tarot

Tarih: 8 Ekim 2026. Hukuki dayanaklar ve açık sorular için iki araştırma raporu esas alındı; bu belge teknik uygulamayı anlatır. **Yayına almadan önce avukat incelemesi gereken noktalar en altta.**

## Canlı sitede tespit edilen izleme (8 Ekim 2026, temiz tarayıcı)

| Kaynak | Ne yapıyor | Çerez | Durum |
| --- | --- | --- | --- |
| `stats.wp.com/e-*.js` → `pixel.wp.com/g.gif` | Jetpack İstatistik (IP, sayfa, yönlendiren, tarayıcı) | Yok | Artık izne bağlı |
| `s0.wp.com/wp-content/js/bilmur.min.js` → `pixel.wp.com/boom.gif` | WordPress.com performans ölçümü | Yok | Artık izne bağlı |
| `fonts.wp.com`, `i0.wp.com`, `c0.wp.com` | Yazı tipi, görsel ve site ikonu (WordPress.com altyapısı) | Yok | Barındırmanın parçası; metinlerde açıklandı, görünümü korumak için değiştirilmedi |

Ziyaretçiye hiç çerez yazılmıyor. Tarayıcı depolaması: `skyra-theme`, `skyra-sign`, `skyra-birth` (gerekli) ve yeni `skyra-consent`.

## Çerez onayı (`skyra-core/includes/class-consent.php`, `assets/js/consent.js`)

- Sayfa HTML'i her ziyaretçi için aynıdır (WordPress.com önbelleğiyle uyumlu). Ölçüm betikleri sunucuda `type="text/plain"` yapılır; tarayıcı bunları ne çalıştırır ne indirir.
- "Tümünü Kabul Et" ya da tercihte "Analitik ve performans" açılırsa betikler aynı sırayla gerçek betiğe çevrilir. Red ve izin geri alma sitenin hiçbir işlevini kısıtlamaz; geri almada sayfa yenilenir.
- Tercih `localStorage` içinde (`skyra-consent`) tutulur, çerez yazılmaz. Footer'a "Çerez tercihleri" bağlantısı eklenir; `#cerez-tercihleri` bağlantısı tercih penceresini açar.
- Engellenen kaynaklar `skyra_consent_gates` filtresiyle genişletilebilir.
- Testler (gerçek tarayıcı, canlıdaki işaretlemenin birebir kopyasıyla): ilk ziyarette istek yok, red sonrası istek yok, izin sonrası iki betik doğru sırayla çalışıyor, tercih sayfalar arasında hatırlanıyor, geri almada istek kesiliyor, mobilde band ekrana sığıyor. 18/18 geçti.

## Bülten

- Kayıt formu ve `/newsletter` (kayıt, onay) uçları kapatıldı. `skyra/newsletter` bloğu kaydedilmediği için ana sayfadaki ve footer'daki alanlar boş görünür.
- Footer, bülten sütunu boşken `assets/css/footer.css` ile dengelenir (logo solda, menüler sağda); bülten yeniden açılırsa temanın düzeni aynen geri gelir.
- Mevcut abone kayıtları silinmedi. Gönderilmiş e-postalardaki "ayrıl" bağlantısı çalışmaya devam eder.
- Yeniden açmak için: `add_filter( 'skyra_newsletter_enabled', '__return_true' );`

## Yasal metinler

- `skyra-core/includes/content/pages.php`: KVKK Aydınlatma Metni, Gizlilik Politikası, Çerez Politikası. Veri sorumlusu: Şuara Güncü; başvuru: skyra.astro@gmail.com.
- Canlıya uygulamak: **Araçlar → Skyra kurulum → Yasal metinleri güncelle.** Önceki metin revizyon olarak saklanır.
- İletişim sayfası metnindeki danışmanlık çağrısı ve iletişim formundaki "Danışmanlık" konusu kaldırıldı (Reklam Yönetmeliği m.27/3).

## Skyra Tarot (`plugins/skyra-tarot`)

- Etkinleştirildiğinde `/tarot/` sayfasını **özel** olarak oluşturur: ziyaretçi 404 alır, arama motorları dizine eklemez (`noindex`). Yalnızca giriş yapmış yöneticiler/editörler görür.
- Herkese açmak tek bir anahtarla: **Ayarlar → Skyra Tarot** (sayfayı yayımlar; kutuyu boşaltınca yeniden özel olur). Avukat onayı olmadan açılmamalı.
- 78 kart, düz ve ters anlamlar, dört açılım (tek kart, üç kart, Kelt Haçı, ilişki). Kart tasarımları ve metinler özgündür (`tools/build-deck.py` metinlerin kaynağıdır).
- Yorumlar yalnızca hazır metinlerden üretilir (`assets/js/tarot-engine.js`). Yapay zekâ için uzantı noktası: `SkyraTarot.registerInterpreter()` ve `skyra_tarot_interpreter` filtresi; bu sürümde yalnızca `local` vardır.
- Soru alanı yok; seçimler sunucuya gönderilmez ve kaydedilmez. Testte açılım boyunca sıfır ağ isteği ve sıfır depolama ölçüldü.
- Eklenti devre dışı bırakıldığında sitenin geri kalanı değişmez; özel sayfa ziyaretçilere zaten kapalıdır.

## Geri alma

| Değişiklik | Nasıl geri alınır |
| --- | --- |
| Skyra Core 1.1.0 | Eklentiler → yükle → önceki zip ile "Mevcut olanı değiştir" |
| Yasal metinler | Sayfa → Revizyonlar → önceki sürümü geri yükle |
| Skyra Tarot | Eklentiyi devre dışı bırak (isteğe bağlı: özel "Tarot" sayfasını sil) |

## Yayından önce avukat incelemesi gerekenler

1. Ücretsiz tarot sayfasının 677 sayılı Kanun karşısındaki durumu (araştırma: belirsiz, emsal yok).
2. WordPress.com (Automattic, ABD) ve Gmail (Google) nedeniyle yurt dışına aktarım: KVKK m.9 güvencesi kurulmadı; metinde "değerlendirme sürüyor" yazıyor.
3. KVKK başvuruları için yazılı başvuru adresi ya da KEP (şu an yalnızca e-posta).
4. Hukuki sebep seçimleri (özellikle hesaplama araçları için m.5/2-c) ve saklama süreleri (iletişim: 6 ay) işletmeci onayı.
5. Eski bülten kayıtlarının ne zaman silineceği.
6. Ana sayfadaki "üçüncü kişilerle paylaşılmaz" ifadesi: barındırma sağlayıcısı veri işleyen olarak verilere erişebilir; ifadenin yumuşatılması değerlendirilmeli.
