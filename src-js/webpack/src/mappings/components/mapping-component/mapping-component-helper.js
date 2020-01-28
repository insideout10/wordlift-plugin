/**
 * MappingComponentHelper : it provides helper functions for the mapping component.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

export default class MappingComponentHelper {
  /**
   * Add some keys to mapping items before setting it as
   * state, it is used by ui.
   * @param {Array} mappingItems Mapping items list
   *
   */
  static applyUiItemFilters(mappingItems) {
    return mappingItems.map(item => ({
      mappingId: item.mapping_id,
      mappingTitle: item.mapping_title,
      mappingStatus: item.mapping_status,
      // initially no item is selected.
      isSelected: false
    }));
  }
  /**
   * Convert ui data to api format before posting to api
   * @param {Array} mappingItems Mapping items list
   *
   */
  static applyApiFilters(mappingItems) {
    return mappingItems.map(item => ({
      mapping_id: item.mappingId,
      mapping_title: item.mappingTitle,
      mapping_status: item.mappingStatus
    }));
  }
}
