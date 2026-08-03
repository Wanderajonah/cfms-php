<?php

declare(strict_types=1);

final class OrderController
{
    private function deliveryFee(): int
    {
        $settings = (new Setting())->all();
        return max(0, (int) ($settings['delivery_fee'] ?? 5000));
    }

    public function index(): void
    {
        $menu = (new MenuItem())->listActive();
        $grouped = [];
        foreach ($menu as $item) {
            $grouped[$item['category']][] = $item;
        }
        View::render('order/online', [
            'title' => 'Order Online - Cafe Javas',
            'categories' => $grouped,
            'branches' => (new Branch())->list(),
            'deliveryFee' => $this->deliveryFee(),
        ], 'public');
    }

    public function placeOrder(): void
    {
        Security::verifyCsrf();

        $name = trim((string) ($_POST['name'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? '')) ?: null;
        $orderType = ($_POST['order_type'] ?? 'delivery') === 'pickup' ? 'pickup' : 'delivery';
        $address = trim((string) ($_POST['delivery_address'] ?? ''));
        $notes = trim((string) ($_POST['notes'] ?? ''));
        $paymentMethod = in_array($_POST['payment_method'] ?? 'cash', ['mtn_momo', 'airtel_money', 'cash'], true)
            ? (string) $_POST['payment_method']
            : 'cash';
        $paymentPhone = in_array($paymentMethod, ['mtn_momo', 'airtel_money'], true)
            ? (trim((string) ($_POST['payment_phone'] ?? '')) ?: $phone)
            : null;

        $branchId = !empty($_POST['branch_id']) ? (int) $_POST['branch_id'] : null;
        if ($branchId !== null && !(new Branch())->find($branchId)) {
            $branchId = null;
        }

        if ($name === '' || $phone === '') {
            Flash::error('Please provide your name and phone number.');
            Response::redirect('/order-online');
        }

        $quantities = array_filter(
            array_map('intval', $_POST['qty'] ?? []),
            static fn (int $q) => $q > 0
        );
        if (empty($quantities)) {
            Flash::error('Your cart is empty. Add at least one item to place an order.');
            Response::redirect('/order-online');
        }

        $lines = [];
        $subtotal = 0;
        foreach ((new MenuItem())->findByIds(array_keys($quantities)) as $item) {
            $qty = $quantities[$item['id']];
            $lineTotal = (int) $item['price'] * $qty;
            $subtotal += $lineTotal;
            $lines[] = [
                'id' => (int) $item['id'],
                'name' => $item['name'],
                'price' => (int) $item['price'],
                'qty' => $qty,
                'lineTotal' => $lineTotal,
            ];
        }
        if (empty($lines)) {
            Flash::error('One or more items in your cart are no longer available.');
            Response::redirect('/order-online');
        }

        $deliveryFee = $orderType === 'delivery'
            ? $this->deliveryFee()
            : 0;

        $order = [
            'order_number' => (new Counter())->next('orders'),
            'customer_name' => $name,
            'phone' => $phone,
            'email' => $email,
            'branch_id' => $branchId,
            'order_type' => $orderType,
            'delivery_address' => $orderType === 'delivery' ? $address : null,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $subtotal + $deliveryFee,
            'payment_method' => $paymentMethod,
            'payment_phone' => $paymentPhone,
            'notes' => $notes,
            'items_json' => json_encode($lines),
        ];
        $id = (new Order())->create($order);
        if (!$id) {
            Flash::error('We could not place your order. Please try again.');
            Response::redirect('/order-online');
        }

        (new AuditLog())->record(null, 'create', 'order', $id, 'Order #' . $order['order_number'] . ' placed online');

        Response::redirect('/order/confirmation/' . $id);
    }

    public function confirmation(int $id): void
    {
        $order = (new Order())->find($id);
        if (!$order) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Not Found']);
            return;
        }

        $items = json_decode((string) $order['items_json'], true) ?: [];
        $branch = null;
        if (!empty($order['branch_id']) && ($row = (new Branch())->find((int) $order['branch_id']))) {
            $branch = $row['name'];
        }

        $payments = [
            'mtn_momo' => 'MTN Mobile Money',
            'airtel_money' => 'Airtel Money',
            'cash' => 'Cash',
        ];

        View::render('order/confirmation', [
            'title' => 'Order #' . $order['order_number'] . ' Confirmed',
            'order' => $order,
            'items' => $items,
            'branch' => $branch,
            'paymentLabel' => $payments[$order['payment_method']] ?? 'Cash',
            'statusLabel' => ucfirst(str_replace('-', ' ', (string) $order['status'])),
        ], 'public');
    }
}