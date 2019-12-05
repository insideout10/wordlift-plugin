/**
 * Sort the provided data structure by name case insensitive.
 *
 * @since 3.20.0
 * @param {{name}[]} data An array of objects with the name field.
 * @returns {Object[]} The sorted array.
 */
const sortByNameCaseInsensitive = data =>
  data.sort((a, b) => {
    const nameA = a.name.toUpperCase();
    const nameB = b.name.toUpperCase();

    if (nameA < nameB) return -1;

    if (nameA > nameB) return 1;

    return 0;
  });

export default sortByNameCaseInsensitive;
