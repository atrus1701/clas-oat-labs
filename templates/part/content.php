<?php //vtt_print('clas-oat-labs:part:content'); ?>


<?php
$widgets = wp_get_sidebars_widgets();
$class = '';
$sidebar_count = 0;
$use_left_sidebar = false;
$use_right_sidebar = false;

if( array_key_exists('vtt-left-sidebar', $widgets) && count($widgets['vtt-left-sidebar']) ):
	$use_left_sidebar = true;
	$sidebar_count++;
	$class .= ' left-sidebar';
endif;

if( array_key_exists('vtt-right-sidebar', $widgets) && count($widgets['vtt-right-sidebar']) ):
	$use_right_sidebar = true;
	$sidebar_count++;
	$class .= ' right-sidebar';
endif;

switch( $sidebar_count )
{
	case 0:
		$class = 'full-width' . $class;
		break;
	case 1:
		$class = 'one-sidebar-width' . $class;
		break;
	case 2:
		$class = 'two-sidebars-width' . $class;
		break;
}
?>


<div id="content-wrapper" class="<?php echo $class; ?> wrapper">
	<div id="content" class="<?php echo $class; ?>">
	
	<div class="clas-search">
	<form role="search" method="get" class="searchform" action="<?php echo esc_attr( site_url() ); ?>">
		<div>
			<label class="screen-reader-text" for="s">Search for:</label>
			<div class="textbox_wrapper">
				<input name="s" type="text" value="" placeholder="<?php echo esc_attr( $placeholder ); ?>" class="ui-autocomplete-input" autocomplete="off">
			</div>
			<input type="submit" id="searchsubmit" value="Search">
		</div>
	</form>
	</div>
	
	<?php vtt_get_template_part( vtt_get_page_content_type(), 'content', vtt_get_queried_object_type() ); ?>
	
	</div><!-- #content -->
</div><!-- #content-wrapper -->
