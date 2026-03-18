<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassSubject;
use App\Models\ClassSubjectSection;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use Carbon\Carbon;

class ClassContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Seeding class content...');

        // Get all class subjects
        $classSubjects = ClassSubject::all();

        if ($classSubjects->isEmpty()) {
            $this->command->warn('No class subjects found. Please run ClassSubject seeder first.');
            return;
        }

        foreach ($classSubjects as $classSubject) {
            $this->command->info("Creating content for {$classSubject->subject->name} - {$classSubject->schoolClass->name}");

            // Update general_info and generate slug
            $classSubject->update([
                'general_info' => $this->getInformasiUmumContent($classSubject->subject->name),
                'slug' => \Illuminate\Support\Str::slug(
                    $classSubject->subject->name . ' ' . 
                    $classSubject->schoolClass->name . ' ' . 
                    $classSubject->id
                ),
            ]);

            // Create 3 sections (Bab) per class subject
            for ($i = 1; $i <= 3; $i++) {
                $section = ClassSubjectSection::create([
                    'class_subject_id' => $classSubject->id,
                    'title' => "Bab {$i}: " . $this->getSectionTitle($classSubject->subject->name, $i),
                    'description' => $this->getSectionDescription($i),
                    'order_number' => $i,
                    'is_published' => true,
                ]);

                // Create 2 Materials per section
                $this->createMaterials($section, 2);

                // Create 1 Assignment per section
                $this->createAssignment($section);

                // Create 1 Quiz per section
                $this->createQuiz($section);
            }
        }

        $this->command->info('Class content seeded successfully!');
    }

    /**
     * Create materials for a section
     */
    private function createMaterials(ClassSubjectSection $section, int $count): void
    {
        $materialTypes = [
            [
                'type' => 'pdf',
                'title' => 'Modul Pembelajaran PDF',
                'file_url' => 'materials/sample-module.pdf',
                'link_url' => null,
            ],
            [
                'type' => 'video',
                'title' => 'Video Pembelajaran',
                'file_url' => null,
                'link_url' => 'https://www.youtube.com/watch?v=P5l3KtQyl6w',
            ],
            [
                'type' => 'link',
                'title' => 'Materi Online',
                'file_url' => null,
                'link_url' => 'https://www.khanacademy.org/',
            ],
            [
                'type' => 'pdf',
                'title' => 'Ringkasan Materi',
                'file_url' => 'materials/summary.pdf',
                'link_url' => null,
            ],
        ];
        
        for ($i = 1; $i <= $count; $i++) {
            $materialConfig = $materialTypes[($i - 1) % count($materialTypes)];
            
            Material::create([
                'section_id' => $section->id,
                'title' => "Materi {$i}: " . $materialConfig['title'],
                'description' => $this->getMaterialDescription($materialConfig['type']),
                'content_type' => $materialConfig['type'],
                'content_url' => $this->getMaterialUrl($materialConfig['type']),
                'file_url' => $materialConfig['file_url'],
                'link_url' => $materialConfig['link_url'],
                'order_number' => $i,
                'published_at' => now(),
                'created_by' => null,
            ]);
        }
    }

    /**
     * Create assignment for a section
     */
    private function createAssignment(ClassSubjectSection $section): void
    {
        // Vary due dates: some past, some future
        $dueDateOptions = [
            Carbon::now()->subDays(7), // Past
            Carbon::now()->addDays(3),  // Soon
            Carbon::now()->addDays(7),  // Next week
            Carbon::now()->addDays(14), // 2 weeks ahead
        ];

        $dueDate = $dueDateOptions[array_rand($dueDateOptions)];
        
        // Sample attachment files (some with, some without)
        $attachments = [
            null,
            'assignments/sample-instruction.pdf',
            'assignments/template.docx',
            null,
        ];

        Assignment::create([
            'section_id' => $section->id,
            'title' => "Tugas: " . $this->getAssignmentTitle($section->title),
            'description' => $this->getAssignmentDescription(),
            'file_url' => $attachments[array_rand($attachments)],
            'due_date' => $dueDate,
            'max_score' => 100,
            'order_number' => 1,
            'created_by' => null,
        ]);
    }

    /**
     * Create quiz for a section
     */
    private function createQuiz(ClassSubjectSection $section): void
    {
        // Vary due dates
        $dueDateOptions = [
            Carbon::now()->subDays(3),  // Past
            Carbon::now()->addDays(5),  // Soon
            Carbon::now()->addDays(10), // Next week
            Carbon::now()->addDays(20), // 3 weeks ahead
        ];

        $endAt = $dueDateOptions[array_rand($dueDateOptions)];

        $quiz = Quiz::create([
            'section_id' => $section->id,
            'title' => "Kuis: " . $this->getQuizTitle($section->title),
            'description' => $this->getQuizDescription(),
            'time_limit' => rand(30, 90), // 30-90 minutes
            'max_attempt' => rand(1, 3),
            'start_at' => Carbon::now()->subDays(1),
            'end_at' => $endAt,
            'is_published' => true,
            'created_by' => null,
        ]);

        // Create 5 questions per quiz
        $this->createQuizQuestions($quiz, 5);
    }

    /**
     * Create quiz questions
     */
    private function createQuizQuestions(Quiz $quiz, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $question = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_text' => "Soal {$i}: " . $this->getQuestionText($i),
                'question_type' => 'multiple_choice',
                'score' => 20,
                'order_number' => $i,
            ]);

            // Create 4 options per question
            $correctOption = rand(0, 3);
            for ($j = 0; $j < 4; $j++) {
                QuizOption::create([
                    'question_id' => $question->id,
                    'option_text' => "Pilihan " . chr(65 + $j) . ": " . $this->getOptionText($j),
                    'is_correct' => ($j === $correctOption),
                ]);
            }
        }
    }

    // Helper methods for generating realistic content

    private function getSectionTitle(string $subjectName, int $index): string
    {
        $titles = [
            'Matematika' => ['Aljabar Dasar', 'Geometri', 'Statistika dan Peluang'],
            'Fisika' => ['Mekanika', 'Termodinamika', 'Gelombang dan Optik'],
            'Kimia' => ['Struktur Atom', 'Ikatan Kimia', 'Reaksi Kimia'],
            'Biologi' => ['Sel dan Jaringan', 'Sistem Organ', 'Genetika'],
            'Sejarah' => ['Masa Pra-Aksara', 'Kerajaan Nusantara', 'Masa Kolonial'],
            'Bahasa Indonesia' => ['Teks Narasi', 'Teks Argumentasi', 'Teks Eksposisi'],
        ];

        foreach ($titles as $key => $sectionTitles) {
            if (str_contains($subjectName, $key)) {
                return $sectionTitles[$index - 1] ?? "Topik {$index}";
            }
        }

        return "Pendahuluan ke Topik {$index}";
    }

    private function getSectionDescription(int $index): string
    {
        $descriptions = [
            "Pada bab ini, siswa akan mempelajari konsep dasar dan fundamental yang menjadi fondasi untuk topik selanjutnya.",
            "Bab ini membahas aplikasi praktis dan studi kasus nyata untuk memperdalam pemahaman materi.",
            "Bab terakhir ini merangkum semua konsep yang telah dipelajari dan mempersiapkan untuk evaluasi akhir.",
        ];

        return $descriptions[$index - 1] ?? "Deskripsi untuk bab {$index}";
    }

    private function getMaterialTitle(string $sectionTitle, int $index): string
    {
        $prefixes = ['Pengenalan', 'Pembahasan', 'Studi Kasus', 'Rangkuman'];
        return $prefixes[$index - 1] ?? "Materi {$index}";
    }

    private function getMaterialDescription(string $type): string
    {
        $descriptions = [
            'pdf' => 'Dokumen PDF berisi penjelasan lengkap dengan ilustrasi dan contoh soal.',
            'video' => 'Video pembelajaran interaktif dengan durasi 15-30 menit.',
            'link' => 'Tautan ke sumber eksternal yang relevan dengan materi pembelajaran.',
        ];

        return $descriptions[$type] ?? 'Materi pembelajaran';
    }

    private function getMaterialUrl(string $type): string
    {
        $urls = [
            'pdf' => 'https://example.com/materials/sample.pdf',
            'video' => 'https://www.youtube.com/watch?v=P5l3KtQyl6w',
            'link' => 'https://example.com/external-resource',
        ];

        return $urls[$type] ?? '#';
    }

    private function getAssignmentTitle(string $sectionTitle): string
    {
        $verbs = ['Analisis', 'Penelitian', 'Presentasi', 'Laporan'];
        return $verbs[array_rand($verbs)] . " tentang " . $sectionTitle;
    }

    private function getAssignmentDescription(): string
    {
        $descriptions = [
            "Buatlah analisis mendalam tentang topik yang telah dipelajari. Sertakan minimal 3 referensi.",
            "Kerjakan soal-soal latihan dan upload hasil pekerjaan dalam format PDF.",
            "Buat presentasi singkat (5-10 slide) menjelaskan konsep utama dari materi ini.",
            "Tulis laporan dengan format ilmiah, minimal 500 kata, dengan struktur yang jelas.",
        ];

        return $descriptions[array_rand($descriptions)];
    }

    private function getQuizTitle(string $sectionTitle): string
    {
        return "Evaluasi Pemahaman " . $sectionTitle;
    }

    private function getQuizDescription(): string
    {
        return "Kuis ini bertujuan untuk mengevaluasi pemahaman Anda terhadap materi yang telah dipelajari. Kerjakan dengan jujur dan teliti.";
    }

    private function getQuestionText(int $index): string
    {
        $questions = [
            "Jelaskan pengertian dari konsep utama yang telah dipelajari!",
            "Apa perbedaan antara konsep A dan konsep B?",
            "Berikan contoh aplikasi dari teori yang telah dijelaskan!",
            "Faktor apa saja yang mempengaruhi fenomena ini?",
            "Bagaimana cara menyelesaikan permasalahan berikut?",
        ];

        return $questions[($index - 1) % count($questions)];
    }

    private function getOptionText(int $index): string
    {
        $options = [
            "Jawaban yang paling tepat berdasarkan teori",
            "Jawaban alternatif yang mirip tetapi kurang tepat",
            "Jawaban yang salah tetapi sering dipilih",
            "Jawaban yang tidak relevan dengan pertanyaan",
        ];

        return $options[$index % count($options)];
    }

    private function getInformasiUmumContent(string $subjectName): string
    {
        return "**Deskripsi Mata Pelajaran**\n\n" .
               "Mata pelajaran {$subjectName} memberikan pembelajaran tentang konsep-konsep fundamental yang penting untuk dipahami. " .
               "Melalui berbagai metode pembelajaran yang interaktif, siswa akan mengembangkan pemahaman mendalam tentang materi yang diajarkan. " .
               "Dengan pendekatan yang sistematis dan terstruktur, siswa diharapkan dapat menguasai kompetensi yang telah ditetapkan.\n\n" .
               
               "**Capaian Pembelajaran Mata Pelajaran**\n\n" .
               "1. Memahami konsep dan prinsip dasar secara komprehensif\n" .
               "2. Menganalisis hubungan antar konsep yang telah dipelajari\n" .
               "3. Menerapkan pengetahuan dalam konteks kehidupan sehari-hari\n" .
               "4. Mengembangkan keterampilan berpikir kritis dan analitis\n" .
               "5. Menyelesaikan permasalahan dengan pendekatan ilmiah\n\n" .
               
               "**Pokok Bahasan Mata Pelajaran**\n\n" .
               "Materi pembelajaran disusun secara sistematis dalam beberapa bab yang saling berkaitan, " .
               "dimulai dari konsep dasar hingga aplikasi lanjutan. Setiap bab dirancang untuk membangun " .
               "pemahaman secara bertahap dan progresif.\n\n" .
               
               "**Pustaka**\n\n" .
               "1. Kementerian Pendidikan dan Kebudayaan. Buku Teks Pelajaran {$subjectName}. Jakarta: Kemendikbud.\n" .
               "2. Referensi tambahan dan sumber pembelajaran yang relevan dengan materi.\n" .
               "3. Sumber digital dan multimedia pembelajaran interaktif.";
    }
}
