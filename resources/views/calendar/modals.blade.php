<!-- =====================
Add Task Modal
===================== -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addTaskForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input name="title" class="form-control mb-2" placeholder="Title" required>
          <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
          <input id="taskDateInput" type="datetime-local" name="due_date" class="form-control mb-2" required>

          <select name="priority" class="form-control mb-2" required>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
          </select>

          <!-- Category Dropdown -->
          <select name="category_id" class="form-control mb-2" required>
            <option value="" disabled selected>Select a Category</option>
            @foreach (\App\Models\Category::all() as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>



<!-- =====================
Delete Task Modal
===================== -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this task?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm">
                    <input type="hidden" id="editTaskId">
                    <div class="mb-3">
                        <label for="editTaskTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editTaskTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTaskDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editTaskDescription" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editTaskDate" class="form-label">Due Date</label>
                        <input type="datetime-local" class="form-control" id="editTaskDate" name="due_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTaskPriority" class="form-label">Priority</label>
                        <select class="form-select" id="editTaskPriority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- =====================
Toast Notification Modal
===================== -->
<div class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 3000;">
    <div id="toastNoDate" class="toast align-items-center text-bg-warning border-0"
         role="alert" aria-live="assertive" aria-atomic="true"
         data-bs-animation="true">
        <div class="d-flex">
            <div class="toast-body fw-bold">
                Please select a date first!
            </div>
            <button type="button" class="btn-close me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
