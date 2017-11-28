<?php // vtt_print('default:other:pagination'); ?>
<?php global $wp_query; ?>

<?php if( $wp_query->max_num_pages > 1 ): ?>

	<div id="page-navigation" role="navigation">
	<?php if( function_exists('wp_pagenavi') ) : ?>
		<?php  wp_pagenavi(); ?>
	<?php else : ?>
		<div class="nav-next">
			<?php next_posts_link( '&laquo; Older Items' ); ?>
		</div>
		<div class="nav-prev">
			<?php previous_posts_link( 'Newer Items &raquo;' ); ?>
		</div>
	<?php endif; ?>
	</div>

<?php endif; ?>
