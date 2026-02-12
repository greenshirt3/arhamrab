<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroPrime | Omni-Channel Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .dashboard-grid { display: grid; grid-template-columns: 250px 1fr; height: 100vh; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800" x-data="app()" x-init="fetchData()">

    <div class="relative min-h-screen">
        
        <nav class="sticky top-0 z-50 bg-slate-900 text-white shadow-lg">
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-bolt text-yellow-400 text-2xl"></i>
                    <span class="text-xl font-bold tracking-tight">Electro<span class="text-yellow-400">Prime</span></span>
                </div>

                <div class="flex gap-4">
                    <button @click="view = 'store'" :class="view === 'store' ? 'text-yellow-400' : 'text-slate-400'" class="hover:text-white transition font-semibold">
                        <i class="fas fa-store mr-1"></i> Online Store
                    </button>
                    <button @click="view = 'admin'" :class="view === 'admin' ? 'text-yellow-400' : 'text-slate-400'" class="hover:text-white transition font-semibold">
                        <i class="fas fa-cash-register mr-1"></i> POS & Admin
                    </button>
                </div>

                <div x-show="view === 'store'" class="relative cursor-pointer" @click="cartOpen = true">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span x-show="cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold" x-text="cartCount"></span>
                </div>
            </div>
        </nav>

        <div x-show="view === 'store'" class="pb-20">
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-20 px-6 text-center">
                <h1 class="text-5xl font-extrabold mb-4">The Future of Mobile</h1>
                <p class="text-slate-400 mb-8">Official Warranty. PTA Approved. Best Prices.</p>
                <button @click="$refs.products.scrollIntoView({behavior: 'smooth'})" class="bg-yellow-400 text-slate-900 px-8 py-3 rounded-full font-bold hover:bg-yellow-300 transition">Shop Now</button>
            </div>

            <div x-ref="products" class="container mx-auto px-6 py-12">
                <h2 class="text-3xl font-bold mb-8 border-l-4 border-yellow-400 pl-4">Latest Arrivals</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <template x-for="p in products" :key="p.id">
                        <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition duration-300 p-4 relative group border border-slate-200">
                            <span class="absolute top-4 left-4 text-xs font-bold px-2 py-1 rounded"
                                  :class="p.stock > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                  x-text="p.stock > 0 ? 'In Stock: ' + p.stock : 'Out of Stock'"></span>
                            
                            <img :src="p.image" class="w-full h-48 object-contain mb-4 group-hover:scale-105 transition">
                            
                            <h3 class="font-bold text-lg" x-text="p.name"></h3>
                            <p class="text-sm text-slate-500 mb-2" x-text="p.specs"></p>
                            <div class="flex justify-between items-center mt-4">
                                <span class="text-xl font-bold text-slate-900" x-text="formatMoney(p.price)"></span>
                                <button @click="addToCart(p)" :disabled="p.stock === 0" 
                                        class="bg-slate-900 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-yellow-400 hover:text-slate-900 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div x-show="view === 'admin'" x-cloak class="bg-slate-200 min-h-screen p-6">
            <div class="container mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <h3 class="text-xl font-bold mb-4 flex justify-between">
                            <span><i class="fas fa-calculator mr-2"></i> POS Terminal</span>
                            <span class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full">Mode: Walk-In Customer</span>
                        </h3>
                        <input type="text" x-model="search" placeholder="Scan Barcode or Type Product Name..." class="w-full mb-4 p-3 border rounded-lg bg-slate-50">
                        
                        <div class="grid grid-cols-3 gap-4 max-h-[400px] overflow-y-auto">
                            <template x-for="p in filteredProducts" :key="p.id">
                                <div @click="addToCart(p)" class="cursor-pointer border border-slate-200 p-3 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-center"
                                     :class="{'opacity-50 pointer-events-none': p.stock === 0}">
                                    <h4 class="font-bold text-sm" x-text="p.name"></h4>
                                    <p class="text-xs text-slate-500" x-text="'Stk: ' + p.stock"></p>
                                    <p class="text-blue-600 font-bold text-sm mt-1" x-text="formatMoney(p.price)"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm overflow-x-auto">
                        <h3 class="font-bold mb-4">Inventory Management</h3>
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 uppercase">
                                <tr>
                                    <th class="p-3">ID</th>
                                    <th class="p-3">Product</th>
                                    <th class="p-3">Price</th>
                                    <th class="p-3">Stock</th>
                                    <th class="p-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="p in products" :key="p.id">
                                    <tr class="border-b">
                                        <td class="p-3" x-text="p.id"></td>
                                        <td class="p-3" x-text="p.name"></td>
                                        <td class="p-3">
                                            <input type="number" x-model="p.price" class="w-24 border rounded px-2 py-1">
                                        </td>
                                        <td class="p-3">
                                            <input type="number" x-model="p.stock" class="w-16 border rounded px-2 py-1">
                                        </td>
                                        <td class="p-3">
                                            <button @click="updateProduct(p)" class="text-green-600 hover:text-green-800 font-bold">Save</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm h-fit">
                    <h3 class="font-bold mb-4">Recent Orders</h3>
                    <div class="space-y-4 max-h-[600px] overflow-y-auto">
                        <template x-for="o in orders" :key="o.order_id">
                            <div class="border-b pb-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-sm" x-text="o.order_id"></p>
                                        <p class="text-xs text-slate-400" x-text="o.date"></p>
                                        <p class="text-xs font-semibold text-blue-600" x-text="o.customer.name || 'Walk-in Customer'"></p>
                                    </div>
                                    <span class="font-bold text-green-600" x-text="formatMoney(o.total)"></span>
                                </div>
                                <div class="mt-2 text-xs text-slate-500">
                                    <template x-for="item in o.items">
                                        <span class="block">• <span x-text="item.qty"></span>x <span x-text="item.name"></span></span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>

        <div x-show="cartOpen" x-cloak class="fixed inset-0 z-[60] flex justify-end">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="cartOpen = false"></div>
            <div class="relative bg-white w-full max-w-md h-full shadow-2xl flex flex-col p-6 animate-slide-in">
                
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h2 class="text-2xl font-bold">Current Order</h2>
                    <button @click="cartOpen = false" class="text-slate-400 hover:text-red-500"><i class="fas fa-times text-xl"></i></button>
                </div>

                <div class="flex-1 overflow-y-auto space-y-4">
                    <template x-if="cart.length === 0"><p class="text-center text-slate-400 mt-10">Cart is empty.</p></template>
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="flex gap-4 items-center bg-slate-50 p-3 rounded-lg">
                            <img :src="item.image" class="w-12 h-12 object-cover rounded">
                            <div class="flex-1">
                                <h4 class="font-bold text-sm" x-text="item.name"></h4>
                                <p class="text-xs text-slate-500" x-text="formatMoney(item.price) + ' x ' + item.qty"></p>
                            </div>
                            <span class="font-bold" x-text="formatMoney(item.price * item.qty)"></span>
                            <button @click="removeFromCart(index)" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                        </div>
                    </template>
                </div>

                <div class="mt-6 border-t pt-4">
                    <div class="flex justify-between text-xl font-bold mb-6">
                        <span>Total:</span>
                        <span class="text-blue-600" x-text="formatMoney(cartTotal)"></span>
                    </div>

                    <div x-show="view === 'store'" class="space-y-3 mb-4">
                        <input type="text" x-model="customerForm.name" placeholder="Full Name" class="w-full border rounded p-2 text-sm">
                        <input type="text" x-model="customerForm.phone" placeholder="Phone Number" class="w-full border rounded p-2 text-sm">
                        <input type="text" x-model="customerForm.address" placeholder="Delivery Address" class="w-full border rounded p-2 text-sm">
                        <p class="text-xs text-slate-400"><i class="fas fa-lock"></i> Cash on Delivery (COD) Enabled</p>
                    </div>

                    <button @click="processCheckout()" 
                            :disabled="cart.length === 0 || loading"
                            class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold hover:bg-green-600 transition flex justify-center items-center gap-2">
                        <span x-show="!loading" x-text="view === 'store' ? 'Place Order (COD)' : 'Complete Sale (POS)'"></span>
                        <i x-show="loading" class="fas fa-spinner fa-spin"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function app() {
            return {
                view: 'store', // 'store' or 'admin'
                products: [],
                orders: [],
                cart: [],
                search: '',
                cartOpen: false,
                loading: false,
                customerForm: { name: '', phone: '', address: '' },

                async fetchData() {
                    this.products = await (await fetch('api.php?action=get_products')).json();
                    this.orders = await (await fetch('api.php?action=get_orders')).json();
                },

                get filteredProducts() {
                    if (this.search === '') return this.products;
                    return this.products.filter(p => p.name.toLowerCase().includes(this.search.toLowerCase()));
                },

                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },

                get cartCount() {
                    return this.cart.reduce((sum, item) => sum + item.qty, 0);
                },

                addToCart(product) {
                    if (product.stock <= 0) return alert('Out of stock!');
                    
                    let existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        if (existing.qty < product.stock) {
                            existing.qty++;
                        } else {
                            alert('Max stock reached for this item.');
                        }
                    } else {
                        this.cart.push({ ...product, qty: 1 });
                    }
                    
                    if(this.view === 'admin') this.cartOpen = true; // Auto open cart in POS mode
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                async processCheckout() {
                    if (this.view === 'store' && !this.customerForm.name) return alert('Please fill in your details.');
                    
                    this.loading = true;
                    
                    const customerData = this.view === 'store' ? this.customerForm : { name: 'Walk-in', type: 'POS' };

                    const res = await fetch('api.php?action=create_order', {
                        method: 'POST',
                        body: JSON.stringify({ cart: this.cart, customer: customerData })
                    });
                    
                    const data = await res.json();
                    
                    if (data.status === 'success') {
                        alert(this.view === 'store' ? 'Order Placed! Order ID: ' + data.order_id : 'Sale Completed!');
                        this.cart = [];
                        this.cartOpen = false;
                        this.customerForm = { name: '', phone: '', address: '' };
                        this.fetchData(); // Refresh stock
                    } else {
                        alert('Error: ' + data.message);
                    }
                    
                    this.loading = false;
                },

                async updateProduct(product) {
                    const res = await fetch('api.php?action=update_product', {
                        method: 'POST',
                        body: JSON.stringify({ id: product.id, price: product.price, stock: product.stock })
                    });
                    const data = await res.json();
                    if(data.status === 'success') alert('Inventory Updated');
                },

                formatMoney(amount) {
                    return 'PKR ' + new Intl.NumberFormat().format(amount);
                }
            }
        }
    </script>
</body>
</html>