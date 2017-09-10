<?php
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) : ?>
				<header class="page-header">
					<h1 class="page-title"><?=post_type_archive_title()?></h1>
				</header>
				<?php while ( have_posts() ) : the_post();?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<strong><?php the_title()?></strong>
						<?php
							$image_id = get_post_meta(get_the_ID(), "_image_id", true);
							$image_src = wp_get_attachment_url($image_id);
						?>
						<img id="logo" src="<?=$image_src?>" style="max-width:100%;" />
						<a href="<?php echo get_post_permalink(get_the_ID())?>">View detail</a>
					</article>
				<?php endwhile; ?>
			<?php else :
				get_template_part( 'content', 'none' );
			endif; ?>

		</main>
	</div>

<?php
get_footer();