<?php

class Controller
{
    /**
     * Return JSON response
     *
     * @param $data
     * @return void
     */
    public function toJson($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Index page
     *
     * @return void
     * @throws Exception
     */
    public function index() {
        App::view('index', [
            'forms' => App::db()->select('forms', 'id, name, created_at, updated_at')
        ]);
    }

    /**
     * Create new form from API
     *
     * @return void
     */
    public function createNewFormFromApi() {
        $name = App::post('name');
        $fields = App::post('fields');

        $errors = [];
        if (empty($name)) {
            $errors[] = 'Name is required';
        }
        if (empty($fields)) {
            $errors[] = 'Fields are required';
        }

        if ( ! empty($errors)) {
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

    /**
     * Load form submissions
     *
     * @return void
     * @throws Exception
     */
    public function formSubmissions() {
        $formId = App::get('id');
        if (empty($formId)) {
            throw new \Exception('Form ID is required');
        }

        $form = $this->getForm($formId);
        $formSubmissions = App::db()->select('form_submissions', '*', ['form_id' => $form['id']]);
        $submissions = [];
        foreach ($formSubmissions as $submission) {
            $data = json_decode($submission['data'], true);

            $formObj = new Form($form['fields'], $data, $form['id']);
            $submission['data'] = $formObj->formatData();

            $submissions[$submission['id']] = $submission;
        }

        App::view('submissions', [
            'form' => $form,
            'submissions' => $submissions,
        ]);
    }

    /**
     * This will return form after fetching from database and also decode the fields
     *
     * @param $id
     * @return mixed
     * @throws Exception
     */
    private function getForm($id) {
        $form = App::db()->select('forms', '*', ['id' => $id]);
        if (empty($form)) {
            throw new \Exception('Form not found');
        }

        $form = $form[0];
        $form['fields'] = json_decode($form['fields'], true);

        return $form;
    }

    /**
     * Loading form (as create form or submission form)
     *
     * @return void
     * @throws Exception
     */
    public function createForm() {
        $formId = App::get('id');
        if (empty($formId)) {
            throw new \Exception('Form ID is required');
        }

        $form = $this->getForm($formId);
        $formObj = new Form($form['fields'], [], $form['id']);

        App::view('create', [
            'form' => $form,
            'formObj' => $formObj,
        ]);
    }

    /**
     * Submit form data
     *
     * @return void
     * @throws Exception
     */
    public function submitForm() {
        $data = App::post('data', []);
        $formId = App::post('form_id');

        $form = $this->getForm($formId);

        $form = new Form($form['fields'], $data, $formId);
        $response = $form->submit();

        // Sending email
        $form->sendEmail(App::config('email_address'));

        $this->toJson($response);
    }
}
