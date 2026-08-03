{{--
    Shared by create and edit. Every field repopulates from old() so a failed
    validation pass never discards what the user typed.

    Expects: $task (existing or fresh), $action, $method ('POST' or 'PUT'), $submit
--}}
<form class="card" action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="field">
        <label class="label" for="title">Title <span class="req">*</span></label>
        <input
            class="input @error('title') has-error @enderror"
            type="text" id="title" name="title" maxlength="255" required
            autocomplete="off" autofocus
            value="{{ old('title', $task->title) }}">
        @error('title')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <div class="field-row">
        <div class="field">
            <label class="label" for="due_date">Due date</label>
            <input
                class="input @error('due_date') has-error @enderror"
                type="date" id="due_date" name="due_date"
                value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
            @error('due_date')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label class="label" for="priority">Priority</label>
            <select class="input @error('priority') has-error @enderror" id="priority" name="priority">
                @foreach (\App\Models\Task::PRIORITIES as $value)
                    <option value="{{ $value }}"
                        @selected(old('priority', $task->priority ?? \App\Models\Task::DEFAULT_PRIORITY) === $value)>
                        {{ ucfirst($value) }}
                    </option>
                @endforeach
            </select>
            @error('priority')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="field">
        <label class="label" for="notes">Notes</label>
        <textarea
            class="input textarea @error('notes') has-error @enderror"
            id="notes" name="notes" rows="5"
            maxlength="5000">{{ old('notes', $task->notes) }}</textarea>
        @error('notes')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label class="check">
            {{-- Hidden partner so an unchecked box still posts a value. --}}
            <input type="hidden" name="is_done" value="0">
            <input type="checkbox" name="is_done" value="1"
                @checked(old('is_done', $task->is_done))>
            <span>Mark as completed</span>
        </label>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit">{{ $submit }}</button>
        <a class="btn btn-ghost" href="{{ route('tasks.index') }}">Cancel</a>
    </div>
</form>
