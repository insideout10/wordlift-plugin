// jQuery Code.
jQuery(document).ready(function ($) {

    // Update Ingredient.
    const ingredientForm = $(".wl-recipe-ingredient-form");
    ingredientForm.on("submit", function (e) {
        e.preventDefault(e);
        const data = `${$(this).serialize()}&action=wl_update_ingredient_post_meta&_wpnonce=${wlRecipeIngredient.nonce}`;
        // Save the ingredient.
        const saveBtn = $(this).find('.wl-recipe-ingredient__save');
        $.ajax({
            type: "GET",
            dataType: "json",
            url: wlRecipeIngredient.ajaxurl,
            data,
            success: function (response) {
                if (response.success) {
                    saveBtn.text('Saved');
                } else if (response.same) {
                    saveBtn.text('Save');
                } else {
                    saveBtn.text('Error');
                }
            }
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
                _wpnonce: wlRecipeIngredient.acNonce,
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
                autocomplete( request.term, (err, data) => {
                    if ( data ) {
                        response( data.options );
                    } else {
                        console.log( err );
                    }
                } );
            },
            select: function (event, ui) { $(`#${uID}`).val(ui); }
        });
    })
});