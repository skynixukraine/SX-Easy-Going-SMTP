<?php
/**
 * SX Easy-going SMTP settings
 */

class SX_SMTP_settings
{
    public $args;

    public function __construct( $args ) {
        $this->args = $args;
    }

    public function init() {
        // Register settings options
        add_action( 'admin_init', array( $this, 'sx_smtp_settings_fields' ) );
        // Create settings subpage
        add_action( 'admin_menu', array( $this, 'sx_smtp_add_settings_page' ) );
    }

    /**
     * Register new settings
     */
    public function sx_smtp_settings_fields(){
        register_setting( 'sx-smtp-settings', $this->args["host"] );
        register_setting( 'sx-smtp-settings', $this->args["port"] );
        register_setting( 'sx-smtp-settings', $this->args["username"] );
        register_setting( 'sx-smtp-settings', $this->args["password"] );
        register_setting( 'sx-smtp-settings', $this->args["enabled"] );
    }

    /**
     * Add admin menu item
     */
    public function sx_smtp_add_settings_page(){
        add_options_page(
            __( "SX Easy-going SMTP settings", $this->args["textdomain"] ),
            __( "SX Easy-going SMTP settings", $this->args["textdomain"] ),
            "manage_options",
            "sx-smtp-settings.php",
            array( $this, 'sx_smtp_settings_page_content' )
        );
    }

    public function sx_smtp_test_mail( $to_email, $subject, $message ) {
        $settings["enabled"]  = get_option( $this->args["enabled"] );
        $settings["host"]     = get_option( $this->args["host"] );
        $settings["port"]     = get_option( $this->args["port"] );
        $settings["username"] = get_option( $this->args["username"] );
        $settings["password"] = get_option( $this->args["password"] );

        require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
        $mail = new PHPMailer();

        $mail->CharSet = get_bloginfo( 'charset' );
        $mail->IsSMTP();
        // send plain text test email
        $mail->ContentType = 'text/plain';
        $mail->IsHTML( false );
        $mail->SMTPAuth	  = true;
        $mail->SMTPSecure = 'tls';
        $mail->Username	  = $settings[ 'username' ];
        $mail->Password	  = $settings['password'];

        /* Set the other options */
//        $mail->SetFrom( 'test@test.net', 'test' );
        $mail->Host	   = $settings[ 'host' ];
        $mail->Port	   = $settings[ 'port' ];
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AddAddress( $to_email );
        global $debugMSG;
        $debugMSG		 = '';
        $mail->Debugoutput	 = function( $str ) {
            global $debugMSG;
            $debugMSG .= $str;
        };
        $mail->SMTPDebug = 2;

        var_dump( $mail );
        /* Send mail and return result */
        if ( ! $mail->Send() ) $errors = $mail->ErrorInfo;

        $mail->ClearAddresses();
        $mail->ClearAllRecipients();

        echo '<div class="swpsmtp-yellow-box"><h3>Debug Info</h3>';
        echo '<textarea rows="20" style="width: 100%;">' . $debugMSG . '</textarea>';
        echo '</div>';

        if ( ! empty( $errors ) ) {
            return $errors;
        } else {
            return 'Test mail was sent';
        }
    }

