# -*- coding: utf-8 -*-
"""Kaynak tablosu, atıf ağı ve bibliyometrik çıktıları üretir.

Çalıştırma (alphy/marka-literatur klasöründen):
    python bibliyometri/build.py
Gerekenler: pandas, networkx, matplotlib.
"""
import csv, json, os, collections, itertools
import networkx as nx
import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(HERE)
import sys; sys.path.insert(0, HERE)
from veri import KAYNAKLAR, PDF_KENARLARI

OA = json.load(open(os.path.join(HERE, "openalex_ham.json"), encoding="utf-8"))
K = {k["id"]: k for k in KAYNAKLAR}
assert len(K) == len(KAYNAKLAR) == 50

# Renkler (dataviz referans paleti, açık tema)
TR_C, EN_C = "#2a78d6", "#eb6834"
INK, INK2, GRID, SURF = "#0b0b0b", "#52514e", "#e4e3df", "#fcfcfb"
plt.rcParams.update({"font.family": "DejaVu Sans", "font.size": 10, "axes.edgecolor": INK2,
                     "axes.labelcolor": INK2, "xtick.color": INK2, "ytick.color": INK2,
                     "figure.facecolor": SURF, "axes.facecolor": SURF, "savefig.facecolor": SURF})

KOD = {"FA": "Farkındalık", "GU": "Güven", "TU": "Tutum", "SN": "Satın alma niyeti", "SA": "Sadakat", "BPI": "BPİ"}

# ---------- 1. kaynaklar.csv ----------
alanlar = ["id", "kisa", "dil", "yil", "dergi", "link", "kavram", "baglam", "yontem", "orneklem", "amac", "bulgu", "iliski", "apa"]
with open(os.path.join(ROOT, "kaynaklar.csv"), "w", newline="", encoding="utf-8") as f:
    w = csv.writer(f); w.writerow(["openalex_id", "kisa_atif", "dil", "yil", "dergi", "dogrulama_linki", "kavramlar",
                                  "iletisim_baglami", "yontem", "orneklem", "amac", "ana_bulgu", "test_edilen_iliski", "apa7"])
    for k in sorted(KAYNAKLAR, key=lambda x: (x["dil"] != "TR", x["yil"], x["kisa"])):
        w.writerow([k[a] for a in alanlar])

# ---------- 2. Atıf kenarları ----------
kenar = {}
for src, d in OA.items():
    for r in d["referenced_works"]:
        if r in K and r != src:
            kenar[(src, r)] = "OpenAlex referenced_works"
for s, t in PDF_KENARLARI:
    assert s in K and t in K
    kenar.setdefault((s, t), "PDF kaynakça taraması")
with open(os.path.join(ROOT, "atif-agi.csv"), "w", newline="", encoding="utf-8") as f:
    w = csv.writer(f)
    w.writerow(["kaynak_id", "kaynak", "kaynak_dil", "kaynak_yil", "atif_yapilan_id", "atif_yapilan", "atif_yapilan_dil",
                "atif_yapilan_yil", "dil_gecisi", "tespit_yontemi"])
    for (s, t), how in sorted(kenar.items(), key=lambda e: (K[e[0][1]]["yil"], K[e[0][1]]["kisa"], K[e[0][0]]["yil"])):
        w.writerow([s, K[s]["kisa"], K[s]["dil"], K[s]["yil"], t, K[t]["kisa"], K[t]["dil"], K[t]["yil"],
                    f'{K[s]["dil"]}→{K[t]["dil"]}', how])

G = nx.DiGraph(); G.add_nodes_from(K)
G.add_edges_from(kenar)
stats = {"makale": len(K), "tr": sum(k["dil"] == "TR" for k in KAYNAKLAR), "en": sum(k["dil"] == "EN" for k in KAYNAKLAR),
         "kenar": G.number_of_edges(), "kenar_openalex": sum(v.startswith("OpenAlex") for v in kenar.values()),
         "kenar_pdf": sum(v.startswith("PDF") for v in kenar.values())}
