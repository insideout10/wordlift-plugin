// jQuery Code.
jQuery(function ($) {
    // Update Ingredient.
    const ingredientFormSubmitBtn = $( '.wl-recipe-ingredient-form__submit' );
    ingredientFormSubmitBtn.on( 'click', function (e) {
        e.preventDefault(e);
        
        const ingredientsData = $( '.wl-table--main-ingredient__data' );
        ingredientsData.each( ( index, element ) => {
            const recipeID = $( element ).find( '#recipe-id' ).val();
            const ingredient = $( element ).find( "input[name='main_ingredient[]']").val();
            if ( ! recipeID && ! ingredient ) {
                return;
            }
            updateIngredient( recipeID, ingredient );
        });
        
    });

    const updateIngredient = ( recipeID, ingredient ) => {
        const data = {
            _wpnonce: _wlRecipeIngredient.nonce,
            main_ingredient: ingredient,
            recipe_id: recipeID,
        };
        // Save the ingredient.
        wp.ajax
            .post(
                "wl_update_ingredient_post_meta",
                data
            ).done(function (response) {
                wp.data.dispatch('core/notices').createNotice(
                    'success',
                    response.message,
                    {
                        type: 'snackbar',
                        isDismissible: true,
                    }
                );
            })
            .fail(function (error) {
                wp.data.dispatch('core/notices').createNotice(
                    'error',
                    error.message,
                    {
                        type: 'snackbar',
                        isDismissible: true,
                    }
                );
            });
    }
});