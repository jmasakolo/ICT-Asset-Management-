<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\AuditLog;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.departments.index', [
            'departments' => Department::orderBy('name')->get(),
            'editing' => $request->filled('edit') ? Department::find($request->integer('edit')) : null,
        ]);
    }

    public function store(DepartmentRequest $request): RedirectResponse
    {
        $department = Department::create($request->validated());

        AuditLog::record('created', $department, "Created department “{$department->name}”.");

        return redirect()
            ->route('admin.departments.index')
            ->with('status', "Added “{$department->name}”.");
    }

    public function update(DepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        AuditLog::record('updated', $department, "Updated department “{$department->name}”.");

        return redirect()
            ->route('admin.departments.index')
            ->with('status', "Updated “{$department->name}”.");
    }

    public function destroy(Department $department): RedirectResponse
    {
        $name = $department->name;
        $department->delete();

        AuditLog::record('deleted', null, "Deleted department “{$name}”.");

        return redirect()
            ->route('admin.departments.index')
            ->with('status', "Deleted “{$name}”.");
    }
}
