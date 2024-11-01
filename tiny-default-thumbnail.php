<?php
/**
 * Plugin Name:       Tiny Default Thumbnail
 * Plugin URI:        https://wordpress.org/plugins/tiny-default-thumbnail
 * Description:       Allows to add a default thumbnail for posts.
 * Version:           0.1.1
 * Requires at least: 4.7
 * Requires PHP:      7.0
 * Author:            Vincent Dubroeucq
 * Author URI:        https://vincentdubroeucq.com/
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tiny-default-thumbnail
 * Domain Path:       /languages
 */

/*
Tiny Default Thumbnail is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Tiny Default Thumbnail is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Tiny Default Thumbnail. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/

 
add_action( 'init', 'tiny_default_thumbnail_load_textdomain' );
/**
 * Load translations
 */
function tiny_default_thumbnail_load_textdomain(){
    load_plugin_textdomain( 'tiny-default-thumbnail', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}


add_filter( 'post_thumbnail_html', 'tiny_default_thumbnail_html', 10, 5 );
/**
 * Filters the thumbnail HTML if no thumbnail is present.
 * 
 * @param   string  $html      Image tag HTML
 * @param   int     $post_id   Post ID.
 * @param   int     $thumb_id  Original thumbnail ID. 0 if no thumbnail for the post.
 * @param   string  $size      Size requested for the image
 * @param   array   $attr      Additional arguments passed in to the_post_thumbnail()
 * @return  string  $html
 */
function tiny_default_thumbnail_html( $html, $post_id, $thumb_id, $size, $attr ){
    if( 'post' === get_post_type( $post ) && empty( $html ) && ! $thumb_id ){
        $default_thumbnail_id = (int) get_option( 'tiny_default_thumbnail_id' );
        if(  $default_thumbnail_id ){
            $html = wp_get_attachment_image( $default_thumbnail_id, $size, false, $attr );
        }
    }
    return $html;
}


add_filter( 'has_post_thumbnail', 'tiny_default_thumbnail_has_post_thumbnail', 10, 3 );
/**
 * Filter to fake the presence of a post thumbnail on posts
 * 
 * @param  bool  $has_thumbnail  Whether the current post has a thumbnail or not.
 * @param  WP_Post  $post  Current post
 * @param  int     $thumb_id  Post thumbnail ID. 0 if no thumbnail for the post.
 */
function tiny_default_thumbnail_has_post_thumbnail( $has_thumbnail, $post, $thumbnail_id ){
    if( 'post' === get_post_type( $post ) && ! $thumbnail_id  ){
        $has_thumbnail = true;
    }
    return $has_thumbnail;
}


add_action( 'customize_register', 'tiny_default_thumbnail_customize_register', 10, 1 );
/**
 * Adds our default thumbnail setting to the customizer
 */
function tiny_default_thumbnail_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'tiny_thumbnail_settings', array(
        'title'       => __( 'Tiny Thumbnail Settings', 'tiny-default-thumbnail' ),
        'description' => __( 'Tweak your thumbnail settings here.', 'tiny-default-thumbnail' ),
    ) );
    $wp_customize->add_setting( 'tiny_default_thumbnail_id', array(
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'tiny_default_thumbnail_id', array(
        'label'       => __( 'Default thumbnail', 'tiny-default-thumbnail' ),
        'section'     => 'tiny_thumbnail_settings',
        'mime_type'   => 'image'
    ) ) );
}