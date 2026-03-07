<?php

namespace Database\Seeders;

use App\Models\ProjectStage;
use Illuminate\Database\Seeder;

class ProjectStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Request Submitted',
                'description' => 'Project request has been submitted and awaiting review',
                'order' => 1,
                'icon' => 'fas fa-paper-plane',
                'color' => '#3498db',
                'estimated_duration' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Under Review',
                'description' => 'Project request is being reviewed by the admin team',
                'order' => 2,
                'icon' => 'fas fa-search',
                'color' => '#9b59b6',
                'estimated_duration' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Requirements Analysis',
                'description' => 'Analyzing project requirements and specifications',
                'order' => 3,
                'icon' => 'fas fa-clipboard-list',
                'color' => '#e67e22',
                'estimated_duration' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Design Phase',
                'description' => 'Creating design mockups and architecture',
                'order' => 4,
                'icon' => 'fas fa-paint-brush',
                'color' => '#f39c12',
                'estimated_duration' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Development',
                'description' => 'Active development and coding phase',
                'order' => 5,
                'icon' => 'fas fa-code',
                'color' => '#1abc9c',
                'estimated_duration' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Testing',
                'description' => 'Quality assurance and testing phase',
                'order' => 6,
                'icon' => 'fas fa-vial',
                'color' => '#e74c3c',
                'estimated_duration' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Client Review',
                'description' => 'Client reviewing and providing feedback',
                'order' => 7,
                'icon' => 'fas fa-user-check',
                'color' => '#34495e',
                'estimated_duration' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Deployment',
                'description' => 'Deploying to production environment',
                'order' => 8,
                'icon' => 'fas fa-rocket',
                'color' => '#16a085',
                'estimated_duration' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Completed',
                'description' => 'Project has been successfully completed',
                'order' => 9,
                'icon' => 'fas fa-check-circle',
                'color' => '#27ae60',
                'estimated_duration' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($stages as $stage) {
            ProjectStage::create($stage);
        }
    }
}
