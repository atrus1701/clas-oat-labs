<?php //vtt_print('clas-oat-labs:part:content'); ?>


<?php
global $searchandfilter, $post;

// requires a custom field named "sfid" with the id of the search and filter
// pro form as its value on the front page
$site_id = "site-".get_current_blog_id();

if (!isset($_SESSION[$site_id]['sfid']) ) {
	$frontpage_id = get_option( 'page_on_front' );
	$_SESSION[$site_id]['sfid'] = get_field("sfid", $frontpage_id);
	printpre ($_SESSION[$site_id]['sfid']);
}

if (isset($searchandfilter) && isset($_SESSION[$site_id]['sfid']) ) {
	$sf_current_query = $searchandfilter->get($_SESSION[$site_id]['sfid'])->current_query();
	$sf_search_term = $sf_current_query->get_search_term();
}

if ( isset ($sf_search_term) ) {
	$search_term = $sf_search_term;
} else {
	$search_term = get_search_query();
}

$sf_term = '_sft_';

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
	printpre($term_link);
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
				<input name="s" type="text" value="<?php echo $search_term ?>" placeholder="<?php echo esc_attr( "Search..." ); ?>" class="ui-autocomplete-input" autocomplete="off">
			</div>
			<input type="submit" id="searchsubmit" value="Search">
		</div>
	</form>
	</div>
	
	<?php vtt_get_template_part( vtt_get_page_content_type(), 'content', vtt_get_queried_object_type() ); ?>
	
	</div><!-- #content -->
</div><!-- #content-wrapper -->
