<?php //vtt_print('default:post:listing'); ?>
<?php
global $vtt_config, $post;
$featured_image_position = $vtt_config->get_value( 'featured-image-position' );
if( $post->post_type === 'references') {
// 	$year_terms = get_the_terms($post, 'year');
// 	$year = $year_terms[0];
	$year = labs_get_acf_select_value( 'publication_year' );
}
?>


<div <?php post_class(); ?>>

	<h2 class="entry-title"><a href="<?php echo get_permalink($post->ID); ?>">
	<?php if( $post->post_type === 'references'): ?>
		<?php echo $year." - ".$post->post_title; ?></a>
	<?php else:?>
		<?php echo $post->post_title; ?></a>
	<?php endif; ?>
	</h2>

	<div class="description">
		
		<div class="entry-content">

			<?php if( $featured_image_position !== 'header' && has_post_thumbnail($post->ID) ): ?>
				<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' ); ?>
				<div class="featured-image <?php echo $featured_image_position; ?>">
					<img src="<?php echo $image[0]; ?>" title="Featured Image" />
				</div>
			<?php endif; ?>
			
			<?php if( $post->post_excerpt || is_search()): ?>
				<?php if( $post->post_type === 'references'): ?>
					Authors: 					
					<?php the_field('article-authors') ?>
				<?php endif; ?>
				<?php the_excerpt(); ?>
			<?php else:?>
				<?php if( $post->post_type === 'references'): ?>
					Authors: 					
					<?php the_field('article-authors') ?>
				<?php endif; ?>
				<?php the_content(); ?>
			<?php endif; ?>

			<?php wp_link_pages('before=<div id="page-links">&after=</div>'); ?>
			
		</div><!-- .entry-content -->

		<?php if( $post->post_type === 'post' ): ?>

		<?php endif; ?>
		
	</div><!-- .description -->

</div><!-- .post -->
