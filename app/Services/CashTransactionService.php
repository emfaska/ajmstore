<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Repositories\Contracts\CashTransactionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class CashTransactionService extends BaseService
{
    /**
     * @var CashTransactionRepositoryInterface
     */
    protected CashTransactionRepositoryInterface $cashTransactionRepository;

    /**
     * CashTransactionService constructor.
     *
     * @param CashTransactionRepositoryInterface $cashTransactionRepository
     */
    public function __construct(CashTransactionRepositoryInterface $cashTransactionRepository)
    {
        $this->cashTransactionRepository = $cashTransactionRepository;
    }

    /**
     * Mencatat transaksi kas masuk (debit) dan kas keluar (credit).
     *
     * @param array $data
     * @return CashTransaction
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function recordTransaction(array $data): CashTransaction
    {
        try {
            $amount = (float) ($data['amount'] ?? 0);
            $type = $data['type'] ?? '';

            if ($amount <= 0) {
                throw new InvalidArgumentException("Nominal transaksi (amount) harus lebih besar dari nol.");
            }

            if (!in_array($type, ['debit', 'credit'])) {
                throw new InvalidArgumentException("Tipe transaksi harus 'debit' (kas masuk) atau 'credit' (kas keluar).");
            }

            // Pastikan transaction_date memiliki default value jika kosong
            $data['transaction_date'] = $data['transaction_date'] ?? now()->toDateString();

            return $this->cashTransactionRepository->create($data);
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error("Gagal mencatat transaksi kas.", [
                'error' => $e->getMessage(),
                'data'  => $data
            ]);
            throw new Exception("Terjadi kesalahan sistem saat mencatat transaksi kas.");
        }
    }

    /**
     * Mengambil riwayat transaksi kas (keseluruhan dengan paginasi).
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getHistory(int $perPage = 15): LengthAwarePaginator
    {
        return $this->cashTransactionRepository->paginate($perPage);
    }

    /**
     * Filter transaksi berdasarkan rentang tanggal.
     *
     * @param string $from
     * @param string $to
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function filterByDate(string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->cashTransactionRepository->getByDateRange($from, $to, $perPage);
    }

    /**
     * Filter berdasarkan tipe 'debit' atau 'credit'.
     *
     * @param string $type
     * @param int $perPage
     * @return LengthAwarePaginator
     * @throws InvalidArgumentException
     */
    public function filterByType(string $type, int $perPage = 15): LengthAwarePaginator
    {
        if (!in_array($type, ['debit', 'credit'])) {
            throw new InvalidArgumentException("Tipe filter tidak valid. Gunakan 'debit' atau 'credit'.");
        }

        return $this->cashTransactionRepository->getByType($type, $perPage);
    }

    /**
     * Menghitung total pemasukan (debit) dari rentang waktu tertentu.
     * Secara bawaan menghitung dari seluruh riwayat ('1970-01-01' ke '2099-12-31').
     *
     * @param string $from
     * @param string $to
     * @return float
     */
    public function getTotalIncome(string $from = '1970-01-01', string $to = '2099-12-31'): float
    {
        return $this->cashTransactionRepository->sumDebit($from, $to);
    }

    /**
     * Menghitung total pengeluaran (credit) dari rentang waktu tertentu.
     * Secara bawaan menghitung dari seluruh riwayat ('1970-01-01' ke '2099-12-31').
     *
     * @param string $from
     * @param string $to
     * @return float
     */
    public function getTotalExpense(string $from = '1970-01-01', string $to = '2099-12-31'): float
    {
        return $this->cashTransactionRepository->sumCredit($from, $to);
    }

    /**
     * Menghitung saldo berjalan (Total Pemasukan - Total Pengeluaran) hingga rentang tertentu.
     * Secara bawaan mengambil saldo kas pada saat ini.
     *
     * @param string $upToDate Batas akhir tanggal penghitungan saldo
     * @return float
     */
    public function getRunningBalance(string $upToDate = '2099-12-31'): float
    {
        $totalIncome = $this->getTotalIncome('1970-01-01', $upToDate);
        $totalExpense = $this->getTotalExpense('1970-01-01', $upToDate);

        return $totalIncome - $totalExpense;
    }
}
