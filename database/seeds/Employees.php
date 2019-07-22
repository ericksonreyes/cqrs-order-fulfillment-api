<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Employees extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = [
            [
                'id' => 'employee-1',
                'username' => 'employee',
                'password' => md5('password')
            ]
        ];

        foreach ($employees as $employee) {
            DB::table('employees')->insert([
                'id' => $employee['id'],
                'username' => $employee['username'],
                'password' => $employee['password']
            ]);
        }
    }
}
