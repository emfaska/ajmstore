<?php

namespace App\Services;

use App\Models\Expense;
use App\Repositories\Contracts\CashTransactionRepositoryInterface;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class ExpenseService extends BaseService
{
    /**
     * @var ExpenseRepositoryInterface
     */
    protected ExpenseRepositoryInterface $expenseRepository;

    /**
     * @var CashTransactionRepositoryInterface
     */
    protected CashTransactionRepositoryInterface $cashTransactionRepository;

    /**
     * ExpenseService constructor.
     *
     * @param ExpenseRepositoryInterface $expenseRepository
     * @param CashTransactionRepositoryInterface $cashTransactionRepository
     */
    public function __construct(
        ExpenseRepositoryInterface $expenseRepository,
        CashTransactionRepositoryInterface $cashTransactionRepository
    ) {
        $this->expenseRepository = $expenseRepository;
        $this->cashTransactionRepository = $cashTransactionRepository;
    }

    /**
     * Create a new expense and automatically record the cash transaction.
     *
     * @param array $expenseData
     * @return Expense
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function createExpense(array $expenseData): Expense
    {
        $amount = (float) ($expenseData['amount'] ?? 0);

        // 1. Validasi nominal pengeluaran
        if ($amount <= 0) {
            throw new InvalidArgumentException("Nominal pengeluaran (amount) harus lebih besar dari nol.");
        }

        try {
            // 2. Database Transaction
            return DB::transaction(function () use ($expenseData, $amount) {
                // 3. Membuat pengeluaran
                $expense = $this->expenseRepository->create([
                    'title'        => $expenseData['title'],
                    'description'  => $expenseData['description'] ?? null,
                    'amount'       => $amount,
                    'expense_date' => $expenseData['expense_date'] ?? now()->toDateString(),
                ]);

                // 4. Update cash transaction otomatis
                $this->cashTransactionRepository->create([
                    'payment_method_id'  => $expenseData['payment_method_id'] ?? null,
                    'type'               => 'credit', // Credit menandakan uang keluar (pengeluaran)
                    'amount'             => $amount,
                    'description'        => "Pembayaran pengeluaran: {$expense->title}",
                    'referenceable_type' => Expense::class,
                    'referenceable_id'   => $expense->id,
                    'transaction_date'   => $expense->expense_date,
                ]);

                return $expense;
            });
        } catch (InvalidArgumentException $e) {
            throw $e; // Re-throw validasi spesifik
        } catch (Exception $e) {
            // 5. Exception handling
            Log::error("Failed to create expense.", [
                'error' => $e->getMessage(),
                'data' => $expenseData
            ]);
            
            throw new Exception("Terjadi kesalahan sistem saat menyimpan data pengeluaran: " . $e->getMessage());
        }
    }
}
