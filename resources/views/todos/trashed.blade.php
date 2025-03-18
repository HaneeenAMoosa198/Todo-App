@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-danger">Trashed Todos</h1>

    <!-- Button to Redirect to Todo Index Page -->
    <div class="mb-3">
        <a href="{{ route('todos.index') }}" class="btn btn-primary">
            Back to Todo List
        </a>
    </div>

    @if($todos->count() > 0)
        <ul class="list-group">
            @foreach ($todos as $todo)
                <li class="list-group-item d-flex justify-content-between align-items-center" id="trashed-todo-{{$todo->id}}">
                    <div>
                        <strong class="text-danger h5">{{ $todo->title }}</strong>
                        <p class="mb-0 small">{{ $todo->description }}</p>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm restore-todo" data-id="{{ $todo->id }}">
                            Restore
                        </button>
                        <button class="btn btn-danger btn-sm force-delete-todo" data-id="{{ $todo->id }}">
                            Delete Forever
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-muted">No trashed todos found.</p>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {
    // Restore Todo
    $('.restore-todo').on('click', function () {
        let todoId = $(this).data('id');
        $.ajax({
            url: `/todos/${todoId}/restore`,
            method: 'PATCH',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                alert(response.message);
                $('#trashed-todo-' + todoId).fadeOut();
            },
            error: function () {
                alert('Error restoring todo.');
            }
        });
    });

    // Force Delete Todo
    $('.force-delete-todo').on('click', function () {
        let todoId = $(this).data('id');
        if (confirm('Are you sure you want to delete this permanently?')) {
            $.ajax({
                url: `/todos/${todoId}/force-delete`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    // Remove the todo item from the list
                    $('#trashed-todo-' + todoId).fadeOut();
                    alert(response.message);
                },
                error: function () {
                    alert('Error deleting todo permanently.');
                }
            });
        }
    });
});
</script>

@endsection