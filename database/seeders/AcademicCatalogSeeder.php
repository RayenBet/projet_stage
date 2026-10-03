<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\Room;
use App\Models\Professor;

class AcademicCatalogSeeder extends Seeder
{
    /**
     * Seeder ADDITIF (aucun truncate) : complète le catalogue existant
     * (classes, matières, salles) sans toucher aux données déjà en base
     * (étudiants, séances, présences, etc.). Rejouable sans créer de
     * doublons grâce à firstOrCreate.
     */
    public function run(): void
    {
        // ---- CLASSES ----
        $classes = [
            ['name' => 'L1 - Informatique', 'department' => 'STIC', 'level' => 'Licence 1', 'campus' => 'ons'],
            ['name' => 'L2 - Informatique', 'department' => 'STIC', 'level' => 'Licence 2', 'campus' => 'ons'],
            // L3 - Informatique existe déjà (id 1)
            ['name' => 'M1 - Informatique', 'department' => 'STIC', 'level' => 'Master 1', 'campus' => 'mharza'],
            ['name' => 'M2 - Informatique', 'department' => 'STIC', 'level' => 'Master 2', 'campus' => 'mharza'],
        ];

        foreach ($classes as $c) {
            Classe::firstOrCreate(['name' => $c['name']], $c);
        }

        // ---- MATIÈRES ----
        $subjects = [
            'Algorithmique et Structures de Données',
            'Programmation Orientée Objet',
            'Réseaux Informatiques',
            "Systèmes d'Exploitation",
            'Intelligence Artificielle',
            'Cybersécurité',
            'Génie Logiciel',
            "Mathématiques pour l'Informatique",
            // Développement Web Laravel et Base de données SQL existent déjà
        ];

        $subjectModels = collect($subjects)->map(
            fn ($name) => Subject::firstOrCreate(['name' => $name])
        );

        // Lier ces nouvelles matières au professeur de démo existant (Dr. Smith),
        // s'il existe, pour qu'elles apparaissent dans son formulaire.
        $professor = Professor::first();
        if ($professor) {
            $professor->subjects()->syncWithoutDetaching($subjectModels->pluck('id'));
        }

        // ---- SALLES ----
        $rooms = [
            // Campus ONS
            ['name' => 'A-01', 'campus' => 'ons', 'type' => 'salle', 'capacity' => 30],
            ['name' => 'A-02', 'campus' => 'ons', 'type' => 'salle', 'capacity' => 30],
            ['name' => 'A-03', 'campus' => 'ons', 'type' => 'salle', 'capacity' => 30],
            ['name' => 'B-01', 'campus' => 'ons', 'type' => 'salle', 'capacity' => 25],
            ['name' => 'B-02', 'campus' => 'ons', 'type' => 'salle', 'capacity' => 25],
            ['name' => 'Salle 102', 'campus' => 'ons', 'type' => 'salle', 'capacity' => 40],
            ['name' => 'Amphi A', 'campus' => 'ons', 'type' => 'amphithéâtre', 'capacity' => 150],
            ['name' => 'Laboratoire 1', 'campus' => 'ons', 'type' => 'laboratoire', 'capacity' => 20],
            ['name' => 'Laboratoire 2', 'campus' => 'ons', 'type' => 'laboratoire', 'capacity' => 20],

            // Campus Mharza
            ['name' => 'Salle Labo B', 'campus' => 'mharza', 'type' => 'laboratoire', 'capacity' => 20],
            ['name' => 'C-01', 'campus' => 'mharza', 'type' => 'salle', 'capacity' => 30],
            ['name' => 'C-02', 'campus' => 'mharza', 'type' => 'salle', 'capacity' => 30],
            ['name' => 'Amphi B', 'campus' => 'mharza', 'type' => 'amphithéâtre', 'capacity' => 120],
            ['name' => 'Laboratoire Réseaux', 'campus' => 'mharza', 'type' => 'laboratoire', 'capacity' => 18],
            ['name' => 'Salle Informatique 1', 'campus' => 'mharza', 'type' => 'laboratoire', 'capacity' => 22],
        ];

        foreach ($rooms as $r) {
            Room::firstOrCreate(['name' => $r['name'], 'campus' => $r['campus']], $r);
        }

        $this->command->info('✅ Catalogue académique complété (classes, matières, salles) sans toucher aux données existantes.');
    }
};