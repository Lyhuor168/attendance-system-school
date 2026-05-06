<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System — Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

<div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-sm">

    {{-- Logo --}}
    <div class="flex flex-col items-center mb-6">
        <div class="bg-blue-100 rounded-full p-3 mb-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                       M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-800">Attendance System</h1>
        <p class="text-sm text-gray-500">សាកលវិទ្យាល័យ</p>
    </div>

    {{-- Role Selector --}}
    <div class="flex gap-2 mb-5" x-data="{ role: '{{ old('role', 'admin') }}' }">
        @foreach(['admin' => 'Admin', 'teacher' => 'គ្រូ', 'student' => 'និស្សិត'] as $val => $label)
        <button type="button"
            onclick="selectRole('{{ $val }}')"
            id="role-{{ $val }}"
            class="flex-1 py-2 rounded-xl border text-sm font-medium transition
                   {{ old('role') == $val ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-500 border-gray-300' }}"
        >{{ $label }}</button>
        @endforeach
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <input type="hidden" name="role" id="selectedRole" value="{{ old('role', 'admin') }}">

        {{-- Email --}}
        <div class="mb-4">
            <label class="block text-sm text-gray-600 mb-1">អ៊ីមែល / Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                placeholder="example@university.edu"
                class="w-full px-4 py-2 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                       {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}" />
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-5">
            <label class="block text-sm text-gray-600 mb-1">លេខសម្ងាត់ / Password</label>
            <input type="password" name="password"
                placeholder="••••••••"
                class="w-full px-4 py-2 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                       {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}" />
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Login Button --}}
        <button type="submit"
            class="w-full bg-gray-800 hover:bg-gray-900 text-white py-3 rounded-xl font-medium text-sm transition">
            ចូលប្រព័ន្ធ / Login
        </button>
    </form>

    <hr class="my-4 border-gray-200">
    <p class="text-center text-xs text-gray-400">
        ប្រសិនបើភ្លេចលេខសម្ងាត់ សូមទាក់ទង Admin
    </p>

</div>

<script>
function selectRole(role) {
    document.getElementById('selectedRole').value = role;
    ['admin','teacher','student'].forEach(r => {
        const btn = document.getElementById('role-' + r);
        if (r === role) {
            btn.className = btn.className.replace('bg-white text-gray-500 border-gray-300',
                'bg-blue-600 text-white border-blue-600');
        } else {
            btn.className = btn.className.replace('bg-blue-600 text-white border-blue-600',
                'bg-white text-gray-500 border-gray-300');
        }
    });
}
</script>
</body>
</html>