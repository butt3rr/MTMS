@include('home._partial.create-modal')
@include('home._partial.edit-modal')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-900 leading-tight">
            {{ __('MICES Task Management System') }}
        </h2>
  </x-slot>

 
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <section class="pt-4">
        <div class="container py-3">
          <div class="row d-flex justify-content-center align-items-start">
            <div class="col">
              <div class="card" id="list1" style="border-radius: .75rem; background-color: #eff1f2;">
                <div class="card-body py-4 px-4 px-md-5">

                  <!-- header -->
                    <p class="h1 text-center mt-3 mb-4 pb-3 text-info">
                      <i class="fas fa-check-square me-1"></i>
                      <u>List of Tasks</u>
                    </p>

                    <div class="pb-2">           
                    <button type="button" class="btn btn-primary" data-mdb-toggle="modal" data-mdb-target="#createModal">
                        <i class="fas fa-plus"></i> Add New Task
                    </button>         
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end align-items-center mb-4 pt-2 pb-3">
                      <p class="small mb-0 me-2 text-muted">Filter</p>
                      <select id="taskFilter">
                          <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All</option>
                          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                          <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                      </select>
                    </div>

                    <!-- task list -->
                    @foreach ($tasks as $task)
                    
                      <li class="list-group-item d-flex align-items-center ps-0 pe-3 py-1 rounded-0 border-0 bg-transparent">
                          <a href="#" class="toggle-task-details text-dark me-2" data-task-id="{{ $task->id }}">
                              <i class="fas fa-chevron-down"></i> 
                          </a>

                          <!-- title -->
                          <p class="lead fw-normal mb-0 flex-grow-1">{{ $task->title }}</p>

                          <!-- due date -->
                          @php
                          $dueDate = \Carbon\Carbon::parse($task->due_date);
                          $daysLeft = now()->diffInDays($dueDate, false);
                          $borderClass = '';
                          $icon = '<i class="fas fa-calendar-alt me-2"></i>'; //default

                          if ($daysLeft <=3) {
                          $borderClass = 'border border-danger';
                          $icon = '<i class="fas fa-exclamation-triangle me-2 text-danger"></i>';
                          } elseif ($daysLeft <= 7) {
                          $borderClass = 'border border-warning';
                          $icon = '<i class="fas fa-hourglass-half me-2 text-warning"></i>';
                          }
                          @endphp

                          <div class="text-end text-muted">
                              <div class="small mb-0 ml-3 py-2 px-3 rounded {{ $borderClass }}" style="border-width: 4px;">
                                  {!! $icon !!} <strong>Due Date:</strong> {{ $dueDate->format('jS M Y') }}
                              </div>
                          </div>

                          <!-- actions -->
                          <div class="d-flex align-items-center">
                              <!-- Status Toggle Button -->
                            
                              <button class="btn btn-sm {{ $task->status === 'completed' ? 'btn-success' : 'btn-warning' }} status-btn"
                                  data-task-id="{{ $task->id }}">
                              {{ $task->status }}
                          </button>

                          <!-- Edit Button (Disabled if Complete) -->
                          <a href="#" class="text-info me-3 edit-task-btn {{ $task->status == 'completed' ? 'disabled' : '' }}" 
                              title="Edit" 
                              data-bs-toggle="modal" 
                              data-bs-target="#editTaskModal" 
                              data-task-id="{{ $task->id }}">
                              <i class="fas fa-pencil-alt"></i>
                          </a>

                          <!-- Delete Button -->
                          <a href="#" class="text-danger delete-task-btn" title="Delete" data-task-id="{{ $task->id }}">
                              <i class="fas fa-trash-alt"></i>
                          </a>

                          <!-- Delete Form -->
                          <form id="deleteTaskForm" method="POST">
                              @csrf
                              @method('DELETE')
                          </form>
                      </div>

                      </li>

                      <!-- task details -->
                      <li id="task-details-{{ $task->id }}" class="list-group-item border-0 bg-transparent d-none">
                          <div class="card p-3 shadow-sm">
                              <p><strong>Description:</strong> {{ $task->description ?? 'No description available' }}</p>
                              <p><strong>Created Date:</strong> {{ \Carbon\Carbon::parse($task->created_at)->format('d M Y, h:i A') }}</p>
                          </div>
                      </li>
                      
                    @endforeach

                    <!-- end of task list -->
            

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
            </div>
    
    </x-app-layout>

    <script>
    $(document).ready(function () {
      //dropdown task details
        $(".toggle-task-details").click(function (e) {
            e.preventDefault(); 
            
            let taskId = $(this).data("task-id");
            let detailsRow = $("#task-details-" + taskId);
            
            detailsRow.toggleClass("d-none");
 
            let icon = $(this).find("i");
            icon.toggleClass("fa-chevron-down fa-chevron-up");
        });

        // Edit task modal
        $(".edit-task-btn").click(function() {
          let taskId = $(this).data("task-id");

          //ajax
          $.ajax({
            url: "/tasks/" + taskId + "/edit",
            type: "GET",
            success: function(response) {
              console.log("Task Data Fetched:", response); 

              $("#edit-title").val(response.title);
              $("#edit-description").val(response.description);
              $("#edit-due-date").val(response.due_date);

              $("#editTaskForm").attr("action", "/tasks/" + taskId);
            },
            error: function(error) {
              alert("Failed to fetch task data.");

            }
          });
        });

        //Delete task button
        $(".delete-task-btn").click(function (e) {
        e.preventDefault();

        let taskId = $(this).data("task-id");

        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {  
                let form = $("#deleteTaskForm");
                form.attr("action", "/tasks/" + taskId); 
                form.submit();
            }
        });
      });

    //Change status
    $(".status-btn").click(function () {
        let button = $(this);
        let taskId = button.data("task-id");

        $.ajax({
            url: "/tasks/" + taskId + "/status",
            type: "PATCH",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"), 
            },
            success: function (response) {
                let newStatus = response.new_status;

                
                button.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                button.removeClass("btn-warning btn-success")
                      .addClass(newStatus === "completed" ? "btn-success" : "btn-warning");

                
                let editButton = $(".edit-task-btn[data-task-id='" + taskId + "']");
                if (newStatus === "completed") {
                    editButton.addClass("disabled").attr("title", "Cannot edit a completed task");
                } else {
                    editButton.removeClass("disabled").attr("title", "Edit");
                }
            },
            error: function () {
                alert("Failed to update status.");
            }
        });
    });

    //Filter tasks
    $("#taskFilter").change(function () {
        let selectedStatus = $(this).val();

        $.ajax({
            url: "/dashboard",
            type: "GET",
            data: { status: selectedStatus },
            success: function (response) {
                let taskList = $("#taskList");
                taskList.html($(response).find("#taskList").html());
            },
            error: function () {
                alert("Failed to filter tasks.");
            }
        });
    });
          document.getElementById("taskFilter").addEventListener("change", function() {
          const selectedStatus = this.value;
          const url = new URL(window.location.href);
          url.searchParams.set('status', selectedStatus);
          window.location.href = url.toString();
      });

    });

    
</script>