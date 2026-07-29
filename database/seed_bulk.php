<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

$db = (new class {
    private PDO $pdo;
    public function __construct()
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $name = getenv('DB_DATABASE') ?: 'customer_feedback_system';
        $user = getenv('DB_USERNAME') ?: 'root';
        $pass = getenv('DB_PASSWORD') ?: '';
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        $this->pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }
    public function pdo(): PDO { return $this->pdo; }
})->pdo();

$categories = ['Food Quality', 'Service', 'Ambiance', 'Cleanliness', 'Pricing', 'Menu', 'Value', 'Other'];
$types = ['compliment', 'suggestion', 'complaint'];
$statuses = ['pending', 'in-progress', 'resolved', 'escalated'];
$ratings = [1, 2, 3, 4, 5];

$firstNames = [
    'Amina', 'Brian', 'Christine', 'Daniel', 'Esther', 'Frank', 'Grace', 'Hassan',
    'Irene', 'Joseph', 'Khadija', 'Lawrence', 'Mariam', 'Nathan', 'Olivia', 'Patrick',
    'Queen', 'Richard', 'Sarah', 'Timothy', 'Umar', 'Victoria', 'William', 'Xavier',
    'Yvonne', 'Zahara', 'Abdul', 'Beatrice', 'Charles', 'Diana', 'Emmanuel', 'Faith',
    'George', 'Helen', 'Isaac', 'Jane', 'Kevin', 'Lilian', 'Morris', 'Nancy',
    'Oscar', 'Patience', 'Ronald', 'Sandra', 'Thomas', 'Ursula', 'Vincent', 'Winnie',
    'Abraham', 'Betty', 'Catherine', 'David', 'Eve', 'Fred', 'Gloria', 'Henry',
    'Immaculate', 'John', 'Keziah', 'Leo', 'Margret', 'Noah', 'Peter', 'Ruth',
    'Samson', 'Tracy', 'Paul', 'Joy', 'Moses', 'Alice', 'Simon', 'Monica',
];

$lastNames = [
    'Nakato', 'Kizza', 'Achieng', 'Mukasa', 'Namutebi', 'Ssentongo', 'Nabatanzi',
    'Wasswa', 'Nankya', 'Kato', 'Nalule', 'Muwonge', 'Nabukenya', 'Lubega',
    'Nakimuli', 'Ssempijja', 'Namulindwa', 'Kiryowa', 'Nakubulwa', 'Mutebi',
    'Nalwoga', 'Bukenya', 'Nakamya', 'Kintu', 'Nakiboneka', 'Kyagaba',
    'Nakazzi', 'Ssewanyana', 'Nabatanzi', 'Mugisha', 'Uwimana', 'Habimana',
    'Hakizimana', 'Kamanzi', 'Niyonzima', 'Rutayisire', 'Tuyishime', 'Bizimana',
    'Mukamana', 'Ndagijimana', 'Bamporiki', 'Cyiza', 'Gakwaya', 'Kabanda',
    'Lugumira', 'Magoola', 'Nsubuga', 'Okello', 'Ochieng', 'Wabwire',
];

$messages = [
    'complaint' => [
        'The food took too long to arrive. We waited over 45 minutes.',
        'My order was wrong. I ordered a steak medium-rare but got well-done.',
        'The restroom was not clean. This needs more attention.',
        'The service was very slow and the staff seemed overwhelmed.',
        'The prices have increased but the portion sizes got smaller.',
        'I found a hair in my food. Very disappointing.',
        'The air conditioning was not working, it was too hot inside.',
        'Our reservation was not honored despite booking in advance.',
        'The music was too loud, could not have a conversation.',
        'The waiter was rude and dismissive when I complained.',
        'My drink had a strange taste, I think it was spoiled.',
        'The table was dirty when we were seated.',
        'We were charged for items we did not order.',
        'The parking attendant was unhelpful.',
        'The kids play area was dirty and unsafe.',
    ],
    'suggestion' => [
        'Please add more vegan and vegetarian options to the menu.',
        'It would be great to have a loyalty program for regular customers.',
        'Consider extending opening hours on weekends.',
        'Adding a outdoor seating area would be wonderful.',
        'Please introduce more traditional Ugandan dishes.',
        'A kids menu with smaller portions and lower prices would help.',
        'Online ordering and delivery would be convenient.',
        'Please add nutritional information to the menu.',
        'More parking spaces would be appreciated.',
        'Having a dedicated events space for parties would be great.',
        'Please consider adding a breakfast buffet on Sundays.',
        'More dessert options including sugar-free choices.',
        'A mobile app for reservations would be convenient.',
        'Please add gluten-free options to the menu.',
        'Consider having themed nights like live music or cultural nights.',
    ],
    'compliment' => [
        'The food was absolutely delicious! Best meal I have had in weeks.',
        'Excellent service from the staff, very attentive and friendly.',
        'The ambiance is lovely, perfect for a romantic dinner.',
        'Great value for money, generous portions and fair prices.',
        'The new menu items are fantastic, keep up the good work.',
        'Our waiter was exceptional, very knowledgeable about the menu.',
        'The restaurant is very clean and well-maintained.',
        'Beautiful presentation of the food, almost too pretty to eat.',
        'The live band on Friday night was amazing.',
        'Best coffee in town, consistently good every time.',
        'The management responded quickly to my previous complaint.',
        'Perfect venue for our family gathering, thank you.',
        'The chef came out to check on us, such a nice touch.',
        'Fastest service I have experienced here, well done team.',
        'The new decor is beautiful, the renovation looks great.',
    ],
];

function randomName(array $first, array $last): string
{
    return $first[array_rand($first)] . ' ' . $last[array_rand($last)];
}

