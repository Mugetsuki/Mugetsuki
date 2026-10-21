# agent-office ve AionUi kıyası

Tarih: 21 Ekim 2026. Hazırlayan: Byte (agent-office, Claude Opus 5.5).

## Kısaca

- Aynı işte AionUi'nin dört ajanlı ekibi yaklaşık **3,5 milyon token** harcadı ve **23 dakika** sürdü. agent-office tarzı tek Claude ajanı aynı işi **341 bin token**, **2 dakika** ve **0,45 $** ile bitirdi.
- AionUi'de yalnızca koordinasyon yapan Claude lideri **0,79 $** harcadı. Bu, tek ajanın bütün işi yapma maliyetinden fazla.
- Maliyeti görmek açısından agent-office daha şeffaf. Her ajanın token ve dolar tutarını gösteriyor ve günlük bütçe sınırı koyabiliyor. AionUi ise Codex'in harcamasını gerçek değerin yaklaşık 7,5'te biri olarak gösterdi.
- AionUi'nin çıktısı daha zengindi (9,2 KB, tek ajanınki 3,8 KB). Ayrıca kodu, yazandan farklı bir model inceledi. Bunlar gerçek artılar.

## Neyi ölçtüm

**AionUi.** 18 Ekim 22:46–23:09 arasındaki "Ana Ekip" çalışması. Görev, `E:\AI\projeler\Deneme` klasöründe kişisel tanıtım sitesi yapmaktı. Kullanıcı iki mesaj yazdı:
1. Antigravity metni yazsın, OpenCode kodlasın, Codex incelesin.
2. Codex'in bulduklarını OpenCode düzeltsin, Codex tekrar baksın.

Kaynaklar:
- AionUi veritabanı (`%APPDATA%\AionUi\aionui\aionui-backend.db`)
- Claude oturum kaydı (`.claude\projects\E--AI-projeler-Deneme\06edabf7….jsonl`)
- Codex oturum kaydı (`.codex\sessions\2026\10\18\rollout-…01a15097….jsonl`)
- OpenCode veritabanı (`.local\share\opencode\opencode.db`)

**agent-office tarafı.** 21 Ekim'de aynı iş, aynı adımlar (metin, kod, inceleme, düzeltme, tekrar kontrol) ve aynı model (Claude Opus 5.5) ile tek ajana verildi. agent-office işçisi Claude Code olduğu için `claude -p` ile ölçüldü. Tutarı Claude Code'un kendisi bildirdi (0,4531 $). Opus 5.5 fiyatlarıyla elle yaptığım hesap aynı sonucu verdi.

## Sonuçlar

| | AionUi ekibi (4 ajan) | Tek Claude ajanı |
|---|---|---|
| Kullanıcı mesajı | 2 | 1 |
| Süre | ~23 dk (12 + 8 dk çalışma) | 2 dk |
| Toplam token | ~3,49 M | 0,34 M |
| Ölçülebilen dolar maliyeti | 0,79 $ (yalnızca Claude lideri) | 0,45 $ |
| Bulunan ve düzeltilen sorun | 5 (Codex buldu, OpenCode düzeltti) | 7 (kendisi buldu ve düzeltti) |
| Çıktı | index.html 9,2 KB | index.html 3,8 KB |

AionUi tarafında ajan ajan dağılım:

| Ajan | Model | Ne yaptı | Token | Ücret |
|---|---|---|---|---|
| Claude Code (lider) | Opus 5.5 | Yalnızca görev dağıttı ve takip etti. 28 API çağrısının 11'i "mesaj var mı?" kontrolüydü. | 1,27 M (9,4 bin çıktı) | 0,79 $ |
| OpenCode | big-pickle (ücretsiz) | Kodu yazdı ve düzeltti. Aynı raporu 5 kez tekrarladı, görevi iki kez kapatmadı, lider elle kapattı. | 1,22 M | 0 $ |
| Antigravity | varsayılan | 2,8 KB'lık metin dosyası için 27 model çağrısı ve ~20 kabuk komutu çalıştırdı. | ~0,68 M (AionUi'nin bildirdiği; doğrulanamadı) | abonelik/kota |
| Codex | gpt-6.1-sol | İki kez inceledi, kontrast oranlarını hesapladı. | 0,32 M (AionUi 42,7 bin gösterdi) | ChatGPT aboneliği/kota |

