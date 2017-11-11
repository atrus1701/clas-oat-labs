<?php
/*
Plugin Name: CLAS OAT Labs
Plugin URI: https://github.com/clas-web/clas-oat-labs
Description: CLAS OAT Labs site custom plugin with custom template files.
Version: 1.3.2
Author: Crystal Barton
Author URI: http://www.crystalbarton.com
GitHub Plugin URI: https://github.com/clas-web/clas-oat-labs
*/


if( !defined('LABS_PLUGIN_NAME') ):

/**
 * 
 * @var  string
 */
define( 'LABS_PLUGIN_NAME', 'CLAS OAT Labs' );

/**
 * 
 * @var  string
 */
define( 'LABS_PLUGIN_VERSION', '1.0.0' );

/**
 * 
 * @var  string
 */
define( 'LABS_PLUGIN_PATH', __DIR__ );

/**
 * 
 * @var  string
 */
define( 'LABS_PLUGIN_URL', plugins_url('', __FILE__) );

endif;


add_action( 'wp_enqueue_scripts', 'labs_enqueue_scripts' );
add_filter( 'mt_related_tax_title', 'labs_related_tax_title', 10, 3 );
add_action( 'vtt-search-folders', 'labs_variations_add_variations_folder' );
add_action( 'wp_head', 'labs_wp_head' );

/**
 * 
 */
function labs_get_anchor( $url, $title, $class = null, $contents = null )
{
	if( empty($url) ) return $contents;
	
	$anchor = '<a href="'.$url.'" title="'.htmlentities($title).'"';
	if( $class ) $anchor .= ' class="'.$class.'"';
	$anchor .= '>';

	if( $contents !== null )
		$anchor .= $contents.'</a>';

	return $anchor;
}

/**
 * 
 */
function labs_get_taxonomy_breadcrumbs( $term_id, $taxonomy = 'category', $include_home = FALSE )
{
	$term = get_term( $term_id, $taxonomy );
	if( $term === null || is_wp_error($term) ) return '';
	
	$breadcrumbs = array();
	
	if( $include_home ) {
		$breadcrumbs[] = '<a href="'.site_url().'" title="Home">Home</a>';
	}
	
	while( $term->parent )
	{
		$term = get_term( $term->parent, $taxonomy );
		$link = get_term_link( $term, $taxonomy );
		$title = $term->name;
		$breadcrumbs[] = '<a href="'.$link.'" title="'.$title.'">'.$title.'</a>';
	}
	
	if( count($breadcrumbs) > 0 ) {
		return implode( ' &raquo; ',  $breadcrumbs ).' &raquo; ';
	}
	return '';
}

/**
 * 
 */
if( !function_exists('labs_enqueue_scripts') ):
function labs_enqueue_scripts()
{
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'labs-script', LABS_PLUGIN_URL . '/script.js', array('jquery'), LABS_PLUGIN_VERSION );
	wp_enqueue_style( 'labs-style', LABS_PLUGIN_URL . '/style.css', false, LABS_PLUGIN_VERSION );
}
endif;


if( !function_exists('labs_wp_head') ):
function labs_wp_head()
{
	if( isset( $_GET ) && isset( $_GET['sfid'] ) )
	{
		vtt_set_page_listing_name( '' );
	}
}
endif;


/**
 * 
 */
if( !function_exists('labs_related_tax_title') ):
function labs_related_tax_title( $title, $taxname, $label )
{
	return $label;
}
endif;


/**
 * Add the plugin folder the list of folders used by VTT to determine available variations.
 */
if( !function_exists('labs_variations_add_variations_folder') ):
function labs_variations_add_variations_folder()
{
	global $vtt_config;
	$vtt_config->add_search_folder( LABS_PLUGIN_PATH, 7 );
}
endif;

/**
 * Get the html for a taxonomy list.
 * @param  string  $taxonomy_name  The name of the taxonomy.
 * @param  WP_Post  $p  The WP_Post object or null if global $post object should be used.
 * @return  string  The generated html.
 */
if( !function_exists( 'labs_get_taxonomy_terms' ) ):
function labs_get_taxonomy_terms( $taxonomy_name, $p, $use_anchor = true, $show_label = true )
{
	global $post;
	if( !$p ) $p = $post;

	$taxonomy = get_taxonomy( $taxonomy_name );
	if( !$taxonomy ) return '';

	$terms = wp_get_post_terms( $p->ID, $taxonomy_name );
	if( count($terms) == 0 ) return '';

	$html = '';
	if( $show_label == "true" ) $html .= '<span class="taxonomy-list taxonomy-spanlist '.$taxonomy->name.'-list">';	

	if( $show_label == "true" )
	{
		$taxonomy_label = $taxonomy->label;
	}
	else
	{
		$taxonomy_label = "";
	}

	if( $taxonomy->label == "Categories" ) 
	{
		$taxonomy_label = get_option('category_base');
		if( !$taxonomy_label ) $taxonomy_label = $taxonomy->label;
		$taxonomy_label_style = "<span class='category-label'>";
	} 
	else if( $taxonomy->label == "Tags" ) 
	{
		$taxonomy_label = get_option('tag_base');
		if( !$taxonomy_label ) $taxonomy_label = $taxonomy->label;
		$taxonomy_label_style = "<span class='tag-label'>";
	}
	else
	{
		$taxonomy_label_style = "<span class='taxonomy-label'>";
	}
	if( $show_label == "true" ) $html .= $taxonomy_label_style.$taxonomy_label.': </span>';

	if( count($terms) > 0 )
	{
		$list = array();
		foreach( $terms as $t )
		{
			if( $use_anchor ) {
				$list[] = '<span>' . vtt_get_anchor( get_term_link($t->term_id, $taxonomy_name), $t->name, $t->slug, $t->name ) . '</span>';
			} else {
				$list[] = '<span>' . $t->name . '</span>';
			}
		}
		
		
	//	$column_item_count = (int)(count( $list ) / $column_count);
		if( $column_item_count === 0 ) $column_item_count = 1;
		$html .= '<span class="list">';
//		for( $i = 0; $i < $column_count; $i++ )
//		{
			//$html .= '<div class="column">';
			$html .= implode( '', array_slice( $list, 0) );
			//$html .= '</div>';
//		}
		$html .= '</span>';
	}
	else
	{
		$html .= '-';
	}
	
	$html .= '</span>';
	
	return $html;
}
endif;

