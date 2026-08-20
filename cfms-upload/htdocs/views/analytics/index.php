<?php $t = $summary['totals']; $s = $summary; $chartId = 'a'; ?>
<div class="stats-grid">
    <?php foreach ([
        ['Total', $t['total'], 'primary', 'bi-chat-square-text'],
        ['Pending', $t['pending'], 'warning', 'bi-hourglass'],
        ['In Progress', $t['inProgress'], 'info', 'bi-arrow-repeat'],
        ['Resolved', $t['resolved'], 'success', 'bi-check-circle'],
        ['Escalated', $t['escalated'], 'danger', 'bi-exclamation-triangle'],
    ] as $card): ?>
        <section class="stat-card">
            <div class="d-flex justify-content-between"><span class="text-<?= $card[2] ?>"><i class="<?= $card[3] ?> me-1"></i><?= $card[0] ?></span></div>
            <strong><?= $card[1] ?></strong>
        </section>
    <?php endforeach; ?>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-3"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-pie-chart me-2"></i>Status</h2><canvas id="aStatus" height="180"></canvas></section></div>
    <div class="col-xl-3"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-pie-chart me-2"></i>Category</h2><canvas id="aCategory" height="180"></canvas></section></div>
    <div class="col-xl-3"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-tags me-2"></i>Type</h2><canvas id="aType" height="180"></canvas></section></div>
    <div class="col-xl-3"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-flag me-2"></i>Priority</h2><canvas id="aPriority" height="180"></canvas></section></div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-6"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-graph-up me-2"></i>Monthly Trend</h2><canvas id="aMonthly" height="120"></canvas></section></div>
    <div class="col-xl-6"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-graph-up me-2"></i>Cumulative Growth</h2><canvas id="aCumulative" height="120"></canvas></section></div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-4">
        <section class="panel"><h2 class="h5 mb-3"><i class="bi bi-clock me-2"></i>Response &amp; Rating</h2>
            <div class="d-flex gap-3">
                <div class="text-center flex-1"><div style="font-size:28px;font-weight:700;color:var(--brand)"><?= $s['avgResponseHours'] ?? '—' ?></div><div class="text-muted small">avg hours</div></div>
                <div class="text-center flex-1"><div style="font-size:28px;font-weight:700;color:#d97706"><?= $s['avgRating'] ?? '—' ?></div><div class="text-muted small">avg rating</div></div>
                <div class="text-center flex-1"><div style="font-size:28px;font-weight:700;color:#16a34a"><?= $s['responseRate'] ?>%</div><div class="text-muted small">responded</div></div>
            </div>
            <div class="mt-3"><h2 class="h5 mb-2">Response Time Trend</h2><canvas id="aRespTrend" height="100"></canvas></div>
        </section>
    </div>
    <div class="col-xl-4">
        <section class="panel"><h2 class="h5 mb-3"><i class="bi bi-star me-2"></i>Rating Distribution</h2>
            <?php for ($r = 5; $r >= 1; $r--):
                $found = current(array_filter($s['ratingDistribution'] ?? [], fn($x) => $x['rating'] === $r));
                $cnt = $found ? $found['count'] : 0; $pct = $t['total'] > 0 ? round($cnt / $t['total'] * 100) : 0;
            ?><div class="d-flex align-items-center gap-2 mb-1">
                <span style="width:25px;font-size:12px;font-weight:600"><?= $r ?><i class="bi bi-star-fill ms-1" style="color:#f59e0b;font-size:9px"></i></span>
                <div class="progress flex-1" style="height:6px;background:#f1f5f9"><div class="progress-bar bg-warning" style="width:<?= $pct ?>%"></div></div>
                <span style="width:24px;font-size:11px;color:var(--muted);text-align:right"><?= $cnt ?></span>
            </div><?php endfor; ?>
        </section>
    </div>
    <div class="col-xl-4"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-graph-down me-2"></i>Daily Activity (30d)</h2><canvas id="aDaily" height="140"></canvas></section></div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-6"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-calendar-week me-2"></i>By Day of Week</h2><canvas id="aWeekday" height="140"></canvas></section></div>
    <div class="col-xl-6"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-clock-history me-2"></i>By Hour of Day</h2><canvas id="aHour" height="140"></canvas></section></div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-6">
        <section class="panel"><h2 class="h5 mb-3"><i class="bi bi-people me-2"></i>Top Customers</h2>
            <div class="table-responsive"><table class="table table-sm small mb-0"><thead><tr><th>Name</th><th>Feedbacks</th><th></th></tr></thead><tbody>
                <?php foreach ($s['topCustomers'] as $c): ?><tr><td><?= Security::e($c['name']) ?></td><td><span class="badge bg-secondary"><?= (int) $c['cnt'] ?></span></td><td><a href="/feedback?search=<?= urlencode($c['name']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td></tr><?php endforeach; ?>
            </tbody></table></div>
        </section>
    </div>
    <div class="col-xl-6">
        <section class="panel"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Escalated</h2><a href="/feedback?status=escalated" class="btn btn-sm btn-outline-danger">View all</a></div>
            <div class="table-responsive"><table class="table table-sm small mb-0"><thead><tr><th>#</th><th>Customer</th><th>Category</th><th>Date</th><th></th></tr></thead><tbody>
                <?php foreach ($s['escalatedList'] as $e): ?><tr><td>#<?= (int) $e['ticket_number'] ?></td><td><?= Security::e($e['name'] ?: 'Anonymous') ?></td><td><?= Security::e($e['category']) ?></td><td><?= date('Y-m-d', strtotime($e['created_at'])) ?></td><td><a href="/feedback/<?= (int) $e['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td></tr><?php endforeach; ?>
            </tbody></table></div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded',function(){
