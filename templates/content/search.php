<?php //vtt_print('default:content:search'); ?>

<?php
if (!session_id()) {
    session_start();
}


global $searchandfilter, $wp_the_query, $post;
$is_sfpage = false;
$sf_names = array();
$sf_descriptions = array();
$site_id = "site-".get_current_blog_id();

// if there is a taxonomy term = archive then set session variable
// this prevents the archive term from being cleared when using the clear button
// this is only used with post type = reference
// sites with reference post type (i.e. k12-diversity) have two distinct archives
// that users usually only want to search separately
if ( isset($_GET['_sft_archive']) ) {
	$_SESSION[$site_id]['sft_archive'] = $_GET['_sft_archive'];
}


if ( isset($_GET['_sft_']) ) {
	unset($_SESSION[$site_id]['sft_archive']);
	//unset($_SESSION['sfid']);
}

if ( is_tax() && isset($_SESSION[$site_id]['sfid'] ) ) {   
	$term_slug = get_query_var( 'term' );
	$taxname = get_query_var( 'taxonomy' );
	$term_link = site_url().'/?sfid='.$_SESSION[$site_id]['sfid']."&".$sf_term.$taxname.'='.$term_slug; 
	if (isset($_SESSION[$site_id]['sft_archive']) && $post->post_type === 'references') {
		$term_link = $term_link.'&_sft_archive='.$_SESSION[$site_id]['sft_archive'];
	}
	//echo "<div class='searching'>taxonomies...</div>";
	wp_redirect( $term_link );
	exit();
} else if ( is_search() && isset($_SESSION[$site_id]['sfid'] ) ){
	$search_term = urlencode(get_search_query());
	$search_link = site_url().'/?sfid='.$_SESSION[$site_id]['sfid']."&".'_sf_s='.$search_term;
	if (isset($_SESSION[$site_id]['sft_archive']) && $post->post_type === 'references') {
		$search_link = $search_link.'&_sft_archive='.$_SESSION[$site_id]['sft_archive'];
	}
	echo "search link:";
	printpre($search_link);
	wp_redirect( $search_link );
	exit();
}

// printpre("Search SESSION</br>");
// printpre($_SESSION);
// printpre("Search GET</br>");
// printpre($_GET);


