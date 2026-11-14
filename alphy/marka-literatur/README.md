# Marka literatür derlemesi (2016–2026)

Bütünleşik (entegre) pazarlama iletişiminin çıktıları olarak marka farkındalığı, güven, tutum, satın alma niyeti ve sadakat üzerine, kurumsal iletişim bakış açısıyla bibliyometrik destekli derleme.

- Derleme (Claude Docs, canlı belge): https://claude.ai/artifact/GNXoZNPwWwXfvASXqUxkPJ
- Sunum (Slides, 25 slayt): https://claude.ai/artifact/XC2jUm8Nr77nqLSD7zaJ1J

| Dosya | İçerik |
| --- | --- |
| `kaynaklar.csv` | 50 makale: künye, doğrulama linki, dil, kavramlar, iletişim bağlamı, yöntem, örneklem, amaç, bulgu, test edilen ilişki, APA 7 |
| `atif-agi.csv` | 68 atıf bağı (kaynak → atıf yapılan), dil geçişi ve tespit yöntemi |
| `derleme.md` | Docs belgesinin markdown kopyası |
| `bibliyometri/` | `veri.py` (veri ve notlar), `build.py` (CSV ve grafikler), `sunum_grafik.py`, grafikler, `ozet-istatistik.json`, `elenenler.md` |
| `sunum-kaynak/` | Sunumun slayt dosyalarının yedeği |

Yeniden üretmek: `python bibliyometri/build.py` (networkx, matplotlib gerekir).

Özet: 50 makale (31 TR, %62; 19 EN), 68 atıf bağı (62 OpenAlex + 6 PDF kaynakça taraması), 1.115 kayıttan PRISMA benzeri akışla. Tarama tarihi 14 Kasım 2026.
