
## Installation
Create database using `src/database/schema.sql` file.

## Config
Update `src/config.php` according to your configuration.

## Create Form
```php
<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://newrich-test-project.test/create-form',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('name' => 'Contact Form','fields' => '[{"name":"name","type":"text","label":"Name","rules":["required"],"email":true},{"name":"email","type":"email","label":"Email","rules":["required"],"email":true},{"name":"age","type":"number","label":"Age","rules":["required","min:10"],"email":true},{"name":"company","type":"text","label":"Company","email":true},{"name":"form_id","type":"hidden","email":false},{"name":"details","type":"textarea","label":"Details","rules":["required"],"email":true}]'),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

```