stats["dil_gecisi"] = dict(collections.Counter(f'{K[s]["dil"]}→{K[t]["dil"]}' for s, t in G.edges()))
stats["yalniz"] = [K[n]["kisa"] for n in G if G.degree(n) == 0]
stats["en_cok_atif_alan"] = [(K[n]["kisa"], G.in_degree(n)) for n in sorted(G, key=lambda n: -G.in_degree(n))[:10]]
stats["en_cok_atif_yapan"] = [(K[n]["kisa"], G.out_degree(n)) for n in sorted(G, key=lambda n: -G.out_degree(n))[:6]]

# Köprü makaleler: hem TR hem EN makalelerle atıf bağı olanlar
kopru = []
for n in G:
    komsu = set(G.predecessors(n)) | set(G.successors(n))
    diller = collections.Counter(K[m]["dil"] for m in komsu)
    if diller["TR"] and diller["EN"]:
        kopru.append((K[n]["kisa"], K[n]["dil"], diller["TR"], diller["EN"]))
kopru.sort(key=lambda x: -(min(x[2], x[3]) * 10 + x[2] + x[3]))
stats["kopru"] = kopru
with open(os.path.join(HERE, "kopru-makaleler.csv"), "w", newline="", encoding="utf-8") as f:
    w = csv.writer(f); w.writerow(["makale", "dil", "bagli_TR_makale", "bagli_EN_makale"]); w.writerows(kopru)

# Kümeler (yönsüz ağda modülerlik)
U = G.to_undirected()
kume = nx.community.greedy_modularity_communities(U.subgraph([n for n in U if U.degree(n) > 0]), seed=7) \
    if "seed" in nx.community.greedy_modularity_communities.__code__.co_varnames else \
    nx.community.greedy_modularity_communities(U.subgraph([n for n in U if U.degree(n) > 0]))
stats["kumeler"] = [sorted(K[n]["kisa"] for n in c) for c in kume]

# Zincirler: uzunluğu en az 3 olan yönlü yollar (A → B → C), en eski hedefe göre
zincir = []
for a in G:
    for b in G.successors(a):
        for c in G.successors(b):
            zincir.append(f'{K[c]["kisa"]} ← {K[b]["kisa"]} ← {K[a]["kisa"]}')
stats["uc_halkali_zincir_sayisi"] = len(zincir)
stats["zincir_ornek"] = zincir[:40]

# ---------- 3. Yıllara göre ----------
yil = collections.Counter((k["yil"], k["dil"]) for k in KAYNAKLAR)
yillar = list(range(2016, 2027))
with open(os.path.join(HERE, "yillara-gore.csv"), "w", newline="", encoding="utf-8") as f:
    w = csv.writer(f); w.writerow(["yil", "TR", "EN", "toplam"])
    for y in yillar: w.writerow([y, yil[(y, "TR")], yil[(y, "EN")], yil[(y, "TR")] + yil[(y, "EN")]])
stats["yillar"] = {y: [yil[(y, "TR")], yil[(y, "EN")]] for y in yillar}

fig, ax = plt.subplots(figsize=(8, 3.6), dpi=200)
tr = [yil[(y, "TR")] for y in yillar]; en = [yil[(y, "EN")] for y in yillar]
ax.bar(yillar, tr, width=0.62, color=TR_C, label="Türkçe", edgecolor=SURF, linewidth=1.5)
ax.bar(yillar, en, width=0.62, bottom=tr, color=EN_C, label="İngilizce", edgecolor=SURF, linewidth=1.5)
for y, a, b in zip(yillar, tr, en):
    if a + b: ax.text(y, a + b + 0.15, str(a + b), ha="center", va="bottom", fontsize=9, color=INK)
