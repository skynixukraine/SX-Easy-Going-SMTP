<?php
/**
 * SX Easy-going SMTP settings
 */

class SX_SMTP_settings
{
    public $args;
    public $menu_slug = 'sx-smtp-settings.php';

    public function __construct( $args ) {
        $this->args = $args;
    }

    public function init() {
        $plugin = basename( dirname( __FILE__ , 2 ) ) . '/init.php';
        // Register settings options
        add_action( 'admin_init', array( $this, 'sx_smtp_settings_fields' ) );
        // Create settings subpage
        add_action( 'admin_menu', array( $this, 'sx_smtp_add_settings_page' ) );
        // Add link to plugin settings page
        add_filter( "plugin_action_links_$plugin", array( $this, 'sx_smtp_add_settings_link' ), 99, 1 );
    }

    /**
     * Add "Settings" link to plugin description on plugins page
     *
     * @param $links
     * @return mixed
     */
    public function sx_smtp_add_settings_link( $links ) {
        $settings_link = '<a href="options-general.php?page=' . $this->menu_slug . '">' . __( 'Settings' ) . '</a>';
        array_unshift( $links, $settings_link );

        return $links;
    }

    /**
     * Register new settings
     */
    public function sx_smtp_settings_fields(){
        register_setting( 'sx-smtp-settings', $this->args["host"] );
        register_setting( 'sx-smtp-settings', $this->args["port"] );
        register_setting( 'sx-smtp-settings', $this->args["username"] );
        register_setting( 'sx-smtp-settings', $this->args["password"] );
        register_setting( 'sx-smtp-settings', $this->args["from_email"] );
    }

    /**
     * Add admin menu item
     */
    public function sx_smtp_add_settings_page(){
        add_options_page(
            __( "SX Easy-going SMTP settings", $this->args["textdomain"] ),
            __( "SX Easy-going SMTP", $this->args["textdomain"] ),
            "manage_options",
            $this->menu_slug,
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

        $host       = get_option( $this->args["host"] );
        $port       = get_option( $this->args["port"] );
        $username   = get_option( $this->args["username"] );
        $password   = get_option( $this->args["password"] );
        $from_email = get_option( $this->args["from_email"] );

        ?>

        <div class="sx_smtp-tab-container wrap" data-tab-name="settings">
            <h1><?php echo esc_html( get_admin_page_title(), $this->args["textdomain"] ); ?></h1>
            <hr>
            <p><?php echo __( 'Feel free to', $this->args["textdomain"] ); ?> <a href="https://skynix.company/wordpress-plugin-development"><?php echo __( 'contact us', $this->args["textdomain"] ); ?></a> <?php echo __( 'if you need any kind of support', $this->args["textdomain"] ); ?></p>
            <hr>
            <form id="settings-form" action="options.php" method="post">
                <table>
                    <tr>
                        <td><label for="<?php echo $this->args["from_email"]; ?>" ><?php _e( 'From e-mail', $this->args["textdomain"] ); ?>*: </label></td>
                        <td><input type="text"
                                   class="sx-smtp-input"
                                   id="<?php echo $this->args["from_email"]; ?>"
                                   name="<?php echo $this->args["from_email"]; ?>"
                                   value="<?php echo $from_email; ?>"></td>
                    </tr>
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

        <?php
    }

}
