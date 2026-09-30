#!/usr/bin/env python3
"""Build plugins/skyra-core/data/places.json from GeoNames dumps.

Usage:
  curl -O https://download.geonames.org/export/dump/cities15000.zip   (unzip it)
  curl -O https://download.geonames.org/export/dump/admin1CodesASCII.txt
  curl -O https://download.geonames.org/export/dump/countryInfo.txt
  python3 tools/build-places.py <dir-with-those-files>

GeoNames data is CC BY 4.0 (https://www.geonames.org/). The site credits it
on the birth chart tools and in the footer.

Selection: every Turkish place with population >= 15k, every place >= 100k in
countries with large Turkish-speaking communities, every place >= 250k
worldwide and every national capital.
"""
import json
import os
import sys
import unicodedata

SRC = sys.argv[1] if len(sys.argv) > 1 else '.'
OUT = os.path.join(os.path.dirname(__file__), '..', 'plugins', 'skyra-core', 'data', 'places.json')

DIASPORA = {'DE', 'NL', 'AT', 'BE', 'FR', 'GB', 'CH', 'AZ', 'CY', 'GR', 'BG', 'SE', 'DK', 'NO', 'KZ', 'UZ',
            'TM', 'KG', 'GE', 'MK', 'XK', 'BA', 'RO', 'US', 'CA', 'AU'}

# Turkish exonyms for places Turkish speakers type differently.
EXONYMS = {
    'London': 'Londra', 'Vienna': 'Viyana', 'Brussels': 'Brüksel', 'Moscow': 'Moskova', 'Athens': 'Atina',
    'Rome': 'Roma', 'Nicosia': 'Lefkoşa', 'Baku': 'Bakü', 'Tbilisi': 'Tiflis', 'Cairo': 'Kahire',
    'Baghdad': 'Bağdat', 'Tehran': 'Tahran', 'Damascus': 'Şam', 'Beirut': 'Beyrut', 'Jerusalem': 'Kudüs',
    'Warsaw': 'Varşova', 'Prague': 'Prag', 'Budapest': 'Budapeşte', 'Bucharest': 'Bükreş', 'Sofia': 'Sofya',
    'Belgrade': 'Belgrad', 'Skopje': 'Üsküp', 'Sarajevo': 'Saraybosna', 'Copenhagen': 'Kopenhag',
    'Stockholm': 'Stokholm', 'Lisbon': 'Lizbon', 'Barcelona': 'Barselona', 'Munich': 'Münih',
    'Cologne': 'Köln', 'The Hague': 'Lahey', 'Geneva': 'Cenevre', 'Zurich': 'Zürih', 'Beijing': 'Pekin',
    'Shanghai': 'Şanghay', 'Seoul': 'Seul', 'New Delhi': 'Yeni Delhi', 'Riyadh': 'Riyad', 'Mecca': 'Mekke',
    'Medina': 'Medine', 'Tashkent': 'Taşkent', 'Almaty': 'Almatı', 'Ashgabat': 'Aşkabat',
    'Bishkek': 'Bişkek', 'Dushanbe': 'Duşanbe', 'Kyiv': 'Kiev', 'Chisinau': 'Kişinev', 'Yerevan': 'Erivan',
    'Thessaloniki': 'Selanik', 'Alexandria': 'İskenderiye', 'Aleppo': 'Halep', 'Mosul': 'Musul',
    'Kirkuk': 'Kerkük', 'Tabriz': 'Tebriz', 'Pristina': 'Priştine', 'Tirana': 'Tiran', 'Venice': 'Venedik',
    'Milan': 'Milano', 'Naples': 'Napoli', 'Florence': 'Floransa', 'Frankfurt am Main': 'Frankfurt',
    'Hanover': 'Hannover', 'Nuremberg': 'Nürnberg', 'Duesseldorf': 'Düsseldorf', 'Dusseldorf': 'Düsseldorf',
    'Saint Petersburg': 'St. Petersburg', 'Kabul': 'Kabil', 'Islamabad': 'İslamabad', 'Mumbai': 'Mumbai',
    'Tokyo': 'Tokyo', 'Astana': 'Astana', 'Minsk': 'Minsk', 'Plovdiv': 'Filibe', 'Kazan': 'Kazan',
    'Kuwait City': 'Kuveyt', 'Doha': 'Doha', 'Amman': 'Amman', 'Tripoli': 'Trablus', 'Tunis': 'Tunus',
    'Algiers': 'Cezayir', 'Rabat': 'Rabat', 'Mexico City': 'Meksiko', 'Washington': 'Washington',
}

# GeoNames spellings that drop Turkish letters.
TR_FIX = {
    'Istanbul': 'İstanbul', 'Umraniye': 'Ümraniye', 'Batikent': 'Batıkent', 'Sarigerme': 'Sarıgerme',
}

