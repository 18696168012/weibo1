<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::factory(100)->create();
        $user=User::find(1);
        $user->name='张宏扬';
        $user->email='791264638@qq.com';
        $user->password=bcrypt(123456);
        $user->is_admin=true;
        $user->save();
    }
}
