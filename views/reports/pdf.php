<?php
$items = $data['items'] ?? [];
$totals = $summary['totals'] ?? [];
$avgHours = $summary['avgResponseHours'] ?? null;
$responseRate = $summary['responseRate'] ?? 0;
$avgRating = $summary['avgRating'] ?? null;
$range = array_filter([$filters['date_from'] ?? '', $filters['date_to'] ?? ''], static fn ($v) => $v !== '');
$generated = date('F j, Y g:i A');
$statusLabel = ($filters['status'] ?? '') !== '' ? $filters['status'] : 'All';
$categoryLabel = ($filters['category'] ?? '') !== '' ? $filters['category'] : 'All';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Cafe Javas - Customer Feedback Report</title>
<style>
    @page {
        size: A4 landscape;
        margin: 14mm 12mm 16mm 12mm;
        @bottom-left { content: "Cafe Javas Feedback Management"; color: #94a3b8; font-size: 9px; }
        @bottom-right { content: "Page " counter(page) " of " counter(pages); color: #94a3b8; font-size: 9px; }
    }
    * { box-sizing: border-box; }
    body { font-family: "Segoe UI", Arial, sans-serif; color: #152033; font-size: 12px; margin: 0; }
    .head { display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid #0f766e; padding-bottom: 10px; margin-bottom: 12px; }
    .brand { display: flex; align-items: center; gap: 10px; }
    .brand img { width: 40px; height: 40px; object-fit: contain; }
    .brand-name { font-size: 15px; font-weight: 700; color: #0f766e; line-height: 1.1; }
    .brand-sub { font-size: 9px; color: #64748b; }
    h1 { font-size: 20px; margin: 0 0 2px; color: #111827; }
    .meta { color: #64748b; font-size: 10px; text-align: right; }
    .meta strong { color: #152033; }
    .stats { display: flex; gap: 8px; margin: 12px 0 10px; }
    .stat { flex: 1; border: 1px solid #dbe3ef; border-radius: 8px; padding: 8px 12px; background: #f8fafc; }
    .stat span { display: block; font-size: 9px; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
    .stat strong { font-size: 19px; color: #0f766e; }
    .filters { font-size: 10px; color: #64748b; margin-bottom: 10px; }
    .filters b { color: #334155; }
    table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
    thead th { background: #0f766e; color: #fff; text-align: left; padding: 7px 8px; font-size: 9px; text-transform: uppercase; letter-spacing: .05em; }
    tbody td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    .customer-name { font-weight: 600; }
    .muted { color: #94a3b8; font-size: 9px; }
    .tag { border-radius: 4px; padding: 2px 6px; font-size: 9px; font-weight: 600; white-space: nowrap; }
    .s-resolved { color: #166534; background: #dcfce7; }
    .s-pending { color: #854d0e; background: #fef9c3; }
    .s-in-progress { color: #1e40af; background: #dbeafe; }
    .s-escalated { color: #991b1b; background: #fee2e2; }
    .p-high { color: #991b1b; background: #fee2e2; }
    .p-medium { color: #854d0e; background: #fef9c3; }
    .p-low { color: #334155; background: #e2e8f0; }
    .t-compliment { color: #166534; background: #dcfce7; }
    .t-suggestion { color: #1e40af; background: #dbeafe; }
    .t-complaint { color: #991b1b; background: #fee2e2; }
    .empty { text-align: center; padding: 40px; color: #64748b; }
    .foot { margin-top: 12px; font-size: 9px; color: #94a3b8; border-top: 1px solid #dbe3ef; padding-top: 6px; }
    .no-print { display: flex; }
    @media screen {
        body { padding-top: 54px; }
        .no-print { position: fixed; top: 0; left: 0; right: 0; z-index: 99; align-items: center; gap: 10px; background: #0f766e; color: #fff; padding: 10px 16px; font-size: 12px; }
        .no-print button, .no-print a { border: none; border-radius: 6px; padding: 8px 14px; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .no-print .btn-print { background: #fff; color: #0f766e; }
        .no-print .btn-back { background: rgba(255,255,255,0.15); color: #fff; }
        .no-print .hint { color: rgba(255,255,255,0.85); margin-left: auto; }
    }
    @media print {
        .no-print { display: none !important; }
    }
</style>
</head>
<body>
<div class="no-print">
    <button type="button" class="btn-print" onclick="window.print()">Save as PDF</button>
    <a class="btn-back" href="/reports">&larr; Back to Reports</a>
    <span class="hint">In the print dialog, choose &ldquo;Save as PDF&rdquo; as the destination.</span>
</div>
<div class="head">
    <div class="brand">
        <img src="<?= Security::e($logoPath) ?>" alt="Cafe Javas">
        <div>
            <div class="brand-name">Cafe Javas</div>
            <div class="brand-sub">Customer Feedback Management System</div>
        </div>
    </div>
    <div class="meta">
        <h1>Customer Feedback Report</h1>
        Generated <?= Security::e($generated) ?><br>
        Records: <strong><?= (int) count($items) ?></strong>
    </div>
</div>

<div class="stats">
    <div class="stat"><span>Total Feedback</span><strong><?= (int) ($totals['total'] ?? 0) ?></strong></div>
    <div class="stat"><span>Resolved</span><strong><?= (int) ($totals['resolved'] ?? 0) ?></strong></div>
    <div class="stat"><span>Escalated</span><strong><?= (int) ($totals['escalated'] ?? 0) ?></strong></div>
    <div class="stat"><span>Avg Response</span><strong><?= $avgHours !== null ? Security::e((string) $avgHours) . ' h' : 'N/A' ?></strong></div>
    <div class="stat"><span>Response Rate</span><strong><?= (float) $responseRate ?>%</strong></div>
    <div class="stat"><span>Avg Rating</span><strong><?= $avgRating !== null ? Security::e((string) $avgRating) . ' / 5' : 'N/A' ?></strong></div>
</div>

<div class="filters">
    <b>Filters:</b>
    <?php if ($range): ?>Date: <b><?= Security::e(implode(' to ', $range)) ?></b> &middot;<?php else: ?>Date: <b>All time</b> &middot;<?php endif; ?>
    Status: <b><?= Security::e($statusLabel) ?></b> &middot;
    Category: <b><?= Security::e($categoryLabel) ?></b>
</div>

<table>
    <thead>
        <tr>
            <th>Ticket</th>
            <th>Customer</th>
            <th>Branch</th>
            <th>Category</th>
            <th>Type</th>
            <th>Rating</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!$items): ?>
        <tr><td colspan="9" class="empty">No feedback records match the selected filters.</td></tr>
    <?php endif; ?>
    <?php foreach ($items as $item): ?>
        <tr>
            <td>#<?= Security::e((string) $item['ticketNumber']) ?></td>
            <td>
                <span class="customer-name"><?= Security::e($item['name'] ?: 'Anonymous') ?></span>
                <div class="muted"><?= Security::e($item['email'] ?: $item['phone']) ?></div>
            </td>
            <td><?= Security::e($item['branchName'] ?? ($item['branch_name'] ?? '-')) ?></td>
            <td><?= Security::e($item['category']) ?></td>
            <td><span class="tag t-<?= Security::e($item['type']) ?>"><?= Security::e($item['type']) ?></span></td>
            <td><?= Security::e((string) ($item['rating'] ?: '-')) ?></td>
            <td><span class="tag s-<?= Security::e($item['status']) ?>"><?= Security::e($item['status']) ?></span></td>
            <td><span class="tag p-<?= Security::e($item['priority']) ?>"><?= Security::e($item['priority']) ?></span></td>
            <td><?= Security::e(substr((string) $item['createdAt'], 0, 16)) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="foot">
    Confidential internal report &middot; Generated by the Cafe Javas Feedback Management System
</div>
<script>
    window.addEventListener('load', function () {
        setTimeout(function () { window.print(); }, 500);
    });
</script>
</body>
</html>