/**
 * Get the html for a taxonomy list.
 * @param  string  $taxonomy_name  The name of the taxonomy.
 * @param  WP_Post  $p  The WP_Post object or null if global $post object should be used.
 * @return  string  The generated html.
 */
if( !function_exists( 'labs_get_taxonomy_list' ) ):
function labs_get_taxonomy_list( $taxonomy_name, $p, $column_count, $use_anchor = true )
{
	global $post;
	if( !$p ) $p = $post;

	$taxonomy = get_taxonomy( $taxonomy_name );
	if( !$taxonomy ) return '';

	$terms = wp_get_post_terms( $p->ID, $taxonomy_name );
	if( count($terms) == 0 ) return '';

	$html = '';
	$html .= '<div class="taxonomy-list '.$taxonomy->name.'-list">';	

	$taxonomy_label = $taxonomy->label;

	if( $taxonomy->label == "Categories" ) 
	{
		$taxonomy_label = get_option('category_base');
		if( !$taxonomy_label ) $taxonomy_label = $taxonomy->label;
		$taxonomy_label_style = "<span class='category-label'>";
	} 
	else if( $taxonomy->label == "Tags" ) 
	{
		$taxonomy_label = get_option('tag_base');
		if( !$taxonomy_label ) $taxonomy_label = $taxonomy->label;
		$taxonomy_label_style = "<span class='tag-label'>";
	}
	else
	{
		$taxonomy_label_style = "<span class='taxonomy-label'>";
	}
	
	$html .= $taxonomy_label_style.$taxonomy_label.': </span>';

	if( count($terms) > 0 )
	{
		$list = array();
		foreach( $terms as $t )
		{
			if( $use_anchor ) {
				$list[] = '<span>' . vtt_get_anchor( get_term_link($t->term_id, $taxonomy_name), $t->name, $t->slug, $t->name ) . '</span>';
			} else {
				$list[] = '<span>' . $t->name . '</span>';
			}
		}
		$column_item_count = (int)(count( $list ) / $column_count);
		if( $column_item_count === 0 ) $column_item_count = 1;
		$html .= '<div class="list">';
		for( $i = 0; $i < $column_count; $i++ )
		{
			$html .= '<div class="column">';
			$html .= implode( '', array_slice( $list, $column_item_count * $i, $column_item_count ) );
			$html .= '</div>';
		}
		$html .= '</div>';
	}
	else
	{
		$html .= '-';
	}
	
	$html .= '</div>';
	
	return $html;
}
endif;




if( ! function_exists('labs_get_sfp_remove_link') ):
function labs_get_sfp_remove_link( $term )
{
	if( ! isset( $_GET ) || ! isset( $_GET['sfid'] ) ) {
		return '';
	}
	
	global $searchandfilter;
	if( ! isset( $searchandfilter ) ) {
		return;
	}
	
	$sfid = (int)( $_GET['sfid'] );
	$sf_inst = $searchandfilter->get( $sfid );
	
	$query_parts = $_GET;
	$key = '_sft_' . $term->taxonomy;
	
	if( array_key_exists( $key, $query_parts ) )
	{
		$e = explode( ' ', $query_parts[ $key ] );
		$e = array_filter( 
			$e, 
			function( $v ) use ($term) {
    			return ( $v != $term->slug );
			}
		);
		if( count( $e ) == 0 ) {
			unset( $query_parts[ $key ] );
		} else {
			$query_parts[ $key ] = implode( '+', $e );
		}
	}
	
	foreach( $query_parts as $key => &$qp )
	{
		$qp = $key . '=' . $qp;
	}
	
	return home_url() . '?' . implode( '&', $query_parts );
}
endif;




if( ! function_exists( 'labs_get_acf_select_value' ) ):
function labs_get_acf_select_value( $name, $post_id = null )
{
	if( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	
	$object = get_field_object( $name, $post_id, array( 'load_value' => true, 'format_value' => false ) );
	
	if( 'null' === $object['value'] ) {
		$object['value'] = '';
	} elseif( ! empty( $object['choices'] ) && isset( $object['choices'][ $object['value'] ] ) ) {
		$object['value'] = $object['choices'][ $object['value'] ];
	}
	
	return $object['value'];
}
endif;


if( ! function_exists( 'labs_get_acf_string_value' ) ):
function labs_get_acf_string_value( $name, $post_id = null )
{
	if( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}

	$object = get_field_object( $name, $post_id, array( 'load_value' => true, 'format_value' => false ) );
	
	if( 'null' === $object['value'] || ! $object['value'] ) {
		$object['value'] = '';
	}
	
	return $object['value'];
}
endif;
