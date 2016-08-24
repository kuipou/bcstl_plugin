<?php 
add_action( 'cmb2_init', 'csn_meta_source' );
function csn_meta_source() {

    $prefix = 'source_';

    $cmb = new_cmb2_box( array(
        'id'           => $prefix . '_box',
        'title'        => __( "Source de l'article", 'sttbcstl' ),
        'object_types' => array( 'post', 'page' ),
        
        'context'      => 'normal',
        'priority'     => 'core',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Source', 'sttbcstl' ),
        'id' => $prefix . 'd',
        'type' => 'text',
        'desc' => __( 'Example: Le devoir 2001', 'sttbcstl' ),
    ) );

    $cmb->add_field( array(
        'name' => __( 'Addresse', 'sttbcstl' ),
        'id' => $prefix . 'url',
        'type' => 'text_url',
        'default' => 'http://',
        'protocols' => array( 'http', 'https' ),
        'desc' => __( 'Doit commencer par http://', 'sttbcstl' ),
    ) );

}
/*
*  The Source
*  @param none
* Gets the current meta of the post and escapes them then returns the url and desc via $output
*/
function the_source() {
    $url = get_post_meta( get_the_ID(),'source_url', true );
    $desc = get_post_meta( get_the_ID(),'source_d', true );
    $esc_url = esc_url( $url );
    $esc_desc = esc_html( $desc );
    $output = "Source: <a href='{$esc_url}'>{$esc_desc}</a>";
    echo $output;
}