    /**
     * Add options to settings page
     */
    public function sx_smtp_settings_page_content() {
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $host     = get_option( $this->args["host"] );
        $port     = get_option( $this->args["port"] );
        $username = get_option( $this->args["username"] );
        $password = get_option( $this->args["password"] );
        $enabled  = ( get_option( $this->args["enabled"] ) ) ? " checked=checked " : "";

        /* Send test letter */
        $sx_smtp_send_to = '';
        if ( isset( $_POST[ 'sx_smtp_test_submit' ] ) ) {
            if (isset($_POST['sx_smtp_send_to'])) {
                $to_email = sanitize_text_field($_POST['sx_smtp_send_to']);
                if (is_email($to_email)) {
                    $sx_smtp_send_to = $to_email;
                } else {
                    $error = __("Please enter a valid email address in the recipient email field.", 'skynix');
                }
            }
            $sx_smtp_subject = isset($_POST['sx_smtp_subject']) ? sanitize_text_field($_POST['sx_smtp_subject']) : '';
            $sx_smtp_message = isset($_POST['sx_smtp_message']) ? sanitize_textarea_field($_POST['sx_smtp_message']) : '';

            //Save the test mail details so it doesn't need to be filled in everytime.
            $smtp_test_mail['sx_smtp_send_to'] = $sx_smtp_send_to;
            $smtp_test_mail['sx_smtp_subject'] = $sx_smtp_subject;
            $smtp_test_mail['sx_smtp_message'] = $sx_smtp_message;

            if (!empty($sx_smtp_send_to)) {
                $result = $this->sx_smtp_test_mail($sx_smtp_send_to, $sx_smtp_subject, $sx_smtp_message);
                var_dump($result);
            }
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title(), $this->args["textdomain"] ); ?></h1>
            <hr>
            <p><?php echo __( 'Feel free to', $this->args["textdomain"] ); ?> <a href="https://skynix.company/wordpress-plugin-development"><?php echo __( 'contact us', $this->args["textdomain"] ); ?></a> <?php echo __( 'if you need any kind of support', $this->args["textdomain"] ); ?></p>
            <hr>
            <form action="options.php" method="post">
                <table>
                    <tr>
                        <td><label for="<?php echo $this->args["host"]; ?>" ><?php _e( 'Hostname', $this->args["textdomain"] ); ?>*: </label></td>
                        <td><input type="text"
                                   class="sx-smtp-input"
                                   id="<?php echo $this->args["host"]; ?>"
                                   name="<?php echo $this->args["host"]; ?>"
                                   value="<?php echo $host; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="<?php echo $this->args["port"]; ?>" ><?php _e( 'Port', $this->args["textdomain"] ); ?>*: </label></td>
                        <td><input type="text"
                                   class="sx-smtp-input"
                                   id="<?php echo $this->args["port"]; ?>"
                                   name="<?php echo $this->args["port"]; ?>"
                                   value="<?php echo $port; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="<?php echo $this->args["username"]; ?>" ><?php _e( 'Username', $this->args["textdomain"] ); ?>*: </label></td>
                        <td><input type="text"
                                   class="sx-smtp-input"
                                   id="<?php echo $this->args["username"]; ?>"
                                   name="<?php echo $this->args["username"]; ?>"
                                   value="<?php echo $username; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="<?php echo $this->args["password"]; ?>" ><?php _e( 'Password', $this->args["textdomain"] ); ?>*: </label></td>
                        <td><input type="password"
                                   class="sx-smtp-password"
                                   id="<?php echo $this->args["password"]; ?>"
                                   name="<?php echo $this->args["password"]; ?>"
                                   value="<?php echo $password; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="<?php echo $this->args["enabled"]; ?>" ><?php _e( 'Check to enable plugin', $this->args["textdomain"] ); ?>: </label></td>
                        <td><input type="checkbox" class="sx-smtp-checkbox" id="<?php echo $this->args["enabled"]; ?>"
                                   name="<?php echo $this->args["enabled"]; ?>" <?php echo $enabled; ?> disabled></td>
                    </tr>
                </table>
                <p><?php echo __( 'All fields must be filled to enable this plugin', $this->args["textdomain"] ); ?></p>

                <?php
                // output security fields for the registered setting "sxpg"
                settings_fields( 'sx-smtp-settings' );

                // output save settings button
                submit_button( __( 'Save', $this->args["textdomain"] ) );
                ?>
            </form>
        </div>

        <div class="sx_smtp-tab-container" data-tab-name="testemail">
            <h3 class="hndle"><label for="title"><?php _e( 'Test Email', 'skynix' ); ?></label></h3>
            <div class="inside">
                 <form id="sx_smtp_settings_form" method="post" action="">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e( "To", 'skynix' ); ?>:</th>
                            <td>
                                <input id="sx_smtp_send_to" type="text" class="ignore-change" name="sx_smtp_send_to" value="" /><br />
                                <p class="description"><?php _e( "Enter the recipient's email address", 'skynix' ); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e( "Subject", 'skynix' ); ?>:</th>
                            <td>
                                <input id="sx_smtp_subject" type="text" class="ignore-change" name="sx_smtp_subject" value="" /><br />
                                <p class="description"><?php _e( "Enter a subject for your message", 'skynix' ); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e( "Message", 'skynix' ); ?>:</th>
                            <td>
                                <textarea name="sx_smtp_message" id="sx_smtp_message" rows="5"></textarea><br />
                                <p class="description"><?php _e( "Write your email message", 'skynix' ); ?></p>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" id="settings-form-submit" class="button-primary" value="<?php _e( 'Send Test Email', 'skynix' ) ?>" />
                        <input type="hidden" name="sx_smtp_test_submit" value="submit" />
                    </p>
                </form>
            </div><!-- end of inside -->
        </div>

        <script type="text/javascript">
            function enableCheck(){
                host     = jQuery('#<?php echo $this->args["host"]; ?>').val();
                port     = jQuery('#<?php echo $this->args["port"]; ?>').val();
                username = jQuery('#<?php echo $this->args["username"]; ?>').val();
                password = jQuery('#<?php echo $this->args["password"]; ?>').val();

                if (
                    typeof host !== 'undefined' && typeof port !== 'undefined' &&
                    typeof username !== 'undefined' && typeof password !== 'undefined' &&
                    host.length > 0 && port.length > 0 && username.length > 0 && password.length > 0
                ) {
                    jQuery('#<?php echo $this->args["enabled"]; ?>').removeAttr("disabled");
                } else {
                    jQuery('#<?php echo $this->args["enabled"]; ?>').attr("disabled", true);
                }
            }
            jQuery(document).ready(function(){
                enableCheck();
                jQuery(document).on('keyup', 'input[type=text]', enableCheck);
            });
        </script>
        <?php
    }

}
