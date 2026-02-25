<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Kalender Akademik') }}
        </h2>
    </x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
        </div>
    @endif

    {{-- Calendar Container --}}
    <div x-data="academicCalendar({{ $year }}, {{ $month }}, {{ $eventsByDay->toJson() }}, {{ $canCreate ? 'true' : 'false' }}, {{ $isStudent ?? false ? 'true' : 'false' }})"
         x-cloak
         class="flex flex-col lg:flex-row gap-6">

        {{-- ========== LEFT: Calendar Grid ========== --}}
        <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-200 p-5 md:p-8">

            {{-- Month + View Tabs Row --}}
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                {{-- Month Navigation --}}
                <div class="flex items-center gap-3">
                    <button @click="prevMonth()" class="p-1.5 rounded-full hover:bg-gray-100 transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <h3 class="text-xl font-bold text-gray-800 uppercase tracking-wide select-none" x-text="monthYearLabel"></h3>
                    <button @click="nextMonth()" class="p-1.5 rounded-full hover:bg-gray-100 transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                {{-- Add Event Button --}}
                <div class="flex items-center gap-3 flex-wrap">
                    <template x-if="canCreate">
                        <button @click="openCreateModal()"
                                class="flex items-center gap-1.5 bg-[#1a6341] hover:bg-[#155c38] text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Rencana
                        </button>
                    </template>
                </div>
            </div>

            {{-- Legend for students --}}
            <template x-if="isStudent">
                <div class="flex flex-wrap gap-3 mb-4 text-[11px] font-medium">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-[#d1fae5] border border-[#a7f3d0]"></span>
                        <span class="text-gray-500">Event</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-emerald-500"></span>
                        <span class="text-gray-500">Tugas Dikumpulkan</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-amber-400"></span>
                        <span class="text-gray-500">Tugas Mendatang</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-red-500"></span>
                        <span class="text-gray-500">Tugas Terlewat</span>
                    </div>
                </div>
            </template>

            {{-- Day Headers --}}
            <div class="grid grid-cols-7 text-center text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 border-b border-gray-100 pb-2">
                <template x-for="d in dayNames" :key="d">
                    <div x-text="d"></div>
                </template>
            </div>

            {{-- Calendar Grid --}}
            <div class="grid grid-cols-7 border-l border-t border-gray-200">
                <template x-for="(cell, idx) in calendarCells" :key="idx">
                    <div @click="cell.day && cell.currentMonth ? selectDate(cell.day) : null"
                         :class="{
                             'bg-white hover:bg-green-50 cursor-pointer': cell.day && cell.currentMonth,
                             'bg-gray-50 text-gray-300': !cell.currentMonth,
                             'ring-2 ring-[#1a6341] ring-inset bg-green-50': cell.day && cell.currentMonth && cell.day === selectedDay,
                             'text-red-500 font-semibold': cell.currentMonth && cell.isWeekend,
                         }"
                         class="relative min-h-[90px] md:min-h-[100px] border-r border-b border-gray-200 p-2 transition-all duration-150">

                        {{-- Day Number --}}
                        <span class="text-sm font-medium leading-none"
                              :class="{'text-gray-400': !cell.currentMonth}"
                              x-text="cell.day"></span>

                        {{-- Event / Assignment Badges --}}
                        <template x-if="cell.currentMonth && cell.events && cell.events.length > 0">
                            <div class="mt-1 space-y-0.5">
                                <template x-for="(ev, i) in cell.events.slice(0, 2)" :key="i">
                                    <div :class="getBadgeClass(ev)"
                                         class="text-[10px] leading-tight font-medium rounded px-1 py-0.5 truncate flex items-center gap-0.5">
                                        {{-- Status icon for assignments --}}
                                        <template x-if="ev.type === 'assignment' && ev.status === 'submitted'">
                                            <svg class="w-2.5 h-2.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        </template>
                                        <template x-if="ev.type === 'assignment' && ev.status === 'missing'">
                                            <svg class="w-2.5 h-2.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        </template>
                                        <span x-text="ev.title" class="truncate"></span>
                                    </div>
                                </template>
                                <template x-if="cell.events.length > 2">
                                    <div class="text-[10px] text-gray-400" x-text="'+' + (cell.events.length - 2) + ' lainnya'"></div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- ========== RIGHT: Jadwal Panel ========== --}}
        <div class="w-full lg:w-80 xl:w-96 bg-white rounded-xl shadow-sm border border-gray-200 p-5 md:p-6 self-start lg:sticky lg:top-20">

            {{-- Panel Header --}}
            <h4 class="text-lg font-bold text-gray-800 mb-1">Jadwal</h4>
            <p class="text-sm text-gray-500 mb-4" x-text="panelScopeLabel"></p>

            {{-- Scope Tabs (Harian / Mingguan / Bulanan) --}}
            <div class="flex bg-gray-100 rounded-lg p-0.5 text-xs font-semibold mb-4">
                <template x-for="scope in scopeTabs" :key="scope">
                    <button @click="currentScope = scope; updatePanelEvents()"
                            :class="currentScope === scope
                                ? 'bg-[#1a6341] text-white shadow-sm'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-2 py-1.5 rounded-md transition-all duration-200 text-center"
                            x-text="scope"></button>
                </template>
            </div>

            {{-- Status Filter Pills (only for students) --}}
            <template x-if="isStudent">
                <div class="flex flex-wrap gap-1.5 mb-4">
                    <template x-for="f in statusFilters" :key="f.value">
                        <button @click="statusFilter = f.value; updatePanelEvents()"
                                :class="statusFilter === f.value
                                    ? f.activeClass
                                    : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                class="text-[11px] font-semibold px-2.5 py-1 rounded-full transition-all duration-150"
                                x-text="f.label"></button>
                    </template>
                </div>
            </template>

            {{-- Loading State --}}
            <template x-if="loadingEvents">
                <div class="flex items-center justify-center py-10">
                    <svg class="animate-spin h-6 w-6 text-[#1a6341]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </div>
            </template>

            {{-- Event List --}}
            <template x-if="!loadingEvents">
                <div>
                    <template x-if="panelEvents.length === 0">
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-sm">Tidak ada jadwal</p>
                        </div>
                    </template>

                    <template x-if="panelEvents.length > 0">
                        <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-1">
                            <template x-for="(ev, i) in panelEvents" :key="i">
                                <div>
                                    {{-- Academic Event Card --}}
                                    <template x-if="ev.type !== 'assignment'">
                                        <div class="bg-[#1a6341] text-white rounded-lg p-3 shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-sm truncate" x-text="ev.title"></p>
                                                    {{-- Show date when scope is not daily --}}
                                                    <template x-if="currentScope !== 'Harian'">
                                                        <p class="text-[11px] opacity-70 mt-0.5" x-text="ev.event_date"></p>
                                                    </template>
                                                </div>
                                                {{-- Edit / Delete buttons for authorized users --}}
                                                <template x-if="canCreate">
                                                    <div class="flex gap-1 shrink-0">
                                                        <button @click.stop="openEditModal(ev)" class="p-1 rounded hover:bg-white/20 transition" title="Edit">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        </button>
                                                        <button @click.stop="confirmDelete(ev)" class="p-1 rounded hover:bg-red-500/50 transition" title="Hapus">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                            <template x-if="ev.description">
                                                <p class="text-xs mt-1 opacity-80" x-text="ev.description"></p>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Assignment Card --}}
                                    <template x-if="ev.type === 'assignment'">
                                        <div :class="getAssignmentCardClass(ev)" class="rounded-lg p-3 shadow-sm border">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1.5 mb-1">
                                                        {{-- Status icon --}}
                                                        <template x-if="ev.status === 'submitted'">
                                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white shrink-0">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                            </span>
                                                        </template>
                                                        <template x-if="ev.status === 'missing'">
                                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white shrink-0">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                                            </span>
                                                        </template>
                                                        <template x-if="ev.status === 'upcoming'">
                                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-400 text-white shrink-0">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                                            </span>
                                                        </template>
                                                        <p class="font-semibold text-sm truncate" x-text="ev.title"></p>
                                                    </div>
                                                    <template x-if="ev.subject">
                                                        <p class="text-xs opacity-70 mb-0.5" x-text="ev.subject"></p>
                                                    </template>
                                                    {{-- Show date when scope is not daily --}}
                                                    <template x-if="currentScope !== 'Harian'">
                                                        <p class="text-[11px] opacity-60 mb-1" x-text="'Deadline: ' + ev.event_date"></p>
                                                    </template>
                                                    {{-- Status label --}}
                                                    <span :class="getStatusLabelClass(ev)" class="inline-block text-[10px] font-bold uppercase tracking-wider rounded-full px-2 py-0.5" x-text="getStatusLabel(ev)"></span>
                                                </div>
                                                {{-- Link to assignment --}}
                                                <template x-if="ev.slug">
                                                    <a :href="'/assignments/' + ev.slug"
                                                       class="shrink-0 mt-0.5 p-1.5 rounded-md hover:bg-black/10 transition" title="Lihat Tugas">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    </a>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- ========== CREATE / EDIT MODAL ========== --}}
        <template x-if="canCreate">
            <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm" style="display:none;">
                <div @click.away="modalOpen = false" x-transition
                     class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4" x-text="editingEvent ? 'Edit Event' : 'Tambah Rencana'"></h3>

                    <form :action="editingEvent
                              ? '{{ url('academic-calendar') }}/' + editingEvent.id
                              : '{{ route('academic-calendar.store') }}'"
                          method="POST">
                        @csrf
                        <template x-if="editingEvent">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                                <input type="text" name="title" required
                                       x-model="formTitle"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#1a6341] focus:border-[#1a6341]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <input type="date" name="event_date" required
                                       x-model="formDate"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#1a6341] focus:border-[#1a6341]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-gray-400">(opsional)</span></label>
                                <textarea name="description" rows="3"
                                          x-model="formDescription"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#1a6341] focus:border-[#1a6341]"></textarea>
                            </div>
                            <input type="hidden" name="target_type" value="school">
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="modalOpen = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Batal</button>
                            <button type="submit"
                                    class="px-5 py-2 text-sm font-semibold text-white bg-[#1a6341] hover:bg-[#155c38] rounded-lg shadow-sm transition">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ========== DELETE CONFIRM MODAL ========== --}}
        <template x-if="canCreate">
            <div x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm" style="display:none;">
                <div @click.away="deleteModalOpen = false" x-transition
                     class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
                    <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Hapus Event?</h3>
                    <p class="text-sm text-gray-500 mb-5">Event "<span x-text="deletingEvent?.title"></span>" akan dihapus secara permanen.</p>

                    <form :action="'{{ url('academic-calendar') }}/' + (deletingEvent?.id || '')" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-center gap-3">
                            <button type="button" @click="deleteModalOpen = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Batal</button>
                            <button type="submit"
                                    class="px-5 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-sm transition">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    {{-- ========== Alpine.js Calendar Logic ========== --}}
    <script>
    function academicCalendar(initialYear, initialMonth, initialEventsByDay, canCreate, isStudent) {
        return {
            currentYear: initialYear,
            currentMonth: initialMonth, // 1-based
            eventsByDay: initialEventsByDay || {},
            canCreate: canCreate,
            isStudent: isStudent,

            selectedDay: new Date().getDate(),
            panelEvents: [],
            loadingEvents: false,

            // Scope & filter state
            currentScope: 'Bulanan',
            scopeTabs: ['Harian', 'Mingguan', 'Bulanan'],
            statusFilter: 'all',
            statusFilters: [
                { value: 'all',       label: 'Semua',              activeClass: 'bg-gray-700 text-white' },
                { value: 'submitted', label: 'Sudah Dikumpulkan',  activeClass: 'bg-emerald-600 text-white' },
                { value: 'upcoming',  label: 'Akan Datang',        activeClass: 'bg-amber-500 text-white' },
                { value: 'missing',   label: 'Terlewat',           activeClass: 'bg-red-600 text-white' },
            ],

            // Modal states
            modalOpen: false,
            deleteModalOpen: false,
            editingEvent: null,
            deletingEvent: null,
            formTitle: '',
            formDate: '',
            formDescription: '',

            dayNames: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],

            monthNames: [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ],

            get monthYearLabel() {
                return this.monthNames[this.currentMonth - 1] + ' ' + this.currentYear;
            },

            get panelScopeLabel() {
                if (this.currentScope === 'Harian') {
                    const d = new Date(this.currentYear, this.currentMonth - 1, this.selectedDay);
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    return days[d.getDay()] + ', ' + this.selectedDay + ' ' + this.monthNames[this.currentMonth - 1] + ' ' + this.currentYear;
                }
                if (this.currentScope === 'Mingguan') {
                    const range = this.getWeekRange(this.selectedDay);
                    return range.startDay + ' - ' + range.endDay + ' ' + this.monthNames[this.currentMonth - 1] + ' ' + this.currentYear;
                }
                return this.monthNames[this.currentMonth - 1] + ' ' + this.currentYear;
            },

            get calendarCells() {
                const cells = [];
                const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1);
                let startDow = firstDay.getDay() - 1;
                if (startDow < 0) startDow = 6;

                const daysInMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
                const daysInPrevMonth = new Date(this.currentYear, this.currentMonth - 1, 0).getDate();

                for (let i = startDow - 1; i >= 0; i--) {
                    cells.push({ day: daysInPrevMonth - i, currentMonth: false, events: [], isWeekend: false });
                }

                for (let d = 1; d <= daysInMonth; d++) {
                    const dow = new Date(this.currentYear, this.currentMonth - 1, d).getDay();
                    const isWeekend = (dow === 0 || dow === 6);
                    const dayEvents = this.eventsByDay[d] || [];
                    cells.push({ day: d, currentMonth: true, events: dayEvents, isWeekend });
                }

                const remaining = 7 - (cells.length % 7);
                if (remaining < 7) {
                    for (let i = 1; i <= remaining; i++) {
                        cells.push({ day: i, currentMonth: false, events: [], isWeekend: false });
                    }
                }

                return cells;
            },

            // Helpers
            getWeekRange(day) {
                const d = new Date(this.currentYear, this.currentMonth - 1, day);
                let dow = d.getDay(); // 0=Sun
                if (dow === 0) dow = 7; // treat Sunday as 7
                const monday = day - (dow - 1);
                const sunday = monday + 6;
                const daysInMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
                return {
                    startDay: Math.max(1, monday),
                    endDay: Math.min(daysInMonth, sunday)
                };
            },

            getDaysForScope() {
                const daysInMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
                if (this.currentScope === 'Harian') {
                    return [this.selectedDay];
                }
                if (this.currentScope === 'Mingguan') {
                    const range = this.getWeekRange(this.selectedDay);
                    const days = [];
                    for (let d = range.startDay; d <= range.endDay; d++) {
                        days.push(d);
                    }
                    return days;
                }
                // Bulanan
                const days = [];
                for (let d = 1; d <= daysInMonth; d++) {
                    days.push(d);
                }
                return days;
            },

            // Badge color based on type and status
            getBadgeClass(ev) {
                if (ev.type === 'assignment') {
                    if (ev.status === 'submitted') return 'bg-emerald-100 text-emerald-800';
                    if (ev.status === 'missing')   return 'bg-red-100 text-red-700';
                    if (ev.status === 'upcoming')  return 'bg-amber-100 text-amber-800';
                }
                return 'bg-[#d1fae5] text-[#065f46]';
            },

            getAssignmentCardClass(ev) {
                if (ev.status === 'submitted') return 'bg-emerald-50 border-emerald-200 text-emerald-900';
                if (ev.status === 'missing')   return 'bg-red-50 border-red-200 text-red-900';
                return 'bg-amber-50 border-amber-200 text-amber-900';
            },

            getStatusLabel(ev) {
                if (ev.status === 'submitted') return 'Sudah Dikumpulkan';
                if (ev.status === 'missing')   return 'Belum Dikumpulkan';
                return 'Akan Datang';
            },

            getStatusLabelClass(ev) {
                if (ev.status === 'submitted') return 'bg-emerald-200 text-emerald-800';
                if (ev.status === 'missing')   return 'bg-red-200 text-red-800';
                return 'bg-amber-200 text-amber-800';
            },

            updatePanelEvents() {
                const days = this.getDaysForScope();
                let allEvents = [];

                days.forEach(day => {
                    const evts = this.eventsByDay[day] || [];
                    evts.forEach(ev => allEvents.push(ev));
                });

                // Apply status filter
                if (this.statusFilter !== 'all') {
                    allEvents = allEvents.filter(ev => {
                        // Always show academic events when any filter is active? No — filter only applies to assignments.
                        if (ev.type !== 'assignment') return true;
                        return ev.status === this.statusFilter;
                    });
                }

                // Sort: assignments by due date, events by event_date
                allEvents.sort((a, b) => {
                    const dateA = a.event_date || '';
                    const dateB = b.event_date || '';
                    return dateA.localeCompare(dateB);
                });

                this.panelEvents = allEvents;
            },

            init() {
                const today = new Date();
                if (today.getFullYear() === this.currentYear && (today.getMonth() + 1) === this.currentMonth) {
                    this.selectedDay = today.getDate();
                } else {
                    this.selectedDay = 1;
                }
                this.updatePanelEvents();
            },

            selectDate(day) {
                this.selectedDay = day;
                this.currentScope = 'Harian';
                this.updatePanelEvents();
            },

            async prevMonth() {
                if (this.currentMonth === 1) {
                    this.currentMonth = 12;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
                this.selectedDay = 1;
                await this.fetchMonthEvents();
            },

            async nextMonth() {
                if (this.currentMonth === 12) {
                    this.currentMonth = 1;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
                this.selectedDay = 1;
                await this.fetchMonthEvents();
            },

            async fetchMonthEvents() {
                this.loadingEvents = true;
                try {
                    const url = `{{ route('academic-calendar.events-for-month') }}?year=${this.currentYear}&month=${this.currentMonth}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
                    const data = await res.json();

                    const grouped = {};
                    (data.events || []).forEach(ev => {
                        if (!grouped[ev.day]) grouped[ev.day] = [];
                        grouped[ev.day].push(ev);
                    });
                    this.eventsByDay = grouped;
                } catch (e) {
                    console.error('Failed to fetch events', e);
                    this.eventsByDay = {};
                }
                this.updatePanelEvents();
                this.loadingEvents = false;
            },

            // Modal helpers
            openCreateModal() {
                this.editingEvent = null;
                const mm = String(this.currentMonth).padStart(2, '0');
                const dd = String(this.selectedDay).padStart(2, '0');
                this.formDate = `${this.currentYear}-${mm}-${dd}`;
                this.formTitle = '';
                this.formDescription = '';
                this.modalOpen = true;
            },

            openEditModal(ev) {
                this.editingEvent = ev;
                this.formTitle = ev.title;
                this.formDate = ev.event_date;
                this.formDescription = ev.description || '';
                this.modalOpen = true;
            },

            confirmDelete(ev) {
                this.deletingEvent = ev;
                this.deleteModalOpen = true;
            },
        };
    }
    </script>
</x-app-layout>