COUNTRY_TR = {
    'TR': 'Türkiye', 'DE': 'Almanya', 'NL': 'Hollanda', 'AT': 'Avusturya', 'BE': 'Belçika', 'FR': 'Fransa',
    'GB': 'Birleşik Krallık', 'CH': 'İsviçre', 'AZ': 'Azerbaycan', 'CY': 'Kıbrıs', 'GR': 'Yunanistan',
    'BG': 'Bulgaristan', 'SE': 'İsveç', 'DK': 'Danimarka', 'NO': 'Norveç', 'KZ': 'Kazakistan',
    'UZ': 'Özbekistan', 'TM': 'Türkmenistan', 'KG': 'Kırgızistan', 'GE': 'Gürcistan', 'MK': 'Kuzey Makedonya',
    'XK': 'Kosova', 'BA': 'Bosna-Hersek', 'RO': 'Romanya', 'US': 'ABD', 'CA': 'Kanada', 'AU': 'Avustralya',
    'RU': 'Rusya', 'UA': 'Ukrayna', 'IT': 'İtalya', 'ES': 'İspanya', 'PT': 'Portekiz', 'PL': 'Polonya',
    'CZ': 'Çekya', 'HU': 'Macaristan', 'RS': 'Sırbistan', 'AL': 'Arnavutluk', 'IE': 'İrlanda',
    'FI': 'Finlandiya', 'IR': 'İran', 'IQ': 'Irak', 'SY': 'Suriye', 'LB': 'Lübnan', 'IL': 'İsrail',
    'JO': 'Ürdün', 'SA': 'Suudi Arabistan', 'AE': 'Birleşik Arap Emirlikleri', 'QA': 'Katar',
    'KW': 'Kuveyt', 'EG': 'Mısır', 'LY': 'Libya', 'TN': 'Tunus', 'DZ': 'Cezayir', 'MA': 'Fas',
    'CN': 'Çin', 'JP': 'Japonya', 'KR': 'Güney Kore', 'IN': 'Hindistan', 'PK': 'Pakistan',
    'AF': 'Afganistan', 'BR': 'Brezilya', 'AR': 'Arjantin', 'MX': 'Meksika', 'ID': 'Endonezya',
    'MY': 'Malezya', 'TJ': 'Tacikistan', 'AM': 'Ermenistan', 'MD': 'Moldova', 'BY': 'Belarus',
    'SI': 'Slovenya', 'HR': 'Hırvatistan', 'SK': 'Slovakya', 'ME': 'Karadağ', 'NZ': 'Yeni Zelanda',
    'ZA': 'Güney Afrika', 'NG': 'Nijerya', 'ET': 'Etiyopya', 'KE': 'Kenya', 'TH': 'Tayland',
    'VN': 'Vietnam', 'PH': 'Filipinler', 'SG': 'Singapur', 'BD': 'Bangladeş', 'CO': 'Kolombiya',
    'CL': 'Şili', 'PE': 'Peru', 'VE': 'Venezuela', 'CU': 'Küba', 'LU': 'Lüksemburg', 'IS': 'İzlanda',
    'LT': 'Litvanya', 'LV': 'Letonya', 'EE': 'Estonya', 'MN': 'Moğolistan', 'SD': 'Sudan', 'YE': 'Yemen',
    'OM': 'Umman', 'BH': 'Bahreyn',
}


def fold(text):
    """Lower-case, Turkish-aware, accent-free search key."""
    text = text.replace('İ', 'i').replace('I', 'ı').lower()
    text = text.replace('ı', 'i')
    text = unicodedata.normalize('NFKD', text)
    return ''.join(c for c in text if not unicodedata.combining(c))


def main():
    countries = {}
    with open(os.path.join(SRC, 'countryInfo.txt'), encoding='utf-8') as fh:
        for line in fh:
            if line.startswith('#'):
                continue
            cols = line.rstrip('\n').split('\t')
            countries[cols[0]] = cols[4]

    admin1 = {}
    with open(os.path.join(SRC, 'admin1CodesASCII.txt'), encoding='utf-8') as fh:
        for line in fh:
            code, name, _ascii, _gid = line.rstrip('\n').split('\t')
            admin1[code] = name

    rows = []
    with open(os.path.join(SRC, 'cities15000.txt'), encoding='utf-8') as fh:
        for line in fh:
            c = line.rstrip('\n').split('\t')
            gid, name, ascii_name, cc, a1, pop, tz, fcode = c[0], c[1], c[2], c[8], c[10], int(c[14] or 0), c[17], c[7]
            keep = (cc == 'TR') or (cc in DIASPORA and pop >= 100000) or pop >= 250000 or fcode == 'PPLC'
            if not keep or not tz:
                continue
            if cc == 'TR':
                name = TR_FIX.get(name, name)
            elif cc not in ('US', 'CA', 'AU'):  # "London, Ontario" stays London.
                name = EXONYMS.get(name, name)
            if cc == 'TR':
                province = admin1.get(f'TR.{a1}', '').removesuffix(' Province')
                province = TR_FIX.get(province, province)
                region = '' if fold(province) == fold(name) else province
            else:
                region = COUNTRY_TR.get(cc, countries.get(cc, cc))
            keys = {fold(name), fold(ascii_name), fold(c[1])}
            rows.append({
                'id': int(gid),
                'n': name,
                'r': region,
                'c': cc,
                'la': round(float(c[4]), 4),
                'lo': round(float(c[5]), 4),
                'z': tz,
                'p': pop,
                'k': ' '.join(sorted(k for k in keys if k)),
            })

    rows.sort(key=lambda r: (r['c'] != 'TR', -r['p']))
    zones = sorted({r['z'] for r in rows})
    zi = {z: i for i, z in enumerate(zones)}
    packed = [[r['id'], r['n'], r['r'], r['c'], r['la'], r['lo'], zi[r['z']], r['p'], r['k']] for r in rows]
    data = {
        'source': 'GeoNames cities15000 (CC BY 4.0) — https://www.geonames.org/',
        'fields': ['id', 'name', 'region', 'country', 'lat', 'lon', 'tz', 'population', 'search'],
        'zones': zones,
        'places': packed,
    }
    with open(OUT, 'w', encoding='utf-8') as fh:
        json.dump(data, fh, ensure_ascii=False, separators=(',', ':'))
    tr = sum(1 for r in rows if r['c'] == 'TR')
    print(f'{len(rows)} places ({tr} in Türkiye), {len(zones)} zones -> {os.path.relpath(OUT)} '
          f'({os.path.getsize(OUT) // 1024} KB)')


if __name__ == '__main__':
    main()