## Şeffaflık

**agent-office (v0.1.211):**
- Her işçinin token sayısını ve dolar tutarını Claude Code kayıtlarından hesaplıyor. Alt ajanlar da hesaba dahil.
- Günlük toplamı gösteriyor ve günlük bütçe koyabiliyor ("💸 Budget spent — hiring resumes tomorrow").
- Codex işçilerinde yalnızca ana oturum sayılıyor. Uygulama bunu kendisi de belirtiyor.

**AionUi:**
- Konuşma başına bağlam çubuğu ve maliyet gösteriyor, ama yalnızca ajan bu bilgiyi bildirirse.
- Claude'un tutarı doğru (0,79 $, benim hesabımla aynı).
- Codex için yalnızca son turu saklıyor: 42,7 bin gösterdi, gerçek değer 320,6 bin.
- Ekibin toplamını gösteren bir yer yok, bütçe sınırı da yok.

## Bu kıyasın sınırları

- Tek bir görev ve tek bir deneme. Aynı iş tekrar çalıştırılsa rakamlar oynar, ama 10 katlık fark rastlantıyla açıklanamayacak kadar büyük.
- Görev küçük ve sıralıydı. Çok ajanlı yapının asıl artısı olan paralel çalışma burada kullanılmadı, çünkü ekibe adımları sırayla yapması söylenmişti.
- Tek ajan kendi kodunu kendisi inceledi. AionUi'de inceleme yazandan farklı bir model tarafından yapıldı, bu daha bağımsız bir kontrol.
- Testte tek ajanın commit izni yoktu (benim kısıtlamamdı), bu yüzden commit atamadı. Token tutarına etkisi küçük.
- Codex, Antigravity ve OpenCode abonelik ya da ücretsiz kota kullandığı için doların karşılığı yok, yalnızca token sayısı var.

## Öneri

1. **Asıl işler için agent-office'te kal.** Maliyeti açıkça görüyorsun, bütçe sınırı koyabiliyorsun. Her ajan kendi branch'inde çalışıp GitHub'a push ediyor. 15 Ekim'deki kayıpta kurtulanlar tam olarak push edilmiş işlerdi.
2. **Veri kaybı endişesinin asıl nedeni araç değil, dosyaların yeri.** agent-office şu an yine WSL içinde çalışıyor (`~/ajan-ofisi/...`, Ubuntu diski). Önlemler:
   - Her iş bitince push et (agent-office ajanları bunu yapıyor).
   - Pazar yedeği zaten var.
   - İstersen ofisi Windows tarafında çalıştır. 15–18 Ekim'de orada çalışıyordu.
3. **AionUi'yi bırakmak zorunda değilsin, ama ekip modunu küçük işlerde kullanma.** Ekip modu küçük işlerde 10 kat fazla harcıyor. Farklı bir modelin ikinci görüşü gerektiğinde tek bir Codex sohbeti açman yeter. Bunu agent-office'te Codex işçisi işe alarak da yapabilirsin.
4. **AionUi'de ekip kurarsan liderin pahalı modelini iş yapan ajan yap.** Yalnızca koordinasyon yapan bir Opus lideri tek başına işin tamamından pahalıya geliyor.
5. **Açık konu:** `E:\AI\projeler\Deneme` deposunun uzak deposu yok. AionUi ekibinin commit'leri (`d95d933`, `e5ee613`) yalnızca bu bilgisayarda duruyor. Deneme olduğu için önemli değil, ama AionUi ile gerçek bir iş yaparsan önce GitHub deposu bağla.

## Yeniden ölçmek için

`rapor/cc_usage.py` bir Claude Code oturum kaydının token toplamını çıkarıyor:

```
python3 rapor/cc_usage.py "<.claude/projects/...>/<oturum>.jsonl"
```

Opus 5.5 fiyatları (1 milyon token başına): girdi 4 $, çıktı 20 $, önbellekten okuma 0,20 $, 1 saatlik önbelleğe yazma 8 $.
