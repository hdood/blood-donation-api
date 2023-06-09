<?php

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
        Schema::create('donors', function (Blueprint $table) {
            $table->uuid("id");
            $table->timestamps();
            $table->string("name");
            $table->string("email")->unique();
            $table->string("password")->nullable();
            $table->date("dob");
            $table->enum("gender", ['male', 'female'])->default("male");
            $table->enum('bloodGroup', ['a', 'b', 'ab', 'o']);
            $table->enum("rhFactor", ["positive", "negative"]);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string("address");
            $table->string("phone");
            $table->boolean("active")->default(false);
            $table->boolean("eligible")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
