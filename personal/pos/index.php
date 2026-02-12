<?php
$password = "2733"; // Access Code
session_start();
if (isset($_POST['p']) && $_POST['p'] == $password) { $_SESSION['logged_in'] = true; }
if (isset($_GET['out'])) { session_destroy(); header("Location: index.php"); exit; }
if (!isset($_SESSION['logged_in'])): ?>
<!DOCTYPE html><body style="background:#0f172a;display:flex;justify-content:center;align-items:center;height:100vh;font-family:sans-serif">
<form method="post" style="background:white;padding:40px;border-radius:10px;text-align:center;width:350px;box-shadow:0 10px 25px rgba(0,0,0,0.1)">
<h2 style="margin:0 0 20px;color:#0f172a;font-weight:800">ARHAM<span style="color:#06b6d4">ERP</span></h2>
<input type="password" name="p" placeholder="Enter PIN" style="width:100%;padding:12px;margin-bottom:15px;border:1px solid #ddd;border-radius:6px;outline:none" required autofocus>
<button style="width:100%;padding:12px;background:#0f172a;border:none;color:white;font-weight:bold;border-radius:6px;cursor:pointer;transition:0.3s">ACCESS SYSTEM</button>
</form></body></html><?php exit; endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arham ERP - Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --blue: #0f172a; --cyan: #06b6d4; --light: #f1f5f9; --border: #e2e8f0; }
        body { background: var(--light); font-family: 'Inter', sans-serif; display: flex; overflow-x: hidden; font-size: 14px; }
        
        /* SIDEBAR */
        #sidebar { width: 260px; min-width: 260px; background: var(--blue); color: #fff; min-height: 100vh; transition: 0.3s; z-index: 1000; }
        #sidebar .brand { padding: 25px; font-size: 1.5rem; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.05); }
        #sidebar ul { list-style: none; padding: 15px 10px; margin: 0; }
        #sidebar a { display: flex; align-items: center; padding: 12px 15px; color: #94a3b8; text-decoration: none; font-weight: 500; border-radius: 8px; margin-bottom: 5px; transition: 0.2s; }
        #sidebar a:hover, #sidebar a.active { background: var(--cyan); color: #fff; }
        #sidebar i { width: 25px; font-size: 1.1rem; }
        .sb-label { font-size: 0.75rem; text-transform: uppercase; color: #64748b; padding: 20px 20px 10px; font-weight: 700; }

        /* CONTENT */
        #content { flex-grow: 1; padding: 0; display:flex; flex-direction:column; max-width: 100%; }
        .topbar { background: #fff; padding: 15px 30px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .main-p { padding: 30px; overflow-y: auto; height: calc(100vh - 70px); }
        .module { display: none; }
        .module.active { display: block; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        /* CARDS */
        .card-custom { background: #fff; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow: hidden; }
        .card-head { background: #f8fafc; padding: 15px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; font-weight: 700; }
        .card-body { padding: 20px; }

        .stat-card { padding: 20px; background: white; border-radius: 10px; border: 1px solid var(--border); }
        .stat-val { font-size: 24px; font-weight: 800; color: var(--blue); }
        .stat-label { font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: 700; }
    </style>
</head>
<body>

    <nav id="sidebar">
        <div class="brand">ARHAM <span style="color:var(--cyan)">ERP</span></div>
        <div class="sb-label">Sales & Finance</div>
        <ul>
            <li><a href="#" onclick="nav('dashboard')" class="active"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="#" onclick="nav('builder')"><i class="fas fa-file-invoice"></i> Sale Invoice</a></li>
            <li><a href="#" onclick="nav('invoices')"><i class="fas fa-list"></i> Sales History</a></li>
            <li><a href="#" onclick="nav('pos')"><i class="fas fa-cash-register"></i> Point of Sale</a></li>
        </ul>
        <div class="sb-label">Stock & Inventory</div>
        <ul>
            <li><a href="#" onclick="nav('purchases')"><i class="fas fa-truck-loading"></i> Purchase Invoice</a></li>
            <li><a href="#" onclick="nav('inventory')"><i class="fas fa-boxes"></i> Inventory / Items</a></li>
            <li><a href="#" onclick="nav('jobs')"><i class="fas fa-print"></i> Printing Jobs</a></li>
            <li><a href="#" onclick="nav('people')"><i class="fas fa-users"></i> Contacts</a></li>
        </ul>
        <div class="mt-auto p-3"><a href="?out=true" class="bg-dark justify-content-center"><i class="fas fa-power-off me-2"></i> Logout</a></div>
    </nav>

    <div id="content">
        <div class="topbar">
            <h5 class="m-0 fw-bold" id="pageTitle">DASHBOARD</h5>
            <div>
                <span class="badge bg-light text-dark border me-2" id="dateDisplay"></span>
                <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="nav('builder')">+ New Sale</button>
            </div>
        </div>

        <div class="main-p">
            
            <div id="dashboard" class="module active">
                <div class="row g-3 mb-4">
                    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Total Revenue</div><div class="stat-val" id="d_revenue">0</div></div></div>
                    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Pending Receivables</div><div class="stat-val text-danger" id="d_due">0</div></div></div>
                    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Active Jobs</div><div class="stat-val text-warning" id="d_jobs">0</div></div></div>
                    <div class="col-md-3"><div class="stat-card"><div class="stat-label">Low Stock Items</div><div class="stat-val text-info" id="d_stock">0</div></div></div>
                </div>
                <div class="card-custom">
                    <div class="card-head">Recent Sales</div>
                    <table class="table table-hover m-0">
                        <thead class="table-light"><tr><th>ID</th><th>Date</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead>
                        <tbody id="dashTable"></tbody>
                    </table>
                </div>
            </div>

            <div id="builder" class="module">
                <div class="card-custom">
                    <div class="card-head">
                        <span><i class="fas fa-file-invoice me-2"></i> New Sale Invoice</span>
                        <button class="btn btn-sm btn-primary" onclick="saveInvoice('sale')">Save Invoice</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="fw-bold small">Customer</label>
                                <div class="input-group">
                                    <select id="bCust" class="form-select"></select>
                                    <button class="btn btn-outline-secondary" onclick="promptAddPerson('cust')">+</button>
                                </div>
                            </div>
                            <div class="col-md-2"><label class="fw-bold small">Inv No</label><input type="text" id="bNo" class="form-control bg-light" readonly></div>
                            <div class="col-md-3"><label class="fw-bold small">Date</label><input type="date" id="bDate" class="form-control"></div>
                            <div class="col-md-3"><label class="fw-bold small">Due Date</label><input type="date" id="bDue" class="form-control"></div>
                        </div>
                        <table class="table table-bordered align-middle">
                            <thead class="table-light"><tr><th width="40%">Item</th><th width="15%">Qty</th><th width="15%">Rate</th><th width="15%">Total</th><th width="5%"></th></tr></thead>
                            <tbody id="bRows"></tbody>
                        </table>
                        <button class="btn btn-light border w-100 mb-3 fw-bold text-primary" onclick="addRow('bRows')">+ Add Item</button>
                        <div class="row">
                            <div class="col-md-6"><textarea id="bNotes" class="form-control" rows="3" placeholder="Terms / Notes..."></textarea></div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between mb-2 fw-bold h5"><span>Total:</span><span id="bTotal">0.00</span></div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Paid:</span><input type="number" id="bPaid" class="form-control form-control-sm w-50 text-end" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="invoices" class="module">
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <h4 class="fw-bold">Sales History</h4>
                    <input type="text" id="invSearch" class="form-control w-25 rounded-pill" placeholder="Search..." onkeyup="renderInvList()">
                </div>
                <div class="card-custom">
                    <table class="table table-hover m-0 align-middle">
                        <thead class="table-light"><tr><th>ID</th><th>Date</th><th>Customer</th><th>Total</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                        <tbody id="invList"></tbody>
                    </table>
                </div>
            </div>

            <div id="purchases" class="module">
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#p-new">New Purchase Bill</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#p-hist">Purchase History</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="p-new">
                        <div class="card-custom">
                            <div class="card-head bg-warning bg-opacity-10 text-dark">
                                <span><i class="fas fa-truck me-2"></i> Receive Stock / Bill</span>
                                <button class="btn btn-sm btn-dark" onclick="saveInvoice('purchase')">Save Bill</button>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="fw-bold small">Supplier</label>
                                        <div class="input-group">
                                            <select id="pSupp" class="form-select"></select>
                                            <button class="btn btn-outline-secondary" onclick="promptAddPerson('supp')">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-2"><label class="fw-bold small">Ref #</label><input type="text" id="pRef" class="form-control" placeholder="Supp Inv #"></div>
                                    <div class="col-md-3"><label class="fw-bold small">Date</label><input type="date" id="pDate" class="form-control"></div>
                                </div>
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light"><tr><th width="40%">Item</th><th width="15%">Qty</th><th width="15%">Cost</th><th width="15%">Total</th><th width="5%"></th></tr></thead>
                                    <tbody id="pRows"></tbody>
                                </table>
                                <button class="btn btn-light border w-100 mb-3 fw-bold text-dark" onclick="addRow('pRows')">+ Add Item</button>
                                <div class="d-flex justify-content-end">
                                    <h4 class="fw-bold">Total: <span id="pTotal">0.00</span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="p-hist">
                        <div class="card-custom">
                            <table class="table table-hover m-0">
                                <thead class="table-light"><tr><th>Date</th><th>Supplier</th><th>Items</th><th>Total Cost</th></tr></thead>
                                <tbody id="purList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="pos" class="module">
                <div class="row h-100">
                    <div class="col-md-7">
                        <input type="text" id="posSearch" class="form-control mb-3" placeholder="Search Item..." onkeyup="renderPosGrid()">
                        <div class="d-flex flex-wrap gap-2" id="posGrid"></div>
                    </div>
                    <div class="col-md-5">
                        <div class="card-custom h-100 d-flex flex-column">
                            <div class="card-head bg-dark text-white">Current Sale <span class="badge bg-danger" id="posCount">0</span></div>
                            <div class="card-body flex-grow-1 p-0" style="overflow-y:auto;max-height:50vh" id="posCart"></div>
                            <div class="p-3 bg-light border-top">
                                <select id="posCust" class="form-select mb-2"></select>
                                <h3 class="fw-bold text-end mb-3" id="posTotal">0</h3>
                                <button class="btn btn-primary w-100 fw-bold py-2" onclick="processPOS()">CHECKOUT</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="inventory" class="module">
                <div class="d-flex justify-content-between mb-3"><h4>Inventory</h4> <button class="btn btn-primary btn-sm" onclick="promptAddItem()">+ New Item</button></div>
                <div class="card-custom"><table class="table table-hover m-0"><thead class="table-light"><tr><th>Name</th><th>Stock</th><th>Cost</th><th>Price</th></tr></thead><tbody id="invTable"></tbody></table></div>
            </div>

            <div id="jobs" class="module">
                <div class="d-flex justify-content-between mb-3"><h4>Jobs</h4> <button class="btn btn-primary btn-sm" onclick="newJob()">+ New Job</button></div>
                <div class="row g-3" id="jobBoard"></div>
            </div>
            
            <div id="people" class="module">
                <div class="row">
                    <div class="col-md-6"><div class="card-custom"><div class="card-head">Customers</div><div class="card-body"><ul id="custList" class="list-group"></ul></div></div></div>
                    <div class="col-md-6"><div class="card-custom"><div class="card-head">Suppliers</div><div class="card-body"><ul id="suppList" class="list-group"></ul></div></div></div>
                </div>
            </div>

        </div>
    </div>

    <datalist id="itemList"></datalist>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // CORE DB
        const db = { items:[], invoices:[], customers:[], suppliers:[], jobs:[], purchases:[] };
        let posCart = [];

        async function init() {
            const types = ['items','invoices','customers','suppliers','jobs','purchases'];
            for(let t of types) {
                try { let r = await fetch(`api.php?action=${t}`); db[t] = await r.json(); } catch(e){ db[t]=[]; }
            }
            if(!db.customers.length) db.customers.push({id:1, name:'Walk-in', phone:'0000'});
            
            renderDashboard();
            renderInvList();
            setupBuilders();
            document.getElementById('dateDisplay').innerText = new Date().toDateString();
            
            // Populate Datalist
            document.getElementById('itemList').innerHTML = db.items.map(i => `<option value="${i.name}">${i.stock} in stock</option>`).join('');
        }
        init();

        function nav(id) {
            document.querySelectorAll('.module').forEach(e => e.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            document.getElementById('pageTitle').innerText = id.toUpperCase();
            if(id==='pos') renderPos();
            if(id==='inventory') renderInventory();
            if(id==='jobs') renderJobs();
            if(id==='people') renderPeople();
            if(id==='purchases') renderPurchases();
        }

        // --- BUILDERS (Sale & Purchase) ---
        function setupBuilders() {
            document.getElementById('bCust').innerHTML = db.customers.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            document.getElementById('pSupp').innerHTML = db.suppliers.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            
            document.getElementById('bDate').valueAsDate = new Date();
            document.getElementById('pDate').valueAsDate = new Date();
            document.getElementById('bNo').value = "INV-" + Date.now().toString().slice(-6);
            
            addRow('bRows');
            addRow('pRows');
        }

        function addRow(tableId) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" class="form-control i-name" list="itemList" onchange="fillPrice(this, '${tableId}')" placeholder="Item"></td>
                <td><input type="number" class="form-control i-qty" value="1" oninput="calcTotal('${tableId}')"></td>
                <td><input type="number" class="form-control i-rate" value="0" oninput="calcTotal('${tableId}')"></td>
                <td class="fw-bold text-end i-total">0.00</td>
                <td><i class="fas fa-times text-danger" onclick="this.closest('tr').remove();calcTotal('${tableId}')"></i></td>
            `;
            document.getElementById(tableId).appendChild(tr);
        }

        function fillPrice(inp, tableId) {
            const item = db.items.find(i => i.name === inp.value);
            if(item) {
                const row = inp.closest('tr');
                row.querySelector('.i-rate').value = tableId === 'bRows' ? item.price : item.cost;
                calcTotal(tableId);
            }
        }

        function calcTotal(tableId) {
            let total = 0;
            document.querySelectorAll(`#${tableId} tr`).forEach(tr => {
                const q = parseFloat(tr.querySelector('.i-qty').value) || 0;
                const r = parseFloat(tr.querySelector('.i-rate').value) || 0;
                const t = q * r;
                tr.querySelector('.i-total').innerText = t.toFixed(2);
                total += t;
            });
            const outId = tableId === 'bRows' ? 'bTotal' : 'pTotal';
            document.getElementById(outId).innerText = total.toFixed(2);
        }

        async function saveInvoice(type) {
            if(type === 'sale') {
                const cust = db.customers.find(c => c.id == document.getElementById('bCust').value);
                const inv = {
                    id: document.getElementById('bNo').value,
                    date: document.getElementById('bDate').value,
                    due: document.getElementById('bDue').value,
                    customer: cust,
                    items: scrapeRows('bRows'),
                    sub: parseFloat(document.getElementById('bTotal').innerText),
                    taxTotal: 0, discount: 0, 
                    paid: parseFloat(document.getElementById('bPaid').value) || 0,
                    notes: document.getElementById('bNotes').value
                };
                inv.total = inv.sub;
                inv.balance = inv.total - inv.paid;
                inv.status = inv.balance <= 0 ? 'Paid' : 'Unpaid';
                
                db.invoices.unshift(inv);
                await saveData('invoices');
                
                // Deduct Stock
                inv.items.forEach(i => {
                    const item = db.items.find(x => x.name === i.name);
                    if(item) item.stock -= i.qty;
                });
                await saveData('items');
                
                alert("Sale Saved!");
                openShareLink(inv.id);
                nav('dashboard'); renderDashboard();

            } else {
                // Purchase
                const supp = db.suppliers.find(s => s.id == document.getElementById('pSupp').value);
                const bill = {
                    id: document.getElementById('pRef').value || "PUR-"+Date.now(),
                    date: document.getElementById('pDate').value,
                    supplier: supp,
                    items: scrapeRows('pRows'),
                    total: parseFloat(document.getElementById('pTotal').innerText)
                };
                
                db.purchases.unshift(bill);
                await saveData('purchases');
                
                // Add Stock & Update Cost
                bill.items.forEach(line => {
                    let item = db.items.find(x => x.name === line.name);
                    if(item) {
                        item.stock = parseInt(item.stock) + parseInt(line.qty);
                        item.cost = line.rate; // Update latest cost
                    } else {
                        // Create new if not exists
                        db.items.push({id: Date.now(), name: line.name, stock: line.qty, cost: line.rate, price: 0});
                    }
                });
                await saveData('items');
                alert("Purchase Recorded & Stock Updated");
                nav('purchases'); renderPurchases();
            }
        }

        function scrapeRows(tableId) {
            const items = [];
            document.querySelectorAll(`#${tableId} tr`).forEach(tr => {
                items.push({
                    name: tr.querySelector('.i-name').value,
                    qty: parseFloat(tr.querySelector('.i-qty').value),
                    rate: parseFloat(tr.querySelector('.i-rate').value),
                    total: parseFloat(tr.querySelector('.i-total').innerText)
                });
            });
            return items;
        }

        // --- DASHBOARD & LISTS ---
        function renderDashboard() {
            const rev = db.invoices.reduce((a,b)=>a+(b.paid||0),0);
            const due = db.invoices.reduce((a,b)=>a+(b.balance||0),0);
            document.getElementById('d_revenue').innerText = rev.toLocaleString();
            document.getElementById('d_due').innerText = due.toLocaleString();
            document.getElementById('d_jobs').innerText = db.jobs.filter(j=>j.status!=='Ready').length;
            document.getElementById('d_stock').innerText = db.items.filter(i=>i.stock<10).length;

            document.getElementById('dashTable').innerHTML = db.invoices.slice(0,5).map(i=>`
                <tr><td>${i.id}</td><td>${i.date}</td><td>${i.customer.name}</td><td>${i.total}</td><td>${getStatusBadge(i.status)}</td></tr>
            `).join('');
        }

        function renderInvList() {
            const q = document.getElementById('invSearch').value.toLowerCase();
            document.getElementById('invList').innerHTML = db.invoices.filter(i=> JSON.stringify(i).toLowerCase().includes(q)).map(i => `
                <tr>
                    <td><a href="#" onclick="openShareLink('${i.id}')" class="fw-bold text-decoration-none">${i.id}</a></td>
                    <td>${i.date}</td>
                    <td>${i.customer.name}</td>
                    <td>${i.total.toLocaleString()}</td>
                    <td>${getStatusBadge(i.status)}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-success" onclick="openShareLink('${i.id}')"><i class="fas fa-share-alt"></i></button>
                    </td>
                </tr>
            `).join('');
        }

        function renderPurchases() {
            document.getElementById('purList').innerHTML = db.purchases.map(p => `
                <tr>
                    <td>${p.date}</td>
                    <td>${p.supplier ? p.supplier.name : '-'}</td>
                    <td>${p.items.map(x=>x.name).join(', ')}</td>
                    <td class="fw-bold">${p.total.toLocaleString()}</td>
                </tr>
            `).join('');
        }

        function openShareLink(id) {
            const url = window.location.href.replace('index.php', '') + `invoice.php?id=${id}`;
            // Copy to clipboard
            navigator.clipboard.writeText(url).then(() => alert("Invoice Link Copied!\nOpening Preview..."));
            window.open(url, '_blank');
        }

        function getStatusBadge(s) {
            return `<span class="badge bg-${s==='Paid'?'success':'danger'}">${s}</span>`;
        }

        // --- POS ---
        function renderPos() {
            document.getElementById('posGrid').innerHTML = db.items.map(i => `
                <div class="card p-2" style="width:120px;cursor:pointer" onclick="addToPos(${i.id})">
                    <div class="fw-bold text-truncate">${i.name}</div>
                    <div class="text-primary small">Rs ${i.price}</div>
                </div>
            `).join('');
            if(!document.getElementById('posCust').options.length) 
                document.getElementById('posCust').innerHTML = db.customers.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
        }
        function addToPos(id) {
            const i = db.items.find(x=>x.id==id);
            const ex = posCart.find(x=>x.id==id);
            if(ex) ex.qty++; else posCart.push({...i, qty:1});
            updatePos();
        }
        function updatePos() {
            let t = 0;
            document.getElementById('posCart').innerHTML = posCart.map((c,idx) => {
                t += c.price*c.qty;
                return `<div class="d-flex justify-content-between p-2 border-bottom">
                    <div>${c.name} <small>x${c.qty}</small></div>
                    <div>${c.price*c.qty} <i class="fas fa-times text-danger ms-2" onclick="posCart.splice(${idx},1);updatePos()"></i></div>
                </div>`;
            }).join('');
            document.getElementById('posTotal').innerText = t;
            document.getElementById('posCount').innerText = posCart.length;
        }
        async function processPOS() {
            if(!posCart.length) return;
            const cust = db.customers.find(c=>c.id==document.getElementById('posCust').value);
            const t = parseFloat(document.getElementById('posTotal').innerText);
            const inv = {
                id: "POS-"+Date.now(), date: new Date().toISOString().split('T')[0],
                customer: cust, items: posCart.map(c=>({name:c.name,qty:c.qty,rate:c.price,total:c.price*c.qty})),
                sub: t, taxTotal: 0, discount: 0, total: t, paid: t, balance: 0, status: 'Paid', notes: 'POS Sale'
            };
            db.invoices.unshift(inv);
            posCart.forEach(c => { const i=db.items.find(x=>x.id==c.id); if(i) i.stock-=c.qty; });
            await saveData('invoices'); await saveData('items');
            posCart=[]; updatePos(); alert("POS Sale Complete"); openShareLink(inv.id);
        }

        // --- COMMON UTILS ---
        async function saveData(type) { await fetch(`api.php?action=${type}`, { method:'POST', body:JSON.stringify(db[type]) }); }
        function promptAddPerson(type) {
            const n = prompt("Name"); const p = prompt("Phone/Contact");
            if(n) { db[type=='cust'?'customers':'suppliers'].push({id:Date.now(),name:n,phone:p}); saveData(type=='cust'?'customers':'suppliers'); alert("Added"); }
        }
        function renderInventory() { document.getElementById('invTable').innerHTML = db.items.map(i=>`<tr><td>${i.name}</td><td>${i.stock}</td><td>${i.cost}</td><td>${i.price}</td></tr>`).join(''); }
        function renderJobs() { document.getElementById('jobBoard').innerHTML = db.jobs.map(j=>`<div class="col-md-4"><div class="card p-3 border-start border-5 border-info"><h5>${j.client}</h5><p>${j.desc}</p><span class="badge bg-secondary">${j.status}</span></div></div>`).join(''); }
        async function newJob() { const c=prompt("Client"); const d=prompt("Desc"); if(c) { db.jobs.push({id:Date.now(),client:c,desc:d,status:'Pending'}); await saveData('jobs'); renderJobs(); } }
        async function promptAddItem() { const n=prompt("Name"); const s=prompt("Stock"); const p=prompt("Price"); if(n) { db.items.push({id:Date.now(),name:n,stock:s,cost:0,price:p}); await saveData('items'); renderInventory(); } }
    </script>
</body>
</html>