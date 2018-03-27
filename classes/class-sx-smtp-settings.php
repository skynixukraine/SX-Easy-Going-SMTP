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
        <script type="text/javascript">
            function enableCheck(){
                host     = jQuery('#<?php echo $this->args["host"]; ?>').val();
                port     = jQuery('#<?php echo $this->args["port"]; ?>').val();
                username = jQuery('#<?php echo $this->args["username"]; ?>').val();
                password = jQuery('#<?php echo $this->args["password"]; ?>').val();
                console.log(host + '__' + port + '__' + username + '__' + password);
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
