jQuery(document).ready( function($) {

    // Called when the Visualization API is loaded.
    function draw( data ) {        
        
        var nodes = data.entities;
        var edges = data.relations;
        var nodesIndexes = [];   // map uri --> index
        
        // build nodes
        for(var i=0; i<nodes.length; i++){
            nodes[i].id = i;
            nodes[i].shape = 'circularImage';
            nodes[i].image = nodes[i].thumbnails[0];
            
            // refresh node indexes hash
            var uri = nodes[i].uri;
            nodesIndexes[uri] = i;
        }

        // build edges
        for( var i=0; i<edges.length; i++){
            var fromUri = edges[i].s;
            edges[i].from = nodesIndexes[fromUri];
            
            var toUri = edges[i].o;
            edges[i].to = nodesIndexes[toUri];
        }
        
        console.log(edges);

        // create the network
        var container = document.getElementById('wl-blog-map');
        var data = {
            nodes: nodes,
            edges: edges
        };
        var options = {
            nodes: {
                borderWidth:4,
                size:30,
                color: {
                    border: '#222222',
                    background: '#666666'
                },
                font:{color:'gray'}
            },
            edges: {
                color: 'gray'
            }
        };
        var network = new vis.Network(container, data, options);
    }
    
    // Query graph data via AJAX and launch blog map!
    $.ajax({
        type: 'POST',
        url: blog_map_params.ajax_url + '?action=' + blog_map_params.action,
        data: {
            post_id: $('#wl-blog-map').data('post-id'),
            depth: $('#wl-blog-map').data('depth')
        },
        success: function(response) {
            draw( response );
        }
    });
});

