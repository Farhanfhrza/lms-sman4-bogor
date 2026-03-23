<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $quiz->title }} — Kuis</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            color: #1f2937;
            min-height: 100vh;
            overflow: hidden;
        }

        .quiz-layout {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* ============ SIDEBAR ============ */
        .quiz-sidebar {
            width: 280px;
            min-width: 280px;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            background: linear-gradient(135deg, #2a8d5f, #1a6341);
            color: #fff;
        }

        .sidebar-header h2 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 4px;
            word-break: break-word;
        }

        .sidebar-header p {
            font-size: 12px;
            opacity: 0.8;
        }

        .timer-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding: 10px 14px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .timer-badge.warning { background: rgba(245, 158, 11, 0.3); }
        .timer-badge.danger { background: rgba(239, 68, 68, 0.3); animation: pulse 1s infinite; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        .question-nav {
            padding: 16px;
            flex: 1;
        }

        .question-nav h3 {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .question-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
        }

        .q-btn {
            width: 100%;
            aspect-ratio: 1;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .q-btn:hover { border-color: #2a8d5f; background: #ecfdf5; }
        .q-btn.active { border-color: #1a6341; background: #2a8d5f; color: #fff; }
        .q-btn.answered { border-color: #10b981; background: #d1fae5; color: #065f46; }
        .q-btn.answered.active { background: #2a8d5f; color: #fff; border-color: #1a6341; }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #2a8d5f, #1a6341);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover { background: linear-gradient(135deg, #1a6341, #145232); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(26, 99, 65, 0.4); }

        .answer-summary {
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            margin-bottom: 12px;
        }

        .answer-summary span { font-weight: 700; color: #1a6341; }

        /* ============ MAIN CONTENT ============ */
        .quiz-main {
            flex: 1;
            overflow-y: auto;
            padding: 32px 40px;
            background: #f9fafb;
        }

        .question-card {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            scroll-margin-top: 20px;
        }

        .question-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .question-number {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2a8d5f, #1a6341);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .question-score {
            font-size: 12px;
            color: #9ca3af;
            font-weight: 500;
        }

        .question-text {
            font-size: 15px;
            line-height: 1.7;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .question-text img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 12px;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .option:hover { border-color: #6ee7b7; background: #ecfdf5; }
        .option.selected { border-color: #1a6341; background: #d1fae5; }

        .option-letter {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 2px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .option.selected .option-letter {
            border-color: #1a6341;
            background: #1a6341;
            color: #fff;
        }

        .option-text { flex: 1; color: #374151; }

        input[type="radio"] { display: none; }

        /* ============ VIOLATION OVERLAY ============ */
        .violation-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #fff;
            text-align: center;
            padding: 40px;
        }

        .violation-overlay.active { display: flex; }

        .violation-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .violation-overlay h2 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .violation-overlay p {
            font-size: 16px;
            opacity: 0.8;
            margin-bottom: 4px;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 768px) {
            .quiz-sidebar { width: 220px; min-width: 220px; }
            .quiz-main { padding: 20px 16px; }
            .question-card { padding: 20px; }
            .question-grid { grid-template-columns: repeat(4, 1fr); }
        }
    </style>
</head>
<body>
    <form id="quizForm" method="POST" action="{{ route('student.quiz.submit', $attempt) }}">
        @csrf

        <input type="hidden" name="violation_reason" id="violationReason" value="">

        <div class="quiz-layout">
            {{-- ====== SIDEBAR ====== --}}
            <aside class="quiz-sidebar">
                <div class="sidebar-header">
                    <h2>{{ $quiz->title }}</h2>
                    <p>{{ $quiz->questions->count() }} soal</p>

                    @if($remainingSeconds !== null)
                    <div class="timer-badge" id="timerBadge">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                        <span id="timerDisplay">--:--</span>
                    </div>
                    @endif
                </div>

                <div class="question-nav">
                    <h3>Navigasi Soal</h3>
                    <div class="question-grid">
                        @foreach($quiz->questions as $idx => $question)
                            <button type="button" class="q-btn {{ isset($savedAnswers[$question->id]) ? 'answered' : '' }}"
                                    data-qid="{{ $question->id }}"
                                    onclick="scrollToQuestion({{ $idx }})"
                                    id="nav-{{ $idx }}">
                                {{ $idx + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="sidebar-footer">
                    <div class="answer-summary">
                        Dijawab: <span id="answeredCount">{{ count($savedAnswers) }}</span> / {{ $quiz->questions->count() }}
                    </div>
                    <button type="button" class="btn-submit" onclick="confirmSubmit()">
                        Kumpulkan Kuis
                    </button>
                </div>
            </aside>

            {{-- ====== MAIN CONTENT ====== --}}
            <main class="quiz-main" id="quizMain">
                @foreach($quiz->questions as $idx => $question)
                <div class="question-card" id="question-{{ $idx }}">
                    <div class="question-header">
                        <div class="question-number">{{ $idx + 1 }}</div>
                        <div>
                            <span class="question-score">{{ $question->score ?? 0 }} poin</span>
                        </div>
                    </div>

                    <div class="question-text">
                        {!! $question->question_text !!}
                        @if($question->image_url)
                            <img src="{{ asset('storage/' . $question->image_url) }}" alt="Gambar Soal {{ $idx + 1 }}">
                        @endif
                    </div>

                    <div class="options-list">
                        @foreach($question->options as $optIdx => $option)
                            <label class="option {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id ? 'selected' : '' }}"
                                   id="option-{{ $question->id }}-{{ $option->id }}">
                                <input type="radio"
                                       name="answers[{{ $question->id }}]"
                                       value="{{ $option->id }}"
                                       {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id ? 'checked' : '' }}
                                       onchange="selectOption({{ $question->id }}, {{ $option->id }}, {{ $idx }})">
                                <span class="option-letter">{{ chr(65 + $optIdx) }}</span>
                                <span class="option-text">{!! $option->option_text !!}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </main>
        </div>
    </form>

    {{-- Violation Overlay --}}
    <div class="violation-overlay" id="violationOverlay">
        <div class="violation-icon">
            <svg width="40" height="40" fill="none" stroke="#fff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
        </div>
        <h2>Pelanggaran Terdeteksi!</h2>
        <p id="violationMessage">Anda terdeteksi meninggalkan halaman kuis.</p>
        <p style="opacity: 0.6; margin-top: 12px;">Kuis Anda sedang otomatis dikumpulkan...</p>
    </div>

    {{-- Violation Warning Overlay --}}
    <div class="violation-overlay" id="warningOverlay" style="background: rgba(245, 158, 11, 0.95);">
        <div class="violation-icon" style="background: #fff; color: #f59e0b;">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
        </div>
        <h2>Peringatan Pelanggaran!</h2>
        <p id="warningMessage" style="color: #fff; font-weight: 600; font-size: 18px; margin-bottom: 8px;">Anda terdeteksi meninggalkan halaman kuis.</p>
        <p id="warningCounter" style="font-size: 16px; margin-bottom: 32px; opacity: 0.9;">(Teguran 1 dari 3)</p>
        <button type="button" onclick="dismissWarning()" style="padding: 14px 28px; background: #fff; color: #f59e0b; border: none; border-radius: 8px; font-weight: 800; font-size: 16px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">Saya Mengerti & Kembali</button>
    </div>

    <script>
        // ============ CONFIG ============
        const REMAINING_SECONDS = {{ $remainingSeconds !== null ? (int) $remainingSeconds : 'null' }};
        const ATTEMPT_ID = {{ $attempt->id }};
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const SAVE_URL = "{{ route('student.quiz.saveAnswer', $attempt) }}";
        const SUBMIT_URL = "{{ route('student.quiz.submit', $attempt) }}";

        let answeredSet = new Set(
            {!! json_encode(array_keys($savedAnswers)) !!}.map(String)
        );
        let isSubmitting = false;

        // ============ TIMER ============
        if (REMAINING_SECONDS !== null) {
            let remaining = Math.floor(REMAINING_SECONDS);
            const timerDisplay = document.getElementById('timerDisplay');
            const timerBadge = document.getElementById('timerBadge');

            function updateTimer() {
                if (remaining <= 0) {
                    timerDisplay.textContent = '00:00';
                    autoSubmit('Waktu habis');
                    return;
                }

                remaining--;
                const m = Math.floor(remaining / 60);
                const s = Math.floor(remaining % 60);
                timerDisplay.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');

                // Color warnings
                if (remaining <= 60) {
                    timerBadge.classList.add('danger');
                    timerBadge.classList.remove('warning');
                } else if (remaining <= 300) {
                    timerBadge.classList.add('warning');
                }
            }

            updateTimer();
            setInterval(updateTimer, 1000);
        }

        // ============ QUESTION NAVIGATION ============
        function scrollToQuestion(idx) {
            const el = document.getElementById('question-' + idx);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            // Update active state in sidebar
            document.querySelectorAll('.q-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('nav-' + idx).classList.add('active');
        }

        // ============ SELECT OPTION + AUTOSAVE ============
        function selectOption(questionId, optionId, qIndex) {
            // Update UI
            const card = document.getElementById('question-' + qIndex);
            card.querySelectorAll('.option').forEach(o => o.classList.remove('selected'));
            document.getElementById('option-' + questionId + '-' + optionId).classList.add('selected');

            // Mark answered in sidebar
            answeredSet.add(String(questionId));
            const navBtn = document.getElementById('nav-' + qIndex);
            navBtn.classList.add('answered');
            document.getElementById('answeredCount').textContent = answeredSet.size;

            // Autosave via AJAX
            fetch(SAVE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    question_id: questionId,
                    selected_option_id: optionId,
                }),
            }).catch(err => console.error('Autosave error:', err));
        }

        // ============ SUBMIT ============
        function confirmSubmit() {
            const totalQ = {{ $quiz->questions->count() }};
            const answered = answeredSet.size;
            const unanswered = totalQ - answered;

            let msg = 'Apakah Anda yakin ingin mengumpulkan kuis ini?';
            if (unanswered > 0) {
                msg += '\n\n⚠️ Masih ada ' + unanswered + ' soal yang belum dijawab.';
            }

            if (confirm(msg)) {
                submitQuiz();
            }
        }

        function submitQuiz(reason = null) {
            if (isSubmitting) return;
            isSubmitting = true;

            if (reason) {
                document.getElementById('violationReason').value = reason;
            }

            // Exit fullscreen before submitting
            if (document.fullscreenElement) {
                document.exitFullscreen().catch(() => {});
            }

            document.getElementById('quizForm').submit();
        }

        function autoSubmit(reason) {
            if (isSubmitting) return;
            isSubmitting = true;

            // Show violation overlay
            const overlay = document.getElementById('violationOverlay');
            const msg = document.getElementById('violationMessage');
            msg.textContent = reason;
            overlay.classList.add('active');

            document.getElementById('violationReason').value = reason;

            // Brief delay so user sees the message
            setTimeout(() => {
                if (document.fullscreenElement) {
                    document.exitFullscreen().catch(() => {});
                }
                document.getElementById('quizForm').submit();
            }, 2000);
        }

        // ============ VIOLATION HANDLER ============
        let warningCount = 0;
        const MAX_WARNINGS = 3;

        function handleViolation(reason) {
            if (isSubmitting) return;

            // Jika warning sudah muncul, abaikan event lain sementara
            if (document.getElementById('warningOverlay').classList.contains('active')) {
                return;
            }

            warningCount++;

            if (warningCount >= MAX_WARNINGS) {
                autoSubmit(reason + ` (Telah melanggar batas peringatan ${MAX_WARNINGS} kali)`);
            } else {
                document.getElementById('warningMessage').textContent = `Sistem mendeteksi Anda: ${reason}.`;
                document.getElementById('warningCounter').textContent = `(Teguran ${warningCount} dari maksimal ${MAX_WARNINGS} Toleransi)`;
                document.getElementById('warningOverlay').classList.add('active');
            }
        }

        function dismissWarning() {
            document.getElementById('warningOverlay').classList.remove('active');
            enterFullscreen();
        }

        // ============ FULLSCREEN ============
        function enterFullscreen() {
            const el = document.documentElement;
            if (el.requestFullscreen) {
                el.requestFullscreen().catch(err => {
                    console.warn('Fullscreen request failed:', err);
                });
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            } else if (el.msRequestFullscreen) {
                el.msRequestFullscreen();
            }
        }

        // Enter fullscreen on load
        window.addEventListener('load', function () {
            enterFullscreen();
        });

        // ============ ANTI-CHEAT: FULLSCREEN EXIT ============
        document.addEventListener('fullscreenchange', function () {
            if (!document.fullscreenElement && !isSubmitting) {
                handleViolation('Keluar dari mode fullscreen');
            }
        });

        document.addEventListener('webkitfullscreenchange', function () {
            if (!document.webkitFullscreenElement && !isSubmitting) {
                handleViolation('Keluar dari mode fullscreen');
            }
        });

        // ============ ANTI-CHEAT: TAB SWITCH ============
        document.addEventListener('visibilitychange', function () {
            if (document.hidden && !isSubmitting) {
                handleViolation('Membuka tab atau aplikasi lain');
            }
        });

        // ============ ANTI-CHEAT: WINDOW BLUR ============
        window.addEventListener('blur', function () {
            // Extra detection in some browsers
            if (!isSubmitting) {
                handleViolation('Meninggalkan fokus halaman kuis');
            }
        });

        // ============ PREVENT KEYBOARD SHORTCUTS ============
        document.addEventListener('keydown', function (e) {
            // Block common shortcuts: Ctrl+T, Ctrl+N, Ctrl+W, Alt+Tab, F11
            if (
                (e.ctrlKey && ['t', 'n', 'w', 'Tab'].includes(e.key)) ||
                (e.altKey && e.key === 'Tab') ||
                e.key === 'F11'
            ) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Block Escape
            if (e.key === 'Escape') {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        // ============ PREVENT RIGHT CLICK ============
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        // ============ SCROLL SPY FOR SIDEBAR NAV ============
        const mainScroller = document.getElementById('quizMain');
        mainScroller.addEventListener('scroll', function () {
            const cards = document.querySelectorAll('.question-card');
            let current = 0;
            cards.forEach((card, idx) => {
                const rect = card.getBoundingClientRect();
                if (rect.top < 200) {
                    current = idx;
                }
            });
            document.querySelectorAll('.q-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('nav-' + current)?.classList.add('active');
        });
    </script>
</body>
</html>
