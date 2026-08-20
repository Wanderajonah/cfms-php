<div id="page_content_wrapper" class="sub-page">

<section class="page-header" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&h=500&fit=crop');">
    <div class="page-header-content">
        <h1>Order Online</h1>
        <p>Pick your favourites, tell us where to send them, and we will handle the rest</p>
    </div>
</section>

<section class="order-section">
    <div class="standard_wrapper">
        <div class="order-layout">
            <div class="order-menu">
                <?php foreach ($categories as $category => $items): ?>
                <div class="menu-category">
                    <h2 class="menu-category-title"><?= Security::e($category) ?></h2>
                    <div class="menu-items order-items">
                        <?php foreach ($items as $item): ?>
                        <div class="menu-item order-item" data-id="<?= (int) $item['id'] ?>" data-name="<?= Security::e($item['name']) ?>" data-price="<?= (int) $item['price'] ?>">
                            <div class="menu-item-image">
                                <img src="<?= Security::e($item['image_url'] ?: 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=100&h=100&fit=crop') ?>" alt="<?= Security::e($item['name']) ?>">
                            </div>
                            <div class="menu-item-info">
                                <div class="menu-item-header">
                                    <h3><?= Security::e($item['name']) ?></h3>
                                    <span class="menu-item-price">UGX <?= number_format((int) $item['price']) ?></span>
                                </div>
                                <p><?= Security::e($item['description']) ?></p>
                                <button type="button" class="button-primary order-add-btn">
                                    <i class="bi bi-plus-circle"></i> Add
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <aside class="order-cart" id="orderCart">
                <div class="order-cart-inner">
                    <h3 class="order-cart-title"><i class="bi bi-cart3"></i> Your Order</h3>
                    <p class="order-cart-subtitle">Review your items and check out below.</p>
                    <div class="cart-items" id="cartItems">
                        <p class="cart-empty">Your cart is empty. Add something delicious.</p>
                    </div>
                    <div class="cart-summary" id="cartSummary" style="display:none;">
                        <div class="cart-line"><span>Subtotal</span><span id="cartSubtotal">UGX 0</span></div>
                        <div class="cart-line" id="cartDeliveryRow"><span>Delivery fee</span><span id="cartDeliveryFee">UGX <?= number_format($deliveryFee) ?></span></div>
                        <div class="cart-line cart-total"><span>Total</span><span id="cartTotal">UGX 0</span></div>
                    </div>

                    <form method="post" action="/order-online" class="order-form" id="orderForm">
                        <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">

                        <div class="form-row">
                            <label>Delivery or Pickup <span class="required">*</span></label>
                            <div class="type-grid order-type-grid" id="orderTypeToggle">
                                <button type="button" class="type-btn active" data-type="delivery"><i class="bi bi-truck"></i> Delivery</button>
                                <button type="button" class="type-btn" data-type="pickup"><i class="bi bi-bag"></i> Pickup</button>
                            </div>
                            <input type="hidden" name="order_type" id="orderType" value="delivery">
                        </div>

                        <div class="form-divider"></div>

                        <div class="order-form-section">
                            <h4 class="form-section-label">Contact details</h4>
                            <div class="field">
                                <label for="name">Full name <span class="required">*</span></label>
                                <input type="text" name="name" id="name" class="form-input" required placeholder="John Doe">
                            </div>
                            <div class="field">
                                <label for="phone">Phone number <span class="required">*</span></label>
                                <input type="tel" name="phone" id="phone" class="form-input" required placeholder="+256 700 000 000">
                            </div>
                            <div class="field">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-input" placeholder="john@example.com">
                            </div>
                        </div>

                        <div class="form-divider"></div>

                        <div class="order-form-section">
                            <h4 class="form-section-label">Delivery details</h4>
                            <div class="field">
                                <label for="branch_id">Branch</label>
                                <select name="branch_id" id="branch_id" class="form-select">
                                    <option value="">Select a branch...</option>
                                    <?php foreach ($branches as $b): ?>
                                    <option value="<?= (int) $b['id'] ?>"><?= Security::e($b['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="field" id="addressRow">
                                <label for="delivery_address">Delivery address <span class="required">*</span></label>
                                <textarea name="delivery_address" id="delivery_address" class="form-textarea" rows="2" placeholder="Street, building, landmark..."></textarea>
                            </div>
                        </div>

                        <div class="form-divider"></div>

                        <div class="order-form-section">
                            <h4 class="form-section-label">Payment method</h4>
                            <div class="pay-options" id="paymentToggle">
                                <label class="pay-option">
                                    <input type="radio" name="payment_method_radio" value="mtn_momo" checked>
                                    <span class="pay-card">
                                        <img src="/assets/uploads/restaurant/mtn-logo.svg" alt="MTN Mobile Money" class="pay-logo">
                                        <span class="pay-name">MTN Mobile Money</span>
                                    </span>
                                </label>
                                <label class="pay-option">
                                    <input type="radio" name="payment_method_radio" value="airtel_money">
                                    <span class="pay-card">
                                        <img src="/assets/uploads/restaurant/airtel-logo.svg" alt="Airtel Money" class="pay-logo">
                                        <span class="pay-name">Airtel Money</span>
                                    </span>
                                </label>
                                <label class="pay-option">
                                    <input type="radio" name="payment_method_radio" value="cash">
                                    <span class="pay-card">
                                        <i class="bi bi-cash-stack pay-icon"></i>
                                        <span class="pay-name">Cash</span>
                                    </span>
                                </label>
                            </div>
                            <input type="hidden" name="payment_method" id="paymentMethod" value="mtn_momo">
                            <div class="field" id="payPhoneRow" style="margin-top:14px;">
                                <label for="payment_phone">Mobile money number <span class="required">*</span></label>
                                <input type="tel" name="payment_phone" id="payment_phone" class="form-input" placeholder="+256 700 000 000">
                                <small class="form-hint">We will send a payment prompt to this number to confirm your order.</small>
                            </div>
                        </div>

                        <div class="form-divider"></div>

                        <div class="field">
                            <label for="notes">Order notes</label>
                            <textarea name="notes" id="notes" class="form-textarea" rows="2" placeholder="Any special instructions..."></textarea>
                        </div>

                        <div class="order-totals" id="cartSummaryForm" style="display:none;">
                            <div class="cart-line"><span>Subtotal</span><span class="js-subtotal">UGX 0</span></div>
                            <div class="cart-line"><span class="js-delivery-label">Delivery fee</span><span class="js-delivery">UGX 0</span></div>
                            <div class="cart-line cart-total"><span>Total</span><span class="js-total">UGX 0</span></div>
                        </div>

                        <button type="submit" class="button-primary order-submit" id="orderSubmitBtn">
                            <i class="bi bi-bag-check"></i>
                            <span>Place Order</span>
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</section>

</div>

<style>
.order-layout { display: grid; grid-template-columns: 1fr 400px; gap: 44px; align-items: start; padding: 56px 0; }
.order-cart { position: sticky; top: 100px; }
.order-cart-inner { background: #fff; border: 1px solid #ede8e0; border-radius: 18px; padding: 28px; box-shadow: 0 14px 44px rgba(0,0,0,.07); }
.order-cart-title { display: flex; align-items: center; gap: 10px; font-family: 'Playfair Display', serif; font-size: 22px; color: var(--text-dark); margin: 0 0 4px; }
.order-cart-subtitle { color: #9a9284; font-size: 13px; margin: 0 0 18px; }

.cart-items { max-height: 240px; overflow-y: auto; margin: 0 -4px 14px; padding: 0 4px; }
.cart-empty { color: #a59d8f; font-size: 14px; margin: 0; padding: 10px 0 16px; }
.cart-item-row { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid #f3efe7; }
.cart-item-row:last-child { border-bottom: none; }
.cart-item-name { flex: 1; font-size: 14px; font-weight: 600; color: var(--text-dark); line-height: 1.25; }
.cart-item-price { font-size: 12px; color: #a59d8f; font-weight: 400; }
.cart-qty { display: flex; align-items: center; gap: 8px; }
.cart-qty button { width: 26px; height: 26px; border: 1px solid #ddd6c8; background: #fff; border-radius: 50%; cursor: pointer; line-height: 1; color: var(--text-dark); font-size: 14px; transition: all .15s; }
.cart-qty button:hover { border-color: var(--gold); color: var(--gold); }
.cart-qty span { min-width: 20px; text-align: center; font-weight: 700; }
.cart-remove { border: none; background: none; color: #b6ad9c; cursor: pointer; font-size: 15px; line-height: 1; }
.cart-remove:hover { color: #dc2626; }

.order-form { margin-top: 4px; }
.form-section-label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: #8a8272; margin: 0 0 14px; }
.field { margin-bottom: 15px; }
.field label { display: block; font-size: 13px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
.field .required { color: #e74c3c; }
.form-hint { display: block; color: #a59d8f; font-size: 12px; margin-top: 6px; line-height: 1.5; }
.order-form .form-divider { margin: 0 0 22px; height: 1px; background: #f0eae1; }
.order-form-section { margin-bottom: 22px; }

.order-type-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.order-type-grid .type-btn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; font-size: 14px; }

.pay-options { display: flex; flex-direction: column; gap: 10px; }
.pay-option { position: relative; display: block; cursor: pointer; }
.pay-option input { position: absolute; opacity: 0; width: 0; height: 0; }
.pay-card { display: flex; align-items: center; gap: 12px; padding: 13px 16px; border: 2px solid #e7e1d4; border-radius: 10px; background: #fff; font-weight: 600; font-size: 14px; color: var(--text-dark); transition: all .15s; }
.pay-card:hover { border-color: var(--gold); }
.pay-option input:focus-visible ~ .pay-card { box-shadow: 0 0 0 3px rgba(166,180,154,.4); }
.pay-option input:checked ~ .pay-card { border-color: var(--gold); background: #fbf7ec; }
.pay-logo { height: 20px; width: auto; }
.pay-icon { font-size: 20px; color: #6f7866; }
.pay-card::after { content: ''; width: 18px; height: 18px; border-radius: 50%; border: 2px solid #d8d2c4; margin-left: auto; flex: none; transition: all .15s; }
.pay-option input:checked ~ .pay-card::after { border-color: var(--gold); background: var(--gold); box-shadow: inset 0 0 0 3px #fff; }

.order-totals { background: #fbf8f1; border: 1px solid #ede8e0; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; }
.order-totals .cart-line { display: flex; justify-content: space-between; font-size: 14px; padding: 3px 0; color: #5b554b; }
.order-totals .cart-total { font-weight: 800; font-size: 16px; color: var(--text-dark); border-top: 1px dashed #e0d7c4; margin-top: 6px; padding-top: 9px; }
.order-submit { width: 100%; padding: 15px; font-size: 16px; }

.order-items .menu-item { align-items: flex-start; }
.order-items .menu-item-info p { -webkit-line-clamp: 2; }
.order-add-btn { margin-top: 12px; font-size: 13px; padding: 8px 16px; }

@media (max-width: 992px) {
    .order-layout { grid-template-columns: 1fr; padding: 40px 0; }
    .order-cart { position: static; }
}
</style>

<script>
(function () {
    var cart = {};
    var deliveryFee = <?= (int) $deliveryFee ?>;

    function fmt(n) {
        return 'UGX ' + Number(n).toLocaleString('en-US');
    }

    function render() {
        var wrap = document.getElementById('cartItems');
        var empty = document.querySelector('.cart-empty');
        var summary = document.getElementById('cartSummary');
        var summaryForm = document.getElementById('cartSummaryForm');
        var ids = Object.keys(cart).map(Number).filter(function (id) { return cart[id] > 0; });
        var subtotal = 0;

        wrap.querySelectorAll('.cart-item-row').forEach(function (r) { r.remove(); });
        if (empty) empty.style.display = ids.length ? 'none' : '';

        ids.forEach(function (id) {
            var btn = document.querySelector('.order-item[data-id="' + id + '"]');
            var price = btn ? parseInt(btn.dataset.price, 10) : 0;
            var name = btn ? btn.dataset.name : 'Item ' + id;
            var qty = cart[id];
            subtotal += price * qty;

            var row = document.createElement('div');
            row.className = 'cart-item-row';
            row.innerHTML =
                '<div class="cart-item-name">' + name +
                '<div class="cart-item-price">' + fmt(price) + ' each</div></div>' +
                '<div class="cart-qty">' +
                '<button type="button" data-act="dec" data-id="' + id + '">&minus;</button>' +
                '<span>' + qty + '</span>' +
                '<button type="button" data-act="inc" data-id="' + id + '">+</button>' +
                '</div>' +
                '<button type="button" class="cart-remove" data-act="del" data-id="' + id + '" aria-label="Remove"><i class="bi bi-x-lg"></i></button>';
            wrap.appendChild(row);
        });

        var isDelivery = document.getElementById('orderType').value === 'delivery';
        var fee = isDelivery ? deliveryFee : 0;
        var total = subtotal + fee;

        document.getElementById('cartSubtotal').textContent = fmt(subtotal);
        document.getElementById('cartDeliveryFee').textContent = fmt(fee);
        document.getElementById('cartTotal').textContent = fmt(total);
        summary.style.display = ids.length ? '' : 'none';
        summaryForm.style.display = ids.length ? '' : 'none';

        document.querySelectorAll('.js-subtotal').forEach(function (el) { el.textContent = fmt(subtotal); });
        document.querySelectorAll('.js-total').forEach(function (el) { el.textContent = fmt(total); });
        document.querySelectorAll('.js-delivery').forEach(function (el) { el.textContent = fmt(fee); });
        document.querySelectorAll('.js-delivery-label').forEach(function (el) { el.textContent = isDelivery ? 'Delivery fee' : 'Pickup (free)'; });

        document.querySelectorAll('#orderForm input[name^="qty"]').forEach(function (inp) { inp.remove(); });
        ids.forEach(function (id) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'qty[' + id + ']';
            inp.value = cart[id];
            document.getElementById('orderForm').appendChild(inp);
        });
    }

    function setQty(id, qty) {
        if (qty <= 0) { delete cart[id]; } else { cart[id] = qty; }
        render();
    }

    document.querySelectorAll('.order-add-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var card = btn.closest('.order-item');
            var id = parseInt(card.dataset.id, 10);
            cart[id] = (cart[id] || 0) + 1;
            render();
        });
    });

    document.getElementById('cartItems').addEventListener('click', function (e) {
        var t = e.target.closest('[data-act]');
        if (!t) return;
        var id = parseInt(t.dataset.id, 10);
        var act = t.dataset.act;
        if (act === 'inc') setQty(id, (cart[id] || 0) + 1);
        else if (act === 'dec') setQty(id, (cart[id] || 0) - 1);
        else if (act === 'del') setQty(id, 0);
    });

    var typeBtns = document.querySelectorAll('#orderTypeToggle .type-btn');
    typeBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            typeBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            document.getElementById('orderType').value = btn.dataset.type;
            document.getElementById('addressRow').style.display = btn.dataset.type === 'delivery' ? '' : 'none';
            document.getElementById('delivery_address').required = btn.dataset.type === 'delivery';
            render();
        });
    });

    var payInputs = document.querySelectorAll('#paymentToggle input[name="payment_method_radio"]');
    function isMobileMoney() {
        var v = document.getElementById('paymentMethod').value;
        return v === 'mtn_momo' || v === 'airtel_money';
    }
    function applyPayment() {
        var row = document.getElementById('payPhoneRow');
        var hasPay = isMobileMoney();
        row.style.display = hasPay ? '' : 'none';
        document.getElementById('payment_phone').required = hasPay;
        if (!hasPay) document.getElementById('payment_phone').value = '';
    }
    payInputs.forEach(function (inp) {
        inp.addEventListener('change', function () {
            document.getElementById('paymentMethod').value = inp.value;
            applyPayment();
        });
    });
    applyPayment();

    document.getElementById('orderForm').addEventListener('submit', function (e) {
        var ids = Object.keys(cart).filter(function (id) { return cart[id] > 0; });
        if (!ids.length) {
            e.preventDefault();
            alert('Your cart is empty. Add at least one item.');
        }
    });
})();
</script>
