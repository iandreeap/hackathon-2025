<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Expense;
use App\Domain\Entity\User;
use App\Domain\Repository\ExpenseRepositoryInterface;
use DateTimeImmutable;
use Psr\Http\Message\UploadedFileInterface;

class ExpenseService
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenses,
    ) {}

    public function listByUserAndMonth(int $userId, int $year, int $month, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        return $this->expenses->getByUserAndMonth($userId, $year, $month, $offset, $limit);
    }

    public function countByUserAndMonth(int $userId, int $year, int $month): int
    {
        return $this->expenses->countByUserAndMonth($userId, $year, $month);
    }

    public function getAvailableCategories(): array
    {
        return ['Mancare', 'Utilitati', 'Transport', 'Haine', 'Sanatate'];
    }

    public function findExpense(int $id): ?Expense
    {
        return $this->expenses->find($id);
    }

    public function delete(int $expenseId): void
    {
        $this->expenses->delete($expenseId);
    }


    public function create(
        User $user,
        float $amount,
        string $description,
        DateTimeImmutable $date,
        string $category,
    ): void {
        // TODO: implement this to create a new expense entity, perform validation, and persist

        // TODO: here is a code sample to start with
        $expense = new Expense(null, $user->id, $date, $category, (int)$amount, $description);
        $this->expenses->save($expense);
    }

    public function update(
        Expense $expense,
        float $amount,
        string $description,
        DateTimeImmutable $date,
        string $category,
    ): void {
        // TODO: implement this to update expense entity, perform validation, and persist
    }

    public function importFromCsv(User $user, UploadedFileInterface $csvFile): int
    {
        // TODO: process rows in file stream, create and persist entities
        // TODO: for extra points wrap the whole import in a transaction and rollback only in case writing to DB fails

        return 0; // number of imported rows
    }
}
