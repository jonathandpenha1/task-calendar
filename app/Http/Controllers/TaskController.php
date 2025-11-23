<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TasksImport;
use Illuminate\Support\Facades\Log;


class TaskController extends Controller
{
    //Display task
    public function getByDate(Request $request)
    {
        $date = $request->date;

        $tasks = Task::whereDate('due_date', $date)
            ->with('category')
            ->get()
            ->map(function ($task) {
                $task->categoryColor = $task->category->color ?? '#6c757d';
                return $task;
            });

        return response()->json($tasks);
    }

    //create task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'required|date',
            'priority'    => 'required|in:low,medium,high',
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);

        $task = Task::create($validated);
        \Log::info($request->all());

        return response()->json($task, 201);
    }

    //edit task
    public function update(Request $request, Task $task)
    {
        $validatedData = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'priority'    => 'nullable|string|in:low,medium,high',
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);

        $task->update($validatedData);
        return response()->json($task, 200);
    }

    //delete task
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => true]);
    }

    //filters
    public function filter(Request $request)
    {
        $tasks = Task::with('category');

        // Search
        if ($request->search) {
            $tasks->where('title', 'like', '%' . $request->search . '%');
        }

        // Priority
        if ($request->priority) {
            $tasks->where('priority', $request->priority);
        }

        // Category
        if ($request->category_id) {
            $tasks->where('category_id', $request->category_id);
        }

        // Status
        if ($request->status) {
            $tasks->where('status', $request->status);
        }

        // Date Filters
        if ($request->dateRange === 'today') {
            $tasks->whereDate('due_date', Carbon::today());
        }

        if ($request->dateRange === 'week') {

            // Week = Sunday → Saturday
            $start = Carbon::now()->startOfWeek(Carbon::SUNDAY);
            $end   = Carbon::now()->endOfWeek(Carbon::SATURDAY);

            $tasks->whereBetween('due_date', [$start, $end]);
        }

        if ($request->dateRange === 'month') {
            $tasks->whereBetween('due_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ]);
        }

        if ($request->dateRange === 'custom') {
            if ($request->startDate && $request->endDate) {
                $tasks->whereBetween('due_date', [
                    Carbon::parse($request->startDate),
                    Carbon::parse($request->endDate),
                ]);
            }
        }

        return response()->json($tasks->orderBy('due_date')->get());
    }

    //mark task as complete
    public function toggleComplete(Task $task)
    {
        $task->status = $task->status === 'completed' ? 'pending' : 'completed';
        $task->save();

        return response()->json($task);
    }


    //export tasks
    public function export(Request $request)
    {
        $tasks = Task::with('category');

        if ($request->search) {
            $tasks->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->priority) {
            $tasks->where('priority', $request->priority);
        }

        if ($request->category_id) {
            $tasks->where('category_id', $request->category_id);
        }

        if ($request->status) {
            $tasks->where('status', $request->status);
        }

        if ($request->dateRange === 'today') {
            $tasks->whereDate('due_date', Carbon::today());
        }

        if ($request->dateRange === 'week') {
            $start = Carbon::now()->startOfWeek(Carbon::SUNDAY);
            $end = Carbon::now()->endOfWeek(Carbon::SATURDAY);
            $tasks->whereBetween('due_date', [$start, $end]);
        }

        if ($request->dateRange === 'month') {
            $tasks->whereBetween('due_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ]);
        }

        if ($request->dateRange === 'custom') {
            if ($request->startDate && $request->endDate) {
                $tasks->whereBetween('due_date', [
                    Carbon::parse($request->startDate),
                    Carbon::parse($request->endDate),
                ]);
            }
        }

        // Fetch the filtered tasks
        $tasks = $tasks->orderBy('due_date')->get();

        // Create a CSV response
        $response = new StreamedResponse(function() use ($tasks) {
            $output = fopen('php://output', 'w');
            
            // Add header to CSV
            fputcsv($output, ['Title', 'Description', 'Due Date', 'Priority', 'Category', 'Status']);

            foreach ($tasks as $task) {
                fputcsv($output, [
                    $task->title,
                    $task->description,
                    $task->due_date->format('Y-m-d H:i:s'),
                    $task->priority,
                    $task->category ? $task->category->name : 'No Category',
                    $task->status
                ]);
            }

            fclose($output);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="tasks.csv"');
        return $response;
    }

    //import tasks
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        try {
            // Handle file upload
            $file = $request->file('file');

            // Log the file name to confirm it's being uploaded
            Log::info('File uploaded: ' . $file->getClientOriginalName());

            // Use the Excel package to handle CSV import
            Excel::import(new TasksImport, $file);

            // After importing, redirect to the homepage with a success message
            return redirect('/')->with('success', 'Tasks imported successfully!');
        } catch (\Exception $e) {
            Log::error('Error importing tasks: ' . $e->getMessage());
            return redirect('/')->with('error', 'Error importing tasks.');
        }
    }


}
