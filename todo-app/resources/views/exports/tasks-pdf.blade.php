<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #333; }
        h1   { font-size: 20px; margin-bottom: 16px; color: #3B82F6; }
        table { width: 100%; border-collapse: collapse; }
        th   { background: #3B82F6; color: white; padding: 8px 10px; text-align: left; }
        td   { padding: 7px 10px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #f9f9f9; }
        .done { text-decoration: line-through; color: #9CA3AF; }
    </style>
</head>
<body>
    <h1>Task List</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
            <tr>
                <td>{{ $task->id }}</td>
                <td class="{{ $task->completed ? 'done' : '' }}">{{ $task->title }}</td>
                <td>{{ $task->category?->name ?? '—' }}</td>
                <td>{{ $task->completed ? 'Completed' : 'Pending' }}</td>
                <td>{{ $task->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>