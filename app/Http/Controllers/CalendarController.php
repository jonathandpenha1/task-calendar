<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;
use Carbon\Carbon;

class CalendarController extends Controller
{
    //
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        // Fetch all tasks for the given month
        $tasks = Task::whereYear('due_date', $year)
                    ->whereMonth('due_date', $month)
                    ->with('category')
                    ->get();
        $categories = Category::all();
        return view('calendar.index', compact('year', 'month', 'tasks', 'categories'));
    }

     public function getTasksForMonth($month, $year)
    {
        $tasks = Task::whereYear('due_date', $year)
                    ->whereMonth('due_date', $month)
                    ->with('category')
                    ->get();

        // Return tasks as JSON for AJAX request
        return response()->json($tasks);
    }
}
