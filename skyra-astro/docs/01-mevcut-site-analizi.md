# Skyra Astro — mevcut site analizi

İnceleme: 30 Eylül 2026. Kaynaklar: [ana sayfa](https://skyraastro.com/), canlı HTML, tema CSS’si ve header logo dosyası. Bulgular kaynak incelemesine dayanır; canlı tarayıcı ekran görüntüsü, Lighthouse, kullanıcı araştırması veya analitik ölçümü yapılmamıştır. Alt sayfa bağlantıları keşfedildi, fakat web okuyucusu bunları getiremedi. Bu sayfaların içerik kalitesi veya çalışma durumu hakkında hüküm verilmez.

## Mevcut marka ve görsel sistem

| Alan | Doğrulanan bulgu | Tasarım değerlendirmesi / öneri |
| --- | --- | --- |
| Logo | Header’da yıldız alınlı, lacivert konturlu sıcak sarı kedi amblemi; kaynak PNG 150 × 79. Görsel dosya yerelde korunmuştur. | Samimi ve ayırt edilebilir. Premium konumlandırmayla gerilim yaratabilecek oyuncu karakteri değiştirilmez; ciddi ürün tipografisi ve sade çevreyle dengelenir. |
| Logo yerleşimi | 56 × 56 koyu kutu; görsel CSS’de 48 × 48, `object-fit:contain`. Kaynak en-boy oranı yaklaşık 1,90. | Dosyadaki saydam boşluk, kare kutu içinde görünen amblemi küçük bırakabilir. Amblemi esnetmeden, saydam sınırları ölçülerek optik yerleştirme yapılmalı. |
| Ana renkler | Mürekkep #0D0A1A, mor #6B4FA8, altın #C9A84C, inci #EDE8F5, lacivert #27356F, krem #FFF5DC, kâğıt #FAF8F2, ikincil metin #625D69, çizgi #DBD6CF. | Açık editorial zemin iyi bir temel. Sarı krem + altın + lacivert birlikteliği yeni ürün yönünde sadeleştirilmeli; logo sarısı kurumsal sarı palete yayılmamalı. |
| Tipografi | Başlıklar Cormorant Garamond 500; gövde DM Sans 18px / 1,75; navigasyon 14px / 500. | Editorial karakter mevcut. Quicksand yalnızca kimlik ve ana başlıklarda; DM Sans gövde ve veri arayüzünde korunarak süreklilik sağlanabilir. Fontun kaynakta tanımlanması gerçek yüklenme hızını kanıtlamaz. |
| Yerleşim | 1200px kapsayıcı; masaüstünde 40px, küçük ekranda 20/17px yatay boşluk; kartlar yaklaşık 8–12px radius. | Bileşen mantığı mevcut. Daha tutarlı token ölçeği, veri kartları ve net birincil aksiyon gerekir. |
| Hero | Soyut kişisel keşif mesajı; haftalık yorum ve tarot danışmanlığına bağlantılar. İnce orbital SVG, altın/lacivert kullanımı. | Orbital dil hedefe yakın. Yeni hero günlük keşif ile kişisel harita arasındaki farkı net anlatmalı. |
| İkonlar | Rehber ve taş kartlarında emoji; burçlarda Unicode semboller. | Emoji platforma göre değişir ve premium tutarlılığı zayıflatır. Tek stroke ailesi ve normalize edilmiş zodiac SVG seti önerilir. |
| Mobil | CSS’de responsive menü, 44px kontrol alanları, mobilde gizlenen hero dekoru. | Mobil düşünülmüş. Gerçek klavye, zoom, menü odağı ve cihaz davranışı ayrıca test edilmeli. |

## İçerik ve sayfa hiyerarşisi

Ana sayfa sırası: hero → haftalık enerji ve 12 burç → üç keşif kategorisi → doğal taşlar → tarot → son yazılar → danışmanlık → footer. Navigasyon altı konu sunuyor: astroloji, haftalık yorum, doğal taşlar, tarot, blog ve danışmanlık. Footer’da astroloji alt konuları aynı kategori bağlantısına yönleniyor; detay sayfasına giden gerçek rotalar ayrıca doğrulanmalı.

Bu yapı konu keşfini destekliyor, ancak günlük geri dönüş ve kişisel harita üretme hedefi için iki temel iş görünür değil. Taş ve tarot içerikleri kaldırılmadan ikincil “Rehberler” kümesine taşınmalı. Haftalık yorum mevcut kullanıcılar için devam etmeli; günlük yorum yeni bir yayın akışı olarak kurulmalı.

Ana sayfada 14 Mart 2026 tarihli bir deneme blog girdisi görünüyor. Yayın öncesi içerik temizliği kritik. Taş açıklamalarındaki şifa/enerji dili ve danışmanlık metinleri marka editörü tarafından gözden geçirilmeli; yeni marka kesin sonuç veya sağlık etkisi vaadi yerine sembolik yorum ve öz farkındalık dili kullanmalı. Ana sayfadaki ay fazı etiketi için doğrulanmış veri kaynağı ve güncelleme zamanı görünmüyor; sabit metin canlı astronomik veri gibi sunulmamalı.

## Kullanıcı yolculukları

| Kullanıcı işi | Mevcut karşılık | Yeni karşılık |
| --- | --- | --- |
| Bugün gökyüzünde ne var? | Haftalık atmosfer ve ay evresi etiketi | Tarih, Europe/Istanbul saat dilimi, güncelleme saati ve veri kaynağı içeren dört bilgi kartı |
| Burcum bugün ne söylüyor? | Haftalık burç seçimi | Hero’dan günlük yorumlara tek bağlantı; 12 burç direkt bağlantıları; günlük/haftalık geçiş |
| Doğum haritamı nasıl görürüm? | Astroloji rehberinde konu olarak anılıyor | Header ve hero’da tek birincil CTA; üç alanlı form; harita + metinsel sonuç |
| İçeriğin güvenilirliği nedir? | Blog ve rehberler, danışmanlık sosyal medya bağlantısı | Yazar profili, düzenleme tarihi, yöntem açıklaması, veri/yorum ayrımı |
| Danışmanlığa nasıl ulaşırım? | Instagram üzerinden başvuru | Kapsam, süre, ücret bilgisi için yapılandırılmış sayfa; mevcut iletişim bağlantısı korunur |

## Öncelikler

P0: deneme içeriğini temizle, logo ana dosyasını teyit et, günlük yorum ve hesaplama için veri/yayın sahipliği belirle, mobil üç temel yolu görünür yap. P1: renk/tipografi tokenlarını uygula, navigasyonu sadeleştir, formların hata/boş/bekleme durumlarını tasarla, eski URL’leri koru. P2: editoryal fotoğraf kütüphanesi, newsletter, koyu tema, kişisel transit deneyimi.

## Kanıt ve sınırlar

Kaynak HTML’de `wp-theme-skyra-editorial is-block-theme` ve Gutenberg varlıkları görüldü. Bu, WordPress blok teması kullanımını destekler; admin kurulumu incelenmedi. Header ve favicon farklı logo dosyalarına işaret ediyor; favicon görseli incelenmedi. Kaynak CSS’de performans veya sürüm açıklaması bulunması üretim uyumluluğunu kanıtlamaz. Mevcut sitenin Core Web Vitals, renk kontrastının tamamı ve ziyaretçi conversion oranı ölçülmedi. Bu belgede hedefler gerçek ölçüm gibi sunulmaz.
