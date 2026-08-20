<?php

declare(strict_types=1);

final class Feedback extends BaseModel
{
    public const CATEGORIES = ['Food Quality', 'Service', 'Ambiance', 'Cleanliness', 'Pricing', 'Menu', 'Value', 'Other'];
    public const TYPES = ['compliment', 'suggestion', 'complaint'];
    public const STATUSES = ['pending', 'in-progress', 'resolved', 'escalated'];
    public const PRIORITIES = ['low', 'medium', 'high'];

    public function paginate(array $filters, int $page = 1, int $limit = 20, string $sort = '-createdAt'): array
    {
        [$where, $params] = $this->where($filters);
        $offset = ($page - 1) * $limit;
        $order = match ($sort) {
            'createdAt' => 'created_at ASC',
            '-rating' => 'rating DESC',
            'rating' => 'rating ASC',
            'status' => 'status ASC',
            default => 'created_at DESC',
        };

        $total = (int) $this->fetchColumn("SELECT COUNT(*) FROM feedback $where", $params);

        $items = $this->fetchAll(
            "SELECT f.*, b.name AS branch_name
             FROM feedback f LEFT JOIN branches b ON b.id = f.branch_id
             $where ORDER BY $order LIMIT :limit OFFSET :offset",
            $params + ['limit' => $limit, 'offset' => $offset]
        );

        return [
            'items' => array_map([$this, 'serialize'], $items),
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'totalPages' => max(1, (int) ceil($total / $limit)),
        ];
    }

    public function find(int $id): ?array
    {
        $row = $this->fetch(
            'SELECT f.*, b.name AS branch_name
             FROM feedback f LEFT JOIN branches b ON b.id = f.branch_id
             WHERE f.id = :id LIMIT 1',
            ['id' => $id]
        );
        return $row ? $this->serialize($row) : null;
    }

    public function findByTicketAndPhone(int $ticket, string $phone): ?array
    {
        $row = $this->fetch(
            'SELECT * FROM feedback WHERE ticket_number = :ticket AND phone = :phone LIMIT 1',
            ['ticket' => $ticket, 'phone' => $phone]
        );
        return $row ? $this->serialize($row) : null;
    }

    public function create(array $data): array
    {
        $ticket = (new Counter())->next('feedback');
        $clean = $this->clean($data);
        if ($clean['message'] === '') {
            throw new InvalidArgumentException('Feedback message is required');
        }

        $id = $this->insert(
            'INSERT INTO feedback
            (ticket_number, name, email, phone, branch_id, category, type, rating, message, status, priority, assigned_to, staff_notes,
             escalation_note, response, responded_at, resolved_at, automated_sms_at, automated_sms_body,
             automated_sms_error, automated_sms_skipped, created_at, updated_at)
             VALUES
            (:ticket_number, :name, :email, :phone, :branch_id, :category, :type, :rating, :message, :status, :priority, :assigned_to,
             :staff_notes, :escalation_note, :response, :responded_at, :resolved_at, :automated_sms_at,
             :automated_sms_body, :automated_sms_error, :automated_sms_skipped, :created_at, :updated_at)',
            $clean + ['ticket_number' => $ticket, 'created_at' => $this->now(), 'updated_at' => $this->now()]
        );
        return $this->find($id);
    }