ax.set_xticks(yillar); ax.set_yticks([]); [ax.spines[s].set_visible(False) for s in ("top", "right", "left")]
ax.set_title("Derlemedeki 50 makalenin yayın yılı: yoğunluk 2019–2023'te", loc="left", fontsize=11, color=INK)
ax.legend(frameon=False, loc="upper left", ncol=2, fontsize=9)
ax.set_ylim(0, max(a + b for a, b in zip(tr, en)) + 1.5)
fig.tight_layout(); fig.savefig(os.path.join(HERE, "yillara-gore.png")); plt.close(fig)

# ---------- 4. Yöntemler ----------
def yontem_grubu(y):
    if "Kavramsal" in y: return "Kavramsal / derleme"
    if "Nitel" in y: return "Nitel (görüşme)"
    if "Deney" in y: return "Deney"
    if "PLS" in y: return "Anket + PLS-YEM"
    if "YEM" in y: return "Anket + kovaryans tabanlı YEM"
    if "regresyon" in y: return "Anket + regresyon / PROCESS"
    return "Anket + betimsel / t-testi / ANOVA"
yon = collections.Counter(yontem_grubu(k["yontem"]) for k in KAYNAKLAR)
stats["yontem"] = dict(yon.most_common())
with open(os.path.join(HERE, "yontemler.csv"), "w", newline="", encoding="utf-8") as f:
    w = csv.writer(f); w.writerow(["yontem", "makale_sayisi"]); w.writerows(yon.most_common())
fig, ax = plt.subplots(figsize=(8, 3.4), dpi=200)
items = yon.most_common()[::-1]
cols = [TR_C if "YEM" in n else "#b9b8b2" for n, _ in items]
ax.barh([n for n, _ in items], [v for _, v in items], color=cols, height=0.6)
for i, (n, v) in enumerate(items): ax.text(v + 0.2, i, str(v), va="center", fontsize=9, color=INK)
ax.set_xticks([]); [ax.spines[s].set_visible(False) for s in ("top", "right", "bottom")]
yem = sum(v for n, v in items if "YEM" in n); anket = sum(v for n, v in items if n.startswith("Anket"))
ax.set_title(f"{anket} makale ankete dayanıyor; {yem} tanesi YEM kullanıyor", loc="left", fontsize=11, color=INK)
fig.tight_layout(); fig.savefig(os.path.join(HERE, "yontemler.png")); plt.close(fig)
stats["anket"] = anket; stats["yem"] = yem

# ---------- 5. Kavram eş-oluşumu ----------
kods = ["FA", "TU", "GU", "SN", "SA", "BPI"]
M = {(a, b): 0 for a in kods for b in kods}
for k in KAYNAKLAR:
    ks = k["kavram"].split(";")
    for a in ks: M[(a, a)] += 1
    for a, b in itertools.combinations(sorted(set(ks)), 2): M[(a, b)] += 1; M[(b, a)] += 1
with open(os.path.join(HERE, "es-olusum.csv"), "w", newline="", encoding="utf-8") as f:
    w = csv.writer(f); w.writerow([""] + [KOD[k] for k in kods])
    for a in kods: w.writerow([KOD[a]] + [M[(a, b)] for b in kods])
stats["es_olusum"] = {f"{KOD[a]}–{KOD[b]}": M[(a, b)] for a, b in itertools.combinations(kods, 2)}
stats["kavram_frekans"] = {KOD[a]: M[(a, a)] for a in kods}
fig, ax = plt.subplots(figsize=(6.4, 5.2), dpi=200)
import numpy as np
arr = np.array([[M[(a, b)] if a != b else np.nan for b in kods] for a in kods], dtype=float)
from matplotlib.colors import LinearSegmentedColormap
cmap = LinearSegmentedColormap.from_list("mavi", ["#eef4fc", "#2a78d6", "#0d3f80"]); cmap.set_bad("#f1f0ec")
im = ax.imshow(arr, cmap=cmap, vmin=0)
for i, a in enumerate(kods):
    for j, b in enumerate(kods):
        v = M[(a, b)] if a != b else M[(a, a)]
        txt = str(v) if a != b else f"({v})"
        ax.text(j, i, txt, ha="center", va="center", fontsize=10,
                color=("white" if a != b and v >= 7 else INK2 if a == b else INK))
