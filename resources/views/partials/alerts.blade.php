<div class="fixed top-5 right-5 z-50 flex flex-col gap-4 w-full max-w-sm pointer-events-none">
    
    @if(session('success'))
        <div id="global-alert-success" 
             class="pointer-events-auto relative flex items-start bg-white p-4 pb-5 shadow-xl rounded border border-slate-100 transition-all duration-500 ease-out translate-x-full opacity-0 overflow-hidden">
            <div class="flex-shrink-0 text-emerald-500 mr-3">
                <i class="fa-solid fa-circle-check text-base"></i>
            </div>
            <div class="flex-1 pt-0.5">
                <h3 class="text-sm font-bold text-slate-800">Success</h3>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
            <button onclick="dismissAlert('global-alert-success')" class="text-slate-300 hover:text-slate-400 ml-4 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
            
            <div class="absolute bottom-0 left-0 h-1 bg-emerald-500 w-full origin-left transition-transform duration-[5000ms] ease-linear scale-x-0" id="progress-success"></div>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div id="global-alert-error" 
             class="pointer-events-auto relative flex items-start bg-white p-4 pb-5 shadow-xl rounded border border-slate-100 transition-all duration-500 ease-out translate-x-full opacity-0 overflow-hidden">
            <div class="flex-shrink-0 text-red-500 mr-3">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
            </div>
            <div class="flex-1 pt-0.5">
                <h3 class="text-sm font-bold text-slate-800">Error</h3>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                    @if(session('error'))
                        {{ session('error') }}
                    @elseif($errors->any())
                        {{ $errors->first() }}
                    @else
                        There was a problem processing your request.
                    @endif
                </p>
            </div>
            <button onclick="dismissAlert('global-alert-error')" class="text-slate-300 hover:text-slate-400 ml-4 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
            
            <div class="absolute bottom-0 left-0 h-1 bg-red-500 w-full origin-left transition-transform duration-[5000ms] ease-linear scale-x-0" id="progress-error"></div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const successAlert = document.getElementById('global-alert-success');
        const errorAlert = document.getElementById('global-alert-error');
        
        const successBar = document.getElementById('progress-success');
        const errorBar = document.getElementById('progress-error');

        // 1. Inward Sliding Entry
        setTimeout(() => {
            if (successAlert) {
                successAlert.classList.remove('translate-x-full', 'opacity-0');
                successAlert.classList.add('translate-x-0', 'opacity-100');
            }
            if (errorAlert) {
                errorAlert.classList.remove('translate-x-full', 'opacity-0');
                errorAlert.classList.add('translate-x-0', 'opacity-100');
            }
        }, 100);

        // 2. Start Bottom Border Animation (Expanding from left to right)
        setTimeout(() => {
            if (successBar) {
                successBar.classList.remove('scale-x-0');
                successBar.classList.add('scale-x-100');
            }
            if (errorBar) {
                errorBar.classList.remove('scale-x-0');
                errorBar.classList.add('scale-x-100');
            }
        }, 300);

        // 3. Auto-Dismiss Sequence matching the progress end (5 seconds total)
        setTimeout(() => { if (successAlert) dismissAlert('global-alert-success'); }, 5300);
        setTimeout(() => { if (errorAlert) dismissAlert('global-alert-error'); }, 5300);
    });

    // 4. Clean Slide-out exit handling
    function dismissAlert(alertId) {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.classList.remove('translate-x-0', 'opacity-100');
            alertElement.classList.add('translate-x-full', 'opacity-0');
            
            setTimeout(() => {
                alertElement.remove();
            }, 500);
        }
    }
</script>