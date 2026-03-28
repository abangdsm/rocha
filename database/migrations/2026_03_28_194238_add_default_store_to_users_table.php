<?php

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       $users = User::all();
        foreach ($users as $user) {
            if ($user->stores()->count() == 0) {
                Store::create([
                    'user_id' => $user->id,
                    'name' => $user->store_name ?? 'Toko ' . $user->name,
                    'slug' => 'toko-' . $user->id,
                    'phone' => $user->phone ?? '08123456789',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
