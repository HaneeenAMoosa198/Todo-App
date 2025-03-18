@extends('layouts.app')

@section('content')
<div class="container">

    <!-- Add Todo Form -->
    <form id="addTodoForm" class="mb-4" action="{{ route('todos.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="text" name="title" class="form-control" placeholder="Todo Title" required>
        </div>
        <div class="mb-3">
            <textarea name="description" class="form-control" placeholder="Todo Description" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Todo</button>
    </form>

    <!-- Todo List -->
    <!-- @if(isset($todos) && $todos->count() > 0) -->
    <ul id="todoList" class="list-group">
        @forelse ($todos as $todo)
            <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $todo->id }}" id="todo{{$todo->id}}">
                <div>
                    <strong class="text-primary {{ $todo->completed ? 'text-decoration-line-through' : '' }} h5" id="title{{$todo->id}}">
                        {{ $todo->title }}
                    </strong>
                    <p class="mb-0 small" id="description{{$todo->id}}">{{ $todo->description }}</p>
                </div>
                <div>
                    <button class="btn btn-success btn-sm toggle-completed" data-id="{{ $todo->id }}" id="toggle-completed{{$todo->id}}">
                        {{ $todo->completed ? 'Undo' : 'Complete' }}
                    </button>
                    <button class="btn btn-primary btn-sm edit-todo" data-id="{{ $todo->id }}" data-bs-toggle="modal" data-bs-target="#editTodoModal{{$todo->id}}">
                        Edit
                    </button>
                    <button class="btn btn-danger btn-sm delete-todo" data-id="{{ $todo->id }}" id="delete{{$todo->id}}">
                        Delete
                    </button>
                </div>
            </li>
<!-- Edit Todo Modal -->
            <div class="modal fade" id="editTodoModal{{$todo->id}}" tabindex="-1" aria-labelledby="editTodoModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content" >
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTodoModalLabel{{$todo->id}}">Edit Todo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editTodoForm{{$todo->id}}" method="post">
                            @csrf
                            @method("put")
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editTodoTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="editTodoTitle{{$todo->id}}" name="title" value="{{$todo->title}}" required >
                                </div>
                                <div class="mb-3">
                                    <label for="editTodoDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="editTodoDescription{{$todo->id}}" name="description" rows="3">{{$todo->description}}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty 
            <P> no data</p>
            @endforelse
        </ul>

    <!-- تحسين الـ Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $todos->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
@else
    <p class="text-muted">No todos found.</p>
@endif

    <!-- View Trashed Todos Button -->
    <div class="mb-3 mt-3">
    <a href="{{ route('todos.trashed') }}" class="btn btn-warning">View Trashed Todos</a>    
</div>



@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="module">
$(document).ready(function () {
    console.log("jq")
    // Add Todo
    $('#addTodoForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("todos.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function () {
                location.reload();
            },
            error: function () {
                alert('Error adding todo.');
            }
        });
    });

    
@foreach($todos as $todo)
// toodup
    $("#editTodoForm{{$todo->id}}").on('submit',function (e) {
        e.preventDefault()

        $.ajax({
           type:"put",
            url:"{{route('todos.update',$todo->id)}}",
            data:{
                "title":$("#editTodoTitle{{$todo->id}}").val(),
                "description":$("#editTodoDescription{{$todo->id}}").val(),
                "_token":"{{csrf_token()}}"
            },
           'success': function (response) {
                        console.log(response);
                        // $("#editTodoTitle{{$todo->id}}").text(response.title)
                        // $("#editTodoDescription{{$todo->id}}").text(response.description)
                        $("#title{{$todo->id}}").text(response.title)
                        $("#description{{$todo->id}}").text(response.description)
                        $("#editTodoModal{{$todo->id}}").modal('hide')
            
                    }
        
        })
    })
    $("#delete{{$todo->id}}").on('click',function () {
        $.ajax({
          method:"delete"  ,
           url:"{{route("todos.destroy",$todo->id)}}",
           data:{
            "_token":"{{csrf_token()}}"
           },
           'success': function (response) {
                        console.log(response);
                        $("#todo{{$todo->id}}").remove()
                    }
        })
    })

    $("#toggle-completed{{$todo->id}}").on('click',function(){
        $.ajax({
          method:"PATCH"  ,
           url:"{{route("todos.complete",$todo->id)}}",
           data:{
            "_token":"{{csrf_token()}}"
           },
           'success': function (response) {
                        console.log(response);
                        let comp=response.completed
                        let text;
                        if(comp){
                            text="Undo"
                        }
                        else{
                            text="complete"
                        }
                        $("#toggle-completed{{$todo->id}}").text(text)
                    }
        })
    })

@endforeach
    // // Edit Todo
    // $(document).on('click', '.edit-todo', function () {
    //     let todoId = $(this).data('id');
    //     // البحث عن نصوص العنوان والوصف داخل العنصر المحدد
    //     let parent = $(this).closest('li');
    //     let title = parent.find('strong').text().trim() || parent.closest('li').find('strong').text().trim();
    //     let description = parent.find('p').text().trim() || parent.closest('li').find('p').text().trim();

    //     console.log("Editing Todo ID:", todoId);  // تحقق من ID
    //     console.log("Title:", title);  // تحقق من العنوان
    //     console.log("Description:", description);  // تحقق من الوصف

    //     // تعبئة النموذج بالقيم الحالية
    //     $('#editTodoId').val(todoId);
    //     $('#editTodoTitle').val(title);
    //     $('#editTodoDescription').val(description);
    // });

    // $('#editTodoForm').on('submit', function (e) {
    //     e.preventDefault();
    //     let todoId = $('#editTodoId').val();
    //     $.ajax({
    //         url: `/todos/${todoId}`,
    //         method: 'PATCH',
    //         data: $(this).serialize(),
    //         success: function (response) {
    //              // تحديث العنصر الموجود في القائمة بدون إعادة تحميل الصفحة
    //              let listItem = $(`li[data-id="${todoId}"]`);
    //             listItem.find('strong').text(response.title);
    //             listItem.find('p').text(response.description);

    //             // إغلاق الـ modal بعد التعديل
    //             $('#editTodoModal').modal('hide');
    //         },
    //         error: function () {
    //             alert('Error updating todo.');
    //         }
    //     });
    // });

    // // Delete Todo
    // $(document).on('click', '.delete-todo', function () {
    //     let todoId = $(this).data('id');
    //     $.ajax({
    //         url: `/todos/${todoId}`,
    //         method: 'DELETE',
    //         data: { _token: '{{ csrf_token() }}' },
    //         success: function () {
    //             location.reload();
    //         },
    //         error: function () {
    //             alert('Error deleting todo.');
    //         }
    //     });
    // });
    // 
    // 
    // // Toggle Complete Status
    // $(document).on('click', '.toggle-completed', function () {
    //     let button = $(this);  // تخزين الزر المحدد
    //     let todoId = button.data('id');  // جلب معرف المهمة

    //     $.ajax({
    //         url: `/todos/${todoId}/complete`,
    //         method: 'PATCH',
    //         data: { _token: '{{ csrf_token() }}' },
    //         success: function (response) {
    //             if (response.completed) {
    //                 button.text('Undo').removeClass('btn-success').addClass('btn-warning');
    //                 button.closest('li').find('strong').addClass('text-decoration-line-through');
    //             } else {
    //                 button.text('Complete').removeClass('btn-warning').addClass('btn-success');
    //                 button.closest('li').find('strong').removeClass('text-decoration-line-through');
    //             }
    //         },
    //         error: function () {
    //             alert('Error updating todo status.');
    //         }
    //     });
    // });    

});
</script>

