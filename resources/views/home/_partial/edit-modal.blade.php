<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="edit-title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit-title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit-description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="edit-description" name="description"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit-due-date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="edit-due-date" name="due_date" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Task</button>
                </form>
            </div>
        </div>
    </div>
</div>
