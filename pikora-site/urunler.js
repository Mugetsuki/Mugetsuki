/* Pikora · ürünler ve ayarlar
   Bu dosya sitenin tek "içerik" dosyasıdır. Ürün eklemek, fiyat yazmak ya da
   gerçek fotoğraf koymak için yalnızca burayı değiştirmek yeterli.

   GERÇEK FOTOĞRAF EKLEMEK:
   1. Fotoğrafı kare kırpıp gorseller/ klasörüne koy (ör. gorseller/kolye-bordo-gunes.jpg).
   2. O ürünün "gorsel" satırına dosya yolunu yaz, "gercek: true" ekle.
      "gercek: true" olunca "tasarım görseli" rozeti kalkar.

   FİYAT: "fiyat: 250" yazarsan kartta "250 ₺" görünür; null ise "Fiyatı sor" yazar. */

window.PIKORA_AYAR = {
  // Ülke koduyla, boşluksuz ve + işaretsiz: "905xxxxxxxxx". Boşsa WhatsApp kişi seçtirir.
  whatsapp: "905314629089",
  // @ işareti olmadan Instagram kullanıcı adı. Boşsa yalnızca mesaj kopyalanır.
  instagram: "pikora.pati"
};

window.PIKORA_AILELER = {
  bahce:   { ad: "Bahçe",   renk: "#F4A5B8" },
  meyve:   { ad: "Meyve",   renk: "#EE7B26" },
  gokyuzu: { ad: "Gökyüzü", renk: "#A8CDE6" },
  deniz:   { ad: "Deniz",   renk: "#A6DCC9" },
  zarafet: { ad: "Zarafet", renk: "#8A2335" }
};

