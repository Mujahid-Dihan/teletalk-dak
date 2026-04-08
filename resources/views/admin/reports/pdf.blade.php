<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #006a4e; padding-bottom: 10px; }
        .header h1 { color: #006a4e; margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0 0; color: #555; }
        table { w-full; border-collapse: collapse; margin-top: 20px; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; color: #333; font-weight: bold; }
        .text-center { text-align: center; }
        .badge { background-color: #e0f2fe; color: #0369a1; padding: 3px 6px; border-radius: 3px; font-size: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Teletalk Bangladesh Limited</h1>
        <p>Dak Management System</p>
        <h2>{{ $title }}</h2>
        <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Tracking ID</th>
                <th>Action By</th>
                <th>Movement</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                <td>{{ $log->dakFile->tracking_id }}</td>
                <td>{{ $log->user->name }}</td>
                <td>
                    {{ $log->fromDepartment->name ?? 'Init' }} &rarr; {{ $log->toDepartment->name ?? 'Done' }}
                </td>
                <td>{{ $log->dakFile->status === 'Completed' ? 'Done' : 'Pending' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
