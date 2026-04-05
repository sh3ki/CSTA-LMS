<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['subject.schoolClass', 'creator', 'submissions']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('task_type')) {
            $query->where('task_type', $request->task_type);
        }

        $tasks = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        $subjects = Subject::where('status', true)->orderBy('name')->get();

        return view('admin.tasks.index', compact('tasks', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'task_type' => 'required|in:Activity,Quiz,Assignment,Others',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'total_points' => 'required|integer|min:1|max:1000',
            'file' => 'nullable|file|max:20480',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'task_type' => $request->task_type,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'total_points' => $request->total_points,
            'created_by' => $request->user()->id,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('tasks', 'public');
            $data['file_name'] = $file->getClientOriginalName();
        }

        $task = Task::create($data);

        AuditLog::record('Create Task', "Admin created task: {$task->title}");

        return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'task_type' => 'required|in:Activity,Quiz,Assignment,Others',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'total_points' => 'required|integer|min:1|max:1000',
            'file' => 'nullable|file|max:20480',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'task_type' => $request->task_type,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'total_points' => $request->total_points,
        ];

        if ($request->hasFile('file')) {
            if ($task->file_path) {
                Storage::disk('public')->delete($task->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('tasks', 'public');
            $data['file_name'] = $file->getClientOriginalName();
        }

        $task->update($data);

        AuditLog::record('Edit Task', "Admin updated task: {$task->title}");

        return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $name = $task->title;

        if ($task->file_path) {
            Storage::disk('public')->delete($task->file_path);
        }

        $task->delete();

        AuditLog::record('Delete Task', "Admin deleted task: {$name}");

        return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function downloadAttachment(Task $task)
    {
        if (!$task->file_path) {
            abort(404);
        }

        $disk = Storage::disk('public');
        return response()->download($disk->path($task->file_path), $task->file_name);
    }
}