window.PIKORA_URUNLER = [
  /* ---------------- Kolyeler ---------------- */
  { id: "kolye-bordo-gunes", tur: "kolye", ad: "Bordo Güneş", aile: "gokyuzu", fiyat: 300, gorsel: null,
    tarif: "Krem ipte bordo çiçek, ortası hardal bir güneş.",
    cizim: { ip: "#F7EEDD", motif: "cicek", yaprak: 8, renk: "#8A2335", gobek: "#E3A72F", yapraklar: [] } },
  { id: "kolye-bordo-papyon", tur: "kolye", ad: "Bordo Papyon", aile: "zarafet", fiyat: 300, gorsel: null,
    tarif: "Krem ipte bordo papyon, yanında minik bir yaprak.",
    cizim: { ip: "#F7EEDD", motif: "papyon", renk: "#8A2335", gobek: "#5E1424", yapraklar: ["#5F6E2E"] } },
  { id: "kolye-lavanta-cicek", tur: "kolye", ad: "Lavanta Çiçek", aile: "bahce", fiyat: 300, gorsel: null,
    tarif: "Krem ipte lavanta çiçek, mor göbek, iki gri-mavi yaprak.",
    cizim: { ip: "#F7EEDD", motif: "cicek", yaprak: 6, renk: "#BBA8DC", gobek: "#7B5EA7", yapraklar: ["#8FA9BF", "#8FA9BF"] } },
  { id: "kolye-mercan-cicek", tur: "kolye", ad: "Mercan Çiçek", aile: "deniz", fiyat: 300, gorsel: null,
    tarif: "Nane ipte mercan çiçek; deniz kenarı keyfi.",
    cizim: { ip: "#A6DCC9", motif: "cicek", yaprak: 6, renk: "#F07A5E", gobek: "#FFFAF2", yapraklar: [] } },
  { id: "kolye-mint-cicek", tur: "kolye", ad: "Mint Çiçek", aile: "bahce", fiyat: 300, gorsel: null,
    tarif: "Mint ipte pembe çiçek, taze bir yaprak.",
    cizim: { ip: "#A6DCC9", motif: "cicek", yaprak: 5, renk: "#F4A5B8", gobek: "#E3A72F", yapraklar: ["#5F6E2E"] } },
  { id: "kolye-minik-portakal", tur: "kolye", ad: "Minik Portakal", aile: "meyve", fiyat: 300, gorsel: null,
    tarif: "Krem ipte yuvarlak bir portakal, iki yeşil yaprak.",
    cizim: { ip: "#F7EEDD", motif: "portakal", renk: "#EE7B26", gobek: "#5F6E2E", yapraklar: ["#5F6E2E", "#7C8F3C"] } },
  { id: "kolye-pembe-bahar", tur: "kolye", ad: "Pembe Bahar", aile: "bahce", fiyat: 300, gorsel: null,
    tarif: "Krem ipte pembe çiçek, sarı göbek, nane yapraklar.",
    cizim: { ip: "#F7EEDD", motif: "cicek", yaprak: 6, renk: "#F4A5B8", gobek: "#E3A72F", yapraklar: ["#A6DCC9", "#A6DCC9"] } },

  /* ---------------- Bandanalar ---------------- */
  { id: "bandana-zeytin-dali", tur: "bandana", ad: "Zeytin Dalı", aile: "bahce", fiyat: 390,
    gorsel: "gorseller/bandana-zeytin-dali", kedi: "siyah kedide",
    tarif: "Krem bandanada siyah zeytin, iki yeşil yaprak, kırmızı kenar.",
    cizim: { zemin: "#F7EEDD", kenar: "#B5283A", motif: "#1D1716" } },
  { id: "bandana-gece-hilali", tur: "bandana", ad: "Gece Hilali", aile: "gokyuzu", fiyat: 390,
    gorsel: "gorseller/bandana-gece-hilali", kedi: "siyam kedide",
    tarif: "Siyah bandanada açık mavi hilal, mor boyun bandı.",
    cizim: { zemin: "#1D1716", kenar: "#A8CDE6", motif: "#A8CDE6" } },
  { id: "bandana-gun-isigi", tur: "bandana", ad: "Gün Işığı", aile: "gokyuzu", fiyat: 390,
    gorsel: "gorseller/bandana-gun-isigi", kedi: "beyaz kedide",
    tarif: "Lacivert bandanada sarı-turuncu güneş.",
    cizim: { zemin: "#1E2A4F", kenar: "#A8CDE6", motif: "#E3A72F" } },
  { id: "bandana-minik-balik", tur: "bandana", ad: "Minik Balık", aile: "deniz", fiyat: 390,
    gorsel: "gorseller/bandana-minik-balik", kedi: "beyaz kedide",
    tarif: "Nane bandanada mercan yüzgeçli küçük balık.",
    cizim: { zemin: "#A6DCC9", kenar: "#FFFAF2", motif: "#F07A5E" } },
  { id: "bandana-bordo-zarafet", tur: "bandana", ad: "Bordo Zarafet", aile: "zarafet", fiyat: 390,
    gorsel: "gorseller/bandana-bordo-zarafet", kedi: "smokin kedide",
    tarif: "Siyah boyunlukta büyük krem-bordo papyon.",
    cizim: { zemin: "#FFFAF2", kenar: "#8A2335", motif: "#8A2335" } },
  { id: "bandana-portakal-bahcesi", tur: "bandana", ad: "Portakal Bahçesi", aile: "meyve", fiyat: 390,
    gorsel: "gorseller/bandana-portakal-bahcesi", kedi: "sarman kedide",
    tarif: "Krem bandanada portakal ve yaprak, turuncu kenar.",
    cizim: { zemin: "#F7EEDD", kenar: "#EE7B26", motif: "#EE7B26" } },
  { id: "bandana-pembe-bahce", tur: "bandana", ad: "Pembe Bahçe", aile: "bahce", fiyat: 390,
    gorsel: "gorseller/bandana-pembe-bahce", kedi: "tekir kedide",
    tarif: "Krem bandanada pembe çiçek, yeşil yapraklar, kahve kenar.",
    cizim: { zemin: "#FFFAF2", kenar: "#6B3A2A", motif: "#F06A9B" } }
];
