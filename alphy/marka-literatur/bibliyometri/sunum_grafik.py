# -*- coding: utf-8 -*-
"""Sunum slaytları için başlıksız, büyük yazılı grafikler üretir (build.py'den sonra çalıştırın)."""
import os, json, csv, collections
import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt

HERE = os.path.dirname(os.path.abspath(__file__))
OUT = os.path.join(HERE, "sunum")
os.makedirs(OUT, exist_ok=True)
st = json.load(open(os.path.join(HERE, "ozet-istatistik.json"), encoding="utf-8"))
TR_C, EN_C, GREY, INK, INK2, BG = "#2A78D6", "#D9622B", "#B9B6AC", "#14213D", "#4A5568", "#FBFAF7"
plt.rcParams.update({"font.family": "DejaVu Sans", "font.size": 20, "text.color": INK, "axes.labelcolor": INK2,
                     "xtick.color": INK2, "ytick.color": INK, "figure.facecolor": BG, "axes.facecolor": BG,
                     "savefig.facecolor": BG})

def clean(ax, keep=("bottom",)):
    for s in ("top", "right", "left", "bottom"):
        ax.spines[s].set_visible(s in keep)
    ax.spines["bottom"].set_color("#9A978E") if "bottom" in keep else None

# 1. Yıllar (yarım genişlik)
yil = st["yillar"]; ys = sorted(yil); tr = [yil[y][0] for y in ys]; en = [yil[y][1] for y in ys]
fig, ax = plt.subplots(figsize=(8, 5.6), dpi=150)
x = range(len(ys))
ax.bar(x, tr, 0.66, color=TR_C, label="Türkçe", edgecolor=BG, linewidth=2)
ax.bar(x, en, 0.66, bottom=tr, color=EN_C, label="İngilizce", edgecolor=BG, linewidth=2)
for i, (a, b) in enumerate(zip(tr, en)):
    if a + b: ax.text(i, a + b + 0.15, str(a + b), ha="center", va="bottom", fontsize=18, color=INK)
ax.set_xticks(list(x)); ax.set_xticklabels([y[2:] and "’" + y[2:] for y in ys], fontsize=16)
ax.set_yticks([]); clean(ax); ax.legend(frameon=False, loc="upper right", fontsize=17)
ax.set_ylim(0, 9.5)
fig.tight_layout(); fig.savefig(os.path.join(OUT, "yillar.png")); plt.close(fig)

# 2. Yöntemler (yarım genişlik)
yon = list(st["yontem"].items())[::-1]
kisa = {"Anket + kovaryans tabanlı YEM": "Anket + YEM", "Anket + regresyon / PROCESS": "Anket + regresyon",
        "Anket + PLS-YEM": "Anket + PLS-YEM", "Anket + betimsel / t-testi / ANOVA": "Anket + betimsel/ANOVA",
        "Kavramsal / derleme": "Kavramsal", "Deney": "Deney", "Nitel (görüşme)": "Nitel"}
fig, ax = plt.subplots(figsize=(8, 5.6), dpi=150)
ax.barh([kisa[n] for n, _ in yon], [v for _, v in yon], height=0.62,
        color=[TR_C if "YEM" in n else GREY for n, _ in yon])
for i, (n, v) in enumerate(yon): ax.text(v + 0.3, i, str(v), va="center", fontsize=18)
ax.set_xticks([]); clean(ax, keep=()); ax.tick_params(axis="y", length=0, labelsize=17)
fig.tight_layout(); fig.savefig(os.path.join(OUT, "yontemler.png")); plt.close(fig)

# 3. Kavram çiftleri (tam genişlik)
pairs = sorted(st["es_olusum"].items(), key=lambda kv: kv[1])
fig, ax = plt.subplots(figsize=(16, 7.2), dpi=120)
labels = [k.replace("–", " – ") for k, _ in pairs]
ax.barh(labels, [v for _, v in pairs], height=0.66,
        color=[TR_C if v == max(dict(pairs).values()) else GREY for _, v in pairs])
for i, (k, v) in enumerate(pairs): ax.text(v + 0.15, i, str(v), va="center", fontsize=17)
ax.set_xticks([]); clean(ax, keep=()); ax.tick_params(axis="y", length=0, labelsize=16)
fig.tight_layout(); fig.savefig(os.path.join(OUT, "kavram-ciftleri.png")); plt.close(fig)
print("ok", os.listdir(OUT))
