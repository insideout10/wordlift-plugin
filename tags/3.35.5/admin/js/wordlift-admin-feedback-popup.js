/*
 * This file contains utilities for deactivation feedback popup.
 */

jQuery(document).ready(function($) {
  // Add deactivation listener.
  const deactivationButton = $(
    '#the-list .active[data-plugin="wordlift/wordlift.php"] .deactivate a'
  );

  // Display the feedback popup on deactivation button click.
  deactivationButton.click(function(e) {
    e.preventDefault();
    $(".wl-modal-deactivation-feedback")
      .removeAttr("style")
      .addClass("visible");
  });

  // Mark the reason item as selected on deactivation reason change.
  $(".wl-code").on("change", function() {
    // Mark the current item as selected.
    $(this)
      .parents(".wl-reason-item")
      .addClass("selected")
      // Remove the selected state from the siblings.
      .siblings()
      .removeClass("selected");
  });

  $(".wl-modal-button-deactivate").on("click", function(e) {
    e.preventDefault();

    // Send the data to the backend service.
    wp.ajax
      .post("wl_deactivation_feedback", {
        code: $(".wl-reason-item.selected .wl-code").val(), // The reason code for deactivation.
        details: $(".wl-reason-item.selected .wl-details").val(), // The additional info.
        wl_deactivation_feedback_nonce: $(
          ".wl_deactivation_feedback_nonce"
        ).val() // The nonce verification.
      })
      .done(function(response) {
        // Redirect if we have success state.
        return (window.location.href = deactivationButton.attr("href"));
      })
      .fail(function(response) {
        if (response.length) {
          // Display the errors to the user.
          $(".wl-errors").html("<p>" + response + "</p>");
        }
      });
  });

  // Close the popup by clicking outside of the body
  // or on "Cancel" button.
  $(".wl-modal-deactivation-feedback, .wl-modal-button-close").on(
    "click",
    function(e) {
      e.preventDefault();

      $(this).removeClass("visible");
    }
  );

  // Prevent the popup from bubbling.
  $(".wl-modal-body, .wl-modal-button-deactivate").on("click", function(e) {
    e.stopPropagation();
  });
});
