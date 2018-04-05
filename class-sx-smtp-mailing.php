<?php
/**
 * SX Easy-going SMTP mailing
 */

class SX_SMTP_mailing
{
    public $args;

    public function __construct( $args ) {
        $this->args = $args;
    }

    public function init()
    {
        add_action( 'phpmailer_init', array( $this, 'sx_smtp_mailer'), 9999 );

        add_filter("wp_mail_from", function(){

            return get_option('admin_email');

        });
    }

    /**
     * Check if plugin is enabled in settings and if all credentials where provided
     *
     * @param array $settings
     * @return bool
     */
    public function sx_smtp_is_enabled( $settings = [] ){
        $response = false;
        if ( empty( $settings ) ) {
            $settings = $this->sx_smtp_get_settings();
        }

        if ( $settings["enabled"] && !empty( $settings["host"] ) && !empty( $settings["port"] ) &&
            !empty( $settings["username"] ) && !empty( $settings["password"] ) )
        {
            $response = true;
        }

        return $response;
    }

    /**
     * Get saved SMTP settings
     *
     * @return array
     */
    public function sx_smtp_get_settings(){
        $settings["enabled"]  = get_option( $this->args["enabled"] );
        $settings["host"]     = get_option( $this->args["host"] );
        $settings["port"]     = get_option( $this->args["port"] );
        $settings["username"] = get_option( $this->args["username"] );
        $settings["password"] = get_option( $this->args["password"] );

        return $settings;
    }

    /**
     * Function to add smtp options in the phpmailer_init
     * @return void
     */
    public function sx_smtp_mailer( &$phpmailer ) {
        // Get user-entered settings
        $settings = $this->sx_smtp_get_settings();
        // Check if plugin is enabled and all required credentials have been set.
        if ( ! $this->sx_smtp_is_enabled( $settings ) ) {
            return;
        }

        $phpmailer->IsSMTP();
        $phpmailer->SetFrom( $phpmailer->From, $phpmailer->FromName );

        $phpmailer->Host       = $settings['host'];
        $phpmailer->Port       = $settings['port'];
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Username   = $settings['username'];
        $phpmailer->Password   = $settings['password'];
        $phpmailer->SMTPSecure = 'tls';
    }

}
