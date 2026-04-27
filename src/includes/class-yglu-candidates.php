<?php

include_once dirname(dirname(__FILE__)) . '/services/apiService.php';

class YGLUCandidates
{
    public function __construct()
    {
        add_action('wpcf7_before_send_mail', [$this, 'sendCandidate'], 10, 1);
    }


    public function sendCandidate($contact_form)
    {
        $target_form_id = get_option('yg_form_id');

        // Comprobamos si el formulario que se está enviando coincide con el objetivo
        if ($contact_form->id() == $target_form_id) {
            $submission = WPCF7_Submission::get_instance();

            if (! $submission) {
                return;
            }

            $posted_data = $submission->get_posted_data();
            $uploaded_files = $submission->uploaded_files();

            // Mapear datos
            $payload_data = [
                "Nombre"        => ($posted_data[get_option('yg_fieldname_name')] ?? '') . ($posted_data[get_option('yg_fieldname_surname')] ?? ''),
                "Cif"           => $posted_data[get_option('yg_fieldname_nif')] ?? '',
                "Email"         => $posted_data[get_option('yg_fieldname_email')] ?? '',
                "Telefono"      => $posted_data[get_option('yg_fieldname_phone')] ?? '',
                "Observaciones" => $posted_data[get_option('yg_fieldname_message')] ?? '',
                "observations2" => $posted_data[get_option('yg_fieldname_departments')] ?? '',
                "promoted"      => 0
            ];

            // CF7 devuelve un array de rutas: ['/ruta/al/archivo.pdf']
            if (!empty($uploaded_files[get_option('yg_fieldname_file')])) {
                $paths = (array) $uploaded_files[get_option('yg_fieldname_file')];
                $files = ['cv' => $paths[0]];
            }

           \ApiService::postWithFiles('employees/create', $payload_data, $files ?? []);
        }
    }
}
