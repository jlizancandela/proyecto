<?php

namespace Shared\Presentation;

use Latte\Engine;
use Usuarios\Application\UserService;
use Usuarios\Presentation\Transformers\UserTransformer;
use Shared\Infrastructure\Pagination\Paginator;

class AdminController
{
    private Engine $latte;
    private ?UserService $userService;

    public function __construct(Engine $latte, ?UserService $userService = null)
    {
        $this->latte = $latte;
        $this->userService = $userService;
    }

    public function index(): string
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Admin.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'admin')
            ]
        );
    }

    public function usersManagement(): string
    {
        $limit = 10;
        $page = (int) ($_GET['page'] ?? 1);
        $search = trim($_GET['search'] ?? '');
        $offset = ($page - 1) * $limit;

        if (!empty($search)) {
            $users = $this->userService->searchUsers($search, $limit, $offset);
            $total = $this->userService->getTotalSearchResults($search);
        } else {
            $users = $this->userService->getAllUsers($limit, $offset);
            $total = $this->userService->getTotalUsers();
        }

        $totalPages = (int) ceil($total / $limit);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/UsersManagement.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'users' => UserTransformer::toArrayCollection($users),
                'page' => $page,
                'totalPages' => $totalPages,
                'search' => $search,
                'total' => $total
            ]
        );
    }
}
