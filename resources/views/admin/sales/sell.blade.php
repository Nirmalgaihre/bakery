<div x-data="posSystem()">
    <form action="{{ route('admin.sales.store') }}" method="POST">
        @csrf
        <select name="customer_id" required>
            @foreach($customers as $c) <option value="{{$c->id}}">{{$c->name}}</option> @endforeach
        </select>

        <template x-for="(row, index) in rows" :key="index">
            <div class="flex gap-2">
                <select :name="'items['+index+'][product_id]'" x-model="row.product_id">
                    @foreach($products as $p) <option value="{{$p->id}}">{{$p->name}}</option> @endforeach
                </select>
                <input type="number" :name="'items['+index+'][qty]'" x-model="row.qty" placeholder="Qty">
                <input type="number" :name="'items['+index+'][price]'" x-model="row.price" placeholder="Price">
            </div>
        </template>
        
        <button type="button" @click="rows.push({product_id: '', qty: 1, price: 0})">Add Product</button>
        
        <select name="payment_method">
            <option value="cash">Cash</option>
            <option value="online">Online</option>
            <option value="credit">Credit Sale</option>
        </select>
        
        <button type="submit">Process Sale</button>
    </form>
</div>

<script>
function posSystem() {
    return {
        rows: [{ product_id: '', qty: 1, price: 0 }]
    }
}
</script>