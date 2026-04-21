<?php

/**
* Plugin Name: YGLU Candidatos
* Plugin URI: https://tuyglu.com/
* Description: Envía las candidaturas de empleados a YGLU.
* Version: 0.1
* Author: YGLU Factory
* Author URI: https://tuyglu.com/
* Requires Plugins: contact-form-7
**/

define("YG_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("YG_PLUGIN_URL", plugin_dir_url(__FILE__));
define("YG_PLUGIN_SLUG", "yglu");

require_once YG_PLUGIN_PATH . "admin.php";

register_activation_hook(__FILE__, "activatePlugin");
function activatePlugin() {
    // TODO esto debería hacerse mediante el formulario de configuración
    add_option('yg_form_id', '7');
    add_option('yg_fieldname_name', 'nombre');
    add_option('yg_fieldname_nif', 'nif');
    add_option('yg_fieldname_email', 'email');
    add_option('yg_fieldname_phone', 'telefono');
    add_option('yg_fieldname_message', 'mensaje');
    add_option('yg_fieldname_file', 'archivo');
}

register_deactivation_hook(__FILE__, "deactivatePlugin");
function deactivatePlugin() {
    delete_option('yg_form_id');
    delete_option('yg_fieldname_name');
    delete_option('yg_fieldname_nif');
    delete_option('yg_fieldname_email');
    delete_option('yg_fieldname_phone');
    delete_option('yg_fieldname_message');
    delete_option('yg_fieldname_file');
}

function get_url($file) {
    return YG_PLUGIN_URL . $file;
}

function get_path($file) {
    return YG_PLUGIN_PATH . $file;
}

function enqueue_yg_styles() {
    wp_enqueue_style("yg-style", get_url("style.css"), array(), filemtime(get_path("style.css")));
}

function enqueue_yg_scripts() {
    wp_enqueue_script("yg-script", get_url("script.js"), array("jquery"), filemtime(get_path("script.js")));
}

add_action("wp_enqueue_scripts", "enqueue_yg_styles");
add_action("wp_enqueue_scripts", "enqueue_yg_scripts");

require_once YG_PLUGIN_PATH.'includes/class-yglu-candidates.php';
function yglu_candidates_init() {
    new YGLUCandidates();
}
add_action('plugins_loaded', 'yglu_candidates_init');