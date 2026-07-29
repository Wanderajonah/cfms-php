<?php

declare(strict_types=1);

final class CustomerController
{
    public function index(): void
    {
        Auth::require();
        $search = trim((string) ($_GET['search'] ?? ''));
        $customers = (new Feedback())->customers($search);
        View::render('customers/index', ['title' => 'Customers', 'customers' => $customers]);
    }
}
