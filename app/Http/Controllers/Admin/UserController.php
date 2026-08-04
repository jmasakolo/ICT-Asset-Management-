<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::withCount('assets')->orderBy('id', 'desc');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        return view('admin.users.index', [
            'users' => $query->get(),
            'search' => $search ?? '',
            'editing' => $request->filled('edit') ? User::find($request->integer('edit')) : null,
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        AuditLog::record('created', $user, "Created user “{$user->name}” ({$user->email}).");

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Added “{$user->name}”.");
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        // Blank password on the edit form means "leave it unchanged".
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        AuditLog::record('updated', $user, "Updated user “{$user->name}” ({$user->email}).");

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Updated “{$user->name}”.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;
        $user->delete();

        AuditLog::record('deleted', null, "Deleted user “{$name}”.");

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Deleted “{$name}”.");
    }
}