ax.set_xticks(range(len(kods))); ax.set_xticklabels([KOD[k] for k in kods], rotation=30, ha="right")
ax.set_yticks(range(len(kods))); ax.set_yticklabels([KOD[k] for k in kods])
[ax.spines[s].set_visible(False) for s in ax.spines]
top = max(((a, b) for a, b in itertools.combinations(kods, 2)), key=lambda p: M[p])
ax.set_title(f"En sık birlikte çalışılan çift: {KOD[top[0]]}–{KOD[top[1]]} ({M[top]} makale)\nKöşegen: kavramın toplam makale sayısı",
             loc="left", fontsize=10.5, color=INK)
fig.tight_layout(); fig.savefig(os.path.join(HERE, "es-olusum.png")); plt.close(fig)

# ---------- 6. Atıf ağı şekli ----------
pos0 = nx.spring_layout(U, seed=11, k=0.55)
pos = {}
by_year = collections.defaultdict(list)
for n in G: by_year[K[n]["yil"]].append(n)
for y, ns in by_year.items():
    ns.sort(key=lambda n: pos0[n][1])
    for i, n in enumerate(ns):
        pos[n] = (y + (i % 2) * 0.32 - 0.16, (i + 0.5) / len(ns) * 10 if len(ns) > 1 else 5)
fig, ax = plt.subplots(figsize=(15, 8.6), dpi=200)
for s, t in G.edges():
    cross = K[s]["dil"] != K[t]["dil"]
    ax.annotate("", xy=pos[t], xytext=pos[s],
                arrowprops=dict(arrowstyle="-|>", color=("#7d7c77" if cross else "#c9c8c2"), lw=0.9 if cross else 0.6,
                                shrinkA=6, shrinkB=7, connectionstyle="arc3,rad=0.12", mutation_scale=7, alpha=0.9))
for n in G:
    d = G.in_degree(n)
    ax.scatter(*pos[n], s=60 + d * 55, color=TR_C if K[n]["dil"] == "TR" else EN_C, edgecolor=SURF, linewidth=1.5, zorder=3)
    ax.text(pos[n][0], pos[n][1] - 0.32 - d * 0.015, K[n]["kisa"].replace(" vd.", " vd.").split(",")[0],
            ha="center", va="top", fontsize=6.3, color=INK if d >= 3 else INK2, zorder=4,
            fontweight="bold" if d >= 5 else "normal")
ax.set_xticks(yillar); ax.set_xlim(2015.4, 2026.6); ax.set_ylim(-0.6, 10.4); ax.set_yticks([])
for y in yillar: ax.axvline(y, color=GRID, lw=0.6, zorder=0)
[ax.spines[s].set_visible(False) for s in ("top", "right", "left")]
ax.scatter([], [], s=80, color=TR_C, label="Türkçe makale"); ax.scatter([], [], s=80, color=EN_C, label="İngilizce makale")
ax.plot([], [], color="#7d7c77", lw=1, label="Diller arası atıf (köprü)"); ax.plot([], [], color="#c9c8c2", lw=1, label="Aynı dilde atıf")
ax.legend(frameon=False, loc="upper left", ncol=4, fontsize=9, bbox_to_anchor=(0, 1.06))
ax.set_title(f"Atıf ağı: {G.number_of_edges()} bağ, 50 makale. Oklar atıf yapandan atıf yapılana; daire büyüklüğü = ağ içinde aldığı atıf",
             loc="left", fontsize=11.5, color=INK, pad=26)
fig.tight_layout(); fig.savefig(os.path.join(HERE, "atif-agi.png")); fig.savefig(os.path.join(HERE, "atif-agi.svg")); plt.close(fig)

json.dump(stats, open(os.path.join(HERE, "ozet-istatistik.json"), "w", encoding="utf-8"), ensure_ascii=False, indent=1)
print(json.dumps({k: v for k, v in stats.items() if k not in ("zincir_ornek",)}, ensure_ascii=False, indent=1)[:6000])
