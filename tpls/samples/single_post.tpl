<?php
/**
 * Sample template for displaying single [+post_type+] posts.
 * Save this file as as single-[+post_type+].php in your current theme.
 *
 * This sample code was based off of the Starkers Baseline theme: http://starkerstheme.com/
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	
[+built_in_fields+]

		<h2>Custom Fields</h2>	
		
[+custom_fields+]

[+comments+]

<?php endwhile; // end of the loop. ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>