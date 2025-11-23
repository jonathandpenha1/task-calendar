<?php

namespace App\Imports;

use App\Models\Task;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class TasksImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        try {
            Log::info('Importing task: ' . json_encode($row));

            $categoryName = $row['category'];

            $category = Category::where('name', 'like', $categoryName)->first();

            // Log a warning if category is not found
            if (!$category) {
                Log::warning('Category not found for task: ' . $row['title'] . ' with category: ' . $categoryName);
                return null;
            }

            // Validate due_date
            $dueDate = \Carbon\Carbon::parse($row['due_date']);
            if (!$dueDate) {
                Log::warning('Invalid due_date for task: ' . $row['title']);
                return null;
            }

            return new Task([
                'title'       => $row['title'],
                'description' => $row['description'],
                'due_date'    => $dueDate,
                'priority'    => $row['priority'],
                'category_id' => $category->id,
                'status'      => $row['status'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing task: ' . $e->getMessage());
            return null;
        }
    }
}
