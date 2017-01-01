<?php
/*
Template Name: Section
*/
?>

<?php get_header(); ?>



<section class="content">

	

	<?php get_template_part('parts/page-title'); ?> 

	

	<div class="pad group">
        
   <!-- Add country/category
	<div class="category">Category: <?php // the_category(', '); ?></div> -->
  
	

		<?php while ( have_posts() ): the_post(); ?>

		

			<article <?php post_class('group'); ?>>

				

				<?php get_template_part('inc/page-image'); ?>

				

				<div class="entry themeform">

					<?php the_content(); ?>
                    
 				   
                 

					<div class="clear"></div>

				</div><!--/.entry-->
             

                <!-- Add last modified -->
                <hr />
				<div class="lastmodified">This page last modified: <?php the_modified_date(); ?></div>

			</article>

			

			<?php if ( ot_get_option('page-comments') == 'on' ) { comments_template('/comments.php',true); } ?>

			

		<?php endwhile; ?>

		

	</div><!--/.pad-->

	

</section><!--/.content-->



<?php get_sidebar(); ?>



<?php get_footer(); ?>