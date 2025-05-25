<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Service\ExpenseService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ExpenseController extends BaseController
{
    private const PAGE_SIZE = 20;

    public function __construct(
        Twig $view,
        private readonly ExpenseService $expenseService,
    ) {
        parent::__construct($view);
    }

    public function index(Request $request, Response $response): Response
    {
        // TODO: implement this action method to display the expenses page

        // Hints:
        // - use the session to get the current user ID
        // - use the request query parameters to determine the page number and page size
        // - use the expense service to fetch expenses for the current user

        // parse request parameters
        // TODO: obtain logged-in user ID from session


        if(!isset($_SESSION['user']['id']))
        {
            return $response->withHeader('Location', '/login')->withStatus(302);

        }

        $userId = (int)$_SESSION['user']['id'];

        $query = $request->getQueryParams();
        $page = (int)($query['page'] ?? 1);
        $pageSize = self ::PAGE_SIZE;

        $currentDate = new \DateTimeImmutable();
        $year = (int)($query['year'] ?? $currentDate->format('Y'));
        $month = (int)($query['month'] ?? $currentDate->format('m'));


        $expenses = $this->ExpenseService->listByUserAndMonth($userId, $year, $month, $page, $pageSize);
        $totalCount = $this->ExpenseService->countByUserAndMonth($userId, $year, $month);
        $totalPages = (int)ceil($totalCount / $pageSize);

        return $this->render($response, 'expenses/index.twig', [
            'expenses' => $expenses,
            'page'     => $page,
            'pageSize' => $pageSize,
            'year' => $year,
            'month' => $month,
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        // TODO: implement this action method to display the create expense page

        // Hints:
        // - obtain the list of available categories from configuration and pass to the view

        if(!isset($_SESSION['user']['id']))
        {
            return $response->withHeader('Location', '/login')->withStatus(302);

        }

        $categories = $this->ExpenseService->getAvailableCategories();





        return $this->render($response, 'expenses/create.twig', [
            'categories' => $categories,
            'errors' => [],
            ]);
    }

    public function store(Request $request, Response $response): Response
    {
        // TODO: implement this action method to create a new expense

        // Hints:
        // - use the session to get the current user ID
        // - use the expense service to create and persist the expense entity
        // - rerender the "expenses.create" page with included errors in case of failure
        // - redirect to the "expenses.index" page in case of success
        if(!isset($_SESSION['user']['id']))
        {
            return $response->withHeader('Location', '/login')->withStatus(302);

        }

        $data = $request->getParsedBody();
        $data= $data['date'] ?? '';
        $category = $data['category'] ?? '';
        $amount = $data['amount'] ?? '';
        $description = $data['description'] ?? '';

        if (empty($date) || new \DateTimeImmutable($date) > new \DateTimeImmutable()) {
        $errors['date'] = 'Data nu este valida - trebuie sa fie astazi sau mai devreme';
        }

        if(empty($category)) 
        {
            $errors['category'] = 'Selecteaza categorie';
        }
        
        if($amount<=0)
        {
            $errors['amount']='Amount trebuie sa fie mai mare decat 0';

        }

        if(empty$description))
        {
            $errors['description'] = 'Descrierea este obligatorie'
        }

        if (!empty($errors)) {
        $categories = $this->expenseService->getAvailableCategories();
        return $this->render($response, 'expenses/create.twig', [
            'errors' => $errors,
            'categories' => $categories
        ]);
    }

         $this->expenseService->create(
            $user,
            (float)$amount,
            $description,
            new \DateTimeImmutable($date),
            $category
        );


        return $response;
    }

    public function edit(Request $request, Response $response, array $routeParams): Response
    {
        // TODO: implement this action method to display the edit expense page

        // Hints:
        // - obtain the list of available categories from configuration and pass to the view
        // - load the expense to be edited by its ID (use route params to get it)
        // - check that the logged-in user is the owner of the edited expense, and fail with 403 if not
         if(!isset($_SESSION['user']['id']))
        {
            return $response->withHeader('Location', '/login')->withStatus(302);

        }
        $expenseId = (int)($routeParams['id'] ?? 0);
        $expense = $this->expenseService->findExpense($expenseId);

        if (!$expense || $expense->userId !== $_SESSION['user']['id']) 
        {
            return $response->withStatus(403);
        }

        $categories = $this->expenseService->getAvailableCategories();
            
        return $this->render($response, 'expenses/edit.twig', ['expense' => $expense, 'categories' => $categories, 'errors'=>[]]);
        }

    public function update(Request $request, Response $response, array $routeParams): Response
    {
        // TODO: implement this action method to update an existing expense

        // Hints:
        // - load the expense to be edited by its ID (use route params to get it)
        // - check that the logged-in user is the owner of the edited expense, and fail with 403 if not
        // - get the new values from the request and prepare for update
        // - update the expense entity with the new values
        // - rerender the "expenses.edit" page with included errors in case of failure
        // - redirect to the "expenses.index" page in case of success
        if (!isset($_SESSION['user']['id'])) {
        return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $expenseId = (int)($routeParams['id'] ?? 0);
        $expense = $this->expenseService->findExpense($expenseId);

        if (!$expense || $expense->userId !== $_SESSION['user']['id']) {
            return $response->withStatus(403);
        }

        $data = $request->getParsedBody();
        $date = $data['date'] ?? '';
        $category = $data['category'] ?? '';
        $amount = $data['amount'] ?? '';
        $description = trim($data['description'] ?? '');

        $errors = [];

        if (empty($date) || new \DateTimeImmutable($date) > new \DateTimeImmutable()) {
            $errors['date'] = 'Data nu este valida - trebuie sa fie astazi sau mai devreme';
        }

        if (empty($category)) {
            $errors['category'] = 'Selecteaza categorie';
        }

        if (!is_numeric($amount) || $amount <= 0) {
            $errors['amount'] = 'Amount trebuie sa fie mai mare decat 0';
        }

        if (empty($description)) {
            $errors['description'] = 'Descrierea este obligatorie';
        }

        if (!empty($errors)) {
            $categories = $this->expenseService->getAvailableCategories();
            return $this->render($response, 'expenses/edit.twig', [
                'expense' => $expense,
                'categories' => $categories,
                'errors' => $errors
            ]);
        }

        $this->expenseService->update(
            $expense,
            (float)$amount,
            $description,
            new \DateTimeImmutable($date),
            $category
        );

        return $response->withHeader('Location', '/expenses')->withStatus(302);
    }

    public function destroy(Request $request, Response $response, array $routeParams): Response
    {
        // TODO: implement this action method to delete an existing expense

        // - load the expense to be edited by its ID (use route params to get it)
        // - check that the logged-in user is the owner of the edited expense, and fail with 403 if not
        // - call the repository method to delete the expense
        // - redirect to the "expenses.index" page
        if (!isset($_SESSION['user']['id'])) {
        return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $expenseId = (int)($routeParams['id'] ?? 0);
        $expense = $this->expenseService->findExpense($expenseId);

        if (!$expense || $expense->userId !== $_SESSION['user']['id']) {
            return $response->withStatus(403);
        }

        $this->expenseService->delete($expenseId);

        return $response->withHeader('Location', '/expenses')->withStatus(302);
    }
}