if( isset( $_SESSION[$site_id]['sfid'] ) ) {

	$sf_filter_slug = '/?sfid='.$_SESSION[$site_id]['sfid'].'&_sft_';
	$sf_id = $_SESSION[$site_id]['sfid'];
	$sf_clear_slug = '/?sfid='.$_SESSION[$site_id]['sfid'];
	$is_sfpage = true;
	
	// print out label for post type for use in display of # of posts found
	if( $post->post_type === 'references') {
		echo "<div class='sf-search-sort'>".do_shortcode('[searchandfilter id="'.$sf_id.'"]')."</div>";
		//if(function_exists('pf_show_link')){echo pf_show_link();}
		echo '<div class="found-posts">'.$wp_the_query->found_posts.' abstracts found.</div>';
	} else if( $post->post_type === 'plants') {
		echo '<div class="found-posts">'.$wp_the_query->found_posts.' plants found.</div>';
	} else {
		echo '<div class="found-posts">'.$wp_the_query->found_posts.' posts found.</div>';
	}
//	echo '<div class="found-posts">'.$wp_the_query->query['paged'].' of '.$wp_the_query->max_num_pages.'</div>';
	
	// print out list of current filters
	echo '<div class="current-filters">';
	echo '<h4>Current Selections</h4>';
	
	// URL for clear button
	// references post types (k16-diversity) do not clear the archive taxonomy term
	if( $post->post_type === 'references') {
		$sf_archive_slug = '&_sft_archive='.$_SESSION[$site_id]['sft_archive'];
		$clear_link = home_url().$sf_clear_slug.$sf_archive_slug;
	} else {
	 	$clear_link = home_url().$sf_clear_slug;
	}
	echo '<a class="clear-filters" href="' . esc_attr( $clear_link ) . '" title="Clear Filters">Clear</a>';
	$sfid = (int)( $_SESSION[$site_id]['sfid'] );
	$sf_inst = $searchandfilter->get( $sfid );
	
	// loop through taxonomy terms to display breadcrumbs
	foreach( $sf_inst->get_fields() as $field )
	{
		if( ! isset( $_GET[ $field ] ) ) {
			continue;
		}
		
		if( 0 === strpos( $field, '_sft_' ) )
		{
			$taxname = substr( $field, 5 );
			$taxonomy = get_taxonomy( $taxname );
			if( strpos( $_GET[ $field ], ' ' ) !== false ) {
				$terms = explode( ' ', $_GET[ $field ] );
			}
			else {
				$terms = explode( ',', $_GET[ $field ] );
			}
			
			foreach( $terms as $term_slug )
			{
				$term = get_term_by( 'slug', $term_slug, $taxname );
				echo '<div class="breadcrumb">';
				echo '<a class="remove" href="' . esc_attr( labs_get_sfp_remove_link( $term ) ) . '" title="Remove '. esc_attr($term->name) . ' from filter"></a>';
				echo $taxonomy->labels->name . ' &raquo; ';
				echo labs_get_taxonomy_breadcrumbs( $term->term_id, $taxname );
				
				if( $post->post_type === 'references') {
					$term_link = site_url().$sf_filter_slug.$taxname.'='.$term_slug.$sf_archive_slug;
					echo '<a href="' . $term_link . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
				} else {
					echo '<a href="' . esc_attr( get_term_link( $term, $taxname ) ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';			
				}
				
				//echo '<div class="page-term-description">' . apply_filters( 'term_description', $term->description ) . '</div>';
				echo '</div>';
				
				$sf_names[] = $term->name;
				$sf_descriptions[] = apply_filters( 'term_description', $term->description );
			}
		}
		elseif( 0 === strpos( $field, '_sfm_' ) )
		{
			$metaname = substr( $field, 5 );
		}
	}
	
	echo '</div>';	
} else {
	echo '<div class="found-posts">'.$wp_the_query->found_posts.' posts found.</div>';
}


// display all posts from current filter
if( have_posts() ):

	if( $is_sfpage ):

	?>
	<div class="page-title">
	<?php echo '<div class="sf-search-title">' . implode( ' / ', $sf_names ) . '</div>'; ?>
	</div>
	<?php
	
	endif;
	
	// print out taxonomy term descriptions
	if( $is_sfpage ) {
		foreach( $sf_descriptions as $description ) {
			echo '<div class="page-term-description">' . $description . '</div>';
		}
	} elseif( vtt_has_page_description() ) {
		echo '<div class="page-term-description">' . vtt_get_page_description() . '</div>';
	}

	vtt_get_template_part( 'listing', 'content', vtt_get_post_type() 
	
	);
	
else:

	?>
	<div class="page-title">

	<?php
	if( vtt_has_page_listing_name() )
		echo '<div class="listing-name">'.vtt_get_page_listing_name().'</div>';

	if( $is_sfpage ) {
		echo '<div class="sf-search-title">' . implode( ' / ', $sf_names ) . '</div>';
	} else { 
		echo '<h1>'.vtt_get_page_title().'</h1>';
	}
	?>
	
	</div>
	

	<?php
	if( $is_sfpage ) {
		foreach( $sf_descriptions as $description ) {
			echo '<div class="page-term-description">' . $description . '</div>';
		}
	} elseif( vtt_has_page_description() ) {
		echo '<div class="page-term-description">' . vtt_get_page_description() . '</div>';
	}
	?>
	
	<?php
	if( $is_sfpage ) {
	
		?>
		<p>Sorry, nothing matched your search criteria.</p>
		<?php
		
	} else {
	
		?>
		<p>Sorry, nothing matched your search criteria.</p>
		<?php
	}
	
endif;
?>
