<?php
/**
* Plugin Name: STTBCSTL Plugins 
* Description: Plugin that encompass the custom posts and extra
* Plugin URI: http://sttbcstl.org
* Author: Christophe Rudyj
* Author URI: http://chrisrudyj.me
* Version: 1.53
* License: GPL2
* Text Domain: 
* Domain Path: sttbcstl
*/

/*
Copyright (C) 2016  Christophe Rudyj contact@chrisrudyj.me

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Get the bootstrap!
 */
if ( file_exists(  __DIR__ . '/cmb2/init.php' ) ) {
  require_once  __DIR__ . '/cmb2/init.php';
} elseif ( file_exists(  __DIR__ . '/CMB2/init.php' ) ) {
  require_once  __DIR__ . '/CMB2/init.php';
}

// include '';


/**
* Registers a new post type
* @uses $wp_post_types Inserts new post type object into the list
*
* @param string  Post type key, must not exceed 20 characters
* @param array|string  See optional args description above.
* @return object|WP_Error the registered post type object, or an error object
*/
function sttbcstl_register_contact() {

  $labels = array(
    'name'                => __( 'contacts', 'sttbcstl' ),
    'singular_name'       => __( 'Contact', 'sttbcstl' ),
    'add_new'             => _x( 'Ajouter un Contact', 'sttbcstl' ),
    'add_new_item'        => __( 'Ajouter un Contact', 'sttbcstl' ),
    'edit_item'           => __( 'Editer un Contact', 'sttbcstl' ),
    'new_item'            => __( 'Noir un Contact', 'sttbcstl' ),
    'view_item'           => __( 'Voir un Contact', 'sttbcstl' ),
    'search_items'        => __( 'Search contacts', 'sttbcstl' ),
    'not_found'           => __( 'No contacts found', 'sttbcstl' ),
    'not_found_in_trash'  => __( 'No contacts found in Trash', 'sttbcstl' ),
    'parent_item_colon'   => __( 'Parent Contact:', 'sttbcstl' ),
    'menu_name'           => __( 'Equipe', 'sttbcstl' ),
  );

  $args = array(
    'labels'                   => $labels,
    'hierarchical'        => false,
    'description'         => 'Ajoute des memembre Syndicat',
    'taxonomies'          => array(),
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
    'supports'            => array(
      'title', 'thumbnail'
      )
  );

  register_post_type( 'contact', $args );
}

add_action( 'init', 'sttbcstl_register_contact' );

//Contact Meta Box

add_action( 'cmb2_admin_init', 'sttbcstl_meta_contact' );
/**
 * Define the metabox and field configurations.
 */
function sttbcstl_meta_contact() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = 'contact';
     $cmb = new_cmb2_box( array(
    'id'           => 'contact_box',
    'title'        => __('Information', 'plmtl'),
    'object_types' => array( 'contact' ),
    'context'      => 'normal',
    'priority'     => 'high',
  ) );

$cmb->add_field( array(
    'name'    => 'Prenom',
    'default' => 'John',
    'id'      => $prefix . '_name',
    'type'    => 'text',
) );
$cmb->add_field( array(
    'name'    => 'Nom de famille',
    'default' => 'Doe',
    'id'      => $prefix . '_last',
    'type'    => 'text',
) );
$cmb->add_field( array(
    'name'    => 'E-mail',
    'desc'    => 'xxx@yyy.zzz',
    'id'      => $prefix . '_mail',
    'type'    => 'text_email',
) );
$cmb->add_field( array(
    'name'    => 'Numero de telephone',
    'desc'    => '555-555-5555',
    'id'      => $prefix . '_num',
    'type'    => 'text',
) );
$cmb->add_field( array(
    'name'    => 'Poste',
    'desc'    => '5555',
    'id'      => $prefix . '_ext',
    'type'    => 'text',
) );

}

function the_contacts() {
  $contact_query = new WP_Query();
  $contact_query-> query('post_type=contact&showposts=-1');
  while ($contact_query->have_posts()) { $contact_query->the_post();
    $contact_img  = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array( 160,160 ), false, '');
    $contact_name = get_post_meta (get_the_id(), contact_name, true);
    $contact_last = get_post_meta (get_the_id(), contact_last, true);
    $contact_num  = get_post_meta (get_the_id(), contact_num,  true);
    $contact_ext  = get_post_meta (get_the_id(), contact_ext,  true);
    $contact_mail = get_post_meta (get_the_id(), contact_mail, true);

    $esc_name = esc_html($contact_name);
    $esc_last = esc_html($contact_last);
    $esc_num  = esc_html($contact_num);
    $esc_mail = is_email($contact_mail);
    $esc_ext  = esc_html($contact_ext);
    $title    = get_the_title();
    $siteurl  = get_bloginfo('wpurl');
  
    $output = " <article class='contact'>
     <section class='contact-img' style='background: url({$siteurl} ?>/wp-content/themes/bcstl/images/bcstl_contactmask.png) no-repeat, url({$contact_img[0]} ?>) no-repeat;'>&nbsp;</section>
     <section class='contact-info'>
      <h1>{$esc_name}&nbsp;{$esc_last}</h1>
      <h2>{$title}</h2>
      <h3><a href='mailto:{$esc_mail}'>{$esc_mail}</a></h3>
      <h4>{$esc_num}&nbsp;#{$esc_ext}</h4>
      </section></article>";
     
     echo $output;
     }
    
}

include 'mob.php';

include 'source.php';

