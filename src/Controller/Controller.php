<?php

class Controller {
    public function index() {
        App::view('index.php', [
            'name' => 'John'
        ]);
    }

    public function createForm() {
        $fields = $_POST['fields'];

        $form = new Form($fields);
        return $form->create();
    }

    public function load() {
        $formId = $_GET['form_id'];
        if (empty($formId)) {
            throw new \Exception('Form ID is required');
        }

        $form = App::db()->select('forms', ['fields'], ['id' => $formId]);
        if (empty($form)) {
            throw new \Exception('Form not found');
        }

        return $form;
    }

    public function submitForm() {
        $data = $_POST['data'];
        $formId = $_POST['form_id'];

        $fields = App::db()->select('forms', ['fields'], ['id' => $formId]);
        echo '<pre>';print_r($fields);echo '</pre>';exit;

        $form = new Form($fields);
        return $form->submit();
    }
}
