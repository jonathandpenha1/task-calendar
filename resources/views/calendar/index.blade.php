@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">

        <!-- Filter Sidebar on the Left -->
        <div class="col-lg-3 mb-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Filter Tasks</h4>
            </div>

            <form id="filterForm">
                <!-- Search -->
                <div class="mb-3">
                    <input type="text" id="search" name="search" class="form-control" placeholder="Search by task title">
                </div>

                <!-- Priority -->
                <div class="mb-3">
                    <select id="priority" name="priority" class="form-control">
                        <option value="">Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <select id="status" name="status" class="form-control">
                        <option value="">Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <!-- Date Filters -->
                <h5 class="mt-4">Date Filters</h5>

                <div class="mb-3">
                    <select id="dateRange" class="form-control">
                        <option value="">Select Range</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                <div id="customRangeFields" style="display: none;">
                    <div class="mb-3">
                        <label>Start Date</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>End Date</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                </div>
            </form>

            <!-- Filtered tasks display -->
            <div id="filteredTasks" class="mt-4">
                <!-- Filtered tasks will be injected here -->
            </div>

            <a href="{{ route('tasks.export') }}" class="btn btn-outline-primary">Export Tasks (CSV)</a>
            <form action="{{ route('tasks.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="form-label">Import CSV</label>
                    <input type="file" class="form-control" id="file" name="file" accept=".csv">
                </div>
                <button type="submit" class="btn btn-outline-success">Import Tasks</button>
            </form>
        </div>

        <!-- Calendar Section -->
        <div class="col-lg-6 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="/?month={{ $month-1 }}&year={{ $year }}" class="btn btn-outline-secondary">&laquo; Prev</a>
                <h3 class="mb-0">{{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</h3>
                <a href="/?month={{ $month+1 }}&year={{ $year }}" class="btn btn-outline-secondary">Next &raquo;</a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-2">
                    <table class="table table-bordered mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Get the first day of the month in IST
                                $carbon = \Carbon\Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Kolkata');
                                $firstDay = $carbon->dayOfWeek;
                                $daysInMonth = $carbon->daysInMonth;
                                $day = 1;
                                $i = 0;

                                // Get today's date in IST format for comparison
                                $today = \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d');
                            @endphp

                            <tr>
                            @for ($x = 0; $x < $firstDay; $x++)
                                <td></td>
                                @php $i++; @endphp
                            @endfor

                            @while ($day <= $daysInMonth)
                                @if ($i % 7 == 0)
                                    </tr><tr>
                                @endif

                                @php
                                    // Create the current date for the day being looped
                                    $currentDate = \Carbon\Carbon::create($year, $month, $day, 0, 0, 0, 'Asia/Kolkata');
                                    
                                    // Check if the current date is today
                                    $todayClass = $currentDate->toDateString() === $today ? 'border border-primary text-primary fw-bold today' : '';

                                    // Filter tasks for the current date
                                    $tasksForDate = $tasks->filter(fn($t) => $t->due_date->isSameDay($currentDate));
                                @endphp

                                <td class="date-cell p-3 {{ $todayClass }}"
                                    data-date="{{ $currentDate->toDateString() }}"
                                    style="cursor:pointer; min-height:120px; vertical-align:top; font-size:1em;">

                                    <div class="fw-bold">{{ $day }}</div>

                                    <div class="events-container" id="events-{{ $currentDate->toDateString() }}">
                                        @foreach ($tasksForDate as $task)
                                            <div class="task-title d-inline-block"
                                                style="border:1px solid {{ $task->category->color ?? '#6c757d' }};
                                                    color:{{ $task->category->color ?? '#6c757d' }};
                                                    border-radius:20px; font-size:0.4em;">
                                                {{ $task->title }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>

                                @php
                                    $day++;
                                    $i++;
                                @endphp
                            @endwhile
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- Right Sidebar -->
        <div class="col-lg-3" style="border-left:1px solid #ddd;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Tasks</h4>
                <button class="btn btn-sm btn-success" onclick="openAddModal()">
                    <i class="bi bi-plus"></i> Add
                </button>
                <button class="btn btn-sm btn-primary" onclick="window.location.href='{{ route('categories.create') }}'">
                    <i class="bi bi-plus"></i> Calendar
                </button>
            </div>

            <div id="taskList">
                <p class="text-muted">Select a date to view tasks</p>
            </div>
        </div>
    </div>
</div>

@include('calendar.modals')
@endsection

@push('scripts')
<script src="/js/calendar.js"></script>

<script>
// ----------------------
// Priority class helper
// ----------------------
function getPriorityClass(priority) {
    switch(priority.toLowerCase()){
        case 'low': return 'secondary';
        case 'medium': return 'warning';
        case 'high': return 'danger';
        default: return 'primary';
    }
}

// ----------------------
// Show/hide custom date fields
// ----------------------
document.getElementById('dateRange').addEventListener('change', function() {
    document.getElementById('customRangeFields').style.display =
        this.value === "custom" ? "block" : "none";
});

// ----------------------
// Filter AJAX Handler
// ----------------------
document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Get filter values
    const search = document.getElementById('search').value.trim();
    const priority = document.getElementById('priority').value;
    const category_id = document.getElementById('category_id').value;
    const status = document.getElementById('status').value;
    const dateRange = document.getElementById('dateRange').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    // Check if any filter has been applied
    const hasFilters = search || priority || category_id || status || dateRange || startDate || endDate;

    if (!hasFilters) {
        return;
    }

    // If filters are provided, proceed to fetch filtered tasks
    const payload = {
        search: search,
        priority: priority,
        category_id: category_id,
        status: status,
        dateRange: dateRange,
        startDate: startDate,
        endDate: endDate
    };

    fetch('/api/tasks/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(tasks => {
        renderTasks(tasks);
    })
    .catch(err => console.error('Filter error:', err));
});

