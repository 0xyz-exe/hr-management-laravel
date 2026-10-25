<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Human Resources Management Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Staff</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $metrics['total_active_headcount'] }}</div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Present Today</div>
                    <div class="mt-2 text-2xl font-bold text-green-600">{{ $metrics['today_checkins'] }}</div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending Leaves</div>
                    <div class="mt-2 text-2xl font-bold {{ $metrics['pending_leaves_count'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">
                        {{ $metrics['pending_leaves_count'] }}
                    </div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Monthly Base Payroll</div>
                    <div class="mt-2 text-2xl font-bold text-indigo-700">${{ number_format($metrics['monthly_payroll_estimate_cents'] / 100, 2) }}</div>
                </div>
            </div>

            <!-- Pending Leave Requests -->
            @if ($pendingLeaves->count() > 0)
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-md">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-amber-900 font-bold text-sm">Action Needed: {{ $pendingLeaves->count() }} Pending Leave Approvals</span>
                        <a href="{{ route('leaves.index') }}" class="text-xs text-amber-900 font-semibold hover:underline">Manage All &rarr;</a>
                    </div>
                    <div class="space-y-2">
                        @foreach ($pendingLeaves as $req)
                            <div class="bg-white p-3 rounded flex justify-between items-center text-sm border border-amber-200">
                                <div>
                                    <span class="font-bold text-gray-900">{{ $req->employee->full_name }}</span>
                                    <span class="text-gray-500 ml-2">({{ ucfirst($req->leave_type) }} Leave &bull; {{ $req->days_count }} days)</span>
                                    <div class="text-xs text-gray-400 mt-0.5">Reason: {{ $req->reason }} &bull; Dates: {{ $req->start_date->format('M d') }} - {{ $req->end_date->format('M d, Y') }}</div>
                                </div>
                                <form action="{{ route('leaves.process', $req) }}" method="POST" class="space-x-1">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-bold">Approve</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Department Headcount Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Department Distribution</h3>
                    <div class="space-y-3">
                        @foreach ($departments as $dept)
                            <div class="flex justify-between items-center text-sm border-b pb-2">
                                <span class="font-semibold text-gray-700">{{ $dept->name }}</span>
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded text-xs">{{ $dept->employees_count }} staff</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-900">Recent Hires</h3>
                        <a href="{{ route('employees.index') }}" class="text-sm text-indigo-600 hover:underline">Full Directory</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($recentEmployees as $emp)
                            <div class="py-2.5 flex justify-between items-center text-sm">
                                <div>
                                    <div class="font-bold text-gray-900">{{ $emp->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $emp->position->title ?? 'Staff' }} &bull; {{ $emp->department->name ?? 'General' }}</div>
                                </div>
                                <span class="text-xs text-gray-400">Hired {{ $emp->hire_date->format('M d, Y') }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 py-3">No employees registered yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
