<?php

/* Registers a new post type 
* @uses $wp_post_types Inserts new post type object into the list
*
* Version: 2
* License: GPL2
*
* @param string  Post type key, must not exceed 20 characters
* @param array|string  See optional args description above.
* @return object|WP_Error the registered post type object, or an error object
*/
function csn_register_mob() {

  $labels = array(
    'name'                => __( 'Mobilisations', 'sttbcstl' ),
    'singular_name'       => __( 'Mobilisation', 'sttbcstl' ),
    'add_new'             => _x( 'Ajouter une Mobilisation', 'sttbcstl' ),
    'add_new_item'        => __( 'Ajouter une Mobilisation', 'sttbcstl' ),
    'edit_item'           => __( 'Editer une Mobilisation', 'sttbcstl' ),
    'new_item'            => __( 'Voir une Mobilisation', 'sttbcstl' ),
    'view_item'           => __( 'Voir une Mobilisation', 'sttbcstl' ),
    'search_items'        => __( 'Search mobilisations', 'sttbcstl' ),
    'not_found'           => __( 'Aucune mobilisations trouvée', 'sttbcstl' ),
    'not_found_in_trash'  => __( 'Aucune mobilisations trouvée dans la corbeille', 'sttbcstl' ),
    'parent_item_colon'   => __( 'Parent Mobilisation:', 'sttbcstl' ),
    'menu_name'           => __( 'Mobilisation', 'sttbcstl' ),
    );
  $args = array(
    'labels'                   => $labels,
    'hierarchical'        => false,
    'description'         => 'Deploi la Banniere de Mobilisation',
    'taxonomies'          => array('category'),
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 5,
    'menu_icon'           => null,
    'show_in_nav_menus'   => true,
    'publicly_queryable'  => true,
    'exclude_from_search' => false,
    'has_archive'         => true,
    'query_var'           => true,
    'can_export'          => true,
    'rewrite'             => false,
    'capability_type'     => 'post',
    'supports'            => array('title', 'editor')
    );
  register_post_type( 'mob', $args );
  register_taxonomy_for_object_type( 'category', 'mob' );

}

  add_action( 'init', 'csn_register_mob' );

/* Function: Important Category
*  
* @param id uses the current post type id to search if its found it stores the mob in important
* @param post 
*/
function csn_mob_important_category( $id, $post ) {
    if ( 'mob' === get_post_type( $id ) ) {
     $category   = get_term_by( 'slug', 'important', 'category' );
     $categories = wp_get_object_terms( $id, 'category', array( 'fields' => 'ids' ) );
     if ( $category and !in_array( $category->term_id, $categories ) ) {
      wp_set_object_terms( $id, ( int ) $category->term_id, 'category', true );
  }
}
} 

add_action( 'wp_insert_post', 'csn_mob_important_category', 10, 2 );

add_action( 'cmb2_init', 'csn_meta_mob' );
function csn_meta_mob() {

    $prefix = 'mob_';

    $cmb = new_cmb2_box( array(
        'id'           => $prefix . 'mob_box',
        'title'        => __( 'Date de Fin', 'sttbcstl' ),
        'object_types' => array( 'mob' ),
        'context'      => 'side',
        'priority'     => 'high',
    ) );

    $cmb->add_field( array(
        'id' => $prefix . 'datetext',
        'type' => 'text_date',
        'date_format' => 'Y-m-d',
    ) );

}

/* The Mob();
*/

function the_mob($mob_debug = false) {
    $mob_query = new WP_Query();
    $mob_query-> query('post_type=mob&showposts=1');
    while ($mob_query->have_posts()) { $mob_query->the_post();
        $mob_cur_date = date('Y-m-d');
        $mob_end_date = get_post_meta(get_the_ID(), 'mob_datetext', true);
        $mob_end_date_string = strtotime($mob_end_date);
        $mob_cur_date_string = strtotime($mob_cur_date);

        // Post Content in vars
        $mob_permalink = get_permalink();
        $mob_title     = get_the_title();
        $mob_content   = get_the_content();
        $mob_important = get_cat_id('important');

        $output = "<section id='mob'><header><a href='{$mob_permalink}'><h1>{$mob_title}</h1></a></header><article>{$mob_content}</article><footer>&nbsp;</footer></section>";

        /* If the Current date is less than the end date
           echo the output and set $mob_debug_date to true to be able to echo if the date is less or not 
           If no other mobilisation has been found matching the param the function will return 0
        */ 
        if ($mob_cur_date_string <= $mob_end_date_string) {
            echo $output;
            $mob_debug_date = true;
        } 
        else {
            return 0;
        }
        /* If the_mob is true debug will enable the echo
        */
        if ($mob_debug == true){

            $debug = "<div id='mob_debug' style='background-color: white; height: 125px; color: black;''>
            <br /><b>Mob Category:{$mob_important}<br /></b>Current Date:</b>{$mob_cur_date}<br /><b>End Date:</b>{$mob_end_date}<br /><b>Current Date String:</b>{$mob_cur_date_string}<br /><b>End Date String:</b>{$mob_end_date_string}";
            if ($mob_debug_date == true) {
                $debug .= "<br /> <b>the date is greater or equal and it works</b>";
            } else {
                $debug .= "<br /><b>the date is less</b> ";
            }

        } 
    } 


    wp_reset_query(); query_posts('cat=-$mob_important');
}