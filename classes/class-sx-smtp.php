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
        'enabled'    => 'sx_smtp_enabled',
    ];

    public function __construct() {
        $this->settings = new SX_SMTP_settings( $this->args );
        $this->mailing  = new SX_SMTP_mailing( $this->args );
    }

    public function init() {
        $this->mailing->init();
        $this->settings->init();
    }

}
