<?php

/**
* Plugin Name: YGLU Candidatos
* Plugin URI: https://tuyglu.com/
* Description: Envía las candidaturas de empleados a YGLU.
* Version: 0.1
* Author: YGLU Factory
* Author URI: https://tuyglu.com/
* Requires Plugins: contact-form-7, yglu-wp
**/

define("YGCA_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("YGCA_PLUGIN_URL", plugin_dir_url(__FILE__));
define("YGCA_PLUGIN_SLUG", "yglu-candidates");

register_activation_hook(__FILE__, "ygca_activate_plugin");
function ygca_activate_plugin() {
    // TODO esto debería hacerse mediante el formulario de configuración
    add_option('yg_form_id', '7');
    add_option('yg_fieldname_name', 'nombre');
    add_option('yg_fieldname_surname', 'apellidos');
    add_option('yg_fieldname_nif', 'nif');
    add_option('yg_fieldname_email', 'email');
    add_option('yg_fieldname_phone', 'telefono');
    add_option('yg_fieldname_message', 'mensaje');
    add_option('yg_fieldname_file', 'archivo');
}

register_deactivation_hook(__FILE__, "ygca_deactivate_plugin");
function ygca_deactivate_plugin() {
    delete_option('yg_form_id');
    delete_option('yg_fieldname_name');
    delete_option('yg_fieldname_surname');
    delete_option('yg_fieldname_nif');
    delete_option('yg_fieldname_email');
    delete_option('yg_fieldname_phone');
    delete_option('yg_fieldname_message');
    delete_option('yg_fieldname_file');
}


function enqueue_ygca_styles() {
    wp_enqueue_style("yg-style", ygwp_get_url("style.css"), array(), filemtime(ygwp_get_path("style.css")));
}

function enqueue_ygca_scripts() {
    wp_enqueue_script("yg-script", ygwp_get_url("script.js"), array("jquery"), filemtime(ygwp_get_path("script.js")));
}

add_action("wp_enqueue_scripts", "enqueue_ygca_styles");
add_action("wp_enqueue_scripts", "enqueue_ygca_scripts");

require_once YGCA_PLUGIN_PATH.'includes/class-yglu-candidates.php';
function ygca_init() {
    new YGLUCandidates();
}
add_action('plugins_loaded', 'ygca_init');