<?php //vtt_print('default:content:listing'); ?>
<?php
global $wp_query, $wp, $post, $searchandfilter;

//echo "</br>begin listing...</br>";
if (!session_id()) {
    session_start();
}

$is_mt = true;
$is_sf = false;
$search_term = "";
$sf_id = '';
$sf_term = '_sft_';
//$archive = NULL;

// if there is a taxonomy term = archive then set session variable
// this prevents the archive term from being cleared when using the clear button
// this is only used with post type = reference
// sites with reference post type (i.e. k12-diversity) have two distinct archives
// that users usually only want to search separately
if ( isset($_GET['_sft_archive']) ) {
	$_SESSION['sft_archive'] = $_GET['_sft_archive'];
}


if ( isset($_GET['sfid']) ) {
	$_SESSION['sfid'] = $_GET['sfid'];
// 	$sf_current_query = $searchandfilter->get($_SESSION['sfid'])->current_query();
// 	$archive = $sf_current_query->get_field_string("_sft_archive");
}

if ( isset($_GET['_sft_']) ) {
	unset($_SESSION['sft_archive']);
	//unset($_SESSION['sfid']);
}

//print_r($_SESSION);

if ( is_tax() && isset($_SESSION['sfid'] ) ) {   
	$term_slug = get_query_var( 'term' );
	$taxname = get_query_var( 'taxonomy' );
	$term_link = site_url().'/?sfid='.$_SESSION['sfid']."&".$sf_term.$taxname.'='.$term_slug; 
	if (isset($_SESSION['sft_archive'])) {
		$term_link = $term_link.'&_sft_archive='.$_SESSION['sft_archive'];
	}
	//echo "<div class='searching'>searching...</div>";
	wp_redirect( $term_link );
	exit();
} else if ( is_search() && isset($_SESSION['sfid'] ) ){
	$search_term = urlencode(get_search_query());
	$search_link = site_url().'/?sfid='.$_SESSION['sfid']."&".'_sf_s='.$search_term;
	if (isset($_SESSION['sft_archive'])) {
		$search_link = $search_link.'&_sft_archive='.$_SESSION['sft_archive'];
	}
	//echo "searching...";
	wp_redirect( $search_link );
	exit();
}
//echo "past url redirects...";
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
	while( have_posts() ):
		the_post();
		if( $post->post_type === 'references') {
			$year = labs_get_acf_select_value( 'publication_year' );
		}
		?>
		<div <?php post_class(); ?>>

		<h2 class="entry-title"><a href="<?php echo get_permalink($post->ID); ?>">
		<?php 
		
		if( $post->post_type === 'references') { 
			echo $number++.") ".$year." - ".$post->post_title."</a>";		
		} else {
			echo $post->post_title."</a>";
		}
		
		echo "</h2>";

			echo "<div id='common-name'>";
			echo the_terms( $post->ID, "common_names" );
			echo " (Location: ";
			echo the_terms( $post->ID, "locations" );
			echo " ";
			echo the_terms( $post->ID, "directions" );
			echo ")";
			echo "</div>";
		}
		vtt_get_template_part( 'listing', 'post', vtt_get_post_type(), $number );
		echo '</div>';
		//$number ++;
	endwhile;
 
	vtt_get_template_part( 'pagination', 'other', vtt_get_queried_object_type() );

};
?>

