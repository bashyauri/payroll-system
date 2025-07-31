<div class="space-y-4">
    <h2 class="text-xl font-bold">My Payslips</h2>

    @forelse($payslips as $payslip)
        <div class="p-4 bg-white rounded shadow dark:bg-gray-800">
            <p class="font-medium">Month: {{ $payslip->month }} {{ $payslip->year }}</p>
            <p>Base Salary: ₦{{ number_format($payslip->base_salary, 2) }}</p>
            <p>Net Pay: ₦{{ number_format($payslip->net_pay, 2) }}</p>

            <button wire:click="download({{ $payslip->id }})"
                class="mt-2 px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                Download PDF
            </button>
        </div>
    @empty
        <p class="text-gray-500">No payslips found.</p>
    @endforelse
</div>