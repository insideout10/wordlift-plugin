import Mustache from "mustache";

function toType(uri) {
  return uri.substring(uri.lastIndexOf("/") + 1);
}

function renderTile({ click, select }) {
  return function(node, elem) {
    if (node.data && node.data.entity && node.data.score) {
      node.data.entity.type = toType(node.data.entity.type);
      // Each star is 20px. The relative score is value between 0.0 and 1.0.
      node.data.entity.width = `${node.data.score.relative * 100}px`;

      // Hook the click event.
      elem.addEventListener("click", () => {
        const state = select();
        console.debug({start:state});
        const elClassListPrev = state && state.node ? state.node.elemClassList : null;
        const elClassListNext = arguments[1].classList;
        console.debug({prev:elClassListPrev, next:elClassListNext});
        if (elClassListPrev !== elClassListNext) {
          console.debug('mutation');
          if (elClassListPrev) {
            elClassListPrev.remove('treemap__hierarchy__tile--selected');
            state.node.elemClassList = null;
          }
          elClassListNext.add('treemap__hierarchy__tile--selected');
          node.data.elemClassList = elClassListNext;
        }
        console.debug({node});
        click(node, arguments);
      });
    }

    return Mustache.render(
      `
        {{#entity}}
        <div class="tile">
          <a href="/wp-admin/admin-ajax.php?action=wl_locate&uri={{itemId}}" class="tile__label">{{label}}</a>
          <div class="tile__type">{{type}}</div>
          <div class="tile__score" style="width: {{width}};"></div>
        </div>
        {{/entity}}
        {{^entity}}{{name}}{{/entity}}
    `,
      node.data
    );
  };
}

export default renderTile;
