<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Employee Directory') }}
            </h2>
            <a href="{{ route('employees.export') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded text-xs font-semibold uppercase hover:bg-gray-700">
                Export CSV &darr;
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Add Employee Form -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Register New Employee</h3>
                    <form action="{{ route('employees.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">First Name</label>
                                <input type="text" name="first_name" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Last Name</label>
                                <input type="text" name="last_name" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Email Address</label>
                            <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Phone</label>
                            <input type="text" name="phone" class="mt-1 block w-full border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Department</label>
                            <select name="hr_department_id" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                                @foreach ($departments as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Job Position</label>
                            <select name="hr_position_id" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Hire Date</label>
                                <input type="date" name="hire_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Salary ($)</label>
                                <input type="number" step="0.01" name="salary" placeholder="5000" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded text-sm">+ Save Employee</button>
                    </form>
                </div>

                <!-- Directory Table -->
                <div class="col-span-2 bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-900">Active Staff Directory</h3>
                        <form action="{{ route('employees.index') }}" method="GET" class="flex items-center space-x-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name/code..." class="border-gray-300 rounded text-xs p-1">
                            <button type="submit" class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold">Filter</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Code</th>
                                    <th class="px-4 py-2 text-left">Staff Name</th>
                                    <th class="px-4 py-2 text-left">Department</th>
                                    <th class="px-4 py-2 text-right">Salary</th>
                                    <th class="px-4 py-2 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($employees as $emp)
                                    <tr>
                                        <td class="px-4 py-3 font-mono text-xs text-indigo-700 font-bold">{{ $emp->employee_code }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-900">{{ $emp->full_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $emp->email }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            <div>{{ $emp->department->name ?? '—' }}</div>
                                            <div class="text-xs text-gray-400">{{ $emp->position->title ?? '—' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono font-bold text-gray-900">${{ number_format($emp->salary_cents / 100, 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $emp->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($emp->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-center text-gray-500">No employees registered.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