if(typeof Chart==='undefined' && document.getElementById('aStatus')){
  document.getElementById('aStatus').insertAdjacentHTML('afterend','<div class="text-muted small py-3 text-center">Chart library loading failed. <a href="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" target="_blank">Load manually</a></div>');
  return;
}
var c=function(id,cfg){var e=document.getElementById(id);if(!e)return;try{new Chart(e,cfg)}catch(x){e.insertAdjacentHTML('afterend','<div class="text-muted small py-2">Chart error: '+x.message+'</div>')}};
c('aStatus',{type:'doughnut',data:{labels:['Pending','In Progress','Resolved','Escalated'],datasets:[{data:[<?= $t['pending'] ?>,<?= $t['inProgress'] ?>,<?= $t['resolved'] ?>,<?= $t['escalated'] ?>],backgroundColor:['#d97706','#2563eb','#16a34a','#dc2626']}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}});
var doughnutColors=['#0f766e','#2563eb','#d97706','#dc2626','#7c3aed','#0891b2','#ca8a04','#64748b'];
var d1=<?= json_encode($s['categories']) ?>;if(d1&&d1.length)c('aCategory',{type:'doughnut',data:{labels:d1.map(function(d){return d.name}),datasets:[{data:d1.map(function(d){return d.value}),backgroundColor:doughnutColors}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}});
var d2=<?= json_encode($s['types'] ?? []) ?>;if(d2&&d2.length)c('aType',{type:'doughnut',data:{labels:d2.map(function(d){return d.name}),datasets:[{data:d2.map(function(d){return d.value}),backgroundColor:['#16a34a','#2563eb','#dc2626']}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}});
var d3=<?= json_encode($s['priorities'] ?? []) ?>;if(d3&&d3.length)c('aPriority',{type:'doughnut',data:{labels:d3.map(function(d){return d.name}),datasets:[{data:d3.map(function(d){return d.value}),backgroundColor:['#dc2626','#d97706','#64748b']}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}});
var m=<?= json_encode($s['monthly']) ?>;if(m&&m.length){c('aMonthly',{type:'bar',data:{labels:m.map(function(d){return d.month}),datasets:[{label:'Total',data:m.map(function(d){return d.total}),backgroundColor:'rgba(15,118,110,.7)',borderRadius:4},{label:'Resolved',data:m.map(function(d){return d.resolved}),backgroundColor:'rgba(34,197,94,.7)',borderRadius:4}]},options:{responsive:true,plugins:{legend:{display:false}}}})
c('aCumulative',{type:'line',data:{labels:m.map(function(d){return d.month}),datasets:[{label:'Cumulative',data:function(){var r=0;return m.map(function(d){r+=d.total;return r})}(),borderColor:'#0f766e',backgroundColor:'rgba(15,118,110,.15)',fill:true,tension:.3,pointRadius:3}]},options:{responsive:true,plugins:{legend:{display:false}}}});}
var rt=<?= json_encode($s['responseTrend'] ?? []) ?>;if(rt&&rt.length)c('aRespTrend',{type:'line',data:{labels:rt.map(function(d){return d.month}),datasets:[{label:'Hours',data:rt.map(function(d){return d.hours}),borderColor:'#0f766e',backgroundColor:'rgba(15,118,110,.1)',fill:true,tension:.3,pointRadius:3}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
var da=<?= json_encode($s['daily']) ?>;if(da&&da.length)c('aDaily',{type:'line',data:{labels:da.map(function(d){return d.date}),datasets:[{label:'Total',data:da.map(function(d){return d.total}),borderColor:'#0f766e',backgroundColor:'rgba(15,118,110,.1)',fill:true,tension:.3,pointRadius:2},{label:'Resolved',data:da.map(function(d){return d.resolved}),borderColor:'#16a34a',backgroundColor:'rgba(22,163,74,.1)',fill:true,tension:.3,pointRadius:2}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{x:{ticks:{font:{size:9}}}}}});
var wd=<?= json_encode($s['weekdays'] ?? []) ?>;if(wd&&wd.length)c('aWeekday',{type:'bar',data:{labels:wd.map(function(d){return d.day.substring(0,3)}),datasets:[{data:wd.map(function(d){return d.count}),backgroundColor:'rgba(15,118,110,.7)',borderRadius:4}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{stepSize:1}}}}});
var hr=<?= json_encode($s['hours'] ?? []) ?>;if(hr&&hr.length)c('aHour',{type:'bar',data:{labels:hr.map(function(d){return d.hour+':00'}),datasets:[{data:hr.map(function(d){return d.count}),backgroundColor:'rgba(37,99,235,.6)',borderRadius:2}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{stepSize:1}}}}});
});
</script>
