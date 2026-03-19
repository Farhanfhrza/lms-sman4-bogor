@props([
    'id'          => 'confirm-delete',
    'title'       => 'Konfirmasi Penghapusan',
    'description' => 'Tindakan ini tidak dapat dibatalkan.',
    'confirmText' => 'HAPUS',
    'action'      => '#',
    'method'      => 'DELETE',
    'buttonLabel' => 'Hapus Sekarang',
])

<div
    x-data="{
        open: false,
        typed: '',
        get canSubmit() { return this.typed === '{{ $confirmText }}'; }
    }"
    x-on:open-confirm-modal-{{ $id }}.window="open = true; typed = ''"
    x-show="open"
    x-cloak
    style="display:none; position:fixed; inset:0; z-index:99999;"
>
    {{-- Backdrop (fixed, covers everything including sidebar) --}}
    <div
        @click="open = false; typed = ''"
        style="position:fixed; inset:0; background:rgba(0,0,0,0.65);"
    ></div>

    {{-- Center wrapper --}}
    <div style="position:fixed; inset:0; display:flex; align-items:center; justify-content:center; padding:1rem;">

        {{-- Modal Panel — uses Tailwind classes for visuals --}}
        <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border-t-4 border-red-500 overflow-hidden"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
        >
            <div class="p-6">

                {{-- Icon --}}
                <div class="flex items-center justify-center w-14 h-14 rounded-full bg-red-100 mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                {{-- Title --}}
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">{{ $title }}</h3>

                {{-- Description --}}
                <p class="text-sm text-gray-600 text-center mb-4 break-words leading-relaxed">{!! $description !!}</p>

                {{-- Confirmation Input Box --}}
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <p class="text-sm text-red-700 font-semibold mb-2">
                        Ketik
                        <code class="bg-white border border-red-300 px-2 py-0.5 rounded text-red-600 text-xs font-mono select-none font-bold">{{ $confirmText }}</code>
                        untuk melanjutkan:
                    </p>
                    <input
                        type="text"
                        x-model="typed"
                        @paste.prevent
                        placeholder="Ketik di sini..."
                        autocomplete="off"
                        class="w-full rounded-lg px-3 py-2 text-sm font-mono focus:outline-none transition-all duration-150"
                        :class="typed.length > 0
                            ? (canSubmit
                                ? 'border-2 border-green-400 bg-green-50 text-green-800'
                                : 'border-2 border-red-400 bg-red-50 text-red-800')
                            : 'border-2 border-gray-300 bg-white text-gray-800'"
                    >
                </div>

                {{-- Hidden form --}}
                <form id="{{ $id }}-form" action="{{ $action }}" method="POST" class="hidden">
                    @csrf
                    @method($method)
                </form>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button
                        type="button"
                        @click="open = false; typed = ''"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-xl transition-colors duration-150 cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="if(canSubmit) document.getElementById('{{ $id }}-form').submit()"
                        :disabled="!canSubmit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-xl transition-all duration-150"
                        :class="canSubmit
                            ? 'bg-red-600 hover:bg-red-700 text-white cursor-pointer shadow-sm'
                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                    >
                        {{ $buttonLabel }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
