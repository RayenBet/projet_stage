<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Vider les tables si elles existent pour éviter les doublons
        DB::statement('PRAGMA foreign_keys = OFF;');
        
        DB::table('student_certificates')->truncate();
        DB::table('attendances')->truncate();
        DB::table('sessions')->truncate();
        DB::table('professor_subjects')->truncate();
        DB::table('students')->truncate();
        DB::table('professors')->truncate();
        DB::table('users')->truncate();
        DB::table('certificates')->truncate();
        DB::table('classes')->truncate();
        DB::table('subjects')->truncate();
        
        DB::statement('PRAGMA foreign_keys = ON;');

        // 2. Insertion des données avec Hash::make()
        // ==========================================
        
        // Classe
        DB::table('classes')->insert([
            'id' => 1, 'name' => 'L3 - Informatique', 'department' => 'STIC', 'level' => 'Licence 3', 'campus' => 'ons'
        ]);

        // Matières
        DB::table('subjects')->insert([
            ['id' => 1, 'name' => 'Développement Web Laravel'],
            ['id' => 2, 'name' => 'Base de données SQL']
        ]);

        // ---- PROFESSEUR ----
        $profId = DB::table('users')->insertGetId([
            'name' => 'Dr. Smith',
            'email' => 'smith@univ.edu',
            'password' => Hash::make('password'), // Hash parfait
            'role' => 'professor',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $professorId = DB::table('professors')->insertGetId([
            'user_id' => $profId,
            'first_name' => 'Dr.',
            'last_name' => 'Smith',
            'specialty' => 'Informatique',
            'department' => 'STIC'
        ]);

        // Lier le prof aux matières
        DB::table('professor_subjects')->insert([
            ['professor_id' => $professorId, 'subject_id' => 1],
            ['professor_id' => $professorId, 'subject_id' => 2],
        ]);

        // Sessions
        $session1 = DB::table('sessions')->insertGetId([
            'class_id' => 1, 'subject_id' => 1, 'professor_id' => $professorId,
            'room' => 'Amphi A', 'campus' => 'ons', 'date' => '2026-08-01',
            'start_time' => '09:00:00', 'end_time' => '11:00:00', 'duration' => 120,
            'status' => 'completed', 'created_at' => now(), 'updated_at' => now()
        ]);
        
        $session2 = DB::table('sessions')->insertGetId([
            'class_id' => 1, 'subject_id' => 2, 'professor_id' => $professorId,
            'room' => 'Salle 102', 'campus' => 'ons', 'date' => '2026-08-02',
            'start_time' => '14:00:00', 'end_time' => '16:00:00', 'duration' => 120,
            'status' => 'completed', 'created_at' => now(), 'updated_at' => now()
        ]);

        // ---- ÉTUDIANT ----
        $studId = DB::table('users')->insertGetId([
            'name' => 'John Doe',
            'email' => 'john.doe@student.com',
            'password' => Hash::make('password'), // Hash parfait
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $studentId = DB::table('students')->insertGetId([
            'user_id' => $studId,
            'class_id' => 1,
            'matricule' => 'STU-2026-001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'score' => 100,
            'total_presence' => 5,
            'total_absence' => 1,
            'total_late' => 2
        ]);

        // Présences
        DB::table('attendances')->insert([
            ['student_id' => $studentId, 'session_id' => $session1, 'status' => 'present', 'arrival_time' => '09:05:00', 'points_earned' => 10],
            ['student_id' => $studentId, 'session_id' => $session2, 'status' => 'late', 'arrival_time' => '14:10:00', 'points_earned' => 5],
        ]);

        // Certificats
        DB::table('certificates')->insert([
            ['id' => 1, 'name' => 'Débutant Laravel', 'min_score' => 50, 'is_active' => 1],
            ['id' => 2, 'name' => 'Expert Base de données', 'min_score' => 100, 'is_active' => 1],
            ['id' => 3, 'name' => 'Certification Fullstack', 'min_score' => 200, 'is_active' => 1],
        ]);

        // Certificats débloqués
        DB::table('student_certificates')->insert([
            ['student_id' => $studentId, 'certificate_id' => 1, 'unlocked_at' => now()],
            ['student_id' => $studentId, 'certificate_id' => 2, 'unlocked_at' => now()],
        ]);

        $this->command->info('✅ Données injectées avec succès !');
    }
}