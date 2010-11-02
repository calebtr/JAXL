<?php

    /**
     * Pre-fetch XMPP/Jabber data using BOSH for populating a webpage
     * This sample code also demonstrate how to remove your application dependency upon jaxl.ini
     *
     * This sample application demonstrate how to pre-fetch XMPP data from the jabber server
     * Specifically, this app will fetch logged in user VCard from the jabber server
     * Pre-fetched data can later be htmlized and displayed on the webpage
     *
     * Usage:
     * ------
     * 1) Put this file under your web folder
     * 2) Edit user/pass/domain/host below for your account
     * 3) Hit this file in your browser
     *
     * View jaxl.log for detail
    */

    // include JAXL core
    define('JAXL_BASE_PATH', '/usr/share/php/jaxl');
    require_once JAXL_BASE_PATH.'/core/jaxl.class.php';
    
    // initialize JAXL instance
    $xmpp = new JAXL(array(
        'user'=>'',
        'pass'=>'',
        'domain'=>'localhost',
        'host'=>'localhost',
        'boshHost'=>'localhost',
        'boshPort'=>5280,
        'boshSuffix'=>'http-bind',
        'boshOut'=>false, // Disable auto-output of Jaxl Bosh Module
        'logLevel'=>5
    ));
    
    // Include required XEP's
    $xmpp->requires(array(
        'JAXL0054', // VCard
        'JAXL0206'  // XMPP over Bosh
    ));

    function postConnect() {
        global $xmpp;
        $xmpp->startStream();
    }

    function doAuth($mechanism) {
        global $xmpp;
        $xmpp->auth('DIGEST-MD5');
    }

    function postAuth() {
        global $xmpp;
        $xmpp->JAXL0054('getVCard', false, $xmpp->jid, 'handleVCard');
    }

    function handleVCard($payload) {
        global $xmpp;
        echo "<b>Successfully fetched VCard</b><br/>";
        print_r($payload);
        $xmpp->JAXL0206('endStream');
    }

    function postAuthFailure() {
        echo "OOPS! Auth failed";
    }

    // Register callbacks for required events
    JAXLPlugin::add('jaxl_post_connect', 'postConnect');
    JAXLPlugin::add('jaxl_get_auth_mech', 'doAuth');
    JAXLPlugin::add('jaxl_post_auth', 'postAuth');
    JAXLPlugin::add('jaxl_post_auth_failure', 'postAuthFailure');

    // Connect to the Bosh Connection Manager
    $xmpp->action = 'connect';
    $xmpp->JAXL0206('startStream', $xmpp->host, $xmpp->port, $xmpp->domain);

?>
