/**
 * CategoryComponent : Displays the list of categories and  user can select
 * select the category
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react";
import PropTypes from "prop-types";

export const TRASH_CATEGORY = "trash";
export const ACTIVE_CATEGORY = "active";

const SingleCategoryItem = ({ choosenCategory, category, source, categorySelectHandler, categoryKeyName }) => {
  return (
    <span className="wl-mappings-link wl-category-title">
      &nbsp;
      {category === choosenCategory ? (
        <b>
          <a className="wl-mappings-link-active">
            {category}&nbsp;(
            {
              // Count the category in the source
              source.filter(el => el[categoryKeyName] === category).length
            }
            )<span className="wl-color-grey">&nbsp;|</span>
            &nbsp;
          </a>
        </b>
      ) : (
        <a
          onClick={() => {
            categorySelectHandler(category);
          }}
        >
          {category}&nbsp;(
          {
            // Count the category in the source
            source.filter(el => el[categoryKeyName] === category).length
          }
          )<span className="wl-color-grey">&nbsp;|</span>
          &nbsp;
        </a>
      )}
      &nbsp;
    </span>
  );
};

class CategoryComponent extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    return (
      <div>
        {this.props.categories.map((category, index) => {
          return <SingleCategoryItem key={index} {...this.props} category={category} />;
        })}
      </div>
    );
  }
}

CategoryComponent.propTypes = {
  // Category key : category key name of  object in source object list.
  categoryKeyName: PropTypes.string.isRequired,
  // List of categories needed to be shown for user
  categories: PropTypes.array.isRequired,
  // Source : Array of objects
  source: PropTypes.array.isRequired,
  // Category select handler
  categorySelectHandler: PropTypes.func.isRequired
};

export default CategoryComponent;
