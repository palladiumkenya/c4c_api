<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $user = new User();
        $user->role_id = 1;
        $user->first_name = "System";
        $user->surname = "Admin";
        $user->email = 'admin@c4c.com';
        $user->msisdn = '254711111111';
        $user->gender = 'MALE';
        $user->password = bcrypt('pass123');
        $user->save();
    }
}
