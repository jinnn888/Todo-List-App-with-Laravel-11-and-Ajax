@extends("layout.default")
@section("content")
<!-- Button trigger modal -->
<button type="button" class="mb-4 btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Create Todo
</button>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">New Todo</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form">
                <div class="modal-body">
                    @csrf
                    <input autofocus type="text" id="todo" class="form-control" placeholder="Todo name">
                    <span class="text-danger small" id="error-message"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Todo</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-form">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="edit-id">
                    <input autofocus type="text" id="edit-todo" class="form-control" placeholder="Todo name">
                      <select id="status" class="mt-2 form-select" aria-label="Default select example">
                          <option disabled selected>Update status</option>
                          <option value="incomplete">Incomplete</option>
                          <option value="complete">Complete</option>
                      </select>
                  <span class="text-danger small" id="error-message"></span>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
</div>


<table class="table table-bordered">
    <thead>
        <th>#</th>
        <th>Task</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Actions</th>
    </thead>
    <tbody class="text-capitalize"></tbody>
</table>

<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            }
        });


    // Initializing datatable
        $('tbody').html("")
        const table = $('table').DataTable({
            ajax: '{{ route('todo.index') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'task', name: 'task' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'actions', name: 'actions' },
                ]
        })

        function reloadTable() {
            return table.ajax.reload(null, false);
        }

        // Handling delete
        $("tbody").on('click', '#delete-btn', function() {
            const id = $(this).data('id')
            
            Swal.fire({
                title: "Are you sure?",
                text: "You won't  be able to retrieve this after.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#D22B2B",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: 'DELETE',
                        url: `{{ route('todo.destroy', ':id') }}`.replace(':id', id),
                        success: function(response){
                            reloadTable();
                            console.log(response)
                            Swal.fire({
                                title: "Deleted", 
                                text:"Todo has been deleted.",
                                icon: "success"
                            });
                        },
                        error: function(xhr) {
                            console.log(xhr)
                        }
                    })
                } 
            });
        })

        // Handling update
        $("tbody").on('click', '#edit-btn', function() {
            const id = $(this).data('id')
            $.ajax({
                method: 'GET',
                url: `{{ route('todo.edit', ':id') }}`.replace(':id', id),
                success: function(response) {
                    $("#edit-todo").val(response.task);
                    $("#edit-id").val(response.id);
                }
            })

        })



        $("#form").on('submit', function(e) {
            e.preventDefault()

            $.ajax({
                type: 'POST',
                url: "{{ route('todo.store') }}",
                data: { todo: $("#todo").val() },
                success: function(response) {
                    Swal.fire({
                        title: "Success", 
                        text:"Todo has been added. Make sure to do them later :)",
                        icon: "success"
                    });
                    $('.modal').modal('hide');
                    todo: $("#todo").val('')
                    reloadTable();

                },
                error: function(xhr) {
                    const response = JSON.parse(xhr.responseText);
                    const errorMessage = response.message;
                    $("#error-message").text(errorMessage);
                }
            })
        })

        $("#edit-form").on('submit', function(e) {
            e.preventDefault()
            const id = $("#edit-id").val();
            $.ajax({
                type: 'PUT',
                url: `{{ route('todo.update', ":id") }}`.replace(":id", id),
                data: { 
                    todo: $("#edit-todo").val() ,
                    status: $("#status").val() 
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success", 
                        text:"Todo has been updated successfully",
                        icon: "success"
                    });
                    $('#editModal').modal('hide');
                    todo: $("#edit-todo").val('')
                    reloadTable();

                },
                error: function(xhr) {
                    const response = JSON.parse(xhr.responseText);
                    const errorMessage = response.message;
                    $("#error-message").text(errorMessage);
                }
            })
        })



    })

</script>
@endsection
