// =====================
// Global variable for deletion
// =====================
let taskToDelete = null;

// =====================
// Open Add Task Modal
// =====================
function openAddModal() {
    const modalEl = document.getElementById('addTaskModal');
    if (!modalEl) return console.error('Add Task modal not found!');

    modalEl.removeAttribute('aria-hidden');
    modalEl.setAttribute('aria-hidden', 'false');
    modalEl.querySelector('.modal-dialog').removeAttribute('inert');

    // Get selected date
    const selectedDateCell = document.querySelector(".date-cell.border-primary");

    if (!selectedDateCell) {
        const toastEl = document.getElementById('toastNoDate');
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
        return;
    }

    const date = selectedDateCell.dataset.date;

    const formattedDate = new Date(date);
    const formatted = formattedDate.toISOString().slice(0, 16); // "YYYY-MM-DDTHH:MM"

    const dateInput = document.getElementById("taskDateInput");
    if (dateInput) {
        dateInput.value = formatted;
    }

    // Show modal
    const myModal = new bootstrap.Modal(modalEl);
    myModal.show();
}

document.getElementById('addTaskModal').addEventListener('hidden.bs.modal', function () {
    const modalEl = document.getElementById('addTaskModal');
    modalEl.setAttribute('aria-hidden', 'true'); // Add aria-hidden back when modal is closed
    modalEl.querySelector('.modal-dialog').setAttribute('inert', '');
});

// =====================
// Open Delete Task Modal
// =====================
function openDeleteModal(taskId, date) {
    taskToDelete = { id: taskId, date: date };

    const deleteModalEl = document.getElementById('deleteTaskModal');
    const deleteModal = new bootstrap.Modal(deleteModalEl);
    deleteModal.show();
}

// =====================
// Delete task confirmation
// =====================
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener('click', async (event) => {
        event.preventDefault();

        if (!taskToDelete) return;

        try {
            // Send the delete request to the backend
            const res = await fetch(`/api/tasks/${taskToDelete.id}`, { method: 'DELETE' });

            if (!res.ok) throw new Error("Failed to delete task");

            // Remove task element from the right sidebar
            const taskElement = document.querySelector(`.task-item[data-id="${taskToDelete.id}"]`);
            if (taskElement) {
                taskElement.remove();
            }

            // Close delete modal in the right sidebar
            const deleteModalEl = document.getElementById('deleteTaskModal');
            const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
            if (deleteModal) deleteModal.hide();

            taskToDelete = null;

            // No need to refresh the left sidebar (as per the requirements)

        } catch (err) {
            console.error('Error deleting task:', err);
        }
    });
}


// =====================
// Return Bootstrap color for priority
// =====================
function getPriorityClass(priority) {
    switch(priority.toLowerCase()){
        case 'low': return 'secondary';
        case 'medium': return 'warning';
        case 'high': return 'danger';
        default: return 'primary';
    }
}

