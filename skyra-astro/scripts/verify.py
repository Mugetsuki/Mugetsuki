"""Dependency-free checks for the handoff's shared tokens and preserved asset."""
from pathlib import Path
import hashlib
import json
import re

root = Path(__file__).resolve().parents[1]
tokens = json.loads((root / 'tokens.json').read_text())
css = (root / 'tokens.css').read_text()

def luminance(value):
    rgb = [int(value[i:i+2], 16) / 255 for i in (1, 3, 5)]
    linear = [n/12.92 if n <= .04045 else ((n+.055)/1.055)**2.4 for n in rgb]
    return sum(n*w for n, w in zip(linear, (.2126, .7152, .0722)))

def contrast(a, b):
    hi, lo = sorted((luminance(a), luminance(b)), reverse=True)
    return (hi+.05)/(lo+.05)

pairs = [('text','background',4.5), ('muted','background',4.5),
         ('text','surface',4.5), ('muted','surface',4.5),
         ('onprimary','primary',4.5), ('onprimary','hover',4.5),
         ('primary','surface',4.5), ('accent','surface',4.5),
         ('muted','secondary',4.5), ('control','surface',3),
         ('focus','background',3), ('error','surface',4.5)]
for mode, colors in tokens['color'].items():
    block = css.split('[data-theme="dark"]')[0 if mode == 'light' else 1]
    for key, value in colors.items():
        assert f'--{key}: {value};' in block, f'{mode} CSS token mismatch: {key}'
    for fg, bg, target in pairs:
        ratio = contrast(colors[fg], colors[bg])
        print(f'{mode:5} {fg:10} / {bg:10} {ratio:.2f}:1')
        assert ratio >= target, f'Insufficient {mode} contrast: {fg}/{bg}'
    theme = json.loads((root / 'wordpress' / ('theme.json' if mode == 'light' else 'night.json')).read_text())
    palette = {entry['slug']:entry['color'] for entry in theme['settings']['color']['palette']}
    assert palette == colors, f'{mode} WordPress palette differs from tokens'
for file in ['index.html','app.js','styles.css','tokens.css','assets/skyra-logo-original.png']:
    assert (root/file).is_file(), f'Missing {file}'
logo_hash = hashlib.sha256((root/'assets/skyra-logo-original.png').read_bytes()).hexdigest()
assert logo_hash in (root/'assets/SOURCES.md').read_text(), 'Original logo hash changed'
html = (root/'index.html').read_text()
for target in re.findall(r'(?:src|href)="([^"?]+)"', html):
    if target.startswith(('http', '#')): continue
    assert (root/target).is_file(), f'Missing local asset: {target}'
print('PASS: contrast pairs, shared tokens, WordPress palettes, local files and original logo.')
