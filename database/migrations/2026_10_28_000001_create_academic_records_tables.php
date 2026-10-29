<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->unique();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('number');
            $table->string('name');
            $table->unique(['academic_year_id', 'number']);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('level')->default('Secundaria');
            $table->timestamps();
            $table->unique(['subject_id', 'name']);
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('rude')->nullable()->unique();
            $table->string('ci')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['academic_year_id', 'course_id', 'student_id']);
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('period_id')->constrained()->restrictOnDelete();
            $table->decimal('ser', 4, 1)->default(0);
            $table->decimal('saber', 4, 1)->default(0);
            $table->decimal('hacer', 4, 1)->default(0);
            $table->decimal('autoevaluacion', 4, 1)->default(0);
            $table->decimal('final_score', 5, 1)->storedAs('ser + saber + hacer + autoevaluacion');
            $table->timestamps();
            $table->unique(['enrollment_id', 'period_id']);
            $table->index('final_score');
        });

        DB::table('academic_years')->insert([
            'year' => 2026,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $yearId = DB::table('academic_years')->where('year', 2026)->value('id');
        foreach (['Primer trimestre', 'Segundo trimestre', 'Tercer trimestre'] as $index => $name) {
            DB::table('periods')->insert([
                'academic_year_id' => $yearId,
                'number' => $index + 1,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ([['Física', 'FIS'], ['Biología', 'BIO']] as [$name, $code]) {
            DB::table('subjects')->insert([
                'name' => $name,
                'code' => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('students');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('periods');
        Schema::dropIfExists('academic_years');
    }
};
