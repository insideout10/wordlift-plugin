const lut = Array(256)
  .fill()
  .map((_, i) => (i < 16 ? "0" : "") + i.toString(16));
const formatUuid = ({ d0, d1, d2, d3 }) =>
  lut[d0 & 0xff] +
  lut[(d0 >> 8) & 0xff] +
  lut[(d0 >> 16) & 0xff] +
  lut[(d0 >> 24) & 0xff] +
  "-" +
  lut[d1 & 0xff] +
  lut[(d1 >> 8) & 0xff] +
  "-" +
  lut[((d1 >> 16) & 0x0f) | 0x40] +
  lut[(d1 >> 24) & 0xff] +
  "-" +
  lut[(d2 & 0x3f) | 0x80] +
  lut[(d2 >> 8) & 0xff] +
  "-" +
  lut[(d2 >> 16) & 0xff] +
  lut[(d2 >> 24) & 0xff] +
  lut[d3 & 0xff] +
  lut[(d3 >> 8) & 0xff] +
  lut[(d3 >> 16) & 0xff] +
  lut[(d3 >> 24) & 0xff];

const getRandomValuesFunc =
  window.crypto && window.crypto.getRandomValues
    ? () => {
        const dvals = new Uint32Array(4);
        window.crypto.getRandomValues(dvals);
        return {
          d0: dvals[0],
          d1: dvals[1],
          d2: dvals[2],
          d3: dvals[3]
        };
      }
    : () => ({
        d0: (Math.random() * 0x100000000) >>> 0,
        d1: (Math.random() * 0x100000000) >>> 0,
        d2: (Math.random() * 0x100000000) >>> 0,
        d3: (Math.random() * 0x100000000) >>> 0
      });

const uuid = () => formatUuid(getRandomValuesFunc());

export default uuid;
