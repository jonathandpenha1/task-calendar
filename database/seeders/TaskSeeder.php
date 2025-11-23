<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Category;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch the categories dynamically
        $workCategory = Category::where('name', 'Work')->first();
        $schoolCategory = Category::where('name', 'School')->first();
        $shoppingCategory = Category::where('name', 'Shopping')->first();
        $personalCategory = Category::where('name', 'Personal')->first();

        // Ensure categories exist before seeding tasks
        if (!$workCategory || !$schoolCategory || !$shoppingCategory || !$personalCategory) {
            throw new \Exception("One or more categories are missing in the database.");
        }

        Task::insert([
            [
                'title' => 'Complete Project Proposal',
                'description' => 'Finish writing the proposal for the upcoming project. Include project goals, timelines, and budget estimates.',
                'due_date' => '2025-11-25 09:00:00',
                'priority' => 'high',
                'category_id' => $workCategory->id, // Dynamic category ID
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Buy Groceries',
                'description' => 'Purchase groceries for the week including vegetables, fruits, dairy, and snacks.',
                'due_date' => '2025-11-23 10:00:00',
                'priority' => 'medium',
                'category_id' => $shoppingCategory->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Attend Yoga Class',
                'description' => 'Join the scheduled online yoga class to improve flexibility and reduce stress.',
                'due_date' => '2025-11-24 07:00:00',
                'priority' => 'low',
                'category_id' => $personalCategory->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Prepare for Final Exam',
                'description' => 'Review notes and practice problems for the final exam in Mathematics. Focus on calculus and probability sections.',
                'due_date' => '2025-11-30 23:59:59',
                'priority' => 'high',
                'category_id' => $schoolCategory->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Organize Family Dinner',
                'description' => 'Plan and prepare a meal for the family. Menu includes lasagna, salad, and garlic bread.',
                'due_date' => '2025-11-26 17:00:00',
                'priority' => 'medium',
                'category_id' => $personalCategory->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Submit Monthly Report',
                'description' => 'Complete and submit the monthly financial report. Ensure all data is accurate and formatted correctly.',
                'due_date' => '2025-11-27 18:00:00',
                'priority' => 'high',
                'category_id' => $workCategory->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Doctor’s Appointment',
                'description' => 'Attend the scheduled check-up appointment at the health center. Ensure to get the routine blood work done.',
                'due_date' => '2025-11-28 09:30:00',
                'priority' => 'low',
                'category_id' => $personalCategory->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Complete Website Mockup',
                'description' => 'Design and finalize the mockup for the client’s website, including homepage, product page, and contact form.',
                'due_date' => '2025-11-29 12:00:00',
                'priority' => 'high',
                'category_id' => $workCategory->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Submit Homework Assignment',
                'description' => 'Finish and submit the homework for History class. Ensure all questions are answered and the essay is well-written.',
                'due_date' => '2025-11-24 20:00:00',
                'priority' => 'medium',
                'category_id' => $schoolCategory->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Clean the House',
                'description' => 'Perform a general cleaning of the house. Vacuum the floors, clean the kitchen, and organize the living room.',
                'due_date' => '2025-11-23 14:00:00',
                'priority' => 'low',
                'category_id' => $personalCategory->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
