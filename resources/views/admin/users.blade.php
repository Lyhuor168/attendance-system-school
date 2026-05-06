<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>គ្រប់គ្រង Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="p-6">
    <h1 class="text-xl font-bold mb-4">👥 គ្រប់គ្រង Users</h1>
    <a href="{{ route('admin.dashboard') }}"
       class="text-blue-500 text-sm">← Back to Dashboard</a>
    <div class="bg-white rounded-xl shadow p-5 mt-4">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 border-b">
                    <th class="pb-2">ឈ្មោះ</th>
                    <th class="pb-2">អ៊ីមែល</th>
                    <th class="pb-2">Role</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-2">{{ $user->name }}</td>
                <td class="py-2">{{ $user->email }}</td>
                <td class="py-2">
                    <span class="px-2 py-0.5 rounded-full text-xs
                        {{ $user->role == 'admin' ? 'bg-blue-100 text-blue-700' :
                          ($user->role == 'teacher' ? 'bg-green-100 text-green-700' :
                           'bg-yellow-100 text-yellow-700') }}">
                        {{ $user->role }}
                    </span>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>