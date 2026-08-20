<?php $t = $summary['totals'] ?>
<div class="stats-grid">
    <?php foreach ([
        ['My Tasks', $t['total'], 'primary', 'bi-chat-square-text'],
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
    <div class="col-xl-3"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-pie-chart me-2"></i>Category</h2><canvas id="sCategory" height="180"></canvas></section></div>
    <div class="col-xl-3"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-tags me-2"></i>Type</h2><canvas id="sType" height="180"></canvas></section></div>
    <div class="col-xl-3"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-flag me-2"></i>Priority</h2><canvas id="sPriority" height="180"></canvas></section></div>
    <div class="col-xl-3"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-pie-chart me-2"></i>Status</h2><canvas id="sStatus" height="180"></canvas></section></div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-6"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-graph-up me-2"></i>Monthly Trend (My Assignments)</h2><canvas id="sMonthly" height="110"></canvas></section></div>
    <div class="col-xl-3">
        <section class="panel"><h2 class="h5 mb-3"><i class="bi bi-clock me-2"></i>My Performance</h2>
            <div class="d-flex gap-2">
                <div class="text-center flex-1"><div style="font-size:22px;font-weight:700;color:var(--brand)"><?= $summary['avgResponseHours'] ?? '—' ?></div><div class="text-muted small">avg hrs</div></div>
                <div class="text-center flex-1"><div style="font-size:22px;font-weight:700;color:#d97706"><?= $summary['avgRating'] ?? '—' ?></div><div class="text-muted small">rating</div></div>
                <div class="text-center flex-1"><div style="font-size:22px;font-weight:700;color:#16a34a"><?= $summary['responseRate'] ?>%</div><div class="text-muted small">resp.</div></div>
            </div>
        </section>
    </div>
    <div class="col-xl-3"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-clock-history me-2"></i>By Hour</h2><canvas id="sHour" height="140"></canvas></section></div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-6"><section class="panel"><h2 class="h5 mb-3"><i class="bi bi-calendar-week me-2"></i>By Day of Week</h2><canvas id="sWeekday" height="120"></canvas></section></div>
    <div class="col-xl-6">
        <section class="panel"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0"><i class="bi bi-list-check me-2"></i>My Recent Assignments</h2><a href="/feedback" class="btn btn-sm btn-outline-primary">View all</a></div>
            <?php if ($recent): ?>
                <div class="table-responsive"><table class="table table-sm small mb-0"><thead><tr><th>#</th><th>Customer</th><th>Category</th><th>Type</th><th>Status</th><th>Priority</th><th>Date</th><th></th></tr></thead><tbody>
                    <?php foreach ($recent as $item): ?><tr><td>#<?= $item['ticketNumber'] ?></td><td><?= Security::e($item['name'] ?: 'Anonymous') ?></td><td><?= Security::e($item['category']) ?></td><td><?= Security::e($item['type']) ?></td><td><span class="badge status-<?= $item['status'] ?>"><?= $item['status'] ?></span></td><td><span class="badge priority-<?= $item['priority'] ?>"><?= $item['priority'] ?></span></td><td><?= date('Y-m-d', strtotime($item['created_at'])) ?></td><td><a class="btn btn-sm btn-outline-primary" href="/feedback/<?= $item['id'] ?>"><i class="bi bi-eye"></i></a></td></tr><?php endforeach; ?>
                </tbody></table></div>
            <?php else: ?><p class="text-muted mb-0">No feedback assigned yet.</p><?php endif; ?>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded',function(){
if(typeof Chart==='undefined'){
  var el=document.getElementById('sStatus');
  if(el)el.insertAdjacentHTML('afterend','<div class="text-muted small py-3 text-center">Charts unavailable (Chart.js not loaded)</div>');
  return;
}
var c=function(id,cfg){var e=document.getElementById(id);if(!e)return;try{new Chart(e,cfg)}catch(x){}};
c('sStatus',{type:'doughnut',data:{labels:['Pending','In Progress','Resolved','Escalated'],datasets:[{data:[<?= $t['pending'] ?>,<?= $t['inProgress'] ?>,<?= $t['resolved'] ?>,<?= $t['escalated'] ?>],backgroundColor:['#d97706','#2563eb','#16a34a','#dc2626']}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}});
var dc=['#0f766e','#2563eb','#d97706','#dc2626','#7c3aed','#0891b2','#ca8a04','#64748b'];
var d1=<?= json_encode($categories) ?>;if(d1&&d1.length)c('sCategory',{type:'doughnut',data:{labels:d1.map(function(d){return d.name}),datasets:[{data:d1.map(function(d){return d.value}),backgroundColor:dc}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}});
var d2=<?= json_encode($types ?? []) ?>;if(d2&&d2.length)c('sType',{type:'doughnut',data:{labels:d2.map(function(d){return d.name}),datasets:[{data:d2.map(function(d){return d.value}),backgroundColor:['#16a34a','#2563eb','#dc2626']}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}});
var d3=<?= json_encode($priorities ?? []) ?>;if(d3&&d3.length)c('sPriority',{type:'doughnut',data:{labels:d3.map(function(d){return d.name}),datasets:[{data:d3.map(function(d){return d.value}),backgroundColor:['#dc2626','#d97706','#64748b']}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}}});
var m=<?= json_encode($monthly) ?>;if(m&&m.length)c('sMonthly',{type:'bar',data:{labels:m.map(function(d){return d.month}),datasets:[{label:'Total',data:m.map(function(d){return d.total}),backgroundColor:'rgba(15,118,110,.7)',borderRadius:4},{label:'Resolved',data:m.map(function(d){return d.resolved}),backgroundColor:'rgba(34,197,94,.7)',borderRadius:4}]},options:{responsive:true,plugins:{legend:{display:false}}}});
var wd=<?= json_encode($weekdays ?? []) ?>;if(wd&&wd.length)c('sWeekday',{type:'bar',data:{labels:wd.map(function(d){return d.day.substring(0,3)}),datasets:[{data:wd.map(function(d){return d.count}),backgroundColor:'rgba(15,118,110,.7)',borderRadius:4}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{stepSize:1}}}}});
var hr=<?= json_encode($hours ?? []) ?>;if(hr&&hr.length)c('sHour',{type:'bar',data:{labels:hr.map(function(d){return d.hour+':00'}),datasets:[{data:hr.map(function(d){return d.count}),backgroundColor:'rgba(37,99,235,.6)',borderRadius:2}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{stepSize:1}}}}});
});
</script>
