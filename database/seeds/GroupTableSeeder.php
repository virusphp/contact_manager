<?php

use Illuminate\Database\Seeder;

class GroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('groups')->truncate();

        $groups = [
        	['id' => 1, 'name' => 'Keluarga', 'created_at' => new DateTime, 'updated_at' => new DateTime],
        	['id' => 2, 'name' => 'Teman', 'created_at' => new DateTime, 'updated_at' => new DateTime],
        	['id' => 3, 'name' => 'Klient', 'created_at' => new DateTime, 'updated_at' => new DateTime],
        ];

        DB::table('groups')->insert($groups);
    }
}
