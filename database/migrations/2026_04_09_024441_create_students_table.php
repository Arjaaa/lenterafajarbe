<?php

//use Illuminate\Database\Migrations\Migration;
//use Illuminate\Database\Schema\Blueprint;
//use Illuminate\Support\Facades\Schema;
//return new class extends Migration
//{
/**
 * Run the migrations.
 */
//public function up(): void
// {
//  Schema::create('students', function (Blueprint $table) {
//  $table->id();
//  $table->string('name'); // ← pastikan ini ada
// $table->timestamps();
//});
//    Schema::create('students', function (Blueprint $table) {
//        $table->id();
//       $table->string('name');
//     $table->string('nis')->unique();
//   $table->date('birth_date')->nullable();
// $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('cascade');
// $table->timestamps();
// });//
//}

/**
 * Reverse the migrations.
 */
//  public function down(): void
//  {
//    Schema::dropIfExists('students');
//  }
//}; 


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('school_name')->nullable();
            $table->text('address')->nullable();
            $table->string('special_needs')->nullable();
            $table->text('diagnosis_notes')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('parent_phone')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
