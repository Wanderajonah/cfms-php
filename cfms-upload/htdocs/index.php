<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$router = new Router();

$router->get("/", function () { View::render("landing", ["title" => "Cafe Javas", "isHome" => true], "public"); });
$router->get("/our-menus", function () { View::render("our-menus", ["title" => "Our Menu - Cafe Javas"], "public"); });
$router->get("/about", function () { View::render("about", ["title" => "About Us - Cafe Javas"], "public"); });
$router->get("/order-online", [OrderController::class, "index"]);
$router->post("/order-online", [OrderController::class, "placeOrder"]);
$router->get("/order/confirmation/{id}", [OrderController::class, "confirmation"]);
$router->get("/feedback/submit", [FeedbackController::class, "publicForm"]);
$router->post("/feedback/submit", [FeedbackController::class, "publicCreate"]);
$router->get("/feedback/track", [FeedbackController::class, "track"]);

$router->get("/login", [AuthController::class, "showLogin"]);
$router->post("/login", [AuthController::class, "login"]);
$router->get("/register", [AuthController::class, "showRegister"]);
$router->post("/register", [AuthController::class, "register"]);
$router->post("/logout", [AuthController::class, "logout"]);
$router->get("/profile", [AuthController::class, "profile"]);
$router->post("/profile", [AuthController::class, "updateProfile"]);

$router->get("/dashboard", [DashboardController::class, "index"]);
$router->get("/inbox", [InboxController::class, "index"]);
$router->get("/complaints", [FeedbackController::class, "complaints"]);
$router->get("/analytics", [AnalyticsController::class, "index"]);
$router->get("/activity", [ActivityController::class, "index"]);
$router->get("/system/audit", [SystemController::class, "audit"]);
$router->get("/system/integrations", [SystemController::class, "integrations"]);
$router->get("/system/maintenance", [SystemController::class, "maintenance"]);
$router->post("/system/maintenance/optimize", [SystemController::class, "optimize"]);
$router->get("/feedback", [FeedbackController::class, "index"]);
$router->post("/feedback", [FeedbackController::class, "create"]);
$router->get("/feedback/{id}", [FeedbackController::class, "show"]);
$router->post("/feedback/{id}/assign", [FeedbackController::class, "assign"]);
$router->post("/feedback/{id}/respond", [FeedbackController::class, "respond"]);
$router->post("/feedback/{id}/status", [FeedbackController::class, "status"]);
$router->post("/feedback/{id}/delete", [FeedbackController::class, "delete"]);

$router->get("/users", [UserController::class, "index"]);
$router->post("/users", [UserController::class, "store"]);
$router->post("/users/{id}", [UserController::class, "update"]);
$router->post("/users/{id}/delete", [UserController::class, "delete"]);

$router->get("/contacts", [ContactController::class, "index"]);
$router->post("/contacts", [ContactController::class, "store"]);
$router->post("/contacts/{id}/delete", [ContactController::class, "delete"]);

$router->get("/reports", [ReportController::class, "index"]);
$router->post("/reports/export/csv", [ReportController::class, "exportCsv"]);
$router->post("/reports/export/pdf", [ReportController::class, "exportPdf"]);
$router->get("/settings", [SettingController::class, "index"]);
$router->post("/settings", [SettingController::class, "save"]);

$router->post("/api/auth/login", [AuthController::class, "apiLogin"]);
$router->post("/api/auth/register", [AuthController::class, "apiRegister"]);
$router->get("/api/auth/me", [AuthController::class, "apiMe"]);
$router->get("/api/auth/users", [AuthController::class, "apiUsers"]);
$router->patch("/api/auth/profile", [AuthController::class, "apiProfile"]);

$router->get("/api/feedback/summary", [FeedbackController::class, "apiSummary"]);
$router->get("/api/feedback", [FeedbackController::class, "apiList"]);
$router->get("/api/feedback/{id}", [FeedbackController::class, "apiGet"]);
$router->post("/api/feedback", [FeedbackController::class, "apiCreate"]);
$router->post("/api/feedback/{id}/assign", [FeedbackController::class, "apiAssign"]);
$router->post("/api/feedback/{id}/respond", [FeedbackController::class, "apiRespond"]);
$router->post("/api/feedback/{id}/resolve", [FeedbackController::class, "apiResolve"]);
$router->post("/api/feedback/{id}/escalate", [FeedbackController::class, "apiEscalate"]);
$router->patch("/api/feedback/{id}", [FeedbackController::class, "apiUpdate"]);
$router->delete("/api/feedback/{id}", [FeedbackController::class, "apiDelete"]);

$router->get("/api/contacts", [ContactController::class, "apiList"]);
$router->post("/api/contacts", [ContactController::class, "apiCreate"]);
$router->delete("/api/contacts/{id}", [ContactController::class, "apiDelete"]);
$router->get("/api/notifications/admin", [NotificationController::class, "apiAdmin"]);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
