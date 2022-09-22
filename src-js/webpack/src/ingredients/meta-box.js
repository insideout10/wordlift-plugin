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
        const saveBtn = $(this).find('input.button');
        saveBtn.val(_wlRecipeIngredient.texts.saving);
        wp.ajax
            .post(
                "wl_update_ingredient_post_meta",
                data
            ).done(function (response) {
                saveBtn.val(response.btnText);
            })
            .fail(function (error) {
                console.log(error);
                saveBtn.val(error.btnText);
            });
    });

    // Auto Complete.
    $('.main-ingredient').on('change keyup', () => {
        const uID = $(this).attr('id');
        $('.main-ingredient').autocomplete({
            minLength: 3,
            delay: 500,
            source: function (request, response) {
                wp.ajax
                    .post("wl_ingredient_autocomplete", {
                        query: request.term,
                        _wpnonce: _wlRecipeIngredient.acNonce,
                    })
                    .done((json) => {
                        response(json);
                    })
                    .fail((err) => {
                        response([{
                            label: _wlRecipeIngredient.texts.noResults,
                            val: -1
                        }]);
                        console.log(err);
                    });
                $(this).removeClass('autocomplete-loading');
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
            }
        });
    })
});