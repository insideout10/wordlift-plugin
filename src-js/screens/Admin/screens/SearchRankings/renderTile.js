import Mustache from "mustache";

function toType(uri) {
  return uri.substring(uri.lastIndexOf("/") + 1);
}

function renderTile({ click }) {
  return function(node, elem) {
    if (node.data && node.data.entity && node.data.score) {
      node.data.entity.type = toType(node.data.entity.type);
      node.data.entity.width = `${node.data.score.value * 35}px`;

      // Hook the click event.
      console.debug({ elem });
      elem.addEventListener("click", () => click(node, arguments));
    }

    return Mustache.render(
      `
        {{#entity}}
        <div class="tile">
          <a href="/wp-admin/admin-ajax.php?action=wl_locate&uri={{itemId}}" class="tile__label">{{label}}</a>
          <div class="tile__type">{{type}}</div>
          <div class="tile__score" style="width: {{width}}; max-width: 100px;"></div>
        </div>
        {{/entity}}
        {{^entity}}{{name}}{{/entity}}
    `,
      node.data
    );
  };
}

export default renderTile;
