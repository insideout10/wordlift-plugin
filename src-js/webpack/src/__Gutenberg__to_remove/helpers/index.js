export const humanize = str => {
  return str
    .replace(/^[\s_]+|[\s_]+$/g, "")
    .replace(/[_\s]+/g, " ")
    .replace(/^[a-z]/, function(m) {
      return m.toUpperCase();
    });
};
