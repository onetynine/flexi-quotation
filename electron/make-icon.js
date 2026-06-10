const { Resvg } = require('@resvg/resvg-js');
const fs = require('fs');
const path = require('path');

const svgPath = path.join(__dirname, '..', 'laravel', 'public', 'images', 'logo.svg');
const outPath = path.join(__dirname, 'assets', 'icon.png');

const inner = fs.readFileSync(svgPath, 'utf8');

// Original SVG is 62x84 (portrait). Centre it in a 512x512 square.
// Scale to fit height 420 → width = 420 * (62/84) = 310. Offset x = (512-310)/2 = 101, y = 46
const scale = 420 / 84;
const sw = Math.round(62 * scale);
const sh = 420;
const ox = Math.round((512 - sw) / 2);
const oy = Math.round((512 - sh) / 2);

// Strip the outer <svg> tag from inner SVG, wrap in square canvas
const innerContent = inner.replace(/<svg[^>]*>/, '').replace(/<\/svg>/, '');

const wrapped = `<svg width="512" height="512" xmlns="http://www.w3.org/2000/svg">
  <rect width="512" height="512" fill="#F5A623"/>
  <g transform="translate(${ox},${oy}) scale(${scale.toFixed(4)})">
    ${innerContent}
  </g>
</svg>`;

const resvg = new Resvg(wrapped, { fitTo: { mode: 'width', value: 512 } });
const rendered = resvg.render();
const png = rendered.asPng();

fs.writeFileSync(outPath, png);
console.log('Icon written:', outPath, `(${png.length} bytes, ${rendered.width}x${rendered.height})`);
