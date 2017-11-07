<?php //vtt_print('default:content:single'); ?>
<?php
global $post;
$featured_image_position = $vtt_config->get_value( 'featured-image-position' );
if( $post->post_type === 'references') {
// 	$year_terms = get_the_terms($post, 'year');
// 	$year = $year_terms[0];
	$year = labs_get_acf_select_value( 'publication_year' );
}
?>


<div class="page-title">
	<div class="breadcrumbs"><?php echo vtt_get_breadcrumbs( $post ); ?></div>
	<?php if( $post->post_type === 'references'): ?>
		<?php echo '<h1>'.$year." - ".vtt_get_page_title().'</h1>'; ?>
	<?php elseif( $post->post_type === 'plants'): ?>
		<?php echo '<h1>'.vtt_get_page_title().'</h1>'; ?>
		<?php echo "<div id='common-name'>"; ?>
		<?php echo the_terms( $post->ID, "common_names" ); ?>
		<?php echo "</div>"; ?>	
	<?php else:?>	
		<?php echo '<h1>'.vtt_get_page_title().'</h1>'; ?>
	<?php endif; ?>
</div>


<div <?php post_class(); ?>>

	<div class="details">

	<div class="entry-content">
	
		<?php if( $featured_image_position !== 'header' && has_post_thumbnail($post->ID) ): ?>
			<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' ); ?>
			<div class="featured-image <?php echo $featured_image_position; ?>">
				<img src="<?php echo $image[0]; ?>" title="Featured Image" />
			</div>
		<?php endif; ?>
		
		<?php if( $post->post_type === 'references'): ?>
		
			<div class="ref-field">
			<span class="ref-label ref-author-label">Attribution:</span> 
			<?php echo labs_get_acf_string_value( 'article-authors' ); ?>
			</div>
			<div class="ref-field"> 
			<?php echo labs_get_taxonomy_terms('researcher', $post); ?>
			</div>
			<div class="ref-field">
			<span class="ref-label ref-university-label">University Affiliation:</span> 
			<?php echo labs_get_acf_string_value( 'university' ); ?>
			</div>
			<div class="ref-field">
			<span class="ref-label ref-email-label">Email:</span> 
			<?php echo labs_get_acf_string_value( 'email' ); ?>
			</div>
			<div class="ref-field">
			<div class="ref-label ref-question-label">Research Question:</div> 
			<?php echo get_post_meta( $post->ID, 'research_question', true ); ?>
			</div>
			<div class="ref-field">
			<span class="ref-label ref-published-label">Published:</span> 
			<?php echo labs_get_acf_select_value( 'published' ); ?>
			</div>
			<div class="ref-field">
			<span class="ref-label ref-journal-label">Journal Name or Institutional Affiliation:</span> 
			<?php echo labs_get_taxonomy_terms('journal', $post, "true", "false"); ?>	
			</div>
			<div class="ref-field">
			<span class="ref-label ref-entry-label">Journal Entry:</span> 
			<?php the_field('journal-entry'); ?>
			</div>
			<div class="ref-field">
			<span class="ref-label ref-year-label">Year:</span> 
			<?php
			if (labs_get_taxonomy_terms('year', $post, "true", "false") != "") {
				echo labs_get_taxonomy_terms('year', $post, "true", "false");
			} else {
				echo labs_get_acf_select_value( 'publication_year' ); 
			}
			?>	
			</div>			
			<span class="ref-label ref-findings-label">Findings:</span>	
		<?php endif; ?>
		 
		<?php echo apply_filters('the_content', $post->post_content); ?>
		
		<?php wp_link_pages('before=<div id="page-links">&after=</div>'); ?>
		
	</div><!-- .entry-content -->

	<?php if( $post->post_type === 'lab'): ?>
		<?php echo labs_get_taxonomy_list( 'building', $post, 3 ); ?>
		<?php echo labs_get_taxonomy_list('department', $post, 3); ?>
		<?php echo labs_get_taxonomy_list('operating-system', $post, 3); ?>
		<?php echo labs_get_taxonomy_list('category', $post, 3); ?>
		<?php echo labs_get_taxonomy_list('availability', $post, 3); ?>
		<?php echo labs_get_taxonomy_list('software', $post, 3); ?>
		<?php echo labs_get_taxonomy_terms('technique', $post, 3); ?>
	<?php endif; ?>
	
	<?php if( $post->post_type === 'post'): ?>
		<?php echo labs_get_taxonomy_terms('platform', $post); ?>
		<?php echo labs_get_taxonomy_terms('vendor', $post); ?>
		<?php echo labs_get_taxonomy_terms('category', $post); ?>
		<?php echo labs_get_taxonomy_terms('technique', $post); ?>
		<?php echo labs_get_taxonomy_terms('discipline', $post); ?>
	<?php endif; ?>

	<?php if( $post->post_type === 'references'): ?>
		<?php echo labs_get_taxonomy_terms('scholarship', $post); ?>
		<?php echo labs_get_taxonomy_terms('keyword', $post, 3); ?>
		<?php echo labs_get_taxonomy_terms('region', $post); ?>
		<?php echo labs_get_taxonomy_terms('methodology', $post); ?>
		<?php echo labs_get_taxonomy_terms('research-design', $post); ?>
		<?php echo labs_get_taxonomy_terms('analysis-methods', $post); ?>
		<span class="ref-label ref-frame-label">Sampling Frame:</span> 
		<?php the_field('sampling_frame'); ?>
		</div>
		<?php echo labs_get_taxonomy_terms('sampling-type', $post); ?>
		<?php echo labs_get_taxonomy_terms('analysis-unit', $post); ?>
		<?php echo labs_get_taxonomy_terms('data-types', $post); ?>
		
		<div class="ref-field">
		<div class="ref-label ref-published-label">Data Description:</div> 
		<?php the_field('data_description'); ?>
		</div>
		<div class="ref-field">
		<span class="ref-label ref-question-label">Relevance:</span> 
		<?php the_field('relevance'); ?>
		</div>

							
		<?php echo labs_get_taxonomy_terms('archive', $post); ?>
	<?php endif; ?>
	
	
	</div><!-- .details -->

	<?php comments_template() ?>
	
</div><!-- .post -->
