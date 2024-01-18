<?php 
/* Template Name: Data Form */ 

// Get the registration message from transient or user meta
// $message_code = get_transient('registration_message') ?: (isset($_GET['message']) ? $_GET['message'] : '');
$message = isset($_GET['message']) ? $_GET['message'] : '';
get_header(); ?>

<?php do_action( 'ocean_before_content_wrap' ); ?>

<div id="content-wrap" class="container clr">

    <?php do_action( 'ocean_before_primary' ); ?>

    <div id="primary" class="content-area clr">

        <?php do_action( 'ocean_before_content' ); ?>

        <div id="content" class="site-content clr">

            <?php do_action( 'ocean_before_content_inner' ); ?>

            <?php
				// Elementor `single` location.
				if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
?>
            <form id="volunteerForm" method="post" onsubmit="return validateForm()">
                <?php wp_nonce_field('volunteer_form_nonce', 'nonce_field'); ?>
                <input type="hidden" name="action" value="register_volunteer_form"/>

                <div class="form-group">
                    <label for="volunteer_id">ID</label>
                    <input type="number" name="volunteer_id" id="volunteer_id" placeholder="Enter ID">
                    <span class="error-message" id="error-volunteer_id"></span>
                </div>

                <div class="form-group">
                    <label for="data_inscricao">Data Inscrição</label>
                    <input type="date" name="data_inscricao" id="data_inscricao" placeholder="Select Date">
                    <span class="error-message" id="error-data_inscricao"></span>
                </div>

                <div class="form-group">
                    <label for="first_name">Primeiro Nome *</label>
                    <input type="text" name="first_name" id="first_name" placeholder="Enter First Name" >
                    <span class="error-message" id="error-first_name"></span>
                </div>

                <div class="form-group">
                    <label for="last_name">Sobrenome *</label>
                    <input type="text" name="last_name" id="last_name" placeholder="Enter Last Name" >
                    <span class="error-message" id="error-last_name"></span>
                </div>

                <div class="form-group">
                    <label for="post_code">Código Postal</label>
                    <input type="text" name="post_code" id="post_code" placeholder="Enter Postal Code">
                    <span class="error-message" id="error-post_code"></span>
                </div>

                <div class="form-group">
                    <label for="morada">Morada</label>
                    <input type="text" name="morada" id="morada" placeholder="Enter Address">
                    <span class="error-message" id="error-morada"></span>
                </div>

                <div class="form-group">
                    <label for="localidade">Localidade *</label>
                    <input type="text" name="localidade" id="localidade" placeholder="Enter City" >
                    <span class="error-message" id="error-localidade"></span>
                </div>

                <div class="form-group">
                    <label for="telemovel">Telemóvel *</label>
                    <input type="tel" name="telemovel" id="telemovel" placeholder="Enter Telemóvel Number" >
                    <span class="error-message" id="error-telemovel"></span>
                </div>

                <div class="form-group">
                    <label for="volunteer_email">Email *</label>
                    <input type="email" name="volunteer_email" id="volunteer_email" placeholder="Enter Email" >
                    <span class="error-message" id="error-volunteer_email"></span>
                </div>

                <div class="form-group">
                    <label for="education">Educação</label>
                    <input type="text" name="education" id="education" placeholder="Enter Educação">
                    <span class="error-message" id="error-education"></span>
                </div>

                <div class="form-group">
                    <label for="profession">Profissão</label>
                    <input type="text" name="profession" id="profession" placeholder="Enter Profissão">
                    <span class="error-message" id="error-profession"></span>
                </div>
                <div class="form-group">
                    <label for="encaminhado">Encaminhado</label>
                    <input type="text" name="encaminhado" id="encaminhado" placeholder="Enter Encaminhado">
                    <span class="error-message" id="error-encaminhado"></span>
                </div>
                <div class="form-group">
                    <label for="a_date">A Date</label>
                    <input type="date" name="a_date" id="a_date" placeholder="Select Date">
                    <span class="error-message" id="error-a_date">
                        <?php if (isset($a_date_error)) echo esc_html($a_date_error); ?>
                    </span>
                </div>
                <div class="form-group">
                    <label for="pref1">Preferência -1</label>
                    <select id="pref1" name="pref1">
                        <option value="">Select</option>
                        <option value="social">Social</option>
                        <option value="elderly">Elderly</option>
                        <option value="children">Children</option>
                    </select>
                    <span class="error-message" id="error-pref1"></span>
                </div>

                <div class="form-group">
                    <label for="pref2">Preferência -2</label>
                    <select id="pref2" name="pref2">
                        <option value="">Select</option>
                        <option value="social">Social</option>
                        <option value="elderly">Elderly</option>
                        <option value="children">Children</option>
                    </select>
                    <span class="error-message" id="error-pref2"></span>
                </div>

                <div class="form-group">
                    <label for="pref3">Preferência -3</label>
                    <select id="pref3" name="pref3">
                        <option value="">Select</option>
                        <option value="social">Social</option>
                        <option value="elderly">Elderly</option>
                        <option value="children">Children</option>
                    </select>
                    <span class="error-message" id="error-pref3"></span>
                </div>

                <div class="form-group">
                    <label for="pref_other">Preferências - outras</label>
                    <textarea id="pref_other" name="pref_other" rows="4"
                        placeholder="Por favor, mencione suas outras preferências."></textarea>
                        <span class="error-message" id="error-pref_other"></span>
                </div>

                <div class="form-group submit">
                    <input type="submit" name="register" value="Register">
                    
                    <?php if (!empty($message)): ?>
                        <p id="registration-message"><?php echo esc_html($message); ?></p>
                    <?php endif; ?>
                </div>
            </form>

            <?php
	
					
					//Start loop.
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