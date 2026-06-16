@extends('layouts.admin')
@section('panel_title', 'Generate Invoice')
@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow" x-data="invoiceSystem({{ $customers->toJson() }})">
    <form action="{{ route('admin.invoices.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div class="space-y-4">
                <h3 class="font-bold text-teal-600 border-b">Invoice Details</h3>
                <input type="text" name="invoice_no" value="{{ $invoiceNo }}" readonly
                    class="w-full border p-2 bg-gray-50">
                <input type="text" name="invoice_date" value="{{ $nepaliDate }}" readonly
                    class="w-full border p-2 bg-gray-50">
            </div>
            <div class="space-y-4" x-data="{ selectedCustomerId: '' }">
                <h3 class="font-bold text-teal-600 border-b">Customer Details</h3>
                <select name="customer_id" x-model="selectedCustomerId" class="w-full border p-2" required>
                    <option value="">-- Choose Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs">Address: <span x-text="getAddress(selectedCustomerId)"></span></p>
            </div>
        </div>

        <table class="w-full border mb-4">
            <thead>
                <tr class="bg-gray-100 uppercase text-xs">
                    <th class="p-2 border">Product</th>
                    <th class="p-2 border">Price/Kg</th>
                    <th class="p-2 border">Qty</th>
                    <th class="p-2 border">Total</th>
                    <th class="p-2 border">Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, index) in rows" :key="index">
                    <tr>
                        <td class="p-2 border">
                            <select :name="'items['+index+'][product_id]'" x-model="row.product_id"
                                @change="updatePrice(index, $event)" class="w-full p-1 border">
                                <option value="">Select Product</option>
                                @foreach($products as $p)
                                <option value="{{$p->id}}" data-price="{{$p->selling_price}}">
                                    {{$p->name}} (Stock: {{ $p->stock }})
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-2 border"><input type="number" :name="'items['+index+'][price]'"
                                x-model="row.price" readonly class="w-full p-1 bg-gray-50"></td>
                        <td class="p-2 border">
                            <div class="flex">
                                <input type="number" :name="'items['+index+'][qty]'" x-model="row.qty"
                                    @input="calculateRow(index)" class="w-20 border p-1" step="0.001">
                                <select :name="'items['+index+'][unit]'" x-model="row.unit"
                                    @change="calculateRow(index)" class="border p-1">
                                    <option value="kg">kg</option>
                                    <option value="g">g</option>
                                </select>
                            </div>
                        </td>
                        <td class="p-2 border"><input type="text" :name="'items['+index+'][total]'" x-model="row.total"
                                readonly class="w-full p-1 bg-gray-50"></td>
                        <td class="p-2 border text-center"><button type="button" @click="rows.splice(index, 1)"
                                class="text-red-500">X</button></td>
                    </tr>
                </template>
            </tbody>
        </table>
        <button type="button" @click="rows.push({product_id:'', price:0, qty:1, unit:'kg', total:0})"
            class="bg-blue-600 text-white px-4 py-2">+ Add Row</button>
        <div class="text-right font-bold text-xl">Grand Total: <input type="number" name="grand_total" readonly
                :value="grandTotal" class="w-32 border p-2"></div>
        <button type="submit" class="bg-teal-600 text-white px-6 py-2 mt-4">Save Invoice</button>
    </form>
</div>
<script>
function invoiceSystem(customers, products) {
    return {
        rows: [{
            product_id: '',
            price: 0,
            qty: 1,
            unit: 'kg',
            total: 0
        }],
        customers: customers,
        products: products, // Pass the products into the component

        getAddress(id) {
            return this.customers.find(c => c.id == id)?.address || '';
        },

        updatePrice(index) {
            let row = this.rows[index];
            let product = this.products.find(p => p.id == row.product_id);

            // If product found, set price, else set 0
            row.price = product ? parseFloat(product.selling_price) : 0;
            this.calculateRow(index);
        },

        calculateRow(index) {
            let r = this.rows[index];
            let kg = r.unit === 'g' ? (r.qty / 1000) : r.qty;
            r.total = (kg * r.price).toFixed(2);
        },

        get grandTotal() {
            return this.rows.reduce((sum, r) => sum + parseFloat(r.total || 0), 0).toFixed(2);
        }
    }
}
</script>
@endsection