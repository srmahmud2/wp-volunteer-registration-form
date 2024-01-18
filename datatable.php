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
//table code starts here
global $wpdb;
$table_name = $wpdb->prefix . 'volunteers';
$result = $wpdb->get_results("select * from $table_name");

function renderTableHeaderFooter() {
    $headers = [
        'ID','Volunteer ID', 'Data Inscrição', 'Primeiro Nome', 'Sobrenome',
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
        <tbody>
            <?php if (!empty($result)) {
                foreach ($result as $volunteer) { ?>
                    <tr>
                        <td><?php echo esc_html($volunteer->id); ?></td>
                        <td><?php echo esc_html($volunteer->volunteer_id); ?></td>
                        <td><?php echo esc_html($volunteer->data_inscricao); ?></td>
                        <td><?php echo esc_html($volunteer->first_name); ?></td>
                        <td><?php echo esc_html($volunteer->last_name); ?></td>
                        <td><?php echo esc_html($volunteer->post_code); ?></td>
                        <td><?php echo esc_html($volunteer->morada); ?></td>
                        <td><?php echo esc_html($volunteer->localidade); ?></td>
                        <td><?php echo esc_html($volunteer->telemovel); ?></td>
                        <td><?php echo esc_html($volunteer->volunteer_email); ?></td>
                        <td><?php echo esc_html($volunteer->education); ?></td>
                        <td><?php echo esc_html($volunteer->profession); ?></td>
                        <td><?php echo esc_html($volunteer->encaminhado); ?></td>
                        <td><?php echo esc_html($volunteer->a_date); ?></td>
                        <td><?php echo esc_html($volunteer->pref1); ?></td>
                        <td><?php echo esc_html($volunteer->pref2); ?></td>
                        <td><?php echo esc_html($volunteer->pref3); ?></td>
                        <td><?php echo esc_html($volunteer->pref_other); ?></td>
                    </tr>
                <?php }
            } ?>
        </tbody>
        <tfoot>
            <?php renderTableHeaderFooter(); ?>
        </tfoot>
    </table>
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
