<?php
get_header();
?>
	<div id="primary">
		<div id="content" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<strong>Title: </strong><?php the_title(); ?><br />
                        <strong>City: </strong><?php echo get_post_meta(get_the_ID(),Sputnik::HOUSE_CITY_FIELD,true); ?><br />
					</header>

					<div class="entry-content">
                        <?
                            $image_id = get_post_meta(get_the_ID(), "_image_id", true);
                            $image_src = wp_get_attachment_url($image_id);
                        ?>
                        <img id="logo" src="<?=$image_src?>" style="max-width:100%;" />
                    </div>
                    <?php
                        $query = new WP_Query(array(
                            "post_type"=>"product",
                            "meta_key"=>Sputnik::PRODUCT_HOUSE_FIELD,
                            "meta_value"=>get_the_ID()
                        ));
                        $posts_ids = wp_list_pluck($query->posts, 'ID');
                    ?>
                    <?php if (!empty($posts_ids)):?>
                        <?php echo do_shortcode("[products ids=\"".implode(", ", $posts_ids)."\"]") ?>
                    <?php endif;?>
				</article>

			<?php endwhile; ?>
		</div>
	</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>