    public function update(int $id, array $data): ?array
    {
        $clean = $this->clean($data, false);
        if (($clean['status'] ?? null) === 'resolved') {
            $clean['resolved_at'] = $this->now();
        }
        if (($clean['status'] ?? null) === 'pending') {
            $clean['resolved_at'] = null;
        }
        if (!empty($clean['response'])) {
            $clean['responded_at'] = $this->now();
        }
        $fields = [];
        $params = ['id' => $id, 'updated_at' => $this->now()];
        foreach ($clean as $key => $value) {
            if ($value !== '__missing__') {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        if (!$fields) {
            return $this->find($id);
        }
        $fields[] = 'updated_at = :updated_at';
        $this->execute('UPDATE feedback SET ' . implode(', ', $fields) . ' WHERE id = :id', $params);
        return $this->find($id);
    }

    public function delete(int $id): bool
    {
        $affected = $this->execute('DELETE FROM feedback WHERE id = :id', ['id' => $id]);
        return $affected > 0;
    }

    public function summary(int $months = 6, int $days = 30, ?string $startDate = null): array
    {
        $total = (int) $this->fetchColumn('SELECT COUNT(*) FROM feedback');
        $statusRows = $this->fetchAll('SELECT status, COUNT(*) AS count FROM feedback GROUP BY status');
        $statusTotals = ['pending' => 0, 'in-progress' => 0, 'resolved' => 0, 'escalated' => 0];
        foreach ($statusRows as $row) {
            $statusTotals[$row['status']] = (int) $row['count'];
        }

        $categoryRows = $this->fetchAll('SELECT category AS name, COUNT(*) AS value FROM feedback GROUP BY category ORDER BY value DESC');

        $monthly = [];
        $start = new DateTimeImmutable('first day of this month');
        $start = $start->modify('-' . max(0, $months - 1) . ' months');
        for ($i = 0; $i < $months; $i++) {
            $date = $start->modify("+$i months");
            $from = $date->format('Y-m-01 00:00:00');
            $to = $date->modify('first day of next month')->format('Y-m-01 00:00:00');
            $row = $this->fetch(
                'SELECT COUNT(*) AS total, SUM(status = "resolved") AS resolved FROM feedback WHERE created_at >= :from AND created_at < :to',
                ['from' => $from, 'to' => $to]
            );
            $monthly[] = ['month' => $date->format('M'), 'total' => (int) $row['total'], 'resolved' => (int) $row['resolved']];
        }

        $dayStart = $startDate ? new DateTimeImmutable($startDate) : (new DateTimeImmutable('today'))->modify('-' . max(0, $days - 1) . ' days');
        $dayCount = $startDate ? max(1, (int) $dayStart->diff(new DateTimeImmutable('today'))->days + 1) : $days;
        $daily = [];
        for ($i = 0; $i < $dayCount; $i++) {
            $date = $dayStart->modify("+$i days");
            $row = $this->fetch(
                'SELECT COUNT(*) AS total, SUM(status = "resolved") AS resolved FROM feedback
                 WHERE created_at >= :from AND created_at < :to',
                [
                    'from' => $date->format('Y-m-d 00:00:00'),
                    'to' => $date->modify('+1 day')->format('Y-m-d 00:00:00'),
                ]
            );
            $daily[] = ['date' => $date->format('M j'), 'total' => (int) $row['total'], 'resolved' => (int) $row['resolved']];
        }

        $avg = $this->fetchColumn('SELECT AVG(TIMESTAMPDIFF(SECOND, created_at, responded_at)) / 3600 FROM feedback WHERE responded_at IS NOT NULL');
        $typeRows = $this->fetchAll("SELECT type, COUNT(*) AS count FROM feedback GROUP BY type");
        $priorityRows = $this->fetchAll("SELECT priority, COUNT(*) AS count FROM feedback GROUP BY priority ORDER BY FIELD(priority,'high','medium','low')");
        $ratingStats = $this->fetch("SELECT AVG(rating) AS avg, COUNT(*) AS total FROM feedback WHERE rating IS NOT NULL");
        $ratingDist = $this->fetchAll("SELECT rating, COUNT(*) AS count FROM feedback WHERE rating IS NOT NULL GROUP BY rating ORDER BY rating DESC");
        $responded = (int) $this->fetchColumn("SELECT COUNT(*) FROM feedback WHERE responded_at IS NOT NULL");
        $escalatedList = $this->fetchAll("SELECT id, ticket_number, name, category, created_at FROM feedback WHERE status = 'escalated' ORDER BY created_at DESC LIMIT 5");
        $topCustomers = $this->fetchAll("SELECT name, email, COUNT(*) AS cnt FROM feedback WHERE name IS NOT NULL GROUP BY COALESCE(email, phone, name) ORDER BY cnt DESC LIMIT 5");
        $weekdayRows = $this->fetchAll("SELECT DAYNAME(created_at) AS day, COUNT(*) AS count FROM feedback GROUP BY DAYNAME(created_at) ORDER BY FIELD(DAYNAME(created_at),'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
        $hourRows = $this->fetchAll("SELECT HOUR(created_at) AS hour, COUNT(*) AS count FROM feedback GROUP BY HOUR(created_at) ORDER BY hour");
        $respTrend = [];
        for ($i = 0; $i < $months; $i++) {
            $d = (new DateTimeImmutable('first day of this month'))->modify('-' . max(0, $months - 1) . ' months')->modify("+$i months");
            $f = $d->format('Y-m-01 00:00:00');
            $t = $d->modify('first day of next month')->format('Y-m-01 00:00:00');
            $val = $this->fetchColumn(
                "SELECT AVG(TIMESTAMPDIFF(SECOND, created_at, responded_at)) / 3600 FROM feedback WHERE created_at >= :from AND created_at < :to AND responded_at IS NOT NULL",
                ['from' => $f, 'to' => $t]
            );
            $respTrend[] = ['month' => $d->format('M'), 'hours' => $val !== null ? round((float) $val, 1) : null];
        }
        $cumulative = 0;
        $cumulativeData = array_map(function ($m) use (&$cumulative) { $cumulative += $m['total']; return ['month' => $m['month'], 'total' => $cumulative]; }, $monthly);

        return [
            'totals' => [
                'total' => $total,
                'pending' => $statusTotals['pending'],
                'inProgress' => $statusTotals['in-progress'],
                'resolved' => $statusTotals['resolved'],
                'escalated' => $statusTotals['escalated'],
            ],
            'categories' => array_map(static fn ($r) => ['name' => $r['name'], 'value' => (int) $r['value']], $categoryRows),
            'types' => array_map(static fn ($r) => ['name' => $r['type'], 'value' => (int) $r['count']], $typeRows),
            'priorities' => array_map(static fn ($r) => ['name' => $r['priority'], 'value' => (int) $r['count']], $priorityRows),
            'monthly' => $monthly,
            'daily' => $daily,
            'avgResponseHours' => $avg !== null ? round((float) $avg, 1) : null,
            'responseRate' => $total > 0 ? round($responded / $total * 100, 1) : 0,
            'avgRating' => $ratingStats && $ratingStats['avg'] !== null ? round((float) $ratingStats['avg'], 1) : null,
            'ratingDistribution' => array_map(static fn ($r) => ['rating' => (int) $r['rating'], 'count' => (int) $r['count']], $ratingDist),
            'escalatedList' => $escalatedList,
            'topCustomers' => $topCustomers,
            'weekdays' => array_map(static fn ($r) => ['day' => $r['day'], 'count' => (int) $r['count']], $weekdayRows),
            'hours' => array_map(static fn ($r) => ['hour' => (int) $r['hour'], 'count' => (int) $r['count']], $hourRows),
            'responseTrend' => $respTrend,
            'cumulative' => $cumulativeData,
        ];
    }

    public function staffSummary(string $email): array
    {
        $count = function (string $where) use ($email): int {
            return (int) $this->fetchColumn(
                "SELECT COUNT(*) FROM feedback WHERE assigned_to = :email $where",
                ['email' => $email]
            );
        };
        $avgHours = $this->fetchColumn(
            "SELECT AVG(TIMESTAMPDIFF(SECOND, created_at, responded_at)) / 3600 FROM feedback WHERE assigned_to = :email AND responded_at IS NOT NULL",
            ['email' => $email]
        );
        $responded = $count("AND responded_at IS NOT NULL");
        $total = $count('');
        $avgRating = $this->fetchColumn(
            "SELECT AVG(rating) FROM feedback WHERE assigned_to = :email AND rating IS NOT NULL",
            ['email' => $email]
        );
        return [
            'totals' => [
                'total' => $total,
                'pending' => $count("AND status = 'pending'"),
                'inProgress' => $count("AND status = 'in-progress'"),
                'resolved' => $count("AND status = 'resolved'"),
                'escalated' => $count("AND status = 'escalated'"),
            ],
            'avgResponseHours' => $avgHours !== null ? round((float) $avgHours, 1) : null,
            'responseRate' => $total > 0 ? round($responded / $total * 100, 1) : 0,
            'avgRating' => $avgRating !== null ? round((float) $avgRating, 1) : null,
        ];
    }

    public function staffMonthly(string $email, int $months = 6): array
    {
        $monthly = [];
        $start = new DateTimeImmutable('first day of this month');
        $start = $start->modify('-' . max(0, $months - 1) . ' months');
        for ($i = 0; $i < $months; $i++) {
            $date = $start->modify("+$i months");
            $from = $date->format('Y-m-01 00:00:00');
            $to = $date->modify('first day of next month')->format('Y-m-01 00:00:00');
            $row = $this->fetch(
                'SELECT COUNT(*) AS total, SUM(status = "resolved") AS resolved FROM feedback WHERE assigned_to = :email AND created_at >= :from AND created_at < :to',
                ['email' => $email, 'from' => $from, 'to' => $to]
            );
            $monthly[] = ['month' => $date->format('M'), 'total' => (int) $row['total'], 'resolved' => (int) $row['resolved']];
        }
        return $monthly;
    }

    public function staffTypes(string $email): array
    {
        return $this->fetchAll(
            "SELECT type AS name, COUNT(*) AS value FROM feedback WHERE assigned_to = :email GROUP BY type",
            ['email' => $email]
        );
    }

    public function staffPriorities(string $email): array
    {
        return $this->fetchAll(
            "SELECT priority AS name, COUNT(*) AS value FROM feedback WHERE assigned_to = :email GROUP BY priority ORDER BY FIELD(priority,'high','medium','low')",
            ['email' => $email]
        );
    }

    public function staffWeekdays(string $email): array
    {
        $rows = $this->fetchAll(
            "SELECT DAYNAME(created_at) AS day, COUNT(*) AS count FROM feedback WHERE assigned_to = :email GROUP BY DAYNAME(created_at) ORDER BY FIELD(DAYNAME(created_at),'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')",
            ['email' => $email]
        );
        return array_map(static fn ($r) => ['day' => $r['day'], 'count' => (int) $r['count']], $rows);
    }

    public function staffHours(string $email): array
    {
        $rows = $this->fetchAll(
            "SELECT HOUR(created_at) AS hour, COUNT(*) AS count FROM feedback WHERE assigned_to = :email GROUP BY HOUR(created_at) ORDER BY hour",
            ['email' => $email]
        );
        return array_map(static fn ($r) => ['hour' => (int) $r['hour'], 'count' => (int) $r['count']], $rows);
    }

    public function staffCategories(string $email): array
    {
        return $this->fetchAll(
            'SELECT category AS name, COUNT(*) AS value FROM feedback WHERE assigned_to = :email GROUP BY category ORDER BY value DESC',
            ['email' => $email]
        );
    }

    public function adminNotifications(): array
    {
        $pending = (int) $this->fetchColumn("SELECT COUNT(*) FROM feedback WHERE type = 'complaint' AND status = 'pending'");
        $escalated = (int) $this->fetchColumn("SELECT COUNT(*) FROM feedback WHERE status = 'escalated'");
        return ['pendingComplaints' => $pending, 'escalations' => $escalated];
    }

    private function clean(array $data, bool $defaults = true): array
    {
        $pick = static fn (string $key) => array_key_exists($key, $data) ? trim((string) $data[$key]) : '__missing__';
        $category = $pick('category');
        $type = $pick('type');
        $status = $pick('status');
        $priority = $pick('priority');
        $rating = array_key_exists('rating', $data) && $data['rating'] !== '' ? (int) $data['rating'] : ($defaults ? null : '__missing__');

        return [
            'name' => $pick('name') === '__missing__' ? ($defaults ? null : '__missing__') : ($pick('name') ?: null),
            'email' => $pick('email') === '__missing__' ? ($defaults ? null : '__missing__') : (strtolower($pick('email')) ?: null),
            'phone' => $pick('phone') === '__missing__' ? ($defaults ? null : '__missing__') : ($pick('phone') ?: null),
            'branch_id' => array_key_exists('branch_id', $data) && $data['branch_id'] !== '' ? (int) $data['branch_id'] : ($defaults ? null : '__missing__'),
            'category' => in_array($category, self::CATEGORIES, true) ? $category : ($defaults ? 'Other' : '__missing__'),
            'type' => in_array($type, self::TYPES, true) ? $type : ($defaults ? 'suggestion' : '__missing__'),
            'rating' => $rating === '__missing__' ? '__missing__' : (($rating >= 1 && $rating <= 5) ? $rating : null),
            'message' => $pick('message') === '__missing__' ? ($defaults ? '' : '__missing__') : $pick('message'),
            'status' => in_array($status, self::STATUSES, true) ? $status : ($defaults ? 'pending' : '__missing__'),
            'priority' => in_array($priority, self::PRIORITIES, true) ? $priority : ($defaults ? 'medium' : '__missing__'),
            'assigned_to' => $pick('assignedTo') === '__missing__' && $pick('assigned_to') === '__missing__' ? ($defaults ? null : '__missing__') : (($data['assignedTo'] ?? $data['assigned_to'] ?? null) ?: null),
            'staff_notes' => $pick('staffNotes') === '__missing__' && $pick('staff_notes') === '__missing__' ? ($defaults ? null : '__missing__') : (($data['staffNotes'] ?? $data['staff_notes'] ?? null) ?: null),
            'escalation_note' => $pick('escalationNote') === '__missing__' && $pick('escalation_note') === '__missing__' ? ($defaults ? null : '__missing__') : (($data['escalationNote'] ?? $data['escalation_note'] ?? null) ?: null),
            'response' => $pick('response') === '__missing__' ? ($defaults ? null : '__missing__') : ($pick('response') ?: null),
            'responded_at' => $pick('responded_at') === '__missing__' ? ($defaults ? null : '__missing__') : $pick('responded_at'),
            'resolved_at' => $defaults ? null : '__missing__',
            'automated_sms_at' => $pick('automated_sms_at') === '__missing__' ? ($defaults ? null : '__missing__') : $pick('automated_sms_at'),
            'automated_sms_body' => $pick('automated_sms_body') === '__missing__' ? ($defaults ? null : '__missing__') : $pick('automated_sms_body'),
            'automated_sms_error' => $pick('automated_sms_error') === '__missing__' ? ($defaults ? null : '__missing__') : $pick('automated_sms_error'),
            'automated_sms_skipped' => $pick('automated_sms_skipped') === '__missing__' ? ($defaults ? null : '__missing__') : $pick('automated_sms_skipped'),
        ];
    }

    private function where(array $filters): array
    {
        $clauses = [];
        $params = [];
        foreach (['status', 'category', 'type', 'priority'] as $field) {
            if (!empty($filters[$field])) {
                $clauses[] = "$field = :$field";
                $params[$field] = (string) $filters[$field];
            }
        }
        if (!empty($filters['assignedTo'])) {
            $clauses[] = 'assigned_to = :assignedTo';
            $params['assignedTo'] = (string) $filters['assignedTo'];
        }
        if (!empty($filters['branch_id'])) {
            $clauses[] = 'branch_id = :branch_id';
            $params['branch_id'] = (int) $filters['branch_id'];
        }
        if (!empty($filters['search'])) {
            $like = '%' . trim((string) $filters['search']) . '%';
            $clauses[] = '(message LIKE :search1 OR name LIKE :search2 OR email LIKE :search3 OR phone LIKE :search4)';
            $params['search1'] = $like;
            $params['search2'] = $like;
            $params['search3'] = $like;
            $params['search4'] = $like;
        }
        if (!empty($filters['date_from'])) {
            $clauses[] = 'created_at >= :date_from';
            $params['date_from'] = $filters['date_from'] . ' 00:00:00';
        }
        if (!empty($filters['date_to'])) {
            $clauses[] = 'created_at <= :date_to';
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }
        return [$clauses ? 'WHERE ' . implode(' AND ', $clauses) : '', $params];
    }

    public function serialize(array $row): array
    {
        $row['_id'] = (string) $row['id'];
        $row['ticketNumber'] = (int) $row['ticket_number'];
        $row['branchId'] = isset($row['branch_id']) && $row['branch_id'] !== null ? (int) $row['branch_id'] : null;
        $row['branchName'] = $row['branch_name'] ?? null;
        $row['assignedTo'] = $row['assigned_to'];
        $row['staffNotes'] = $row['staff_notes'];
        $row['escalationNote'] = $row['escalation_note'];
        $row['respondedAt'] = $row['responded_at'];
        $row['resolvedAt'] = $row['resolved_at'];
        $row['createdAt'] = $row['created_at'];
        $row['updatedAt'] = $row['updated_at'];
        return $row;
    }
}
