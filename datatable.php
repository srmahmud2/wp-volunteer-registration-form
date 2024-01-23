<?php 
/* Template Name: Data Table */ 


get_header(); ?>

	<?php do_action( 'ocean_before_content_wrap' ); ?>

	<div id="content-wrap" class="container clr">

		<?php do_action( 'ocean_before_primary' ); ?>

		<div id="primary" class="content-area clr" style="width: 100%;">

			<?php do_action( 'ocean_before_content' ); ?>

			<div id="content" class="site-content clr">

				<?php do_action( 'ocean_before_content_inner' ); ?>

				<?php
				// Elementor `single` location.
				if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {

?>
<?php

function renderTableHeaderFooter() {
    $headers = [
        'Edit', 'Delete', 'ID','Volunteer ID', 'Data Inscrição', 'Primeiro Nome', 'Sobrenome',
        'Código Postal', 'Morada', 'Localidade','Telemovel', 'Email',
        'Educação', 'Profissão', 'Encaminhado', 'A Date',
        'Preferência -1', 'Preferência -2', 'Preferência -3', 'Preferências - outras'
    ];

    echo '<tr>';
    foreach ($headers as $header) {
        echo '<th>' . esc_html($header) . '</th>';
    }
    echo '</tr>';
}
?>
					<div class="datatable-area">
						<table id="volunteerTable" class="table table-striped volunteer-table" style="width:100%">
							<thead>
								<?php renderTableHeaderFooter(); ?>
							</thead>
							<!-- <tbody> dynamically generated from volunteer-datatables -->
							<tfoot>
								<?php renderTableHeaderFooter(); ?>
							</tfoot>
						</table>
						<div id="spinner" class="fa-3x" style="display: none;">
							<i class="fas fa-spinner fa-spin"></i>
						</div>
						<!-- //display error/success message here -->
						<div id="form-errors" class="text-danger" style="display: none;"></div>
						<div id="form-success" class="text-success" style="display: none;"></div>
						
					</div>
<?php
					// Start loop.
					while ( have_posts() ) :
						the_post();

						get_template_part( 'partials/page/layout' );

					endwhile;

				}
				?>

				<?php do_action( 'ocean_after_content_inner' ); ?>

			</div><!-- #content -->

			<?php do_action( 'ocean_after_content' ); ?>

		</div><!-- #primary -->

		<?php do_action( 'ocean_after_primary' ); ?>

	</div><!-- #content-wrap -->

	<?php do_action( 'ocean_after_content_wrap' ); ?>

<?php get_footer(); ?>
