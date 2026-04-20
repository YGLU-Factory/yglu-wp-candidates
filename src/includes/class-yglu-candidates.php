<?php

class YGLUCandidates
{
    public function __construct()
    {
        add_action('wpcf7_before_send_mail', [$this, 'send_to_external_api'], 10, 1);
    }


    public function send_to_external_api($contact_form)
    {
        $target_form_id = 7;

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
                "Nombre"        => ($posted_data['your-name'] ?? '') . ' ' . ($posted_data['apellido'] ?? ''),
                "Cif"           => $posted_data['your-cif'] ?? '',
                "Email"         => $posted_data['your-email'] ?? '',
                "Telefono"      => $posted_data['tel-750'] ?? '',
                "Observaciones" => $posted_data['your-message'] ?? '',
                "promoted"      => 0
            ];

            // CF7 devuelve un array de rutas: ['/ruta/al/archivo.pdf']
            if (!empty($uploaded_files['file-274'])) {
                $paths = (array) $uploaded_files['file-274'];
                $files = ['cv' => $paths[0]];
            }

            $res = ApiService::postWithFiles('employees/create', $payload_data, $files ?? []);
        }
    }
}
