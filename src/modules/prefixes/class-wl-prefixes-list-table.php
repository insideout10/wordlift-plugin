<?php
/**
 * This file contains the Prefixes List table.
 */

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * The prefixes list table class.
 * See http://codex.wordpress.org/Class_Reference/WP_List_Table and http://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
 *
 * @since 3.0.0
 */
class WL_Prefixes_List_Table extends WP_List_Table {

    var $_column_headers;

    function get_columns(){
        $columns = array(
//            'cb'        => '<input type="checkbox" />',
            'prefix'    => 'Prefix',
            'namespace' => 'Namespace'
        );
        return $columns;
    }

    function prepare_items() {
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $prefixes = wl_prefixes_list();
        usort( $prefixes, array( &$this, 'usort_reorder' ) );
        $this->items = $prefixes;

    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'prefix':
            case 'namespace':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function column_prefix( $item ) {
        $actions = array(
//            'edit'   => sprintf( '<a href="?page=%s&action=%s&prefix=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['prefix'] ),
            'delete' => sprintf( '<a href="?page=%s&action=%s&prefix=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['prefix'] )
        );

        return sprintf( '%1$s %2$s', $item['prefix'], $this->row_actions($actions) );
    }


    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="prefix[]" value="%s" />', $item['prefix']
        );
    }


    function get_sortable_columns() {
        $sortable_columns = array(
            'prefix'    => array('prefix',false),
            'namespace' => array('namespace',false)
        );
        return $sortable_columns;
    }

    function usort_reorder( $a, $b ) {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'prefix';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => __( 'Delete', 'wordlift' )
        );
        return $actions;
    }

}
