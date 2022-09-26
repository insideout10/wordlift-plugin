/**
 * Load the Main Ingredient Select.
 *
 * @since 3.38.3
 */
import ReactDOM from "react-dom";
import React from "react";
import Select from "react-select";

// ### Render the sameAs metabox field autocomplete select.
window.addEventListener("load", () => {
    // Set a reference to the WordLift's settings stored in the window instance.
    const settings = window["_wlRecipeIngredientSettings"] || {};

    let autocompleteTimeout = null;

    const DEFAULT_OPTIONS = [
        { label: settings.l10n["(don't change)"], value: "DONT_CHANGE" },
        { label: settings.l10n["(unset)"], value: "UNSET" }
    ];

    const autocomplete = (query, callback) => {
        // Minimum 3 characters.
        if (3 > query.length) {
            callback(null, {
                options: DEFAULT_OPTIONS
            });
            return;
        }

        // Clear any existing query.
        if (null !== autocompleteTimeout) clearTimeout(autocompleteTimeout);

        // Send our query.
        autocompleteTimeout = setTimeout(
            () =>
                wp.ajax
                    .post("wl_ingredient_autocomplete", {
                        query,
                        _wpnonce: settings.acNonce
                    })
                    .done(json => callback(null, { options: DEFAULT_OPTIONS.concat(json) }))
                    .fail(() => {
                        console.log("error");
                        callback(null, { options: [] });
                    }),
            1000
        );
    };

    class MainIngredientSelect extends React.Component {

        constructor(props) {
            super(props);
            this.onChange = this.onChange.bind(this);
            this.state = { value: DEFAULT_OPTIONS[0] };
        }

        onChange(value) {
            this.setState({ value });
        }
        render() {
            return (
                <Select.Async
                    multi={false}
                    name="wl_recipe_main_ingredient[]"
                    value={this.state.value}
                    onChange={this.onChange}
                    loadOptions={autocomplete}
                ></Select.Async>
            );
        }
    }

    document.querySelectorAll(".wl-select-main-ingredient").forEach(el => {
        ReactDOM.render(<MainIngredientSelect />, el);
    });


    document.getElementById('wl-recipe-ingredient-form__submit__btn')
        .addEventListener('click', () => {
            // Get all recipe ids + jsonld.

        })

});




// jQuery Code.
jQuery(function ($) {
    // Update Ingredient.
    const ingredientFormSubmitBtn = $('.wl-recipe-ingredient-form__submit__btn');
    ingredientFormSubmitBtn.on('click', function (e) {
        e.preventDefault(e);

        const ingredientsData = $('.wl-table--main-ingredient__data');
        let recipeData = [];
        ingredientsData.each((index, element) => {
            const recipeID = $(element).find('#recipe-id').val();
            const ingredient = $(element).find("input[name='wl_recipe_main_ingredient[]']").val();
            if (!recipeID || !ingredient) {
                return;
            }
            recipeData.push({
                recipe_id: recipeID,
                ingredient: ingredient
            });
        });

        const data = {
            _wpnonce: _wlRecipeIngredientSettings.nonce,
            data: JSON.stringify( recipeData )
        };
        const ingredientFormMessage = $('.wl-recipe-ingredient-form__submit__message');
        // Save the ingredient.
        wp.ajax
            .post(
                "wl_update_ingredient_post_meta",
                data
            ).done(function (response) {
                ingredientFormMessage.html( response.message );
            })
            .fail(function (error) {
                ingredientFormMessage.html( error.message );
            });
    });
});