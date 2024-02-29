<?php
require_once 'fields.php';

class Form {
    protected $fields;
    protected $errors;
    protected $invalidFields = [];
    /**
     * @var mixed|null
     */
    private mixed $formId;

    public function __construct(array $fields, array $values = [], $formId = null) {
        $this->fields = [];
        $this->formId = $formId;

        // Convert field data into appropriate field objects
        foreach ($fields as $field) {
            $type = $field['type'];
            $name = $field['name'];
            $label = $field['label'] ?? '';
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

    public function create($name) {
        if (!empty($this->invalidFields)) {
            return [
                'status' => false,
                'errors' => 'Invalid fields provided: ' . implode(', ', $this->invalidFields)
            ];
        }

        App::db()->insert('forms', [
            'name' => $name,
            'fields' => json_encode($this->formFieldsArray()),
        ]);

        return [
            'status' => true
        ];
    }

    /**
     * Perform form validation
     *
     * @return bool
     */
    public function validateForm() {
        $this->errors = [];
        $captchaResp = App::post('g-recaptcha-response');
        if (empty($captchaResp)) {
            $this->errors['g-recaptcha-response'] = 'Please verify you are not a robot.';
        } else {
            $secret = App::config('recaptcha')['secret'];
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $captchaResp;
            $response = file_get_contents($url);
            $response = json_decode($response, true);
            if (!$response['success']) {
                $this->errors['g-recaptcha-response'] = 'Failed reCAPTCHA verification.';
            }
        }

        foreach ($this->fields as $field) {
            if (!$field->validate()) {
                $this->errors[$field->getName()] = implode("\n", $field->getErrors());
            }
        }

        return empty($this->errors);
    }

    /**
     * Create new form from API and send email
     *
     * @return array|true[]
     */
    public function submit() {
        if ($this->validateForm()) {
            $data = [];
            foreach ($this->fields as $field) {
                $data[$field->getName()] = $field->getValue();
            }

            // saving to database
            App::db()->insert('form_submissions', [
                'form_id' => $this->formId,
                'data' => json_encode($data),
            ]);

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

    /**
     * Generate HTML for form
     *
     * @param $action
     * @param $method
     * @return string
     */
    public function render($action = '', $method = 'post') {
        $html = "<form action='$action' method='$method'>\n";
        foreach ($this->fields as $field) {
            $html .= $field->render() . "\n";
        }
        $siteKey = App::config('recaptcha')['site_key'];
        $html .= "<div class='g-recaptcha mt-3' data-sitekey='$siteKey'></div>";
        $html .= "<input type='hidden' name='form_id' value='{$this->formId}'>";
        $html .= "<div class='text-center mt-3'>";
        $html .= "<button class='btn btn-primary' type='submit'>Submit</button>";
        $html .= "</div>";
        $html .= "</form>";
        return $html;
    }

    /**
     * Returns form data into label and value pair
     * @return array
     */
    public function formatData() {
        $data = [];
        foreach ($this->fields as $field) {
            $data[$field->getLabel()] = $field->getValue();
        }
        return $data;
    }

    public function sendEmail($to) {
        // Formatting HTML for email
        $html = "<h1>New Form Submission</h1>";
        $html .= "<ul>";
        foreach ($this->fields as $field) {
            if ($field->sendInEmail()) {
                $html .= "<li><strong>{$field->getLabel()}:</strong> {$field->getValue()}</li>";
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
