<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("database/data/default_role.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            \App\Role::firstOrCreate(array(
                'id' => $obj->id,
                'name' => $obj->name
            ));
        }
    }
}
