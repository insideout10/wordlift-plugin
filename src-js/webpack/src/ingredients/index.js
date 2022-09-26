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
        {label: settings.l10n["(don't change)"], value: "DONT_CHANGE"},
        {label: settings.l10n["(unset)"], value: "UNSET"}
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
            () => {


                const formData = new FormData();
                formData.set("_wpnonce", settings.acNonce)
                formData.set("query", query)

                fetch(settings["ajaxurl"] + "?action=wl_ingredient_autocomplete", {
                    method: "POST",
                    body: formData
                }).then(response => response.json())
                    .then((result) => {
                        callback(null, {options: DEFAULT_OPTIONS.concat(result.data)})
                    }).catch(() => {
                    callback(null, {options: []})
                })


            },

            1000
        );
    };

    class MainIngredientSelect extends React.Component {

        constructor(props) {
            super(props);
            this.onChange = this.onChange.bind(this);
            this.state = {value: DEFAULT_OPTIONS[0]};
        }

        onChange(value) {
            this.setState({value});
        }

        render() {
            const {recipeId} = this.props
            return (
                <Select.Async
                    multi={false}
                    name={"wl_recipe_main_ingredient[" + recipeId + "]"}
                    value={this.state.value}
                    onChange={this.onChange}
                    loadOptions={autocomplete}
                ></Select.Async>
            );
        }
    }

    document.querySelectorAll(".wl-select-main-ingredient").forEach(el => {
        ReactDOM.render(<MainIngredientSelect recipeId={el.dataset.recipeId}/>, el);
    });


    document.getElementById('wl-recipe-ingredient-form__submit__btn')
        .addEventListener('click', (event) => {

            event.preventDefault()

            const notification = document.getElementById('wl-recipe-ingredient-form__submit__message');

            // Get all recipe ids + jsonld.
            const recipes = []
            document.querySelectorAll("input[name*='wl_recipe_main_ingredient']")
                .forEach((element) => {
                    console.log(JSON.parse(element.value))
                    console.log(JSON.stringify( JSON.parse(element.value) ))
                    recipes.push({
                        recipe_id: element.getAttribute("name")
                            .replace("wl_recipe_main_ingredient[", "")
                            .replace("]", ""),
                        ingredient: JSON.stringify( JSON.parse(element.value) )
                    })
                })

            const formData = new FormData();
            formData.set("_wpnonce", settings.nonce)
            formData.set("data", JSON.stringify(recipes))

            fetch(settings["ajaxurl"] + "?action=wl_update_ingredient_post_meta", {
                method: "POST",
                body: formData
            }).then(response => response.json())
                .then((result) => {
                    notification.innerText = result.data.message
                }).catch(() => {
            })
        })

});