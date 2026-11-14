/* Pikora · çizimler (SVG). Site ve sosyal medya kiti ortak kullanır. */
window.PIKORA_CIZIM = (function () {
  "use strict";
  var CIZGI = 'stroke="rgba(42,27,30,.2)" stroke-width="1.5"';
  function yaprak(x, y, aci, renk, boy) {
    return '<path transform="translate(' + x + ' ' + y + ') rotate(' + aci + ') scale(' + boy + ')" d="M0 0C14-17 42-15 54 0C42 15 14 17 0 0Z" fill="' + renk + '" ' + CIZGI + '/>' +
      '<path transform="translate(' + x + ' ' + y + ') rotate(' + aci + ') scale(' + boy + ')" d="M6 0H44" fill="none" stroke="rgba(255,255,255,.45)" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 5"/>';
  }
  function cicek(x, y, r, n, renk, gobek) {
    var s = "", i, a;
    for (i = 0; i < n; i++) {
      a = (i / n) * Math.PI * 2 - Math.PI / 2;
      var px = x + Math.cos(a) * r * 0.6, py = y + Math.sin(a) * r * 0.6, pr = r * (n > 6 ? 0.36 : 0.44);
      s += '<circle cx="' + px.toFixed(1) + '" cy="' + py.toFixed(1) + '" r="' + pr.toFixed(1) + '" fill="' + renk + '" ' + CIZGI + '/>' +
        '<circle cx="' + px.toFixed(1) + '" cy="' + py.toFixed(1) + '" r="' + (pr * 0.58).toFixed(1) + '" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="' + (r * 0.05).toFixed(1) + '" stroke-dasharray="' + (r * 0.07).toFixed(1) + ' ' + (r * 0.11).toFixed(1) + '" stroke-linecap="round"/>';
    }
    return s + '<circle cx="' + x + '" cy="' + y + '" r="' + (r * 0.36).toFixed(1) + '" fill="' + gobek + '" ' + CIZGI + '/>' +
      '<circle cx="' + x + '" cy="' + y + '" r="' + (r * 0.19).toFixed(1) + '" fill="none" stroke="rgba(42,27,30,.25)" stroke-width="' + (r * 0.05).toFixed(1) + '" stroke-dasharray="' + (r * 0.06).toFixed(1) + ' ' + (r * 0.09).toFixed(1) + '" stroke-linecap="round"/>';
  }
  function papyon(x, y, r, renk, dugum) {
    var k = r / 64, t = 'transform="translate(' + x + ' ' + y + ') scale(' + k.toFixed(3) + ')"';
    return '<g ' + t + '><path d="M0 0C-18-40-70-44-74-4C-76 40-22 40 0 0Z" fill="' + renk + '" ' + CIZGI + '/><path d="M0 0C18-40 70-44 74-4C76 40 22 40 0 0Z" fill="' + renk + '" ' + CIZGI + '/>' +
      '<path d="M-58-6C-44-22-26-18-14-6M58-6C44-22 26-18 14-6" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="3" stroke-linecap="round" stroke-dasharray="4 6"/>' +
      '<rect x="-13" y="-18" width="26" height="36" rx="11" fill="' + dugum + '" ' + CIZGI + '/></g>';
  }
  function portakal(x, y, r, renk) {
    return '<circle cx="' + x + '" cy="' + y + '" r="' + (r * 0.8).toFixed(1) + '" fill="' + renk + '" ' + CIZGI + '/>' +
      '<circle cx="' + x + '" cy="' + y + '" r="' + (r * 0.54).toFixed(1) + '" fill="none" stroke="rgba(255,255,255,.42)" stroke-width="' + (r * 0.05).toFixed(1) + '" stroke-dasharray="' + (r * 0.08).toFixed(1) + ' ' + (r * 0.12).toFixed(1) + '" stroke-linecap="round"/>' +
      '<circle cx="' + x + '" cy="' + y + '" r="' + (r * 0.26).toFixed(1) + '" fill="none" stroke="rgba(255,255,255,.42)" stroke-width="' + (r * 0.05).toFixed(1) + '" stroke-dasharray="' + (r * 0.08).toFixed(1) + ' ' + (r * 0.12).toFixed(1) + '" stroke-linecap="round"/>';
  }
  function motif(c, x, y, r) {
    var s = "", yp = c.yapraklar || [];
    if (c.motif === "portakal") {
      if (yp[0]) s += yaprak(x - r * 0.05, y - r * 0.72, -150, yp[0], r / 64);
      if (yp[1]) s += yaprak(x + r * 0.05, y - r * 0.72, -30, yp[1], r / 64);
      return portakal(x, y, r, c.renk) + s;
    }
    if (yp[0]) s += yaprak(x - r * 0.55, y + r * 0.2, 160, yp[0], r / 58);
    if (yp[1]) s += yaprak(x + r * 0.55, y + r * 0.2, 20, yp[1], r / 58);
    return s + (c.motif === "papyon" ? papyon(x, y, r, c.renk, c.gobek) : cicek(x, y, r, c.yaprak, c.renk, c.gobek));
  }
  // Kolyenin büyük çizimi (gerçek fotoğraf gelene kadar)
  function kolyeSVG(c) {
    var ip = "M40 28C56 300 344 300 360 28";
    return '<svg viewBox="0 0 400 400" aria-hidden="true">' +
      '<ellipse cx="200" cy="205" rx="150" ry="140" fill="rgba(255,250,242,.55)"/>' +
      '<path d="' + ip + '" fill="none" stroke="rgba(42,27,30,.22)" stroke-width="21" stroke-linecap="round"/>' +
      '<path d="' + ip + '" fill="none" stroke="' + c.ip + '" stroke-width="17" stroke-linecap="round"/>' +
      '<path d="' + ip + '" fill="none" stroke="rgba(42,27,30,.16)" stroke-width="13" stroke-dasharray="2 10" stroke-linecap="round"/>' +
      '<path d="' + ip + '" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="4" stroke-dasharray="1 11" stroke-dashoffset="6" stroke-linecap="round"/>' +
      '<ellipse cx="200" cy="318" rx="62" ry="9" fill="rgba(94,20,36,.10)"/>' +
      motif(c, 200, 232, 70) + '</svg>';
  }
  // Küçük boncuk simgesi (ipte, panelde)
  function boncukSVG(u) {
    var c = u.cizim;
    if (u.tur === "kolye") return '<svg viewBox="0 0 64 64" aria-hidden="true">' + motif(c, 32, 32, 26) + '</svg>';
    return '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M7 13H57L32 56Z" fill="' + c.zemin + '" stroke="' + c.kenar + '" stroke-width="5" stroke-linejoin="round"/>' +
      '<path d="M14 19H50" fill="none" stroke="rgba(127,127,127,.4)" stroke-width="2" stroke-dasharray="2 4" stroke-linecap="round"/><circle cx="32" cy="31" r="8" fill="' + c.motif + '"/></svg>';
  }
  return { motif: motif, kolyeSVG: kolyeSVG, boncukSVG: boncukSVG };
})();
