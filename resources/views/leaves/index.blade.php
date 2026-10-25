<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leave & Time-Off Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Submit Request Form -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Submit Leave Request</h3>
                    <form action="{{ route('leaves.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Employee</label>
                            <select name="hr_employee_id" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->remaining_leave_days }} days left)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Leave Type</label>
                            <select name="leave_type" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                                <option value="annual">Annual Leave</option>
                                <option value="sick">Sick Leave</option>
                                <option value="maternity">Maternity/Paternity</option>
                                <option value="unpaid">Unpaid Leave</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Start Date</label>
                                <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">End Date</label>
                                <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+2 days')) }}" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Days Count</label>
                            <input type="number" name="days_count" value="3" min="1" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Reason</label>
                            <textarea name="reason" rows="2" class="mt-1 block w-full border-gray-300 rounded text-sm" required></textarea>
                        </div>
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded text-sm">+ Request Leave</button>
                    </form>
                </div>

                <!-- Leave Requests Table -->
                <div class="col-span-2 bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Leave Requests & Approvals</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Employee</th>
                                    <th class="px-4 py-2 text-left">Type & Period</th>
                                    <th class="px-4 py-2 text-center">Days</th>
                                    <th class="px-4 py-2 text-center">Status</th>
                                    <th class="px-4 py-2 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($requests as $req)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900">
                                            <div>{{ $req->employee->full_name }}</div>
                                            <div class="text-xs text-gray-400">Balance: {{ $req->employee->remaining_leave_days }} days</div>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            <span class="font-bold capitalize">{{ $req->leave_type }}</span> &bull; 
                                            {{ $req->start_date->format('M d') }} - {{ $req->end_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-900">{{ $req->days_count }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold
                                                @if($req->status === 'approved') bg-green-100 text-green-800
                                                @elseif($req->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if ($req->status === 'pending')
                                                <div class="inline-flex space-x-1">
                                                    <form action="{{ route('leaves.process', $req) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="p-1 px-2 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-bold">Approve</button>
                                                    </form>
                                                    <form action="{{ route('leaves.process', $req) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="p-1 px-2 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-bold">Reject</button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">Processed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-center text-gray-500">No leave requests found.</td>
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
