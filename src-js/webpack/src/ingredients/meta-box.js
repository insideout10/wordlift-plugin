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
            const ingredient = $(element).find("input[name='recipe_main_ingredient[]']").val();
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