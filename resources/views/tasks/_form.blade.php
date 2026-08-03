@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<div>
    <label for="title">Title</label>
    <input id="title" type="text" name="title" value="{{ old('title', $task->title) }}" required>
</div>

<div>
    <label for="notes">Notes</label>
    <textarea id="notes" name="notes">{{ old('notes', $task->notes) }}</textarea>
</div>

<div>
    <label for="priority">Priority</label>
    <select id="priority" name="priority">
        @foreach (['low', 'medium', 'high'] as $priority)
            <option value="{{ $priority }}" @selected(old('priority', $task->priority) === $priority)>
                {{ ucfirst($priority) }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label for="due_date">Due date</label>
    <input id="due_date" type="date" name="due_date"
           value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}">
</div>

<div>
    <label>
        <input type="hidden" name="is_done" value="0">
        <input type="checkbox" name="is_done" value="1" @checked(old('is_done', $task->is_done))>
        Done
    </label>
</div>

<button type="submit">Save</button>
