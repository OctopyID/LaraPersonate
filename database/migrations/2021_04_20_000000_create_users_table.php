<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Octopy\LaraPersonate\Tests\Stubs\Models\User;

/**
 * Class CreateUsersTable
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('admin');
            $table->boolean('impersonated');
            $table->timestamps();
        });

        $this->seed();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }

    /**
     * @return void
     */
    protected function seed()
    {
        User::create([
            'name'         => 'Super Admin',
            'email'        => 'super@example.com',
            'password'     => bcrypt('secret'),
            'admin'        => true,
            'impersonated' => false,
        ]);

        User::create([
            'name'         => 'Admin',
            'email'        => 'admin@example.com',
            'password'     => bcrypt('secret'),
            'admin'        => true,
            'impersonated' => true,
        ]);

        User::create([
            'name'         => 'User',
            'email'        => 'user@example.com',
            'password'     => bcrypt('secret'),
            'admin'        => false,
            'impersonated' => true,
        ]);
    }
}
