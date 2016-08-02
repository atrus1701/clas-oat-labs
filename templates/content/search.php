<?php //vtt_print('default:content:search'); ?>


<?php

global $searchandfilter, $wp_the_query;
$is_sfpage = false;
$sf_names = array();
$sf_descriptions = array();

if( isset( $_GET ) && isset( $_GET['sfid'] ) )
{
	$is_sfpage = true;
	echo '<div class="found-posts">'.$wp_the_query->found_posts.' posts found.</div>';
	
	echo '<div class="current-filters">';
	echo '<h4>Current Selections</h4>';
	echo '<a class="clear-filters" href="' . esc_attr( home_url() ) . '" title="Clear Filters">Clear</a>';
	
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
			
			foreach( $terms as $term )
			{
				$term = get_term_by( 'slug', $term, $taxname );
				echo '<div class="breadcrumb">';
				echo '<a class="remove" href="' . esc_attr( labs_get_sfp_remove_link( $term ) ) . '" title="Remove '. esc_attr($term->name) . ' from filter"></a>';
				echo $taxonomy->labels->name . ' &raquo; ';
				echo vtt_get_taxonomy_breadcrumbs( $term->term_id, $taxname );
				echo '<a href="' . esc_attr( get_term_link( $term, $taxname ) ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
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
}



if( have_posts() ):

	if( $is_sfpage ):

	?>
	<div class="page-title">
	<?php echo '<h1>' . implode( ' / ', $sf_names ) . '</h1>'; ?>
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

	vtt_get_template_part( 'listing', 'content', vtt_get_post_type() );
	
else:

	?>
	<div class="page-title">

	<?php
	if( vtt_has_page_listing_name() )
		echo '<div class="listing-name">'.vtt_get_page_listing_name().'</div>';

	if( $is_sfpage ) {
		echo '<h1>' . implode( ' / ', $sf_names ) . '</h1>';
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
		<p>No posts found that matched the current selections.  Try removing a keyword from the selections to get better results.</p>
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
