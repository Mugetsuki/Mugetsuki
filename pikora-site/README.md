# Pikora sitesi (örnek sürüm, 14 Kasım 2026)

Tek sayfa, derleme adımı yok: `index.html` dosyasına çift tıklayınca açılır.
Bu sürümde giriş, ürünler (7 kolye, 7 bandana), "İpim" sipariş paneli, sipariş adımları ve sosyal girişim ("Pay") bölümü var.
Sonraki bölümler (Kedine yakışanı bul, Üzerinde dene) kurucu onayından sonra eklenecek.

- **İçerik tek dosyada:** `urunler.js`. Ürün adı, tarif, fiyat, görsel, WhatsApp numarası ve Instagram adı buradan değişir. Dosyanın başında nasıl yapılacağı yazıyor.
- **Görseller:** bandanalar kurucunun 27 Haziran 2026 tarihli yapay zeka tasarım görselleri (`gorseller/`). Kolyelerin görseli yok; `uygulama.js` tariflerden çizim üretiyor. Gerçek fotoğraf gelince `urunler.js`'te `gorsel` ve `gercek: true` yazmak yeterli.
- **Sipariş:** ödeme altyapısı yok. Seçilenler hazır bir mesaja dönüşür, WhatsApp ya da Instagram'dan gönderilir. Seçimler yalnızca tarayıcıda (`localStorage`) durur.
- **Marka:** `tokens.css` marka kitinden kopyalandı (sürüm 1.1), değiştirilmedi. Yazı tipleri `fonts/` içinde, dış servis yok.
- **Destek cümlesi:** "Ürün giderleri düşülünce kalanın %10'u barınaktaki hayvanlara ayrılır." Marka kitindeki `referanslar/destek-cumleleri.md` ile birebir; değiştirme. "Satışın/fiyatın %10'u", "bağış" gibi ifadeler kullanılmaz.
- **Çizimler:** `cizim.js` site ve `pikora-sosyal-kit` tarafından ortak kullanılır.
