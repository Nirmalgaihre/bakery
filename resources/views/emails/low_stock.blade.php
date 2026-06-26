<x-mail::message>
# Critical Stock Alert!

The following product has dropped below its threshold and requires immediate restocking.

**Product Name:** {{ $product->name }}  
**Current Available Stock:** <span style="color: red; font-weight: bold;">{{ (float) $product->initial_stock }}</span>  
**Alert Level Set At:** {{ (float) $product->alert_stock_level }}  

<x-mail::button :url="route('admin.inventory.manageLowStock')">
View Low Stock Manager
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} System
</x-mail::message>