// jQuery Code.
jQuery(document).ready(function ($) {
    // Update Ingredient.
    const ingredientForm = $(".wl-recipe-ingredient-form");
    ingredientForm.on("submit", function (e) {
        e.preventDefault(e);
        const data = {
            _wpnonce: _wlRecipeIngredient.nonce,
            main_ingredient: $(this).find(".main-ingredient").val(),
            recipe_id: $(this).find("#recipe_id").val(),
        };
        // Save the ingredient.
        const saveBtn = $(this).find('.wl-recipe-ingredient__save');
        saveBtn.html(_wlRecipeIngredient.texts.saving);
        wp.ajax
            .post(
                "wl_update_ingredient_post_meta",
                data
            ).done(function (response) {
                saveBtn.text(response.btnText);
            })
            .fail(function (error) {
                console.log(error);
                saveBtn.text(error.btnText);
            });
    });

    // Ingredient Autocomplete.
    let autocompleteTimeout = null;
    const autocomplete = (query, callback) => {
        // Minimum 3 characters.
        if (3 > query.length) {
            callback(null, {
                options: []
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
                _wpnonce: _wlRecipeIngredient.acNonce,
            })
            .done((json) => callback(null, {
                options: json
            }))
            .fail(() => {
                console.log("error");
                callback(null, {
                    options: []
                });
            }),
            500
        );
    };

    // Auto Complete.
    $('.main-ingredient').on('change keyup', () => {
        const uID = $(this).attr('id');
        $('.main-ingredient').autocomplete({
            source: function (request, response) {
                autocomplete(request.term, (err, data) => {
                    if (data.options && data.options.length) {
                        response(data.options);
                    } else {
                        response([{
                            label: _wlRecipeIngredient.texts.noResults,
                            val: -1
                        }]);
                        console.log(err);
                    }
                });
            },
            search: function () {
                $(this).addClass('autocomplete-loading');
            },
            open: function () {
                $(this).removeClass('autocomplete-loading');
            },
            select: function (event, u) {
                if (u.item.val == -1) {
                    // Clear the AutoComplete TextBox.
                    $(this).val("");
                    return false;
                }
                $(`#${uID}`).val(u);
            },
            close: function(event, ui) {
                $(this).val("");
            }
        });
    })
});