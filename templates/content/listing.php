<?php //vtt_print('default:content:listing'); ?>
<?php
$is_mt = false;
if( function_exists('mt_is_archive') && function_exists('mt_is_search') && 
	( mt_is_archive() || mt_is_search() ) )
{
	$is_mt = true;
}
?>

<div class="page-title">
	
	<?php
	if( $is_mt )
	{
		$filter_terms = array();
		$current_filters = mt_get_current_filter_data();
		foreach( $current_filters['taxonomies'] as $taxname => $terms )
		{
			$taxonomy = get_taxonomy( $taxname );
			foreach( $terms as $term_slug )
			{
				$term = get_term_by( 'slug', $term_slug, $taxname );
				$link = vtt_get_anchor(
					get_term_link( $term, $taxname ),
					$term->name,
					null,
					$term->name
				);
				if( $term ) {
					echo '<div class="breadcrumbs">' .
						$taxonomy->label .
						' &raquo; ' .
 						vtt_get_taxonomy_breadcrumbs( $term->term_id, $taxname ) .
 						$link .
						'</div>';
						
					$filter_terms[] = $term;
				}
			}
		}
	}
	elseif( is_a( get_queried_object(), 'WP_Term' ) )
	{
		$qo = get_queried_object();
		echo '<div class="breadcrumbs">' .
			vtt_get_taxonomy_breadcrumbs( $qo->term_id, $qo->taxonomy ) .
			'</div>';
	}
	?>
	
	<?php
	if( vtt_has_page_listing_name() )
		echo '<div class="listing-name">'.vtt_get_page_listing_name().'</div>';
	?>

	<?php
	if( $is_mt )
	{
		echo '<h1>';
		if( count( $filter_terms ) > 0 ) {
			$term_names = array();
			foreach( $filter_terms as $term ) {
				$term_names[] = $term->name;
			}
			echo implode( ' / ', $term_names );
		}
		elseif( mt_is_filtered_archive() ) {
			echo 'Filtered Results';
		}
		elseif( mt_is_combined_archive() ) {
			echo 'Combined Results';
		}
		elseif( mt_is_filtered_search() ) {
			echo 'Filtered Search Results';
		}
		elseif( mt_is_combined_search() ) {
			echo 'Combined Search Results';
		}
		else {
			echo 'Archives';
		}
		echo '</h1>';
	}
	elseif( !is_home() )
	{
		echo '<h1>'.vtt_get_page_title().'</h1>';
	}
	?>

</div>


<?php
if( $is_mt && count( $filter_terms ) > 0 ) {
	foreach( $filter_terms as $term ) {
		echo '<div class="description">' . apply_filters( 'term_description', $term->description ) . '</div>';
	}
}
elseif( vtt_has_page_description() ) {
	echo '<div class="description">' . vtt_get_page_description() . '</div>';
}
?>


<?php
if( !have_posts() ):

	echo '<p>No posts.</p>';

else:

	while( have_posts() ):
		the_post();
		vtt_get_template_part( 'listing', 'post', vtt_get_post_type() );
	endwhile;
 
	vtt_get_template_part( 'pagination', 'other', vtt_get_queried_object_type() );

endif;
?>

