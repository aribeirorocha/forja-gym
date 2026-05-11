<?php

declare(strict_types = 1);

class User {

    public int $id;
    public string $username;
    public string $name;
    public string $email;
    public string $photoPath;
    public string $role;

    public function __construct(int $id, string $username, string $name, string $email, string $photoPath, string $role) {
        $this->id = $id;
        $this->username = $username;
        $this->name = $name;
        $this->email = $email;
        $this->photoPath = $photoPath;
        $this->role = $role; 
    }

}