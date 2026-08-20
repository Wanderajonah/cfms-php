<div id="page_content_wrapper" class="sub-page order-receipt-page">

<div class="receipt-wrap">
    <div class="receipt">
        <header class="receipt-bandband">
            <div class="receipt-tools">
                <button type="button" class="receipt-tool" id="receiptPrintBtn" aria-label="Print or save receipt as PDF" title="Print / Save as PDF"><i class="bi bi-printer"></i><span>Print / PDF</span></button>
                <a href="/order-online" class="receipt-tool receipt-close" aria-label="Close receipt" title="Continue to ordering"><i class="bi bi-x-lg"></i></a>
            </div>
            <img src="/assets/uploads/restaurant/logo-white.png" alt="Cafe Javas" class="receipt-brand">
            <div class="receipt-brandname">Cafe Javas</div>
            <div class="receipt-title">Official Order Receipt</div>
        </header>

        <div class="receipt-doc">
            <div class="doc-block">
                <span class="meta-label">Receipt No.</span>
                <span class="meta-value">CJ-<?= str_pad((string) $order['order_number'], 4, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="doc-block">
                <span class="meta-label">Order No.</span>
                <span class="meta-value">#<?= (int) $order['order_number'] ?></span>
            </div>
            <div class="doc-block">
                <span class="meta-label">Date</span>
                <span class="meta-value"><?= Security::e(date('d M Y, h:i A', strtotime($order['created_at']))) ?></span>
            </div>
            <div class="doc-block">
                <span class="meta-label">Status</span>
                <span class="meta-value"><span class="status-chip"><?= Security::e($statusLabel) ?></span></span>
            </div>
        </div>

        <div class="receipt-grid">
            <div class="bill-block">
                <span class="meta-label">Billed to</span>
                <span class="bill-name"><?= Security::e($order['customer_name']) ?></span>
                <span class="bill-line"><?= Security::e($order['phone']) ?></span>
                <?php if (!empty($order['email'])): ?>
                <span class="bill-line"><?= Security::e($order['email']) ?></span>
                <?php endif; ?>
                <?php if (!empty($order['delivery_address'])): ?>
                <span class="bill-line"><?= Security::e($order['delivery_address']) ?></span>
                <?php endif; ?>
            </div>
            <div class="bill-block">
                <span class="meta-label">Order type</span>
                <span class="meta-value"><?= ucfirst($order['order_type']) ?></span>
                <span class="meta-label" style="margin-top:10px;">Branch</span>
                <span class="meta-value"><?= $branch ? Security::e($branch) : '—' ?></span>
            </div>
        </div>

        <div class="receipt-items-wrap">
            <table class="receipt-items">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="center">Qty</th>
                        <th class="right">Unit price</th>
                        <th class="right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $line): ?>
                    <tr>
                        <td><?= Security::e($line['name']) ?></td>
                        <td class="center"><?= (int) $line['qty'] ?></td>
                        <td class="right">UGX <?= number_format((int) $line['price']) ?></td>
                        <td class="right">UGX <?= number_format((int) $line['lineTotal']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="receipt-bottom">
            <div class="receipt-note">
                <p>
                    <?php if ($order['order_type'] === 'delivery'): ?>
                        We will call you on <strong><?= Security::e($order['phone']) ?></strong> to confirm delivery.
                    <?php else: ?>
                        We will call you on <strong><?= Security::e($order['phone']) ?></strong> when your order is ready for pickup.
                    <?php endif; ?>
                    <?php if ($order['payment_method'] !== 'cash'): ?>
                        Approve the payment prompt on your mobile money number to complete checkout.
                    <?php endif; ?>
                </p>
            </div>
            <div class="receipt-totals">
                <div class="rline"><span>Subtotal</span><span>UGX <?= number_format((int) $order['subtotal']) ?></span></div>
                <div class="rline"><span><?= $order['order_type'] === 'delivery' ? 'Delivery fee' : 'Pickup' ?></span><span><?= $order['order_type'] === 'delivery' ? 'UGX ' . number_format((int) $order['delivery_fee']) : 'Free' ?></span></div>
                <div class="rline rtotal"><span>Total</span><span>UGX <?= number_format((int) $order['total']) ?></span></div>
                <div class="rline pay-line">
                    <span>
                        <?php if ($order['payment_method'] === 'mtn_momo'): ?>
                            <img src="/assets/uploads/restaurant/mtn-logo.svg" alt="MTN" class="pay-logo">
                        <?php elseif ($order['payment_method'] === 'airtel_money'): ?>
                            <img src="/assets/uploads/restaurant/airtel-logo.svg" alt="Airtel" class="pay-logo">
                        <?php else: ?>
                            <i class="bi bi-cash-stack pay-icon"></i>
                        <?php endif; ?>
                        <?= Security::e($paymentLabel) ?>
                    </span>
                    <span><?= $order['payment_phone'] ? Security::e($order['payment_phone']) : '—' ?></span>
                </div>
            </div>
        </div>

        <footer class="receipt-footer">
            <p>Thank you for choosing Cafe Javas. We hope you enjoy your meal!</p>
            <p class="receipt-footer-muted">For enquiries call +256 (0) 76-890-214 &bull; feedback@cafejavas.com</p>
        </footer>
    </div>
</div>

</div>

<style>
.order-receipt-page { background: var(--bg-cream); }
.receipt-wrap { padding: 48px 20px 80px; display: flex; justify-content: center; }
.receipt { position: relative; width: 100%; max-width: 640px; background: #fff; border: 1px solid #e7e1d6; border-radius: 6px; box-shadow: 0 18px 60px rgba(0,0,0,.10); padding: 0 44px 30px; overflow: hidden; font-family: var(--font-body); }
.receipt-bandband { position: relative; background: linear-gradient(135deg, #211a15, #3a332b); margin: 0 -44px 28px; padding: 34px 44px 26px; text-align: center; color: #fff; }
.receipt-tools { position: absolute; top: 14px; right: 16px; display: flex; align-items: center; gap: 8px; }
.receipt-tool { display: inline-flex; align-items: center; gap: 7px; height: 34px; padding: 0 12px; border: 1px solid rgba(255,255,255,.4); background: rgba(255,255,255,.1); color: #fff; border-radius: 30px; font-family: var(--font-body); font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all .15s; }
.receipt-tool:hover { background: #fff; color: #211a15; border-color: #fff; }
.receipt-close { width: 34px; padding: 0; justify-content: center; font-size: 15px; border-radius: 50%; }
.receipt-brand { height: 52px; width: auto; display: inline-block; }
.receipt-brandname { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; letter-spacing: .02em; margin-top: 8px; }
.receipt-title { display: inline-block; font-size: 11px; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: var(--gold); border: 1px solid rgba(255,255,255,.25); padding: 5px 16px; border-radius: 2px; margin-top: 14px; }
.receipt-doc { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
.doc-block { background: #fbf9f4; border: 1px solid #efe9de; border-radius: 4px; padding: 10px 12px; }
.meta-label { display: block; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .09em; color: #a59d8f; margin-bottom: 3px; }
.meta-value { font-size: 13px; font-weight: 700; color: var(--text-dark); }
.status-chip { display: inline-block; background: #eef6ee; color: #2e7d32; border-radius: 20px; padding: 2px 12px; font-size: 12px; font-weight: 700; text-transform: capitalize; }
.receipt-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 22px; }
.bill-block { display: flex; flex-direction: column; }
.bill-name { font-size: 16px; font-weight: 800; color: var(--text-dark); margin: 2px 0 4px; }
.bill-line { font-size: 13px; color: #6f6758; line-height: 1.5; }
.receipt-items-wrap { border: 1px solid #e7e1d6; border-radius: 4px; overflow: hidden; margin-bottom: 22px; }
.receipt-items { width: 100%; border-collapse: collapse; }
.receipt-items th { background: #fbf9f4; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .09em; color: #8a8272; text-align: left; padding: 10px 14px; border-bottom: 1px solid #e7e1d6; }
.receipt-items th.center { text-align: center; }
.receipt-items th.right { text-align: right; }
.receipt-items td { padding: 12px 14px; font-size: 14px; color: var(--text-dark); border-bottom: 1px solid #f3efe6; }
.receipt-items td.center { text-align: center; }
.receipt-items td.right { text-align: right; font-weight: 600; }
.receipt-items tbody tr:last-child td { border-bottom: none; }
.receipt-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start; }
.receipt-note p { margin: 0; font-size: 12.5px; line-height: 1.6; color: #6f6758; border-left: 3px solid var(--gold); padding: 10px 14px; background: #fbf8f1; border-radius: 0 4px 4px 0; }
.receipt-totals { border: 1px solid #e7e1d6; border-radius: 4px; padding: 12px 16px; }
.rline { display: flex; justify-content: space-between; font-size: 13px; color: #5b554b; padding: 3px 0; }
.rline.rtotal { font-weight: 800; font-size: 16px; color: var(--text-dark); border-top: 1px dashed #d8cfc0; margin-top: 5px; padding-top: 8px; }
.rline.pay-line { border-top: 1px solid #f0eae1; margin-top: 8px; padding-top: 8px; font-weight: 600; color: var(--text-dark); }
.pay-line span { display: inline-flex; align-items: center; gap: 6px; }
.pay-logo { height: 16px; width: auto; }
.pay-icon { font-size: 16px; color: #6f7866; }
.receipt-footer { text-align: center; margin-top: 26px; padding-top: 18px; border-top: 1px dashed #d8cfc0; }
.receipt-footer p { margin: 0 0 4px; font-size: 13px; font-weight: 700; color: var(--text-dark); }
.receipt-footer .receipt-footer-muted { font-weight: 400; font-size: 11.5px; color: #a59d8f; }
@media (max-width: 640px) {
    .receipt { padding: 0 22px 24px; }
    .receipt-bandband { margin: 0 -22px 22px; padding: 28px 22px 22px; }
    .receipt-doc { grid-template-columns: repeat(2, 1fr); }
    .receipt-grid, .receipt-bottom { grid-template-columns: 1fr; }
}

@media print {
    @page { size: A4 portrait; margin: 15mm 18mm; }
    html, body { margin: 0 !important; padding: 0 !important; background: #fff !important; }

    #preloader, #toTop, .header_style_wrapper, .top_bar, .footer_bar, .mobile_logo, #mobile_nav_icon, .receipt-tools { display: none !important; }

    #page_content_wrapper { position: static; display: block; margin: 0; padding: 0; }
    .order-receipt-page { display: block; background: #fff !important; }

    .receipt-wrap { padding: 0; width: 100%; max-width: 100%; display: block; }
    .receipt { width: 100%; max-width: 100% !important; margin: 0 auto; border: 1px solid #d8d0c0; box-shadow: none; border-radius: 0; padding: 0 34px 30px; }
    .receipt-bandband { -webkit-print-color-adjust: exact; print-color-adjust: exact; margin: 0 0 26px; padding: 30px 34px 24px; border-radius: 0; }
    .receipt-brand { height: 48px; }
    .receipt-doc { grid-template-columns: repeat(4, 1fr); }
    .receipt-grid { grid-template-columns: 1fr 1fr; }
    .receipt-bottom { grid-template-columns: 1fr 1fr; }
}
</style>

<script>
(function () {
    var btn = document.getElementById('receiptPrintBtn');
    if (btn) {
        btn.addEventListener('click', function () { window.print(); });
    }
})();
</script>


