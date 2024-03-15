<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('address')->nullable(); // Ajoutez la colonne 'address'
            $table->string('city')->nullable();    // Ajoutez la colonne 'city'
            $table->string('postal_code')->nullable(); 
            $table->string('profile_image')->nullable();
            $table->integer('status')->default(0);
            $table->string('role')->default('user'); // Ajoutez la colonne 'role' par défaut 'user'
            $table->rememberToken();
            $table->timestamps();
        });

        // Ajouter l'administrateur par défaut
        \DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456789'), // Assurez-vous de hasher le mot de passe
            'role' => 'admin',
            'status' => 1, // Mettez le statut à 1 pour activer l'administrateur
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Ajouter le technicien par défaut
        \DB::table('users')->insert([
            'name' => 'Technicien',
            'email' => 'technicien@example.com',
            'password' => Hash::make('123456789'), // Assurez-vous de hasher le mot de passe
            'role' => 'technicien',
            'status' => 1, // Mettez le statut à 1 pour activer le technicien
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

