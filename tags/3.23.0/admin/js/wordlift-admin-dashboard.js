/*
 * Dashboard latest newst widget functions.
 */
jQuery(document).ready(function($) {
  // More articles click
  $(".wl_more_posts").on("click", function(e) {
    e.preventDefault();
    // Remove more articles link
    $(".wl_more_posts").remove();

    // Ajax request for more articles
    wp.ajax
      .post("wordlift_get_latest_news", {
        more_posts_link_id: $(this).attr("id")
      })
      .done(function(data) {
        $.each(data.posts_data, function(index, item) {
          post_url =
            '<a target = "_blank" href = "' +
            item.post_url +
            '">' +
            item.post_title +
            "</a>";
          // Append each article to news_container
          $("<div>")
            .append(post_url, $("<p>").html(item.post_description))
            .appendTo("#news_container");
        });
      });
  });
});
