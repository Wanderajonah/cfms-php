<div class="row g-4">
    <div class="col-md-6">
        <section class="panel">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div><h2 class="h5 mb-1">SMS Gateway</h2><p class="text-muted small mb-0">Comms/EgoSMS</p></div>
                <span class="badge <?= getenv('SMS_ENABLED') === 'true' ? 'bg-success' : 'bg-secondary' ?>"><?= getenv('SMS_ENABLED') === 'true' ? 'Active' : 'Disabled' ?></span>
            </div>
            <dl class="row small mb-0">
                <dt class="col-4 text-muted">Endpoint</dt>
                <dd class="col-8"><?= Security::e(getenv('SMS_ENDPOINT') ?: '—') ?></dd>
                <dt class="col-4 text-muted">Sender</dt>
                <dd class="col-8"><?= Security::e(getenv('SMS_SENDER_ID') ?: '—') ?></dd>
                <dt class="col-4 text-muted">Username</dt>
                <dd class="col-8"><?= Security::e(getenv('SMS_USERNAME') ?: '—') ?></dd>
            </dl>
        </section>
    </div>
    <div class="col-md-6">
        <section class="panel">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div><h2 class="h5 mb-1">AI Auto-Response</h2><p class="text-muted small mb-0">Groq Cloud</p></div>
                <span class="badge <?= getenv('GROQ_API_KEY') ? 'bg-success' : 'bg-secondary' ?>"><?= getenv('GROQ_API_KEY') ? 'Connected' : 'Not configured' ?></span>
            </div>
            <dl class="row small mb-0">
                <dt class="col-4 text-muted">Model</dt>
                <dd class="col-8"><?= Security::e(getenv('GROQ_MODEL') ?: '—') ?></dd>
                <dt class="col-4 text-muted">API Key</dt>
                <dd class="col-8"><?= getenv('GROQ_API_KEY') ? '••••' . substr(getenv('GROQ_API_KEY'), -4) : '—' ?></dd>
                <dt class="col-4 text-muted">Brand</dt>
                <dd class="col-8"><?= Security::e(getenv('AI_BRAND_NAME') ?: '—') ?></dd>
            </dl>
        </section>
    </div>
    <div class="col-md-12">
        <section class="panel">
            <div class="d-flex justify-content-between align-items-start">
                <div><h2 class="h5 mb-1">API Endpoints</h2><p class="text-muted small mb-0">RESTful JSON API</p></div>
                <span class="badge bg-success">Available</span>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-sm small mb-0">
                    <thead><tr><th>Method</th><th>Endpoint</th><th>Auth</th></tr></thead>
                    <tbody>
                        <tr><td><span class="badge bg-success">POST</span></td><td><code>/api/auth/login</code></td><td>None</td></tr>
                        <tr><td><span class="badge bg-info">GET</span></td><td><code>/api/feedback</code></td><td>Bearer token</td></tr>
                        <tr><td><span class="badge bg-info">GET</span></td><td><code>/api/feedback/{id}</code></td><td>Bearer token</td></tr>
                        <tr><td><span class="badge bg-warning">POST</span></td><td><code>/api/feedback</code></td><td>Bearer token</td></tr>
                        <tr><td><span class="badge bg-primary">PATCH</span></td><td><code>/api/feedback/{id}</code></td><td>Bearer token</td></tr>
                        <tr><td><span class="badge bg-danger">DELETE</span></td><td><code>/api/feedback/{id}</code></td><td>Bearer token</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
