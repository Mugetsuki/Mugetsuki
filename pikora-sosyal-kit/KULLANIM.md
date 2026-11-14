# Pikora sosyal medya kiti (15 Kasım 2026)

Hepsi yüklemeye hazır PNG. Görseller `kaynak/uret.sh` ile yeniden üretilir; ürün adı ya da fiyat değişirse `pikora-site/urunler.js`'i değiştirip betiği çalıştırmak yeterli.

## 1. Profil (`1-profil/`)
- `profil-fotografi.png`: Instagram ve WhatsApp profil fotoğrafı (1080×1080). Daire içine sığacak şekilde kenar boşluğu var.
- `profil-fotografi-bordo.png`: aynı işaretin bordo zeminli hali. İkisinden birini seç, her yerde aynısını kullan.
- Bu işaret marka kitindeki "Yön A · Yumak kedi". Kitte logo yönü için "kurucu onayı bekliyor" yazıyor; site ve bu kit Yön A ile yapıldı.

**Kullanıcı adı:** @pikora.pati · **Ad alanı:** Pikora · el örgüsü kedi aksesuarı

**Biyografi (143 karakter, sınır 150):**
```
İlmek ilmek, pati pati 🧶
El örgüsü kedi kolyesi ve bandanası
Ürün giderleri düşülünce kalanın %10'u barınaktaki hayvanlara ayrılır.
Sipariş: DM
```
**Bağlantı:** site yayına girince site adresi; o zamana kadar `https://wa.me/905314629089`

## 2. Öne çıkanlar (`2-one-cikanlar/`)
Beş kapak, 1080×1920. Instagram kapağı ortadaki daireden keser.

| Kapak | Öne çıkanın adı | İçine ne konur |
|---|---|---|
| `kapak-urunler` | Ürünler | Ürün hikâyeleri |
| `kapak-siparis` | Sipariş | Nasıl sipariş verilir, ölçü nasıl alınır |
| `kapak-kadro` | Kadro | Beş kedi |
| `kapak-pay` | Pay | Pay Notları: ne ayrıldı, ne teslim edildi |
| `kapak-sorular` | Sorular | Sık sorulanlar |

## 3. Tanıtım gönderileri (`3-tanitim-gonderileri/`, 1080×1350)
İlk üç gönderi olarak bu sırayla paylaşılabilir: `1-tanisma`, `2-siparis`, `3-pay`.

## 4. Ürün gönderileri (`4-urun-gonderileri/`, 1080×1350)
14 ürün, her birinde ad, aile ve fiyat (kolye 300 ₺, bandana 390 ₺).
- Bandanalarda "tasarım görseli", kolyelerde "tasarım çizimi" rozeti var. Gerçek fotoğraf gelince görsel değişir, rozet kalkar.
- Örnek açıklama: "Gece Hilali sırasını bitirdik. Siyah bandanada açık mavi hilal, mor boyun bandı. 390 ₺. Sipariş için DM."

## 5. Hikâye (`5-hikaye/`)
`hikaye-zemin.png`: boş zemin (1080×1920). Üst kısım boş; yazıyı Instagram'ın yazı aracıyla eklersin.

## 6. Öne çıkanlara göre hikâyeler (`6-hikayeler/`, 1080×1920)
Her klasör bir öne çıkan başlığı. Hikâyeyi paylaş, sonra "Öne çıkar" ile aynı adlı başlığa ekle. Üst ve alt kısımlar Instagram arayüzü için boş bırakıldı.

| Öne çıkan | Hikâyeler |
|---|---|
| **Ürünler** | 1 yedi kolye · 2 yedi bandana · 3 beş aile · 4 Gece Hilali · 5 Pembe Bahar |
| **Sipariş** | 1 üç ilmekte sipariş · 2 ölçü nasıl alınır · 3 örnek mesaj · 4 fiyatlar |
| **Kadro** | 1 beş kedi · 2 Bombay'ın ip kontrolü · 3 Tekir ve yumak · 4 Sarman, sabahın üçü · 5 Smokin ve kutu |
| **Pay** | 1 Pikora bir sosyal girişim · 2 pay nasıl hesaplanır · 3 Pay Notu nedir · 4 kim örüyor · 5 Pay Notu şablonu (boş, doldurulur) |
| **Sorular** | 1 ölçü · 2 ne zaman hazır · 3 takınca nelere dikkat · 4 görseldekiyle aynı mı · 5 soru kutusu zemini |

- Başka bir ürünün tek ürün hikâyesi için `kaynak/hikaye.html` içindeki `tek("ürün-id")` satırını çoğalt.
- Kadro'da kedilerin adı yok, renkleriyle anılıyorlar (Bombay, Tekir, Sarman, Smokin). Adlar gelince `kaynak/hikaye.html`'de değişir.
- Kadro hikâyeleri kedi sesiyle; Pay, fiyat ve güvenlik hikâyeleri insan sesiyle yazıldı (marka kitindeki ses tonu kuralı).

## Yazarken dikkat
- Destek cümlesi her yerde aynı: "Ürün giderleri düşülünce kalanın %10'u barınaktaki hayvanlara ayrılır." "Satışın %10'u", "bağış yapıyoruz", "hayat kurtar" denmez.
- Gerçek teslim yapılmadan "teslim ettik / aktardık" yazılmaz.
- "Güvenli", "kopmaz", "%100" gibi sözler kullanılmaz; "gözetim altında tak" denir.
