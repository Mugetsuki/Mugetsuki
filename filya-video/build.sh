#!/usr/bin/env bash
# Filya "Gerçeklik nedir?" — hikaye tahtasından 25 sn dikey (1080x1920) taslak video.
# Kullanım: ./build.sh [cikti.mp4]   (Windows'taki ffmpeg.exe'yi WSL'den çağırır)
set -euo pipefail
cd "$(dirname "$0")"
FF="${FFMPEG:-/mnt/c/Users/YAKIN EVREN/AppData/Local/Microsoft/WinGet/Packages/Gyan.FFmpeg_Microsoft.Winget.Source_8wekyb3d8bbwe/ffmpeg-9.0.2-full_build/bin/ffmpeg.exe}"
OUT="${1:-filya-gerceklik-taslak.mp4}"
IN="$(wslpath -w hikaye-tahtasi.png)"
FPS=30
# sahne: x y (9:16 kırpma, 328x583) süre(sn) yakınlaşma_başı yakınlaşma_sonu
SC=( "1 250 4.5 1.00 1.07"
     "337 200 5.5 1.08 1.00"
     "673 280 6.5 1.00 1.10"
     "1010 60 6.5 1.00 1.07"
     "1345 150 4.0 1.03 1.00" )
FC=""; i=0
for s in "${SC[@]}"; do
  read -r x y d z0 z1 <<<"$s"
  n=$(python3 -c "print(round($d*$FPS))")
  FC+="[0:v]crop=328:583:$x:$y,scale=2160:3840:flags=lanczos,"
  FC+="zoompan=z='$z0+($z1-$z0)*on/$n':x='iw/2-iw/zoom/2':y='ih/2-ih/zoom/2':d=$n:s=1080x1920:fps=$FPS,"
  FC+="unsharp=5:5:0.6,vignette=PI/5,noise=alls=4:allf=t,format=yuv420p,setsar=1[s$i];"
  i=$((i+1))
done
FC+="[s0][s1]xfade=transition=fade:duration=0.5:offset=4.0[a];"
FC+="[a][s2]xfade=transition=fade:duration=0.5:offset=9.0[b];"
FC+="[b][s3]xfade=transition=fade:duration=0.5:offset=15.0[c];"
FC+="[c][s4]xfade=transition=fade:duration=0.5:offset=21.0,fade=t=in:d=0.6,fade=t=out:st=24.2:d=0.8[v]"
"$FF" -y -loglevel error -loop 1 -framerate $FPS -t 27 -i "$IN" -filter_complex "$FC" -map "[v]" \
  -c:v libx264 -preset slow -crf 18 -pix_fmt yuv420p -movflags +faststart -t 25 "$(wslpath -w "$PWD")\\$OUT"
echo "Hazır: $PWD/$OUT"
