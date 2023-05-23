<?php
/**
 * Shortcode [cmkk-form]
 *
 * @author  Marco Di Bella
 * @package cm-theme-addon-ticketpool
 */

namespace cm_theme_addon_ticketpool;


/** Prevent direct access */

defined( 'ABSPATH' ) or exit;



/**
 * Shortcode to create a form with which people can register as participants.
 *
 * @since 1.0.0
 *
 * @param array  $atts    The attributes (parameters) of the shorcode.
 * @param string $content The content bracketed by the shortcode.
 *
 * @return string The output generated by the shortcode.
 */

function cmkk_shortcode_form( $atts, $content = null )
{
    $code          = 0;
    $user_lastname = '';
    $user_forename = '';
    $user_email    = '';


    /* Process passed parameters */

    $default_atts  = array(
        'event_id' => '',
    );

    extract( shortcode_atts( $default_atts, $atts ) );

    if( empty( $event_id ) ) :      // move to output?
        return '';
    endif;


    /* Process form if already submitted */

    if( isset( $_POST['action'] ) ) :

        if( true === isset( $_POST['cmkk_lastname'] ) ) :
            $user_lastname = $_POST['cmkk_lastname'];
        endif;

        if( true === isset( $_POST['cmkk_forename'] ) ) :
            $user_forename = $_POST['cmkk_forename'];
        endif;

        if( true === isset( $_POST['cmkk_email'] ) ) :
            $user_email = $_POST['cmkk_email'];
        endif;

        $code = cmkk_add_user( $event_id, $user_lastname, $user_forename, $user_email );
    endif;


    /* Output the shortcode */

    ob_start();

    if( 0 !== $code ) :
        cmkk_display_notice( $code );
    endif;
    ?>
    <form class="cmkk-form" method="post" action="">
        <table>
            <tr>
                <th><?php echo __( 'Ihr Vorname', 'cm-tp'); ?></th>
                <td>
                    <input id="vorname" name="cmkk_forename" type="text" value="<?php echo $user_forename; ?>">
                </td>
            </tr>
            <tr>
                <th><?php echo __( 'Ihr Nachname', 'cm-tp'); ?></th>
                <td>
                    <input id="nachname" name="cmkk_lastname" type="text"value="<?php echo $user_lastname; ?>">
                </td>
            </tr>
            <tr>
                <th><?php echo __( 'Ihre E-Mail-Adresse', 'cm-tp'); ?></th>
                <td>
                    <input id="email" name="cmkk_email" type="email"value="<?php echo $user_email; ?>">
                </td>
            </tr>
            <tr>
                <th></th>
                <td>
                    <div class="cmkk-gdpr">
                        <input type="checkbox" id="gdpr" name="gdpr">
                        <label for="gdpr"><?php echo sprintf(
                            __( 'Ich willige ein, dass meine Daten zum Zwecke der Kontaktaufnahme gespeichert und verarbeitet werden. Weitere Informationen hierzu in der <a href="%1$s">Datenschutzerklärung</a>.', 'cm-tp' ),
                            get_privacy_policy_url(),
                        );
                        ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <div class="wp-block-button">
                        <button id="cmkk-submit" type="submit" class="wp-block-button__link" name="action" value="add" disabled="disabled"><?php echo __( 'Teilnahme anfordern', 'cm-tp' );?></button>
                    </div>
                </th>
                <td></td>
            </tr>
        </table>
        <input id="event-id" name="event-id" type="hidden" value="<?php echo $event_id; ?>">
    </form>
    <?php
    wp_enqueue_script(
        'cmkk-script',
        esc_url( plugins_url( 'assets/build/js/frontend.js', dirname( __FILE__ ) ) ) . '',
        array( 'jquery' ),
        false,
        true
    );    

    $output_buffer .= ob_get_contents();
    ob_end_clean();
    return $output_buffer;
}

add_shortcode( 'cmkk-form', __NAMESPACE__ . '\cmkk_shortcode_form' );
