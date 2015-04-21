<?php

use Illuminate\Database\Seeder;
use Owl\Repositories\Models\User;

class UserTableSeeder extends Seeder {

    public function run()
    {
        $user = new User;
        $user->fill(array(
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ));
        $user->save();
    }
}

