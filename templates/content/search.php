<?php //vtt_print('default:content:search'); ?>

<?php

global $searchandfilter, $wp_the_query, $post;
$is_sfpage = false;
$sf_names = array();
$sf_descriptions = array();

if( isset( $_GET ) && isset( $_GET['sfid'] ) ) {
	//$sf_filter_slug = '/?sfid=1724&_sft_';
	$sf_filter_slug = '/?sfid='.$_GET['sfid'].'&_sft_';
	$sf_id = $_GET['sfid'];
	$sf_clear_slug = '/?sfid='.$_GET['sfid'];
	$is_sfpage = true;
	if( $post->post_type === 'references') {
		echo "<div class='sf-search-sort'>".do_shortcode('[searchandfilter id="'.$sf_id.'"]')."</div>";
		//if(function_exists('pf_show_link')){echo pf_show_link();}
		echo '<div class="found-posts">'.$wp_the_query->found_posts.' abstracts found.</div>';
	} else {
		echo '<div class="found-posts">'.$wp_the_query->found_posts.' posts found.</div>';
	}
//	echo '<div class="found-posts">'.$wp_the_query->query['paged'].' of '.$wp_the_query->max_num_pages.'</div>';
	echo '<div class="current-filters">';
	echo '<h4>Current Selections</h4>';
	if( $post->post_type === 'references') {
		$sf_archive_slug = '&_sft_archive='.$_SESSION['sft_archive'];
		$clear_link = home_url().$sf_clear_slug.$sf_archive_slug;
	} else {
	 	home_url();
	}
	echo '<a class="clear-filters" href="' . esc_attr( $clear_link ) . '" title="Clear Filters">Clear</a>';
	$sfid = (int)( $_GET['sfid'] );
	$sf_inst = $searchandfilter->get( $sfid );
	
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



if( have_posts() ):

	if( $is_sfpage ):

	?>
	<div class="page-title">
	<?php echo '<div class="sf-search-title">' . implode( ' / ', $sf_names ) . '</div>'; ?>
	</div>
	<?php
	
	endif;
	
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
		<p>No posts found that matched the current selections.</p>
		<?php
		
	} else {
	
		?>
		<p>Sorry, but nothing matched your search criteria. Please try again with some different keywords.</p>
		<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>" >
			<label class="screen-reader-text" for="s">Search for:</label>
			<div class="textbox_wrapper"><input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" /></div>
			<input type="submit" id="searchsubmit" value="Search" />
		</form>
		<?php
	}
	
endif;
?>
