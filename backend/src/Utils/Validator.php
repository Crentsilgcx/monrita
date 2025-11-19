<?php

namespace App\Utils;

class Validator
{
    public static function requireFields(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $errors[$field] = 'This field is required';
            }
        }
        return $errors;
    }
}
