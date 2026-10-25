<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daily Attendance Tracking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Log Attendance Form -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Record Attendance</h3>
                    <form action="{{ route('attendance.record') }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Employee</label>
                            <select name="hr_employee_id" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->employee_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Date</label>
                            <input type="date" name="date" value="{{ $date }}" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Check In</label>
                                <input type="text" name="check_in_time" placeholder="09:00" class="mt-1 block w-full border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Check Out</label>
                                <input type="text" name="check_out_time" placeholder="18:00" class="mt-1 block w-full border-gray-300 rounded text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Status</label>
                            <select name="status" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                                <option value="present">Present</option>
                                <option value="late">Late Arrival</option>
                                <option value="half_day">Half Day</option>
                                <option value="absent">Absent</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded text-sm">+ Save Attendance</button>
                    </form>
                </div>

                <!-- Daily Attendance Sheet -->
                <div class="col-span-2 bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-900">Attendance Sheet: {{ $date }}</h3>
                        <form action="{{ route('attendance.index') }}" method="GET" class="flex items-center space-x-2">
                            <input type="date" name="date" value="{{ $date }}" class="border-gray-300 rounded text-xs p-1">
                            <button type="submit" class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold">Change Date</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Employee</th>
                                    <th class="px-4 py-2 text-center">Check-In</th>
                                    <th class="px-4 py-2 text-center">Check-Out</th>
                                    <th class="px-4 py-2 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($attendances as $att)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $att->employee->full_name }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $att->check_in_time ?? '—' }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $att->check_out_time ?? '—' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold
                                                @if($att->status === 'present') bg-green-100 text-green-800
                                                @elseif($att->status === 'late') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($att->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">No attendance records for this date.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
