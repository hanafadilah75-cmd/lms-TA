@extends('layouts.app')

@section('page_title', 'Mengerjakan Kuis')

@section('content')
<div id="quiz-container" class="max-w-3xl mx-auto space-y-6">
    <!-- Sticky Timer Header -->
    <div class="sticky top-4 z-20 bg-white/95 backdrop-blur-md rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-[#534ebb]/10 text-[#534ebb]">
                Waktu Tersisa
            </span>
            <h2 class="text-base font-extrabold text-slate-900 mt-1 truncate max-w-xs md:max-w-md">{{ $quiz->title }}</h2>
        </div>
        <!-- Timer Clock Display -->
        <div class="flex items-center gap-2 px-4 py-2 bg-[#534ebb]/10 rounded-2xl">
            <span class="text-lg">⏱️</span>
            <span id="countdown-timer" class="text-2xl font-black font-mono text-[#534ebb]">00:00</span>
        </div>
    </div>

    <!-- Quiz Form -->
    <form id="quiz-form" action="{{ route('quizzes.submit', $quiz->id) }}" method="POST" class="space-y-6">
        @csrf

        @foreach($quiz->questions as $index => $question)
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                <!-- Question Title -->
                <div class="flex items-start gap-4 mb-6">
                    <span class="w-10 h-10 rounded-2xl bg-[#534ebb]/10 text-[#534ebb] flex items-center justify-center font-extrabold text-base shrink-0">
                        {{ $index + 1 }}
                    </span>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-relaxed pt-1">
                        {{ $question->question }}
                    </h3>
                </div>

                <!-- Answer inputs -->
                @if($question->type === 'multiple_choice')
                    <div class="space-y-3 sm:pl-14">
                        @foreach($question->options as $option)
                            <label class="flex items-center gap-3.5 p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-[#534ebb]/5 hover:border-[#534ebb]/30 transition cursor-pointer font-bold text-sm text-slate-800">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required
                                    class="w-5 h-5 border-slate-300 text-[#534ebb] focus:ring-[#534ebb]">
                                <span>{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <!-- Essay -->
                    <div class="sm:pl-14">
                        <textarea name="answers[{{ $question->id }}]" required rows="6" placeholder="Tuliskan jawaban lengkap Anda di sini..."
                            class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition leading-relaxed"></textarea>
                    </div>
                @endif
            </div>
        @endforeach

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" id="submit-btn" class="px-8 py-3.5 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-sm font-extrabold text-white shadow-sm transition">
                Kirim Jawaban Kuis ➔
            </button>
        </div>
    </form>
</div>

<!-- Countdown Timer & Auto-Submit Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const durationMinutes = {{ $quiz->duration ?? 30 }};
        let timeRemaining = durationMinutes * 60;
        const timerElement = document.getElementById('countdown-timer');
        const form = document.getElementById('quiz-form');
        const submitBtn = document.getElementById('submit-btn');

        function updateTimer() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;

            timerElement.textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (timeRemaining <= 60) {
                timerElement.classList.remove('text-[#534ebb]');
                timerElement.classList.add('text-rose-600', 'animate-pulse');
            }

            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                autoSubmitQuiz();
            } else {
                timeRemaining--;
            }
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();

        let isSubmitting = false;

        function autoSubmitQuiz() {
            if (isSubmitting) return;
            isSubmitting = true;
            
            showToast('⏰ Waktu pengerjaan telah habis! Jawaban Anda sedang dikirim otomatis...', 'warning');
            
            // Remove required attribute from unanswered inputs so form can submit
            const requiredInputs = form.querySelectorAll('[required]');
            requiredInputs.forEach(input => input.removeAttribute('required'));

            // Submit using Ajax handler
            executeSubmission();
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (isSubmitting) return;
            isSubmitting = true;

            executeSubmission();
        });

        function executeSubmission() {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengirim Jawaban...';

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok && data.success) {
                    showSuccessView(data.stats, data.redirect_url);
                } else {
                    throw new Error(data.message || 'Gagal mengirim kuis. Silakan coba lagi.');
                }
            })
            .catch(error => {
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim Jawaban Kuis ➔';
                showToast(error.message || 'Terjadi kesalahan jaringan.', 'error');
            });
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-6 right-6 rounded-2xl shadow-lg p-4 max-w-sm z-50 transition-all duration-300 ${
                type === 'warning' ? 'bg-amber-500 text-white' : type === 'error' ? 'bg-rose-500 text-white' : 'bg-[#534ebb] text-white'
            } text-xs font-bold`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 4000);
        }

        function showSuccessView(stats, redirectUrl) {
            const quizContainer = document.getElementById('quiz-container');
            if (!quizContainer) return;
            
            let statsHtml = '';
            
            if (!stats.has_essay) {
                statsHtml = `
                    <div class="bg-slate-50 rounded-2xl p-6 mb-6 mt-2 max-w-md mx-auto border border-slate-100">
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div class="p-3 rounded-2xl bg-emerald-50 text-emerald-800">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Benar</span>
                                <span class="text-xl font-extrabold mt-1 block">${stats.correct}</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-rose-50 text-rose-800">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Salah</span>
                                <span class="text-xl font-extrabold mt-1 block">${stats.incorrect}</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-[#534ebb]/10 text-[#534ebb]">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Skor</span>
                                <span class="text-xl font-extrabold mt-1 block">${stats.score}/100</span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                statsHtml = `
                    <div class="bg-amber-50/80 border border-amber-100 rounded-2xl p-6 mb-6 mt-2 max-w-md mx-auto text-center space-y-2">
                        <div class="text-amber-900 text-sm font-extrabold flex items-center justify-center gap-1.5 leading-relaxed">
                            ⏳ Jawaban esai Anda sedang menunggu proses pemeriksaan tutor.
                        </div>
                        <p class="text-amber-700 text-xs font-semibold">
                            Nilai akhir akan diperbarui setelah proses review selesai.
                        </p>
                    </div>
                `;
            }
            
            quizContainer.innerHTML = `
                <div class="bg-white rounded-3xl p-10 sm:p-14 text-center max-w-xl mx-auto space-y-6 border border-slate-100 shadow-sm" id="success-card">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-3xl bg-emerald-100 text-emerald-700 text-4xl">
                        ✓
                    </div>
                    
                    <div class="space-y-2">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Berhasil! Jawaban Terkirim.
                        </h1>
                        <p class="text-slate-400 text-xs font-semibold max-w-md mx-auto">
                            Terima kasih telah menyelesaikan kuis evaluasi ini.
                        </p>
                    </div>
                    
                    ${statsHtml}
                    
                    <div class="flex flex-col items-center justify-center pt-2">
                        <a href="{{ route('dashboard') }}" class="px-8 py-3.5 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-extrabold shadow-sm transition">
                            Kembali ke Dashboard ➔
                        </a>
                    </div>
                </div>
            `;
        }
    });
</script>
@endsection
