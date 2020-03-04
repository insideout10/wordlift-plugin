/**
 * This file is used to provide helpers for styling.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 *
 */

/**
 * classExtractor helps to return class name by applying boolean logic.
 * @param classConfig {Object} should be in format { "class-name": Boolean }
 * @returns {string} combined class name.
 */
export const classExtractor = classConfig => {
  let className = "";
  for (let key of Object.keys(classConfig)) {
    if (classConfig[key]) {
      className += ` ${key}`;
    }
  }
  return className.trim();
};
