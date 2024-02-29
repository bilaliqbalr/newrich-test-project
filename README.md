
## Installation
Create database using `src/database/schema.sql` file.

## Config
Update `src/config.php` according to your configuration.

## Form API request
```php
$url = "http://newrich-test-project.test/create-form";
$data = [
    "name" => "Form",
    "fields" => [
        [
            "name" => "name",
            "type" => "text",
            "label" => "Name",
            "rules" => ["required"],
            "sendInEmail" => false,
        ],
        [
            "name" => "email",
            "type" => "email",
            "label" => "Email",
            "rules" => ["required"],
            "sendInEmail" => true,
        ],
        [
            "name" => "age",
            "type" => "number",
            "label" => "Age",
            "rules" => ["required", "min:10"],
            "sendInEmail" => false,
        ],
        [
            "name" => "company",
            "type" => "text",
            "label" => "Company",
            "sendInEmail" => false,
        ],
        [
            "name" => "gender",
            "type" => "radio",
            "label" => "Gender",
            "options" => [
                "male" => "Male",
                "Female" => "Female",
            ],
            "rules" => ["required"],
            "sendInEmail" => true,
        ],
        [
            "name" => "allow",
            "type" => "select",
            "label" => "Allow",
            "options" => [
                "yes" => "yes",
                "no" => "No",
            ],
            "rules" => ["required"],
            "sendInEmail" => true,
        ],
        [
            "name" => "how_you_know",
            "type" => "checkbox",
            "label" => "How you know about us",
            "options" => [
                "facebook" => "Facebook",
                "google" => "Google",
                "linkedin" => "Linked In",
                "other" => "Other",
            ],
            "rules" => ["required"],
            "sendInEmail" => true,
        ],
        [
            "name" => "details",
            "type" => "textarea",
            "label" => "Details",
            "rules" => ["required"],
            "sendInEmail" => false,
        ],
        [
            "name" => "id",
            "type" => "hidden",
            "sendInEmail" => false,
        ],
    ]
];
```
