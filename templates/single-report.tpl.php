<?php
/**
 * @file
 * Custom page template for the powerbi embedded dashboard.
 */
?>
<?php get_header('powerbi-reports'); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

  <div class="powerbi">

    <section class="powerbi__dashboard <?php echo dt_sidebar_position($post->ID); ?>">
      <article id="page-<?php the_ID(); ?>" class="begin-content">
        <?php the_content(); ?>
      </article>
    </section>
  </div><!--end dashboard-->

<?php endwhile; ?>
<?php endif; ?>


<?php get_footer(); ?>