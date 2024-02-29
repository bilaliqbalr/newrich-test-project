<?php

class Field {
    protected $type;
    protected $name;
    protected $label;
    protected $rules;
    protected $value;
    protected $sendInEmail;
    protected $errors = [];

    public function __construct($type, $name, $label = null, $rules = [], $value = null, $sendInEmail = false) {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->rules = $rules;
        $this->value = $value;
        $this->sendInEmail = $sendInEmail;
    }

    public function getName() {
        return $this->name;
    }

    public function getLabel() {
        return $this->label;
    }

    public function getValue() {
        return $this->value;
    }

    public function sendInEmail() {
        return $this->sendInEmail;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function label() {
        $html = "<label for='{$this->name}'>{$this->label}</label>";
        return $html;
    }

    public function render() {

    }

    public function validate() {
        $valid = true;
        foreach ($this->rules as $rule) {
            $param = '';
            if (is_int(strpos($rule, ':'))) {
                list($rule, $param) = explode(':', $rule);
            }

            switch ($rule) {
                case 'required':
                    $valid = $valid && !empty($this->value);
                    $this->errors[] = "$this->label is required";
                    break;
                case 'min':
                    $valid = $valid && (is_numeric($this->value) ? $this->value >= $param : false);
                    $this->errors[] = "$this->label must have min $this->value";
                    break;
                case 'max':
                    $valid = $valid && (is_numeric($this->value) ? $this->value <= $param : false);
                    $this->errors[] = "$this->label must have max $this->value";
                    break;
                case 'email':
                    $valid = $valid && filter_var($this->value, FILTER_VALIDATE_EMAIL);
                    $this->errors[] = "$this->label must be a valid email";
                    break;
                case 'password':
                    $valid = $valid && preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()])(?=.{6,})/', $this->value);
                    $this->errors[] = "$this->label must have min 6 characters required";
                    break;
                default:
                    // Handle unsupported validation rule
                    break;
            }
            if (!$valid) {
                break;
            }
        }
        return $valid;
    }

    public function toArray() {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->label,
            'rules' => $this->rules,
            'value' => $this->value,
            'sendInEmail' => $this->sendInEmail,
        ];
    }
}

class InputField extends Field {
    public function render() {
        $allowedTypes = ['text', 'email', 'password', 'url', 'number'];
        $type = in_array($this->type, $allowedTypes) ? $this->type : 'text';
        $html = "<div class='form-group mt-3'>";
        $html .= $this->label();
        $html .= "<input type='$type' class='form-control' id='{$this->name}' name='data[{$this->name}]' value='{$this->value}'";
        if (in_array('required', $this->rules)) {
            $html .= " required";
        }
        $html .= ">";
        $html .= "</div>";
        return $html;
    }
}

class HiddenField extends Field {
    public function render() {
        $html = "<input type='hidden' id='{$this->name}' name='data[{$this->name}]' value='{$this->value}' />";
        return $html;
    }
}

class TextAreaField extends Field {
    public function render() {
        $html = "<div class='form-group mt-3'>";
        $html .= $this->label();
        $html .= "<textarea class='form-control' id='{$this->name}' name='data[{$this->name}]' rows='5'";
        if (in_array('required', $this->rules)) {
            $html .= " required";
        }
        $html .= ">{$this->value}</textarea>";
        $html .= "</div>";
        return $html;
    }
}

class CheckboxField extends Field {
    public $options;

    public function __construct($type, $name, $label, $rules, $options, $value = null, $sendInEmail = false) {
        parent::__construct($type, $name, $label, $rules, $value, $sendInEmail);
        if (!is_array($value)) {
            $this->value = (array) $value;
        }
        $this->options = $options;
    }

    public function render() {
        $html = "";
        $html .= "<div class='form-group mt-3'>";
        $html .= "<label>{$this->label}</label>";
        foreach ($this->options as $key => $optionValue) {
            $checked = in_array($key, $this->value) ? 'checked' : '';
            $html .= "<div class='form-check'>";
            $html .= "<input class='form-check-input' type='checkbox' name='data[{$this->name}][]' id='{$this->name}_{$key}' value='$key' $checked>";
            $html .= "<label class='form-check-label' for='{$this->name}_{$key}'>{$optionValue}</label>";
            $html .= "</div>";
        }
        $html .= "</div>";
        return $html;
    }

    public function validate() {
        $valid = parent::validate(['required']);
        if ($valid && in_array('required', $this->rules) && empty($this->value)) {
            $valid = false;
            $this->errors[] = "$this->label is required";
        }
        return $valid;
    }

    public function toArray() {
        $array = parent::toArray();
        $array['options'] = $this->options;
        return $array;
    }
}

class RadioField extends Field {
    public $options;

    public function __construct($type, $name, $label, $rules, $options, $value = null, $sendInEmail = false) {
        parent::__construct($type, $name, $label, $rules, $value, $sendInEmail);
        $this->options = $options;
    }

    public function render() {
        $html = "";
        $html .= "<div class='form-group mt-3'>";
        $html .= "<label>{$this->label}</label>";
        foreach ($this->options as $key => $optionValue) {
            $checked = ($key == $this->value) ? 'checked' : '';
            $html .= "<div class='form-check'>";
            $html .= "<input class='form-check-input' type='radio' name='data[{$this->name}]' id='{$this->name}_{$key}' value='$key' $checked>";
            $html .= "<label class='form-check-label' for='{$this->name}_{$key}'>{$optionValue}</label>";
            $html .= "</div>";
        }
        $html .= "</div>";
        return $html;
    }

    public function validate() {
        $valid = parent::validate();
        if ($valid && in_array('required', $this->rules) && !isset($this->options[$this->value])) {
            $valid = false;
            $this->errors[] = "$this->label is required";
        }
        return $valid;
    }

    public function toArray() {
        $array = parent::toArray();
        $array['options'] = $this->options;
        return $array;
    }
}

class SelectField extends Field {
    public $options;

    public function __construct($type, $name, $label, $rules, $options, $value = null, $sendInEmail = false) {
        parent::__construct($type, $name, $label, $rules, $value, $sendInEmail);
        $this->options = $options;
    }

    public function render() {
        $html = "<div class='form-group mt-3'>";
        $html .= $this->label();
        $html .= "<select class='form-control' id='{$this->name}' name='data[{$this->name}]'>";
        foreach ($this->options as $key => $optionValue) {
            $selected = ($key == $this->value) ? 'selected' : '';
            $html .= "<option value='$key' $selected>$optionValue</option>";
        }
        $html .= "</select>";
        $html .= "</div>";
        return $html;
    }

    public function validate() {
        $valid = parent::validate();
        if ($valid && in_array('required', $this->rules) && !isset($this->options[$this->value])) {
            $valid = false;
            $this->errors[] = "$this->label is required";
        }
        return $valid;
    }

    public function toArray() {
        $array = parent::toArray();
        $array['options'] = $this->options;
        return $array;
    }
}