// =====================
// Load tasks dynamically
// =====================
async function loadTasks(date = null, filters = {}) {
    try {
        // Build the URL with query parameters for date and filters
        let url = `/api/tasks?date=${date || ''}`;

        // Append filters to the URL if they are set
        if (filters) {
            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    url += `&${key}=${filters[key]}`;
                }
            });
        }

        // Fetch tasks from the backend with applied filters
        const res = await fetch(url);
        if (!res.ok) throw new Error("Failed to load tasks");

        const tasks = await res.json();

        // Clear the task list in the sidebar before adding new tasks
        document.getElementById("taskList").innerHTML = '';

        // If no tasks, show a message
        if (tasks.length === 0) {
            document.getElementById("taskList").innerHTML = '<p class="text-muted">No tasks found.</p>';
        }

        tasks.forEach(t => {
            // Create a div element to hold the task
            const div = document.createElement("div");
            div.className = "task-item " + (t.status === "completed" ? "completed" : "");
            div.setAttribute("data-id", t.id); // Set the task ID as a data attribute

            // Get priority badge color
            const priorityClass = getPriorityClass(t.priority);
            const priorityLabel = `<span class="badge bg-${priorityClass}">${t.priority}</span>`;

            // Category ribbon (if any)
            const categoryRibbon = t.categoryColor ? `<div class="category-ribbon" style="background-color: ${t.categoryColor};"></div>` : '';

            // Determine the task's status and corresponding icon
            const statusIcon = t.status === 'completed' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-hourglass-half"></i>';
            const statusText = t.status === 'completed' ? 'Completed' : 'Pending';
            const statusClass = t.status === 'completed' ? 'completed' : '';

            // Set the inner HTML of the task item
            div.innerHTML = `
                ${categoryRibbon}
                <strong>${t.title}</strong>
                <p>${t.description}</p>
                <small>Due: ${new Date(t.due_date).toLocaleString()}</small>
                <div class="priority-label">${priorityLabel}</div>
                <div class="task-status ${statusClass}">
                    ${statusIcon} <span>${statusText}</span>
                </div>
                <div class="task-buttons mt-2">
                    <input type="checkbox" class="form-check-input btn-toggle" data-id="${t.id}" data-date="${t.due_date}" ${t.status === 'completed' ? 'checked' : ''}>
                    <label class="form-check-label">
                        ${t.status === 'completed' ? 'Undo' : 'Complete'}
                    </label>
                    <button class="btn btn-sm btn-delete" data-id="${t.id}" data-date="${t.due_date}">
                        <i class="fas fa-trash-alt" style="color: #dc3545;"></i> <!-- Red color for delete -->
                    </button>
                    <button class="btn btn-sm btn-edit" data-id="${t.id}" data-date="${t.due_date}">
                        <i class="fas fa-edit" style="color: #17a2b8;"></i> <!-- Blue color for edit -->
                    </button>
                </div>
            `;
            document.getElementById("taskList").appendChild(div);

            // Attach event listener to the checkbox for task completion toggle
            const checkbox = div.querySelector('.btn-toggle');
            if (checkbox) {
                checkbox.addEventListener('change', async (event) => {
                    const taskId = event.target.dataset.id;
                    const isChecked = event.target.checked;

                    try {
                        // Send the toggle request to the backend to update the task status
                        const res = await fetch(`/api/tasks/${taskId}/toggle`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ status: isChecked ? 'completed' : 'pending' })
                        });

                        if (!res.ok) throw new Error("Failed to toggle task");

                        // Update the task status visually based on the checkbox state
                        const taskElement = document.querySelector(`.task-item[data-id="${taskId}"]`);
                        if (taskElement) {
                            // Toggle the 'completed' class to apply/remove line-through
                            taskElement.classList.toggle("completed", isChecked);

                            // Update the status text and color
                            const statusSpan = taskElement.querySelector('.task-status span');
                            const statusIcon = taskElement.querySelector('.task-status i');
                            const statusLabel = taskElement.querySelector('label');
                            
                            if (isChecked) {
                                statusSpan.textContent = 'Completed';
                                statusSpan.parentElement.classList.add('completed');
                                statusIcon.classList.remove('fa-hourglass-half');
                                statusIcon.classList.add('fa-check-circle');
                                statusLabel.textContent = 'Undo';
                            } else {
                                statusSpan.textContent = 'Pending';
                                statusSpan.parentElement.classList.remove('completed');
                                statusIcon.classList.remove('fa-check-circle');
                                statusIcon.classList.add('fa-hourglass-half');
                                statusLabel.textContent = 'Complete';
                            }
                        }
                    } catch (err) {
                        console.error('Error toggling task:', err);
                    }
                });
            }

            // Attach event listener to the delete button
            const deleteBtn = div.querySelector('.btn-delete');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', (event) => {
                    const taskId = event.target.closest('button').dataset.id;
                    const taskDate = event.target.closest('button').dataset.date;
                    openDeleteModal(taskId, taskDate);
                });
            }

            // Attach event listener to the edit button
            const editBtn = div.querySelector('.btn-edit');
            if (editBtn) {
                editBtn.addEventListener('click', (event) => {
                    const taskId = event.target.closest('button').dataset.id;
                    openEditModal(taskId);
                });
            }
        });

    } catch (err) {
        console.error("Error loading tasks:", err);
    }
}



// =====================
// Toggle task complete status
// =====================
document.querySelectorAll('.btn-toggle').forEach(checkbox => {
    checkbox.addEventListener('change', async (event) => {
        const taskId = event.target.dataset.id;
        const isChecked = event.target.checked;
        
        // Send the toggle request to the backend to update the task status
        try {
            const res = await fetch(`/api/tasks/${taskId}/toggle`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: isChecked ? 'completed' : 'pending' })  // Sending updated status
            });

            if (!res.ok) {
                throw new Error("Failed to toggle task");
            }

            const taskElement = document.querySelector(`.task-item[data-id="${taskId}"]`);
            if (taskElement) {
                const statusSpan = taskElement.querySelector('.task-status span');
                const statusIcon = taskElement.querySelector('.task-status i');
                const statusLabel = taskElement.querySelector('label');

                if (isChecked) {
                    statusSpan.textContent = 'Completed';
                    statusIcon.classList.remove('fa-hourglass-half');
                    statusIcon.classList.add('fa-check-circle');
                    statusLabel.textContent = 'Undo';
                    taskElement.classList.add('completed');
                } else {
                    statusSpan.textContent = 'Pending';
                    statusIcon.classList.remove('fa-check-circle');
                    statusIcon.classList.add('fa-hourglass-half');
                    statusLabel.textContent = 'Complete';
                    taskElement.classList.remove('completed');
                }
            }

        } catch (err) {
            console.error('Error toggling task status:', err);
        }
    });
});

