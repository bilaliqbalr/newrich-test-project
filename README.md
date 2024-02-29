
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
            "email" => false,
        ],
        [
            "name" => "email",
            "type" => "email",
            "label" => "Email",
            "rules" => ["required"],
            "email" => true,
        ],
        [
            "name" => "age",
            "type" => "number",
            "label" => "Age",
            "rules" => ["required", "min:10"],
            "email" => false,
        ],
        [
            "name" => "company",
            "type" => "text",
            "label" => "Company",
            "email" => false,
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
            "email" => true,
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
            "email" => true,
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
            "email" => true,
        ],
        [
            "name" => "details",
            "type" => "textarea",
            "label" => "Details",
            "rules" => ["required"],
            "email" => false,
        ],
        [
            "name" => "id",
            "type" => "hidden",
            "email" => false,
        ],
    ]
];
```