// Function to render tasks (filtered or unfiltered)
function renderTasks(tasks) {
    let list = document.getElementById('filteredTasks');
    list.innerHTML = '';

    if (tasks.length === 0) {
        list.innerHTML = '<p>No tasks found.</p>';
        return;
    }

    tasks.forEach(t => {
        let categoryRibbon = t.category
            ? `<span class="badge" style="background-color:${t.category.color};">${t.category.name}</span>`
            : '';

        let priorityLabel = `<span class="badge bg-${getPriorityClass(t.priority)}">${t.priority}</span>`;

        let statusClass = t.status === 'completed' ? 'bg-success' : 'bg-warning';
        let statusIcon = t.status === 'completed'
            ? '<i class="bi bi-check-circle"></i>'
            : '<i class="bi bi-clock"></i>';

        let div = document.createElement('div');
        div.classList.add('task-item');
        div.setAttribute('data-id', t.id);
        div.innerHTML = `
            ${categoryRibbon}
            <strong>${t.title}</strong>
            <p>${t.description ?? ''}</p>
            <small>Due: ${new Date(t.due_date).toLocaleString()}</small>
            <div class="priority-label">${priorityLabel}</div>
            <div class="task-status ${statusClass}">
                ${statusIcon} <span>${t.status === 'completed' ? 'Completed' : 'Pending'}</span>
            </div>
            <div class="task-buttons mt-2">
                <button class="btn btn-sm btn-delete" data-id="${t.id}" data-date="${t.due_date}">
                    <i class="fas fa-trash-alt" style="color:#dc3545;"></i>
                </button>
                <button class="btn btn-sm btn-edit" data-id="${t.id}">
                    <i class="fas fa-edit" style="color:#17a2b8;"></i>
                </button>
            </div>
        `;

        list.appendChild(div);

        const deleteBtn = div.querySelector('.btn-delete');
        deleteBtn.addEventListener('click', e => {
            const btn = e.target.closest('button');
            openDeleteModal(btn.dataset.id, btn.dataset.date);
        });

        const editBtn = div.querySelector('.btn-edit');
        editBtn.addEventListener('click', e => {
            const id = e.target.closest('button').dataset.id;
            openEditModal(id);
        });
    });
}

// ----------------------
// Focus on Today when page loads
// ----------------------
document.addEventListener('DOMContentLoaded', function() {
    const todayCell = document.querySelector('.today');
    if (todayCell) {
        todayCell.click();
        todayCell.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Reload Calendar with tasks for selected month and year
function reloadCalendar(month, year) {
    fetch(`/tasks/${month}/${year}`)
        .then(response => response.json())
        .then(tasks => {
            // Clear existing task events in the calendar
            document.querySelectorAll('.events-container').forEach(container => {
                container.innerHTML = '';
            });

            // Add the new tasks to the calendar cells
            tasks.forEach(task => {
                const taskDate = task.due_date.split(' ')[0]; // '2025-11-23'
                const taskDateCell = document.querySelector(`[data-date="${taskDate}"] .events-container`);

                if (taskDateCell) {
                    const taskElement = document.createElement('div');
                    taskElement.classList.add('task-title');
                    taskElement.style.border = `1px solid ${task.category.color || '#6c757d'}`;
                    taskElement.style.color = task.category.color || '#6c757d';
                    taskElement.style.borderRadius = '20px';
                    taskElement.style.fontSize = '0.4em';
                    taskElement.textContent = task.title;

                    taskDateCell.appendChild(taskElement);
                }
            });
        })
        .catch(error => console.error('Error fetching tasks:', error));
}

// Attach click event listeners to the navigation buttons
document.querySelectorAll('.btn-outline-secondary').forEach(button => {
    button.addEventListener('click', function(event) {
        const month = parseInt(event.target.href.split('month=')[1].split('&')[0], 10);
        const year = parseInt(event.target.href.split('year=')[1], 10);
        reloadCalendar(month, year);
    });
});

</script>
@endpush
