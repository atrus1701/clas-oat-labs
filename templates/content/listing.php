<?php //vtt_print('default:content:listing'); ?>
<?php
global $wp_query, $wp, $post, $searchandfilter;

//echo "</br>begin listing...</br>";
//$archive = NULL;

?>

<div class="page-title">
	
<?php
$filter_terms = array();

// current_filters = currently filtered taxonomies and post types
$current_filters = labs_get_current_filter_data();
foreach( $current_filters['taxonomies'] as $taxname => $terms )
{
	$taxonomy = get_taxonomy( $taxname );
	foreach( $terms as $term_slug )
	{
		$term = get_term_by( 'slug', $term_slug, $taxname );
		$link = labs_get_anchor(
			get_term_link( $term, $taxname ),
			$term->name,
			null,
			$term->name
		);
						
		if( $post->post_type === 'references') {					
			$term_link = site_url().$sf_filter_slug.$taxname.'='.$term_slug;
			$title = $term->name;
			$content = $term->name;
			$link = labs_get_anchor(
				$term_link,
				$term->name,
				null,
				$term->name
			);				
		
		}
					
		if( $term ) {
			$post_count = $wp_query->found_posts;
			echo '<div class="found-posts">'.$post_count.' posts found</div>';
		//	echo '<div class="current-filters"><h4>Current Selection</h4></div>';
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

if( vtt_has_page_listing_name() )
	echo '<div class="listing-name">'.vtt_get_page_listing_name().'</div>';

if( count( $filter_terms ) > 0 ) {
	$term_names = array();
	foreach( $filter_terms as $term ) {
		$term_names[] = $term->name;
	}
	echo '<h1>';
	echo implode( ' / ', $term_names );
	echo '</h1>';
}

?>

</div>


<?php
if( count( $filter_terms ) > 0 ) {
	foreach( $filter_terms as $term ) {
		echo '<div class="description">' . apply_filters( 'term_description', $term->description ) . '</div>';
	}
}
elseif( vtt_has_page_description() ) {
	echo '<div class="description">' . vtt_get_page_description() . '</div>';
}
?>


<?php
if( !have_posts() ) {

	echo '<p>No posts.</p>';

} else {
	
	$number = 1;
	// print out header for Harwood site			
	if( $post->post_type === 'plants' && is_object(get_taxonomy( 'genus')) ) { 
	?>
		<div class="divTable plants-table">
		<div class="divTableHeading">
		<div class="divTableRow">
		<div class="divTableHead">Botanical Name</div>
		<div class="divTableHead">Location</div>
		<div class="divTableHead">Common Name</div>
		</div>
		</div>
		<div class="divTableBody">	
	<?php 
	} 
	
	while( have_posts() ):
		the_post();
		if( $post->post_type === 'references') {
			$year = labs_get_acf_select_value( 'publication_year' );
		}
		
		if( $post->post_type != 'plants' || !is_object(get_taxonomy( 'genus')) ) {
		?>
		<div <?php post_class(); ?>>
		<?php 
		}
				
		// print out title, taxonomy and field data for k16-diversity site
		if( $post->post_type === 'references') { 
			echo "<h2 class='entry-title'><a href=".get_permalink($post->ID).">";			
			echo $number++.") ".$year." - ".$post->post_title."</a></h2>";
			echo '</div>';
		
		// print out title, taxonomy and field data for Harwood site			
		} else if( $post->post_type === 'plants' && is_object(get_taxonomy( 'genus')) ) { 
			echo "<div class='divTableRow'>";
			echo "<div class='divTableCell'>";
			echo "<a href=".get_permalink($post->ID).">".$post->post_title."</a>";
			echo "</div>";
			echo "<div class='divTableCell'>";
			echo the_terms( $post->ID, "locations" );
			echo "</div>";
			echo "<div class='divTableCell'>";
			echo the_terms( $post->ID, "common_names" );
			echo "</div>";
			echo "</div>";		
		
		// print out title, taxonomy and field data for Glen site	
		} else if( $post->post_type === 'plants' ) {
			echo "<h2 class='entry-title'><a href=".get_permalink($post->ID).">";
			echo $post->post_title."</a></h2>";
			echo "<div id='common-name'>";
			echo the_terms( $post->ID, "common_names" );
			echo " (Location: ";
			echo the_terms( $post->ID, "locations" );
			echo " ";
			echo the_terms( $post->ID, "directions" );
			echo ")";
			echo "</h2>";
			echo "</div>";
		} else {
			echo "<h2 class='entry-title'><a href=".get_permalink($post->ID).">";
			echo $post->post_title."</a></h2>";
			echo '</div>';
		}		
		
		if( $post->post_type != 'plants' || !is_object(get_taxonomy( 'genus')) ) {
			vtt_get_template_part( 'listing', 'post', vtt_get_post_type(), $number );
		}
		
		//$number ++;
	endwhile;
	
	if( $post->post_type === 'plants' && is_object(get_taxonomy( 'genus')) ) { 
		echo "</div></div>";
	}

	vtt_get_template_part( 'pagination', 'other', vtt_get_queried_object_type() );

};
?>

