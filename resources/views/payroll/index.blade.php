<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payroll Management') }}
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
                <!-- Generate Payroll Form -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Generate Pay Slip</h3>
                    <form action="{{ route('payroll.generate') }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Employee</label>
                            <select name="hr_employee_id" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->full_name }} (${{ number_format($e->salary_cents / 100, 0) }}/mo)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Month (1-12)</label>
                                <input type="number" name="period_month" value="{{ $month }}" min="1" max="12" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Year</label>
                                <input type="number" name="period_year" value="{{ $year }}" min="2020" max="2050" class="mt-1 block w-full border-gray-300 rounded text-sm" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Allowances ($)</label>
                                <input type="number" step="0.01" name="allowances" value="0.00" class="mt-1 block w-full border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700">Deductions ($)</label>
                                <input type="number" step="0.01" name="deductions" value="0.00" class="mt-1 block w-full border-gray-300 rounded text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Notes</label>
                            <input type="text" name="notes" placeholder="Monthly performance bonus" class="mt-1 block w-full border-gray-300 rounded text-sm">
                        </div>
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded text-sm">+ Process Pay Slip</button>
                    </form>
                </div>

                <!-- Payroll Sheet Table -->
                <div class="col-span-2 bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="font-bold text-gray-900">Payroll Period: {{ date("F", mktime(0, 0, 0, $month, 10)) }} {{ $year }}</h3>
                            <div class="text-xs text-gray-500">Total Settled: ${{ number_format($totalPaidCents / 100, 2) }}</div>
                        </div>
                        <form action="{{ route('payroll.index') }}" method="GET" class="flex items-center space-x-2">
                            <input type="number" name="month" value="{{ $month }}" min="1" max="12" class="w-16 border-gray-300 rounded text-xs p-1">
                            <input type="number" name="year" value="{{ $year }}" min="2020" max="2050" class="w-20 border-gray-300 rounded text-xs p-1">
                            <button type="submit" class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold">View</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Employee</th>
                                    <th class="px-4 py-2 text-right">Base</th>
                                    <th class="px-4 py-2 text-right">Net Salary</th>
                                    <th class="px-4 py-2 text-center">Status</th>
                                    <th class="px-4 py-2 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($payrolls as $p)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $p->employee->full_name }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600">${{ number_format($p->base_salary_cents / 100, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($p->net_salary_cents / 100, 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $p->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($p->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if ($p->payment_status !== 'paid')
                                                <form action="{{ route('payroll.paid', $p) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-bold">Mark Paid</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">Paid on {{ $p->payment_date ? $p->payment_date->format('M d') : 'N/A' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-center text-gray-500">No payroll records for this period.</td>
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
