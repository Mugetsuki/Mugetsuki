#!/usr/bin/env bash
# Pikora sosyal kit görsellerini üretir (WSL + Windows'taki Chrome).
# Kullanım: bash pikora-sosyal-kit/kaynak/uret.sh   (depo kökünden)
# kit.html ve pikora-site, Windows geçici klasörüne kopyalanır; Chrome oradan açıp PNG çeker.
set -euo pipefail
KOK="$(cd "$(dirname "$0")/../.." && pwd)"
CIKTI="$KOK/pikora-sosyal-kit"
GECICI="/mnt/c/Users/YAKIN EVREN/AppData/Local/Temp/pikora-kit"
CHROME="/mnt/c/Program Files/Google/Chrome/Application/chrome.exe"

mkdir -p "$GECICI/pikora-sosyal-kit/kaynak" "$GECICI/png"
cp -r "$KOK/pikora-site" "$GECICI/"
cp "$KOK/pikora-sosyal-kit/kaynak/kit.html" "$GECICI/pikora-sosyal-kit/kaynak/"
W="$(wslpath -w "$GECICI")"
URL="file:///${W//\\//}/pikora-sosyal-kit/kaynak/kit.html"
URL="${URL// /%20}"

cek() { # cek <klasör/ad> <en> <boy> <sorgu>
  mkdir -p "$CIKTI/$(dirname "$1")"
  "$CHROME" --headless=new --disable-gpu --hide-scrollbars --allow-file-access-from-files \
    --user-data-dir="$W\\profil" --virtual-time-budget=3000 --window-size="$2,$3" \
    --screenshot="$W\\png\\gecici.png" "$URL?$4" >/dev/null 2>&1
  cp "$GECICI/png/gecici.png" "$CIKTI/$1.png"
  echo "$1.png"
}

cek "1-profil/profil-fotografi" 1080 1080 "t=avatar"
cek "1-profil/profil-fotografi-bordo" 1080 1080 "t=avatar-ters"
for k in urunler siparis kadro pay sorular; do cek "2-one-cikanlar/kapak-$k" 1080 1920 "t=kapak-$k"; done
cek "3-tanitim-gonderileri/1-tanisma" 1080 1350 "t=tanisma"
cek "3-tanitim-gonderileri/2-siparis" 1080 1350 "t=siparis"
cek "3-tanitim-gonderileri/3-pay" 1080 1350 "t=pay"
for u in $(grep -o 'id: "[a-z-]*"' "$KOK/pikora-site/urunler.js" | cut -d'"' -f2); do cek "4-urun-gonderileri/$u" 1080 1350 "t=urun&u=$u"; done
cek "5-hikaye/hikaye-zemin" 1080 1920 "t=hikaye-zemin"
