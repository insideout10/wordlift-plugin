import Mustache from "mustache";

function toType(uri) {
  return uri.substring(uri.lastIndexOf("/") + 1);
}

function renderTile(node) {
  if (node.data && node.data.entity) {
    node.data.entity.type = toType(node.data.entity.type);
    node.data.entity.width = `${node.data.entity.score * 100}px`;
  }

  return Mustache.render(
    `
        {{#entity}}
        <div class="tile">
          <a href="/wp-admin/admin-ajax.php?action=wl_locate&uri={{itemId}}" class="tile__label">{{label}}</a>
          <div class="tile__type">{{type}}</div>
          <div class="tile__score" style="width: {{width}};">{{score}}</div>
        </div>
        {{/entity}}
        {{^entity}}{{name}}{{/entity}}
    `,
    node.data
  );
}

export default renderTile;
