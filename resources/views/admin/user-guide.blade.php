@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Deurali Chemicals - User Guide</h2>
        <p class="text-gray-600">सिस्टमका मुख्य फिचरहरू र तिनको प्रयोग गर्ने तरिका:</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-blue-700 mb-2">
                <i class="fa-solid fa-boxes-stacked mr-2"></i> Inventory Management
            </h3>
            <p class="text-sm text-gray-600">नयाँ स्टक थप्न र भएको स्टकको समायोजन (Adjustment) गर्न 'Inventory' मेनुमा जानुहोस्। low-stock अलर्टलाई नियमित चेक गर्नुहोस्।</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-green-700 mb-2">
                <i class="fa-solid fa-cart-shopping mr-2"></i> Sales & POS
            </h3>
            <p class="text-sm text-gray-600">छिटो बिल काट्न POS ड्यासबोर्ड प्रयोग गर्नुहोस्। ग्राहकको लेजर हेर्न फोन नम्बर वा नामद्वारा सर्च गर्न सकिन्छ।</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-purple-700 mb-2">
                <i class="fa-solid fa-file-invoice mr-2"></i> Invoice Management
            </h3>
            <p class="text-sm text-gray-600">बिलहरू प्रिन्ट गर्न वा डिजिटल रूपमा ग्राहकलाई सेयर गर्न 'Invoice' सेक्सनमा जानुहोस्।</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-red-700 mb-2">
                <i class="fa-solid fa-shield-halved mr-2"></i> Security Tips
            </h3>
            <p class="text-sm text-gray-600">आफ्नो पासवर्ड कसैसँग सेयर नगर्नुहोस्। काम सकिएपछि अनिवार्य रूपमा 'Logout' गर्नुहोस्।</p>
        </div>

    </div>

    <div class="mt-10 p-6 bg-blue-50 rounded-xl border border-blue-100 text-center">
        <h4 class="font-bold text-blue-900">केही समस्या छ?</h4>
        <p class="text-sm text-blue-700">थप सहयोगको लागि सिस्टम एडमिनलाई सम्पर्क गर्नुहोस्।</p>
    </div>
</div>
@endsection