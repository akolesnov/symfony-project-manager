<?php

declare(strict_types=1);

namespace App\ReadModel\User;

class AuthView
{
    public $id;
    public $email;
    public $password_hash;
    public $role;
    public $status;

    public static function fromArray(array $data = []): self
    {
        foreach (get_object_vars($obj = new self) as $property => $default) {
            $obj->$property = $data[$property] ?? $default;
        }
        return $obj;
    }
}