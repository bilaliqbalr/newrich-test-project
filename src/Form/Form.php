<?php
require_once 'fields.php';

class Form {
    protected $fields;
    protected $errors;
    protected $invalidFields = [];

    public function __construct(array $fields, array $values = []) {
        $this->fields = [];

        // Convert field data into appropriate field objects
        foreach ($fields as $field) {
            $type = $field['type'];
            $name = $field['name'];
            $label = $field['label'];
            $rules = isset($field['rules']) ? $field['rules'] : [];
            $options = isset($field['options']) ? $field['options'] : [];
            $sendInEmail = isset($field['email']) ? $field['email'] : false;
            $value = isset($values[$name]) ? $values[$name] : null;

            switch ($type) {
                case 'text':
                case 'email':
                case 'password':
                case 'url':
                case 'number':
                    $this->fields[] = new InputField($type, $name, $label, $rules, $value, $sendInEmail);
                    break;
                case 'hidden':
                    $this->fields[] = new HiddenField($type, $name, $label, $rules, $value, $sendInEmail);
                    break;
                case 'textarea':
                    $this->fields[] = new TextAreaField($type, $name, $label, $rules, $value, $sendInEmail);
                    break;
                case 'select':
                    $this->fields[] = new SelectField($type, $name, $label, $rules, $options, $value, $sendInEmail);
                    break;
                case 'radio':
                    $this->fields[] = new RadioField($type, $name, $label, $rules, $options, $value, $sendInEmail);
                    break;
                case 'checkbox':
                    $this->fields[] = new CheckboxField($type, $name, $label, $rules, $options, $value, $sendInEmail);
                    break;
                default:
                    $this->invalidFields[] = $field['name'];
                    break;
            }
        }
    }

    public function formFieldsArray() {
        $formFields = [];
        foreach ($this->fields as $field) {
            $formFields[] = $field->toArray();
        }
        return $formFields;
    }

    public function create() {
        if (!empty($this->invalidFields)) {
            return [
                'status' => false,
                'errors' => 'Invalid fields provided: ' . implode(', ', $this->invalidFields)
            ];
        }

        App::db()->insert('forms', [
            'fields' => json_encode($this->formFieldsArray()),
        ]);

        return [
            'status' => true
        ];
    }

    public function validateForm() {
        $this->errors = [];
        if (empty($_POST['g-recaptcha-response'])) {
            $this->errors['g-recaptcha-response'] = 'Please verify you are not a robot.';
        } else {
            $secret = App::config('recaptcha')['secret'];
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response'];
            $response = file_get_contents($url);
            $response = json_decode($response);
            if (!$response->success) {
                $this->errors[] = 'Failed reCAPTCHA verification.';
            }
        }

        foreach ($this->fields as $field) {
            if (!$field->validate()) {
                $this->errors[$field->name] = implode("\n", $field->getErrors());
            }
        }

        return empty($this->errors);
    }

    public function submit() {
        if ($this->validateForm()) {
            $data = [];
            foreach ($this->fields as $field) {
                $data[$field->name] = $field->value;
            }

            // saving to database
            App::db()->insert('forms', $data);

            return [
                'status' => true
            ];

        } else {
            return [
                'status' => false,
                'errors' => $this->errors
            ];
        }
    }

    public function render($action = '', $method = 'post') {
        $html = "<form action='$action' method='$method'>";
        foreach ($this->fields as $field) {
            $html .= $field->render();
        }
        $siteKey = App::config('recaptcha')['site_key'];
        $html .= "<div class='g-recaptcha' data-sitekey='$siteKey'></div>";
        $html .= "<button type='submit'>Submit</button>";
        $html .= "</form>";
        return $html;
    }

    public function sendEmail($to) {
        // Formatting HTML for email
        $html = "<h1>New Form Submission</h1>";
        $html .= "<ul>";
        foreach ($this->fields as $field) {
            if ($field->sendInEmail()) {
                $html .= "<li><strong>{$field->label}:</strong> {$field->value}</li>";
            }
        }
        $html .= "</ul>";

        // Sending email
        $headers  = "IME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "To: <$to>" . "\r\n";
        $headers .= "From: no-reply <no-reply@example.com>" . "\r\n";
        return mail($to, 'New Form Submission', $html, $headers);
    }
}