function randomEmail(string $name): string
{
    $clean = strtolower(str_replace(' ', '.', $name));
    $domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'cafejavas.ug', 'example.ug'];
    return $clean . '@' . $domains[array_rand($domains)];
}

function randomPhone(): string
{
    $prefixes = ['+256700', '+256701', '+256702', '+256703', '+256704', '+256705', '+256770', '+256771', '+256772', '+256773', '+256774', '+256775', '+256780', '+256781', '+256782'];
    return $prefixes[array_rand($prefixes)] . str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
}

function randomDate(int $monthsBack): string
{
    $daysBack = random_int(0, $monthsBack * 30);
    return date('Y-m-d H:i:s', strtotime("-{$daysBack} days " . random_int(6, 20) . " hours " . random_int(0, 59) . " minutes"));
}

function statusByAge(string $createdAt): array
{
    $created = strtotime($createdAt);
    $daysOld = (time() - $created) / 86400;

    $roll = random_int(1, 100);

    if ($daysOld < 2) {
        if ($roll <= 40) return ['in-progress', 'medium'];
        if ($roll <= 70) return ['pending', 'medium'];
        return ['resolved', 'low'];
    }

    if ($daysOld < 14) {
        if ($roll <= 45) return ['resolved', 'low'];
        if ($roll <= 75) return ['in-progress', 'medium'];
        if ($roll <= 90) return ['pending', 'medium'];
        return ['escalated', 'high'];
    }

    if ($roll <= 70) return ['resolved', 'low'];
    if ($roll <= 85) return ['escalated', 'high'];
    if ($roll <= 95) return ['in-progress', 'medium'];
    return ['pending', 'medium'];
}

$responses = [
    'Thank you for your feedback! We have addressed this issue with our team.',
    'We appreciate your suggestion and will consider it in our next menu review.',
    'Thank you for the kind words! We are glad you enjoyed your visit.',
    'We are sorry about your experience. Our manager will follow up with you.',
    'We have noted your concern and are working on improving this area.',
    'Thank you for bringing this to our attention. We have taken corrective action.',
    'Your feedback has been shared with the team. We value your input.',
    'We are delighted you had a great experience. Looking forward to serving you again!',
    'This has been escalated to management for immediate action.',
    'Thank you for your suggestion. We are always looking to improve our service.',
];

$now = time();
$records = [];
$ticketStart = (int) $db->query('SELECT COALESCE(MAX(ticket_number), 3) + 1 FROM feedback')->fetchColumn();

$totalRecords = 1000;

echo "Generating $totalRecords feedback records spanning 7 months...\n";

$stmtInsert = $db->prepare(
    'INSERT INTO feedback (ticket_number, name, email, phone, category, type, rating, message, status, priority, assigned_to, response, responded_at, resolved_at, created_at, updated_at)
     VALUES (:tn, :name, :email, :phone, :cat, :type, :rating, :msg, :status, :priority, :assigned, :response, :responded, :resolved, :created, :updated)'
);

$db->beginTransaction();

$assignedTo = ['admin@cafejavas.test', 'staff@cafejavas.test', null];
$existingContactNames = [];

for ($i = 0; $i < $totalRecords; $i++) {
    $name = randomName($firstNames, $lastNames);
    $email = randomEmail($name);
    $phone = randomPhone();
    $category = $categories[array_rand($categories)];
    $type = $types[array_rand($types)];
    $rating = $type === 'complaint' ? random_int(1, 3) : ($type === 'compliment' ? random_int(4, 5) : random_int(2, 5));
    $message = $messages[$type][array_rand($messages[$type])];
    $createdAt = randomDate(7);
    $updatedAt = $createdAt;
    [$status, $priority] = statusByAge($createdAt);

    $response = null;
    $respondedAt = null;
    $resolvedAt = null;

    if ($status === 'resolved' || $status === 'in-progress') {
        $response = $responses[array_rand($responses)];
        $respondedAt = date('Y-m-d H:i:s', strtotime($createdAt . ' + ' . random_int(1, 48) . ' hours'));
    }
    if ($status === 'resolved') {
        $resolvedAt = date('Y-m-d H:i:s', strtotime($respondedAt . ' + ' . random_int(1, 24) . ' hours'));
    }

    $assignee = $assignedTo[array_rand($assignedTo)];

    $stmtInsert->execute([
        'tn' => $ticketStart + $i,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'cat' => $category,
        'type' => $type,
        'rating' => $rating,
        'msg' => $message,
        'status' => $status,
        'priority' => $priority,
        'assigned' => $assignee,
        'response' => $response,
        'responded' => $respondedAt,
        'resolved' => $resolvedAt,
        'created' => $createdAt,
        'updated' => $updatedAt,
    ]);

    $existingContactNames[] = $name;

    if ($i % 100 === 99) {
        echo "  Inserted " . ($i + 1) . " records...\n";
    }
}

$db->exec("UPDATE counters SET seq = seq + $totalRecords WHERE name = 'feedback'");

echo "Inserting sample contacts...\n";
$contactNames = array_unique(array_slice($existingContactNames, 0, 20));
$contactStmt = $db->prepare(
    'INSERT IGNORE INTO contacts (name, phone, email, notes, is_active, created_at, updated_at) VALUES (:name, :phone, :email, :notes, 1, NOW(), NOW())'
);
foreach ($contactNames as $cn) {
    $contactStmt->execute([
        'name' => $cn,
        'phone' => randomPhone(),
        'email' => randomEmail($cn),
        'notes' => 'Bulk seed contact',
    ]);
}

$db->commit();

echo "\nDone! Inserted $totalRecords feedback records spanning the last 7 months.\n";
echo "Counter updated. Inserted " . count($contactNames) . " sample contacts.\n";
