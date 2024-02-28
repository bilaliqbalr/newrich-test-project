<?php

class Controller {
    public function toJson($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function index() {
        App::view('index', [
            'forms' => App::db()->select('forms', 'id, name, created_at, updated_at')
        ]);
    }

    public function createForm() {
        $name = App::post('name');
        $fields = App::post('fields');

        $errors = [];
        if (empty($name)) {
            $errors[] = 'Name is required';
        }
        if (empty($fields)) {
            $errors[] = 'Fields are required';
        }

        if (!empty($errors)) {
            $this->toJson([
                'status' => false,
                'errors' => $errors
            ]);
        }

        $fields = json_decode($fields, true);

        $form = new Form($fields);
        $this->toJson(
            $form->create($name)
        );
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
