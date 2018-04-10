<?php
/**
 * SX Easy-going SMTP init
 */

class SX_SMTP
{
    public $args = [
        'textdomain' => 'skynix',
        'host'       => 'sx_smtp_host',
        'port'       => 'sx_smtp_port',
        'username'   => 'sx_smtp_username',
        'password'   => 'sx_smtp_password',
        'from_email' => 'sx_smtp_from_email',
    ];

    public function __construct() {
        $this->settings = new SX_SMTP_settings( $this->args );
        $this->mailing  = new SX_SMTP_mailing( $this->args );
        add_filter( 'ccf_email_headers', array( $this, 'sx_smtp_fix_ccf_conflict' ), 99, 5 );
    }

    public function init() {
        $this->mailing->init();
        $this->settings->init();
    }

    /**
     * Remove mime version to prevent double mime header error that happend in ccf plugin
     *
     * @param $headers
     * @param $form_id
     * @param $email
     * @param $form_page
     * @param $notification
     */
    public function sx_smtp_fix_ccf_conflict( $headers, $form_id, $email, $form_page, $notification ){
        $mime = array_search( 'MIME-Version: 1.0', $headers );
        if ( $mime !== false ) { unset( $headers[$mime] ); $headers = array_values( $headers ); }
    }

}
