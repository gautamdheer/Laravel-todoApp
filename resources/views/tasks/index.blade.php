<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Php Todo listing app !!!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="container py-5">

    <h1>PHP Simple TODO App !!! </h1>
    {{-- Notification message --}}
    <div id="notificationArea" class="mb-3"></div>

    <div class="input-group mb-3">
        <input type="text" id="taskTitle" placeholder="Enter task...." class="form-control">
        <button id="addTask" class="btn btn-primary">Add Task</button>
    </div>
    </div>

    <ul id="taskList" class="list-group mb-3">
        @foreach ($tasks as $task)
            <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $task->id }}">
                <div>
                    <input type="checkbox" class="toggleCompletion me-2" {{ $task->is_completed ? 'checked' : '' }}>
                    <span>{{ $task->title }}</span>

                </div>
                <div>
                    <button class="btn btn-sm btn-secondary editTask me-2">Edit</button>
                    <button class="btn btn-sm btn-danger deleteTask">Delete</button>
                </div>
            </li>
        @endforeach
    </ul>

    <button id="showAll" class="btn btn-outline-primary">Show All Tasks</button>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="editTaskTitle" class="form-control" placeholder="Enter new task title">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="saveTask" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>

        function showNotification(message, type = 'success') {
            let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            $('#notificationArea').html(`<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`);
        }
        $(document).ready(function() {

            // Add task
            $('#addTask').click(function() {
                let title = $('#taskTitle').val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                if (title) {
                    $.ajax({
                        url: '/tasks',
                        type: 'POST',
                        data: {
                            title: title
                        },
                        success: function(task) {
                            $('#taskList').append(`<li class="list-group-item" data-id="${task.id}">
                                <input type="checkbox" class="toggleCompletion">
                                ${task.title}
                                <button class="btn btn-sm btn-secondary editTask me-2">Edit</button>
                                <button class="deleteTask btn btn-danger">Delete</button>
                            </li>`);
                            $('#taskTitle').val('');
                            showNotification('Task added successfully!');
                        }
                    });
                }
            });

            // Toggle Completion
            $('#taskList').on('click', '.toggleCompletion', function () {
                let taskId = $(this).closest('li').data('id');
                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                $.ajax({
                    url: `/tasks/${taskId}/toggle`,
                    type: 'PUT',
                    success: function (task) {
                        let taskElement = $(`li[data-id="${task.id}"] span`);
                        if (task.is_completed) {
                            taskElement.css('text-decoration', 'line-through');
                            showNotification('Task marked as completed.');
                        } else {
                            taskElement.css('text-decoration', 'none');
                            showNotification('Task marked as not completed.');
                        }
                    },
                    error: function () {
                        showNotification('Failed to toggle task completion.', 'error');
                       }
                });
            });

            // Edit Task
            $('#taskList').on('click', '.editTask', function() {
                taskId = $(this).closest('li').data('id');
                let currentTitle = $(this).closest('li').find('span').text();
                $('#editTaskTitle').val(currentTitle);
                $('#editTaskModal').modal('show');
            });

            // Save Edited Task
            $('#saveTask').click(function() {
                let newTitle = $('#editTaskTitle').val();
                if (newTitle) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'PUT',
                        data: {
                            title: newTitle
                        },
                        success: function(task) {
                            $(`li[data-id="${task.id}"] span`).text(task.title);
                            $('#editTaskModal').modal('hide');
                        }
                    });
                }
            });

            // Delete Task
            $('#taskList').on('click', '.deleteTask', function() {
                if (confirm('Are you sure to delete this task?')) {
                    let taskId = $(this).closest('li').data('id');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'DELETE',
                        success: function() {
                            $(`li[data-id="${taskId}"]`).remove();
                            showNotification('Successfully deleted task');
                        }
                    });
                }
            });


            // Show All Tasks
            $('#showAll').click(function () {
                $.get('/', function (data) {
                    $('#taskList').html(data);
                    showNotification('All tasks displayed.');
                }).fail(function () {
                    showNotification('Failed to retrieve tasks.', 'error');
                });
            });

        });
    </script>
</body>

</html>
