<?php
/**
 * SX Easy-going SMTP init
 */

class SX_SMTP
{

    public function __construct() {
        $this->mailing  = new SX_SMTP_mailing();
        $this->settings = new SX_SMTP_settings();
    }

    public function init() {
        $this->mailing->init();
        $this->settings->init();
    }

}