// =====================
// DOMContentLoaded
// =====================
document.addEventListener("DOMContentLoaded", () => {
    // Highlight today's date by default
    const today = new Date();
    const todayDateString = today.toISOString().slice(0, 10); // "YYYY-MM-DD"
    
    const dateCells = document.querySelectorAll(".date-cell");
    dateCells.forEach(cell => {
        if (cell.dataset.date === todayDateString) {
            cell.classList.add('border-primary', 'border-3');
            loadTasks(cell.dataset.date); // Load tasks for today's date
        }

        // Add click event listener to each date cell
        cell.addEventListener("click", () => {
            dateCells.forEach(c => c.classList.remove('border-primary', 'border-3'));
            cell.classList.add('border-primary', 'border-3');
            loadTasks(cell.dataset.date); // Load tasks for the selected date
        });
    });

    // Add Task form submission
    const addTaskForm = document.getElementById("addTaskForm");
    if (addTaskForm) {
        addTaskForm.addEventListener("submit", async function(e) {
            e.preventDefault();

            const formData = new FormData(addTaskForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const res = await fetch("/api/tasks", {
                    method: "POST",
                    headers: { 
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(data)
                });

                if (!res.ok) throw new Error("Failed to save task");
                const task = await res.json();

                // Close modal
                const modalEl = document.getElementById('addTaskModal');
                const myModal = bootstrap.Modal.getInstance(modalEl);
                if (myModal) myModal.hide();

                // Reset form
                addTaskForm.reset();

                // Refresh tasks for selected date
                const selectedDateCell = document.querySelector(".date-cell.border-primary");
                if (selectedDateCell) loadTasks(selectedDateCell.dataset.date);

            } catch(err) {
                console.error("Error adding task:", err);
            }
        });
    }
});

// Open Edit Task Modal
function openEditModal(taskId) {
    const modalEl = document.getElementById('editTaskModal');
    if (!modalEl) return console.error('Edit Task modal not found!');

    const taskElement = document.querySelector(`.task-item[data-id="${taskId}"]`);
    if (!taskElement) return console.error('Task not found!');

    // Get the task data
    const title = taskElement.querySelector('strong').textContent;
    const description = taskElement.querySelector('p').textContent;
    const dueDate = taskElement.querySelector('small').textContent;
    const priorityBadge = taskElement.querySelector('.priority-label .badge');
    const priority = priorityBadge ? priorityBadge.textContent.toLowerCase() : '';

    // Parse the due date from the task (this might be in UTC)
    const taskDueDate = new Date(dueDate);

    // Extract the local date and time parts (year, month, day, hours, minutes)
    const year = taskDueDate.getFullYear();
    const month = String(taskDueDate.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    const day = String(taskDueDate.getDate()).padStart(2, '0');
    const hours = String(taskDueDate.getHours()).padStart(2, '0');
    const minutes = String(taskDueDate.getMinutes()).padStart(2, '0');

    // Create a formatted date string for datetime-local input
    const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`;

    // Fill the form fields in the modal
    document.getElementById('editTaskId').value = taskId;
    document.getElementById('editTaskTitle').value = title;
    document.getElementById('editTaskDescription').value = description;
    document.getElementById('editTaskPriority').value = priority;

    // Set the formatted due date in the input field
    document.getElementById('editTaskDate').value = formattedDate;

    // Show the modal
    const myModal = new bootstrap.Modal(modalEl);
    myModal.show();
}



// Handle Edit Task Form Submission (Right Sidebar)
document.getElementById('editTaskForm').addEventListener("submit", async function(e) {
    e.preventDefault();

    const taskId = document.getElementById("editTaskId").value;
    const title = document.getElementById("editTaskTitle").value;
    const description = document.getElementById("editTaskDescription").value;
    const dueDate = document.getElementById("editTaskDate").value;
    const priority = document.getElementById("editTaskPriority").value;

    try {
        const res = await fetch(`/api/tasks/${taskId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, description, due_date: dueDate, priority })
        });

        if (!res.ok) throw new Error("Failed to update task");

        // Close the modal in the right sidebar
        const modalEl = document.getElementById('editTaskModal');
        const myModal = bootstrap.Modal.getInstance(modalEl);
        if (myModal) myModal.hide();

        // Update the task details directly in the right sidebar without affecting the left sidebar
        const taskElement = document.querySelector(`.task-item[data-id="${taskId}"]`);
        if (taskElement) {
            taskElement.querySelector('strong').textContent = title;
            taskElement.querySelector('p').textContent = description;
            taskElement.querySelector('small').textContent = `Due: ${new Date(dueDate).toLocaleString()}`;
            taskElement.querySelector('.priority-label .badge').textContent = priority;
        }

    } catch (err) {
        console.error("Error updating task:", err);
    }
});