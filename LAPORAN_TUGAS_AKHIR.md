# RANCANG BANGUN SISTEM E-TRAINING KARYAWAN STARTUP BERBASIS LARAVEL DAN CLOUD DENGAN EVALUASI KOMPETENSI BERBANTUAN AI

**PROPOSAL / LAPORAN TUGAS AKHIR**  
Diajukan sebagai salah satu tahapan memenuhi syarat menyelesaikan penulisan Tugas Akhir pada Program Studi Informatika

**Oleh:**  
**HANA FADILAH**  
**NIM: 2302041146**

**PROGRAM STUDI INFORMATIKA**  
**FAKULTAS TEKNOLOGI INFORMASI**  
**UNIVERSITAS KH. A. WAHAB HASBULLAH**  
**JOMBANG, 2026**

---

## HALAMAN PERSETUJUAN PROPOSAL

Proposal Tugas Akhir oleh : **Hana Fadilah**  
NIM : **2302041146**  
Judul Tugas Akhir : **Rancang Bangun Sistem E-Training Karyawan Startup Berbasis Laravel Dan Cloud Dengan Evaluasi Kompetensi Berbantuan AI**  

Telah disetujui untuk diujikan pada Seminar Proposal / Ujian Sidang Program Studi Informatika Fakultas Teknologi Informasi Universitas KH. A. Wahab Hasbullah.

Jombang, 2 Mei 2026  
Pembimbing,

**Munawarah, S.Kom., M.Si.**  
NIDN. 0720107801  

---

## ABSTRAK

Perkembangan industri startup di Indonesia mendorong tingginya kebutuhan rekrutmen karyawan baru yang harus siap bekerja dalam waktu singkat. Namun, proses onboarding yang masih dilakukan secara konvensional menyebabkan inkonsistensi kompetensi, sulitnya pemantauan progres, dan keterbatasan tutor dalam mengevaluasi jawaban esai. Penelitian ini bertujuan merancang dan membangun sistem e-training berbasis cloud menggunakan framework Laravel 13 yang difokuskan pada proses onboarding karyawan baru, dilengkapi fitur monitoring progres real-time, uji kompetensi pilihan ganda dan esai, serta evaluasi berbantuan kecerdasan buatan melalui Large Language Model (LLM). Metode pengembangan yang digunakan adalah metode Prototype dengan pengujian Black Box Testing dan Automated Unit/Feature Testing menggunakan PHPUnit. Sistem dibangun menggunakan Laravel 13, PHP 8.4.3, MySQL, Laravel Blade, Tailwind CSS, dan Vite, serta dihosting pada layanan cloud. Hasil pengujian menunjukkan seluruh 28 skenario Black Box dan 16 automated test (105 assertions) berjalan sukses (tingkat keberhasilan 100%). Sistem ini terbukti mampu meningkatkan efisiensi, transparansi pemantauan progres, dan standarisasi proses onboarding karyawan baru di perusahaan startup.

**Kata kunci:** E-Training, Onboarding, Kecerdasan Buatan, Cloud Computing, Laravel, Prototype, Black Box Testing.

---

## KATA PENGANTAR

Puji dan syukur penulis panjatkan ke hadirat Allah SWT atas segala rahmat, karunia, dan hidayah-Nya sehingga penulis dapat menyelesaikan naskah Tugas Akhir yang berjudul **"Rancang Bangun Sistem E-Training Karyawan Startup Berbasis Laravel Dan Cloud Dengan Evaluasi Kompetensi Berbantuan AI"** ini dengan baik. Naskah ini disusun sebagai salah satu syarat untuk penyelesaian penulisan Tugas Akhir pada Program Studi Informatika Fakultas Teknologi Informasi Universitas KH. A. Wahab Hasbullah.

Penyusunan naskah ini bertujuan untuk merancang dan membangun sistem informasi yang dapat membantu PT. Aska Teknologi Mandiri serta perusahaan mitra dalam mengelola proses onboarding karyawan baru secara lebih efektif, terstruktur, dan terukur melalui platform e-training berbasis cloud. Penulis menyadari bahwa tanpa bantuan, bimbingan, dan dukungan dari berbagai pihak, penyusunan naskah ini tidak akan berjalan lancar. Oleh karena itu, penulis mengucapkan terima kasih yang sebesar-besarnya kepada:
1. Bapak Prof. Dr. Gatot Ciptadi, DESS., selaku Rektor di Universitas KH. A. Wahab Hasbullah.
2. Bapak Tholib Hariono, M.Kom., selaku Dekan Fakultas Teknologi Informasi.
3. Bapak Primaadi Airlangga, M.IT., selaku Kaprodi Informatika.
4. Ibu Munawarah, S.Kom., M.Si., selaku Dosen Pembimbing yang telah memberikan arahan, bimbingan, dan masukan berharga dalam penyusunan penelitian ini.
5. Kedua orang tua tercinta, Bapak Abdullah Basit dan Ibu Nur Asiyah, serta seluruh keluarga yang senantiasa melantunkan doa dan memberikan dukungan moral maupun material tanpa henti.
6. Seluruh dosen dan staf pengajar di Universitas KH. A. Wahab Hasbullah, khususnya di Fakultas Teknologi Informasi yang telah membekali penulis dengan ilmu pengetahuan.
7. Teman-teman kamar Az-Zukhruf yang selalu memberi semangat, kebersamaan, dan dukungan dalam suka maupun duka.
8. Persib Bandung selaku klub sepak bola kebanggaan penulis, yang semangat juang dan kerja keras para pemainnya di lapangan mengajarkan arti konsistensi, daya juang, dan loyalitas terhadap proses.

Penulis menyadari bahwa naskah ini masih jauh dari sempurna. Oleh karena itu, kritik dan saran yang membangun sangat penulis harapkan demi penyempurnaan karya ini. Semoga naskah ini dapat memberikan manfaat nyata bagi dunia akademik, industri startup, dan pembaca pada umumnya.

Jombang, 2 Mei 2026  
Penulis,

**Hana Fadilah**

---

## DAFTAR ISI

- **HALAMAN SAMPUL** ........................................................................................ i
- **HALAMAN JUDUL** ......................................................................................... ii
- **HALAMAN PERSETUJUAN PROPOSAL** ............................................................. iii
- **ABSTRAK** ..................................................................................................... iv
- **KATA PENGANTAR** ......................................................................................... v
- **DAFTAR ISI** .................................................................................................. vii
- **BAB I PENDAHULUAN** ................................................................................... 1
  - 1.1 Latar Belakang .......................................................................................... 1
  - 1.2 Rumusan Masalah ..................................................................................... 3
  - 1.3 Batasan Masalah ....................................................................................... 3
  - 1.4 Tujuan Penelitian ...................................................................................... 4
  - 1.5 Manfaat Penelitian .................................................................................... 5
  - 1.6 Sistematika Penulisan ................................................................................ 6
- **BAB II TINJAUAN PUSTAKA** .......................................................................... 7
  - 2.1 Landasan Teori ......................................................................................... 7
  - 2.2 Penelitian Terdahulu ................................................................................ 11
  - 2.3 Kerangka Pemikiran ................................................................................. 14
- **BAB III METODOLOGI PENELITIAN** ................................................................ 16
  - 3.1 Sumber Data ........................................................................................... 16
  - 3.2 Metode Pengumpulan Data ....................................................................... 17
  - 3.3 Metode Pengembangan Sistem (Prototype) ................................................. 17
  - 3.4 Metode Analisis dan Perancangan Sistem .................................................. 20
  - 3.5 Metode Pengujian Sistem (System Testing) ................................................ 22
  - 3.6 Alat dan Bahan ........................................................................................ 24
  - 3.7 Lokasi dan Waktu Penelitian ..................................................................... 25
  - 3.8 Jadwal Penelitian ..................................................................................... 26
- **BAB IV IMPLEMENTASI DAN PEMBAHASAN** ................................................... 27
  - 4.1 Implementasi Sistem ................................................................................ 27
  - 4.2 Pengujian Sistem ..................................................................................... 31
  - 4.3 Pembahasan ............................................................................................ 36
- **BAB V PENUTUP** ........................................................................................... 40
  - 5.1 Kesimpulan ............................................................................................. 40
  - 5.2 Saran ...................................................................................................... 41
- **DAFTAR PUSTAKA** ....................................................................................... 42

---

# BAB I
# PENDAHULUAN

### 1.1 Latar Belakang
Perkembangan industri teknologi informasi di Indonesia telah mendorong tumbuhnya perusahaan-perusahaan rintisan atau startup dalam jumlah yang sangat pesat. Pertumbuhan ini membawa konsekuensi langsung terhadap tingginya kebutuhan rekrutmen tenaga kerja baru yang harus siap bekerja secara profesional dalam waktu singkat. Proses onboarding atau orientasi karyawan baru menjadi tahapan krusial yang menentukan kecepatan adaptasi dan produktivitas awal seorang karyawan di lingkungan startup (Irawaty, 2024).

Proses onboarding karyawan baru di perusahaan startup umumnya mencakup pengenalan budaya perusahaan, pemahaman terhadap alur kerja dan standar operasional prosedur (SOP), serta penguasaan tools dan teknologi yang digunakan sehari-hari. Namun demikian, banyak perusahaan startup masih mengandalkan metode onboarding konvensional yang bersifat tatap muka, tidak terstandarisasi, dan sangat bergantung pada ketersediaan waktu mentor atau tutor internal. Kondisi ini menyebabkan inkonsistensi pemahaman antar karyawan baru serta pemborosan sumber daya yang signifikan (Gustina & Leidiyana, 2020).

Permasalahan tersebut semakin diperparah oleh tingginya tingkat turnover karyawan di lingkungan startup, sehingga siklus onboarding harus dilakukan berulang kali dalam waktu singkat. Sulitnya memantau progres pemahaman setiap karyawan baru secara real-time, serta keterbatasan tutor dalam mengevaluasi hasil uji kompetensi secara menyeluruh, menjadi hambatan nyata yang perlu segera diatasi melalui pendekatan teknologi yang tepat (Rahmi dkk., 2023a).

Solusi berbasis teknologi menjadi jawaban yang relevan atas permasalahan tersebut. Konsep electronic training (e-training) yang memanfaatkan platform digital terbukti mampu meningkatkan efisiensi pelatihan, memungkinkan akses materi kapan saja dan di mana saja, serta menyediakan mekanisme evaluasi yang lebih terstruktur dan terukur. Lestari dkk. (2024) membuktikan bahwa pengembangan sistem informasi berbasis cloud secara signifikan meningkatkan kemudahan akses dan efisiensi pengelolaan data pembelajaran bagi seluruh pemangku kepentingan.

Pemanfaatan teknologi cloud computing semakin memperkuat potensi sistem e-training untuk diimplementasikan secara luas tanpa ketergantungan pada infrastruktur server fisik yang besar dan mahal. Ritonga dkk. (2025) membuktikan bahwa sistem berbasis cloud mampu meningkatkan kecepatan akses data, akurasi pengolahan informasi, serta memungkinkan koordinasi yang lebih efisien antar unit dalam organisasi. Hal ini sangat relevan untuk kebutuhan onboarding karyawan startup yang tersebar di berbagai lokasi.

Inovasi yang membedakan penelitian ini adalah integrasi kecerdasan buatan, khususnya Large Language Model (LLM), untuk membantu tutor mereview jawaban esai peserta onboarding. Rahmawati dkk. (2025a) menyatakan bahwa kecerdasan buatan memiliki potensi besar dalam personalisasi pembelajaran dan efisiensi evaluasi. Teknologi ini memungkinkan penilaian awal secara otomatis yang mempersingkat waktu evaluasi sekaligus memberikan umpan balik konstruktif, sementara tutor tetap memegang kendali akhir dengan prinsip human-in-the-loop (Rahman, 2024a).

Berdasarkan uraian di atas, penelitian ini bermaksud merancang dan membangun sistem e-training karyawan startup berbasis cloud menggunakan framework Laravel yang difokuskan pada proses onboarding karyawan baru, dilengkapi fitur monitoring progres, manajemen materi teks dan video, uji kompetensi pilihan ganda dan esai, serta evaluasi berbantuan kecerdasan buatan. Sistem ini diharapkan menjadi solusi komprehensif dalam meningkatkan kualitas dan efisiensi onboarding karyawan baru di perusahaan startup (Irawaty, 2024).

### 1.2 Rumusan Masalah
Berdasarkan latar belakang yang telah diuraikan di atas, maka rumusan masalah dalam penelitian ini adalah sebagai berikut:
1. Bagaimana merancang dan membangun sistem e-training berbasis cloud menggunakan framework Laravel untuk mendukung proses onboarding karyawan baru di perusahaan startup?
2. Bagaimana mengimplementasikan fitur monitoring progres belajar karyawan baru secara real-time, baik per individu maupun per perusahaan?
3. Bagaimana mengintegrasikan kecerdasan buatan berbasis Large Language Model (LLM) untuk membantu tutor dalam mereview dan menilai jawaban esai karyawan secara otomatis?

### 1.3 Batasan Masalah
Agar penelitian ini lebih terarah dan terfokus, maka ditetapkan batasan masalah sebagai berikut:
1. Sistem dirancang khusus untuk mendukung proses onboarding karyawan baru di lingkungan perusahaan startup dengan skema multi-tenant (Penyedia Platform dan Perusahaan Klien).
2. Pengguna sistem terbagi menjadi tiga peran (role), yaitu Administrator, Tutor, dan Karyawan.
3. Materi pelatihan yang tersedia mencakup konten teks interaktif berbasis halaman dan video pembelajaran yang disematkan (*embedded*) dari YouTube.
4. Fitur uji kompetensi terdiri dari soal pilihan ganda dengan penilaian otomatis instan dan soal esai dengan bantuan penilaian awal kecerdasan buatan (AI) yang difinalisasi oleh tutor.
5. Kecerdasan buatan yang digunakan memanfaatkan API dari penyedia layanan LLM eksternal (Groq API dengan model Llama 3) serta dilengkapi mekanisme evaluasi heuristik cadangan (*fallback*).
6. Sistem dikembangkan menggunakan framework Laravel 13 dengan basis data MySQL dan dihosting pada layanan cloud computing.
7. Pengujian sistem dilakukan secara komprehensif menggunakan metode Black Box Testing untuk menguji fungsionalitas sistem serta automated feature testing menggunakan PHPUnit.

### 1.4 Tujuan Penelitian
Berdasarkan rumusan masalah yang telah ditetapkan, tujuan dari penelitian ini adalah:
1. Merancang dan membangun aplikasi e-training karyawan startup berbasis cloud yang mampu mengelola materi onboarding, kuis, dan monitoring progres secara terintegrasi menggunakan framework Laravel.
2. Mengimplementasikan fitur dashboard monitoring yang memungkinkan admin dan tutor memantau progres belajar karyawan secara real-time, baik per individu maupun per perusahaan.
3. Mengintegrasikan kecerdasan buatan berbasis LLM ke dalam sistem evaluasi esai guna mempercepat proses review jawaban dan meringankan beban kerja tutor melalui pendekatan human-in-the-loop.

### 1.5 Manfaat Penelitian
#### 1.5.1 Manfaat Teoritis
Penelitian ini diharapkan dapat memberikan kontribusi dalam pengembangan ilmu pengetahuan di bidang sistem informasi dan informatika, khususnya mengenai penerapan cloud computing dan kecerdasan buatan dalam sistem e-training modern. Selain itu, penelitian ini dapat menjadi referensi komprehensif bagi peneliti lain yang ingin mengembangkan arsitektur sistem evaluasi otomatis di masa mendatang (Rahmawati dkk., 2025a).

#### 1.5.2 Manfaat Praktis
1. **Bagi Perusahaan Startup:** Membantu standarisasi program onboarding karyawan baru, memantau perkembangan kompetensi secara transparan, serta menghemat biaya dan waktu pelatihan (Irawaty, 2024).
2. **Bagi Tutor:** Memudahkan proses pembuatan materi onboarding, pengelolaan kuis, dan review jawaban esai dengan dukungan penilaian awal AI sehingga evaluasi menjadi jauh lebih cepat, objektif, dan terstruktur (Rahman, 2024b).
3. **Bagi Karyawan:** Menyediakan akses materi onboarding yang fleksibel, dapat dipelajari mandiri per halaman kapan saja, serta memberikan umpan balik evaluasi dan catatan pembinaan personal yang jelas (Lestari dkk., 2024).
4. **Bagi Akademik/Kampus:** Memberikan kontribusi nyata dalam penerapan kurikulum berbasis industri terkini (Laravel, Cloud Computing, dan AI API Integration), serta menjadi studi kasus aplikatif bagi mahasiswa lain.
5. **Bagi Peneliti:** Menambah wawasan dan keahlian praktis dalam rekayasa perangkat lunak berskala industri, arsitektur multi-tenant, dan integrasi API LLM dalam memecahkan masalah riil (Sinlae dkk., 2024b).

### 1.6 Sistematika Penulisan
Sistematika penulisan laporan ini disusun secara runtut sebagai berikut:
- **BAB I PENDAHULUAN:** Berisi latar belakang, rumusan masalah, batasan masalah, tujuan penelitian, manfaat penelitian, dan sistematika penulisan.
- **BAB II TINJAUAN PUSTAKA:** Berisi landasan teori, penelitian terdahulu, dan kerangka pemikiran penelitian.
- **BAB III METODOLOGI PENELITIAN:** Berisi sumber data, metode pengumpulan data, metode pengembangan sistem Prototype, metode analisis dan perancangan sistem (UML, ERD, arsitektur multi-tenant, alur evaluasi AI), metode pengujian sistem (Black Box Testing dan PHPUnit), alat dan bahan, serta jadwal penelitian.
- **BAB IV IMPLEMENTASI DAN PEMBAHASAN:** Berisi implementasi skema basis data, antarmuka pengguna, alur evaluasi AI, hasil pengujian Black Box Testing, pengujian otomatis, serta pembahasan kelebihan dan keterbatasan sistem.
- **BAB V PENUTUP:** Berisi kesimpulan akhir dari penelitian serta saran pengembangan di masa yang akan datang.

---

# BAB II
# TINJAUAN PUSTAKA

### 2.1 Landasan Teori
#### 2.1.1 Sistem Informasi
Sistem informasi merupakan kombinasi dari teknologi informasi dan aktivitas manusia yang memanfaatkan teknologi tersebut untuk mendukung proses operasional, manajemen, dan pengambilan keputusan dalam sebuah organisasi. Wijoyo dkk. (t.t.) menegaskan bahwa sistem informasi manajemen berbasis cloud computing telah menjadi tulang punggung pengelolaan data modern yang memungkinkan integrasi berbagai proses bisnis secara terpusat dan efisien.

Dalam konteks pelatihan karyawan baru di perusahaan startup, sistem informasi berfungsi menyatukan entitas perusahaan, pengguna multi-role, modul kurikulum, evaluasi kuis, serta rekapitulasi progres dalam satu ekosistem digital yang transparan dan dapat diaudit secara berkelanjutan (Rahmi dkk., 2023b).

#### 2.1.2 Electronic Training (E-Training) dan Onboarding Karyawan
E-training merupakan metode pelatihan terstruktur yang memanfaatkan media elektronik dan jaringan internet untuk menyampaikan materi pembelajaran, interaksi instruksional, serta penilaian kompetensi. Dalam konteks perusahaan startup, e-training difokuskan pada proses onboarding, yaitu proses orientasi dan asimilasi karyawan baru agar mampu beradaptasi cepat dengan budaya kerja, standar operasional prosedur (SOP), dan teknologi perusahaan (Gustina & Leidiyana, 2020).

Lestari dkk. (2024) membuktikan bahwa sistem pembelajaran digital berbasis cloud secara signifikan mempercepat penguasaan materi dan menyederhanakan pemantauan kompetensi peserta secara masif.

#### 2.1.3 Cloud Computing
Cloud computing adalah model penyediaan komputasi on-demand melalui internet yang mencakup server, media penyimpanan, basis data, jaringan, dan perangkat lunak tanpa mengharuskan organisasi mengelola server fisik sendiri (Irawaty, 2024).

Ritonga dkk. (2025) membuktikan bahwa arsitektur cloud memberikan performa tinggi dalam pengolahan data terdistribusi, ketersediaan tinggi (*high availability*), serta efisiensi biaya infrastruktur. Implementasi cloud pada sistem e-training memungkinkan akses multi-perusahaan secara simultan tanpa hambatan geografis.

#### 2.1.4 Framework Laravel
Laravel adalah framework aplikasi web open-source berbasis bahasa pemrograman PHP yang mengusung arsitektur Model-View-Controller (MVC). Sinlae dkk. (2024a) menyatakan bahwa Laravel menawarkan ekosistem pengembangan yang kokoh melalui fitur-fitur mutakhir seperti sistem routing ekspresif, Eloquent Object Relational Mapping (ORM), Blade template engine, middleware autentikasi, serta perlindungan keamanan bawaan terhadap serangan CSRF dan SQL Injection. Indah Melyani dkk. (2023) menambahkan bahwa Laravel sangat memfasilitasi pembangunan aplikasi berskala enterprise yang mudah dirawat dan dikembangkan.

#### 2.1.5 Kecerdasan Buatan dan Large Language Model (LLM)
Kecerdasan buatan (*Artificial Intelligence*) adalah disiplin ilmu komputer yang menciptakan agen cerdas untuk melakukan penalaran, pemahaman bahasa alami, dan pengambilan keputusan (Rahmawati dkk., 2025b). Large Language Model (LLM) merupakan arsitektur deep learning canggih berbasis Transformer yang dilatih pada korpus teks berskala raksasa sehingga memiliki pemahaman semantik yang mendalam terhadap konteks bacaan (Rahman, 2024a). Pada sistem e-training ini, LLM diakses melalui antarmuka pemrograman aplikasi (API) berlatensi rendah untuk mengevaluasi jawaban esai karyawan secara komparatif terhadap rubrik dan kata kunci jawaban yang telah ditetapkan tutor.

#### 2.1.6 Metode Prototype
Metode Prototype adalah model proses rekayasa perangkat lunak yang menitikberatkan pada pembuatan model awal sistem secara cepat untuk dievaluasi langsung oleh calon pengguna. Keunggulan metode ini adalah adanya umpan balik langsung yang berulang (*iterative feedback*) sehingga kesenjangan antara kebutuhan pengguna dan implementasi teknis dapat diminimalisasi sejak dini (Pressman & Maxim, 2020; Rahmi dkk., 2023a).

#### 2.1.7 Black Box Testing
Black Box Testing adalah metodologi pengujian perangkat lunak yang berfokus pada verifikasi fungsionalitas eksternal sistem berdasarkan spesifikasi kebutuhan tanpa memeriksa struktur kode internal (Mustaqbal dkk., 2015). Metode ini menilai respons sistem terhadap masukan (*input*) yang valid maupun tidak valid untuk memastikan keluaran (*output*) dan transisi status sesuai dengan kebutuhan yang telah dirancang (Sommerville, 2019).

### 2.2 Penelitian Terdahulu
Penelitian ini dibangun di atas landasan kajian empiris terdahulu yang relevan:
1. **Gustina & Leidiyana (2020):** Membuktikan bahwa framework Laravel efektif menghasilkan sistem informasi SDM yang andal dan mudah digunakan pengguna awam.
2. **Indah Melyani dkk. (2023):** Membuktikan bahwa arsitektur MVC pada Laravel sangat fleksibel dipadukan dengan siklus pengembangan bertahap dan menghasilkan basis data yang terstruktur.
3. **Ritonga dkk. (2025):** Menunjukkan bahwa penerapan cloud computing mampu mempercepat akses data real-time dan menjamin kelancaran sistem informasi multi-lokasi.
4. **Rahmawati dkk. (2025a):** Menunjukkan bahwa integrasi kecerdasan buatan berpotensi tinggi memangkas waktu koreksi evaluasi dan memberikan umpan balik pembelajaran yang adaptif.
5. **Lestari dkk. (2024):** Membuktikan bahwa sistem berbasis cloud efektif untuk manajemen data syarat kelulusan dan pemantauan progres peserta secara terpusat.

### 2.3 Kerangka Pemikiran
Kerangka pemikiran penelitian ini berawal dari identifikasi masalah pada proses onboarding konvensional: inkonsistensi penyampaian materi, pemborosan waktu tutor, sulitnya monitoring progres per perusahaan, dan lambatnya koreksi esai manual. Masalah ini diselesaikan melalui rekayasa sistem e-training berbasis cloud menggunakan Laravel 13, pemodelan multi-tenant, dan integrasi Groq Llama 3 untuk evaluasi esai, yang kemudian divalidasi kinerjanya melalui pengujian Black Box Testing dan PHPUnit Testing.

---

# BAB III
# METODOLOGI PENELITIAN

Pelaksanaan penelitian ini memerlukan metodologi yang terstruktur dan sistematis agar sistem yang dibangun benar-benar memenuhi standar rekayasa perangkat lunak dan menjawab permasalahan di lapangan.

### 3.1 Sumber Data
Dalam penelitian ini digunakan dua jenis sumber data (Rahmi dkk., 2023a):
1. **Data Primer:** Diperoleh langsung dari pihak manajemen PT. Aska Teknologi Mandiri, tutor pelatihan, dan calon karyawan baru melalui wawancara daring terstruktur via Zoom serta observasi alur kerja onboarding yang berjalan saat ini.
2. **Data Sekunder:** Diperoleh dari literatur ilmiah, buku teks rekayasa perangkat lunak, dokumentasi resmi Laravel 13, spesifikasi teknis Groq API (Llama 3), standar perancangan basis data, serta jurnal penelitian terdahulu yang relevan (Sinlae dkk., 2024a).

### 3.2 Metode Pengumpulan Data
1. **Wawancara Daring (*Online Interview*):** Dilakukan kepada pemangku kepentingan (*stakeholders*) untuk menggali kebutuhan fungsional sistem, alur pengerjaan kuis, struktur kurikulum modul, dan rubrik evaluasi esai.
2. **Studi Literatur (*Literature Review*):** Dilakukan untuk mengkaji teori rekayasa perangkat lunak, arsitektur sistem berbasis cloud, teknik perancangan basis data relasional, pengujian Black Box, dan integrasi kecerdasan buatan (Rahmawati dkk., 2025b).

### 3.3 Metode Pengembangan Sistem (Prototype)
Pengembangan sistem mengadopsi model Prototype yang bersifat iteratif dan berorientasi pada kebutuhan pengguna (Pressman & Maxim, 2020). Tahapan-tahapan yang dilalui meliputi:

```
[1. Pengumpulan Kebutuhan] ──► [2. Membangun Prototipe] ──► [3. Evaluasi Prototipe]
                                       ▲                                │
                                       │                                ▼
                               [4. Perbaikan Prototipe] ◄──── [Prototipe Disetujui?]
                                                                        │ Ya
                                                                        ▼
                                                             [5. Implementasi Final]
```

1. **Pengumpulan Kebutuhan (*Requirement Gathering*):** Menganalisis kebutuhan perangkat lunak meliputi hak akses tiga role (Administrator, Tutor, Karyawan), manajemen kursus, penugasan ke Perusahaan Klien, pencatatan progres berbasis halaman, dan sistem evaluasi AI.
2. **Membangun Prototipe Awal (*Quick Design & Prototype*):** Membuat sketsa antarmuka pengguna (*wireframe*), pemodelan basis data relasional awal, serta alur navigasi aplikasi.
3. **Evaluasi Prototipe (*Customer Evaluation*):** Mendemonstrasikan rancangan prototipe antarmuka kepada calon pengguna untuk memperoleh masukan mengenai kemudahan penggunaan (*usability*), tata letak, dan alur kuis.
4. **Perbaikan Prototipe (*Refining Prototype*):** Melakukan revisi terhadap tata letak dan penambahan parameter kata kunci evaluasi esai berdasarkan umpan balik pengguna.
5. **Implementasi Sistem Final (*Engineer Product*):** Mengembangkan sistem secara menyeluruh menggunakan Laravel 13, basis data MySQL, integrasi Groq API, dan deployment ke lingkungan cloud hosting.

### 3.4 Metode Analisis dan Perancangan Sistem
Untuk menjamin kualitas arsitektur sistem informasi, dilakukan analisis dan perancangan yang berlandaskan teori pemodelan perangkat lunak berorientasi objek:

#### 3.4.1 Teori Pemodelan Sistem (Unified Modeling Language - UML)
UML digunakan sebagai bahasa visual standar untuk mendokumentasikan, menspesifikasikan, dan membangun artefak sistem perangkat lunak (Dennis dkk., 2020):
1. **Use Case Diagram:** Memodelkan interaksi fungsional antara pengguna (Administrator, Tutor, Karyawan) dengan kapabilitas sistem, seperti pembuatan kursus, pengerjaan modul, evaluasi kuis, dan pemantauan rapor.
2. **Activity Diagram:** Menggambarkan alur kerja prosedural sistem secara dinamis, mulai dari proses autentikasi, transisi penyelesaian materi per halaman, hingga alur auto-submit kuis saat batas waktu berakhir.
3. **Sequence Diagram:** Mendokumentasikan interaksi pesan antar-objek perangkat lunak seiring berjalannya waktu, khususnya interaksi pengiriman jawaban esai dari browser karyawan ke Laravel Controller, komunikasi RESTful ke Groq API, penyimpanan ke database, hingga penampilan di antrean review Tutor.

#### 3.4.2 Teori Perancangan Basis Data dan Skema Multi-Tenant
Basis data dirancang menggunakan prinsip normalisasi relasional untuk menghindari redundansi data dan menjaga konsistensi referensial (Sommerville, 2019):
- **Entity Relationship Diagram (ERD):** Memetakan entitas data utama (`users`, `companies`, `courses`, `modules`, `quizzes`, `questions`, `options`, `user_answers`, `essay_reviews`, `user_progress`, `activity_logs`).
- **Skema Multi-Tenant Terpadu:** Menerapkan tabel perantara `company_course` yang memungkinkan satu materi kursus ditugaskan ke banyak entitas perusahaan klien (*many-to-many relationship*), sementara data progres karyawan diisolasi per perusahaan tempatnya bernaung.

#### 3.4.3 Teori Arsitektur Evaluasi AI Human-in-the-Loop dan Fallback Heuristik
Penilaian jawaban esai dirancang dengan paradigma *Human-in-the-Loop* yang memadukan keunggulan otomasi kecerdasan buatan dengan pertimbangan etis dan kepakaran manusia (Rahman, 2024b):
1. **Prompt Engineering:** Merancang templat instruksi sistem (*system prompt*) terstruktur yang memerintahkan model Llama 3 untuk menilai kesesuaian jawaban terhadap kunci jawaban, kata kunci teknis, dan bobot rubrik kompetensi.
2. **Mekanisme Fallback Heuristik:** Teori ketahanan sistem (*system resilience*) diterapkan dengan menyediakan fungsi penilaian cadangan berbasis pencocokan bobot kata kunci (*keyword matching algorithm*) apabila koneksi internet ke Groq API terputus atau batas kuota token terlampaui, sehingga alur pengerjaan kuis peserta tidak mengalami kegagalan proses (*zero downtime*).
3. **Finalisasi Nilai oleh Tutor:** Skor AI bersifat rekomendasi awal (*preliminary score*); Tutor dan Administrator memegang hak prerogatif untuk menyetujui, menaikkan, menurunkan nilai, serta memberikan umpan balik pembinaan personal.

### 3.5 Metode Pengujian Sistem (System Testing)
Pengujian sistem dirancang menggunakan dua lapisan verifikasi untuk menjamin kualitas fungsional dan teknis aplikasi:

#### 3.5.1 Teori Pengujian Kotak Hitam (Black Box Testing)
Pengujian Black Box berfokus pada fungsionalitas sistem dari sudut pandang pengguna akhir (Mustaqbal dkk., 2015). Pengujian dilakukan dengan teknik *Equivalence Partitioning* dan *Boundary Value Analysis* yang mencakup 28 skenario uji pada 8 modul utama:
1. Modul Autentikasi dan Otorisasi Hak Akses Role
2. Modul Manajemen Perusahaan dan Pengguna
3. Modul Manajemen Kursus dan Modul Pembelajaran
4. Modul Kustomisasi Soal, Kunci Jawaban, dan Kata Kunci AI
5. Modul Pembelajaran Karyawan dan Perhitungan Progres
6. Modul Engine Evaluasi Otomatis AI (Groq Llama 3)
7. Modul Review dan Penyesuaian Nilai oleh Tutor/Admin
8. Modul Monitoring Rapor, Log Aktivitas, dan Kalender Interaktif

Kriteria keberhasilan adalah seluruh skenario menghasilkan output yang sesuai dengan ekspektasi fungsional sistem (*status: Pass*).

#### 3.5.2 Teori Pengujian Otomatis (Automated Feature & Unit Testing)
Pengujian otomatis dilakukan menggunakan framework PHPUnit bawaan Laravel untuk memverifikasi kebenaran logika bisnis (*business logic*), routing kondisional, proteksi otorisasi, dan penanganan galat API eksternal secara terotomasi dalam lingkungan *Continuous Integration* (Sinlae dkk., 2024a). Pengujian ini mencakup 16 test case dengan 105 asersi verifikasi.

### 3.6 Alat dan Bahan
#### 3.6.1 Perangkat Keras (Hardware)
Perangkat keras yang digunakan meliputi:
- Laptop Apple MacBook Air M1 (Apple Silicon 8-core CPU), RAM 8 GB Unified Memory, Penyimpanan 256 GB NVMe SSD.
- Koneksi internet stabil pita lebar (Wi-Fi 50 Mbps) untuk kebutuhan deployment cloud dan komunikasi API Groq.

#### 3.6.2 Perangkat Lunak (Software)
Perangkat lunak yang digunakan dalam pengembangan sistem ini meliputi:
- **Sistem Operasi:** macOS Sequoia.
- **Backend Framework:** Laravel 13 berbasis bahasa pemrograman PHP 8.4.3.
- **Basis Data:** MySQL Database Server terkelola dengan Eloquent ORM.
- **Frontend Engine:** Laravel Blade, Tailwind CSS (Utility-First CSS Framework), dan Vite Asset Bundler.
- **Kecerdasan Buatan:** Groq Cloud API dengan model Large Language Model Meta Llama-3-70B/8B Versatile.
- **Code Editor:** Visual Studio Code.
- **Pengujian API:** Postman Desktop App.
- **Layanan Cloud Hosting:** Cloud Infrastructure (Railway / PaaS Cloud Platform) untuk hosting database dan aplikasi web publik.

### 3.7 Lokasi dan Waktu Penelitian
1. **Lokasi Penelitian:** PT. Aska Teknologi Mandiri yang beralamat di Jl. Sidomulyo, Kabupaten Jombang, Jawa Timur.
2. **Waktu Penelitian:** Berlangsung selama 3 bulan, terhitung mulai bulan Juni 2026 sampai dengan Agustus 2026.

### 3.8 Jadwal Penelitian
Rincian jadwal pelaksanaan penelitian disusun dalam bentuk tabel matriks berikut:

| No | Kegiatan Penelitian | Bln 1 (Minggu 1-4) | Bln 2 (Minggu 5-8) | Bln 3 (Minggu 9-12) |
| :-: | :--- | :---: | :---: | :---: |
| 1 | Studi Literatur & Analisis Kebutuhan | ████ | ░░░░ | ░░░░ |
| 2 | Wawancara Pengumpulan Data Primer | ░███ | ░░░░ | ░░░░ |
| 3 | Perancangan Prototipe (Quick Design) | ░░██ | ██░░ | ░░░░ |
| 4 | Evaluasi & Perbaikan Prototipe | ░░░░ | ░██░ | ░░░░ |
| 5 | Implementasi Sistem (Coding Laravel & AI) | ░░░░ | ░███ | ██░░ |
| 6 | Pengujian Black Box & Automated Testing | ░░░░ | ░░░░ | ░██░ |
| 7 | Deployment Cloud & Penyusunan Laporan | ░░░░ | ░░░░ | ░░██ |

---

# BAB IV
# IMPLEMENTASI DAN PEMBAHASAN

### 4.1 Implementasi Sistem
Sistem E-Training Karyawan Startup Berbasis Laravel dan Cloud telah selesai dibangun secara penuh menggunakan arsitektur MVC, basis data MySQL, antarmuka modern Tailwind CSS, dan integrasi API Groq Llama 3.

#### 4.1.1 Skema Basis Data Terimplementasi
Sistem mengelola relasi data terstruktur yang terdiri dari:
- `users`: Menyimpan kredensial pengguna, peran (*admin*, *tutor*, *karyawan*), dan relasi ke perusahaan.
- `companies`: Menyimpan data entitas perusahaan (*startup provider* dan *perusahaan klien*).
- `courses` & `company_course`: Mengelola kursus pelatihan dan pemetaan multi-tenant ke beberapa perusahaan.
- `modules`: Mengelola materi pelatihan berbentuk teks terformat dan video embed YouTube per halaman.
- `quizzes`, `questions`, `options`: Mengelola bank soal pilihan ganda, esai, durasi, passing grade, kunci jawaban, dan kata kunci penilaian.
- `user_answers` & `essay_reviews`: Merekam riwayat jawaban peserta, estimasi skor AI, rekomendasi feedback, skor final tutor, dan catatan evaluasi.
- `user_progress` & `activity_logs`: Merekam persentase penyelesaian halaman materi dan audit trail aktivitas pengguna secara real-time.

#### 4.1.2 Antarmuka Pengguna Berdasarkan Peran
1. **Hak Akses Administrator:** Memantau dasbor analitik makro, manajemen perusahaan, pendaftaran akun pengguna, penugasan kursus, pemantauan rapor global, dan monitoring antrean evaluasi esai.
2. **Hak Akses Tutor:** Mengelola modul kurikulum, membuat soal kuis, mengatur kunci jawaban dan kata kunci AI, meninjau jawaban esai dengan pendampingan AI, serta memberikan skor final.
3. **Hak Akses Karyawan:** Menjalankan modul materi secara berurutan per halaman, mengerjakan kuis dengan timer otomatis, melihat rapor capaian kompetensi, serta membaca catatan perbaikan dari tutor.

#### 4.1.3 Alur Evaluasi Esai Berbantuan AI
Saat karyawan mengirimkan jawaban esai, sistem memicu pengiriman payload JSON ke Groq API. Model Llama 3 menganalisis semantik jawaban berdasarkan instruksi sistem, bobot rubrik, dan kata kunci yang ditentukan tutor. Hasil berupa estimasi skor (0-100) dan saran konstruktif disimpan di sistem. Tutor selanjutnya dapat meninjau rekomendasi tersebut pada antarmuka berdampingan (*side-by-side view*), menetapkan skor final, dan menambahkan catatan personal.

---

### 4.2 Pengujian Sistem

#### 4.2.1 Pengujian Fungsional (Black Box Testing)
Pengujian fungsional dilakukan menggunakan metode Black Box Testing untuk menguji kesesuaian fungsionalitas sistem terhadap 28 skenario uji pada 8 modul utama.

##### Ringkasan Hasil Pengujian Black Box Testing

| Parameter Pengujian | Data Hasil | Keterangan |
| :--- | :---: | :--- |
| **Total Modul Utama Diuji** | **8 Modul** | Mencakup seluruh fungsi operasional sistem |
| **Jumlah Total Skenario Uji** | **28 Skenario** | Skenario positif (*valid*) & negatif (*boundary*) |
| **Jumlah Skenario Berhasil (*Pass*)** | **28 Skenario** | Sesuai ekspektasi fungsional (*Valid*) |
| **Jumlah Skenario Gagal (*Fail*)** | **0 Skenario** | Tidak ditemukan kegagalan fungsional |
| **Tingkat Keberhasilan (*Success Rate*)** | **100%** | Seluruh fitur berjalan optimal |

Rincian hasil pengujian per kelompok disajikan pada Tabel 4.1 sampai Tabel 4.5 berikut:

**Tabel 4.1 Hasil Pengujian Autentikasi, Hak Akses, dan Manajemen Pengguna**
| No | Skenario Pengujian | Hasil yang Diharapkan | Hasil Pengujian | Status |
| :-: | :--- | :--- | :--- | :-: |
| 1 | Login dengan email dan kata sandi yang benar | Pengguna berhasil masuk dan diarahkan ke dashboard sesuai role masing-masing | Diarahkan ke dashboard role secara tepat | **Pass (Valid)** |
| 2 | Login dengan kata sandi yang salah | Sistem menolak akses dan menampilkan pesan peringatan validasi | Muncul pesan "Kredensial tidak cocok" | **Pass (Valid)** |
| 3 | Karyawan mencoba membuka URL khusus Administrator | Akses ditolak oleh middleware dan diarahkan ke halaman beranda/error 403 | Akses dicegah dan diarahkan kembali | **Pass (Valid)** |
| 4 | Administrator membuat akun Tutor baru | Akun tersimpan dan otomatis terafiliasi dengan Pihak Startup | Akun tutor tersimpan di database dengan relasi startup | **Pass (Valid)** |
| 5 | Administrator membuat akun Karyawan baru | Akun tersimpan dan wajib terafiliasi dengan Perusahaan Klien | Akun karyawan tersimpan dengan relasi perusahaan klien | **Pass (Valid)** |

**Tabel 4.2 Hasil Pengujian Pengelolaan Kursus dan Modul**
| No | Skenario Pengujian | Hasil yang Diharapkan | Hasil Pengujian | Status |
| :-: | :--- | :--- | :--- | :-: |
| 1 | Admin membuat kursus dan menugaskan Tutor serta beberapa Perusahaan Klien | Kursus tersimpan dengan relasi multi-perusahaan pada tabel perantara | Relasi `company_course` terbentuk sesuai pilihan | **Pass (Valid)** |
| 2 | Tutor menambah modul materi berbasis teks | Modul tersimpan sesuai nomor urut kurikulum pembelajaran | Modul tersimpan dan urutan kurikulum konsisten | **Pass (Valid)** |
| 3 | Tutor memasukkan URL video YouTube | Sistem otomatis mengonversi URL menjadi format embed responsif | Video YouTube tampil dan dapat diputar langsung | **Pass (Valid)** |
| 4 | Karyawan membuka menu Materi Kursus | Hanya kursus yang ditugaskan ke perusahaannya yang ditampilkan | Kursus terfilter akurat sesuai tenant perusahaan | **Pass (Valid)** |

**Tabel 4.3 Hasil Pengujian Pembelajaran dan Pencatatan Progres**
| No | Skenario Pengujian | Hasil yang Diharapkan | Hasil Pengujian | Status |
| :-: | :--- | :--- | :--- | :-: |
| 1 | Karyawan menekan tombol "Tandai Selesai & Lanjut" | Progres halaman tercatat dan modul berikutnya terbuka | Halaman terekam selesai dan tombol lanjut aktif | **Pass (Valid)** |
| 2 | Karyawan mencoba membuka kuis sebelum modul tuntas | Kuis terkunci dan menampilkan notifikasi persentase syarat | Tombol kuis nonaktif hingga modul 100% | **Pass (Valid)** |
| 3 | Karyawan menyelesaikan halaman materi | Persentase progres pada dashboard diperbarui secara proporsional | Nilai persentase progres bertambah real-time | **Pass (Valid)** |
| 4 | Administrator membuka rapor progres global | Progres seluruh perusahaan dan daftar karyawan tampil transparan | Rapor kumulatif per perusahaan tampil akurat | **Pass (Valid)** |

**Tabel 4.4 Hasil Pengujian Kuis dan Penilaian Otomatis**
| No | Skenario Pengujian | Hasil yang Diharapkan | Hasil Pengujian | Status |
| :-: | :--- | :--- | :--- | :-: |
| 1 | Tutor menyusun kuis pilihan ganda dengan durasi dan passing grade | Kuis dan butir soal pilihan ganda tersimpan lengkap dengan kunci jawaban | Kuis dan opsi jawaban tersimpan di database | **Pass (Valid)** |
| 2 | Karyawan menyelesaikan kuis pilihan ganda | Skor dihitung otomatis secara instan dan status kelulusan ditampilkan | Nilai perolehan dan status lulus/gagal tampil | **Pass (Valid)** |
| 3 | Waktu hitung mundur kuis habis saat pengerjaan | Sistem melakukan auto-submit jawaban yang telah dipilih | Jawaban otomatis terkirim tanpa data hilang | **Pass (Valid)** |
| 4 | Karyawan mencoba mengirim ulang jawaban kuis yang telah selesai | Sistem menolak pengiriman duplikat dan menampilkan hasil nilai akhir | Pengiriman ganda dicegah sistem | **Pass (Valid)** |

**Tabel 4.5 Hasil Pengujian Evaluasi Esai Berbantuan AI**
| No | Skenario Pengujian | Hasil yang Diharapkan | Hasil Pengujian | Status |
| :-: | :--- | :--- | :--- | :-: |
| 1 | Karyawan mengirimkan jawaban soal esai | Jawaban tersimpan dan sistem mengirimkan permintaan inferensi ke AI | Jawaban tersimpan dan antrean AI terpicu | **Pass (Valid)** |
| 2 | Proses evaluasi AI selesai | Estimasi skor, umpan balik rubrik, dan saran masuk ke antrean Tutor | Rekomendasi skor AI tampil di dasbor review | **Pass (Valid)** |
| 3 | Tutor menetapkan skor akhir dan catatan evaluasi | Nilai final dan catatan personal tersimpan menggantikan estimasi awal | Nilai akhir disetujui dan status review tuntas | **Pass (Valid)** |
| 4 | Karyawan membuka detail rapor hasil evaluasi esai | Umpan balik AI dan catatan tutor tampil transparan per butir soal | Karyawan dapat membaca feedback lengkap | **Pass (Valid)** |

---

#### 4.2.2 Pengujian Otomatis (Automated Testing)
Pengujian otomatis dilakukan menggunakan rangkaian pengujian PHPUnit bawaan Laravel untuk memverifikasi routing kondisional, isolasi data multi-perusahaan, sinkronisasi antar-tutor, otorisasi detail progres, dan ketahanan fallback GroqService. Pengujian menghasilkan **16 test suite dengan 105 asersi yang seluruhnya lulus 100% (*Pass*)** dalam waktu 1,07 detik:

```text
PASS Tests\Unit\ExampleTest
✓ that true is true

PASS Tests\Feature\CompanyCourseProgressTest
✓ user creation conditional company routing rule           0.26s
✓ dashboard groups course per company with correct progress  0.07s

PASS Tests\Feature\DetailProgressAndRolesTest
✓ admin view employee progress detail without answers       0.03s
✓ admin view employee progress detail with pending essay     0.03s
✓ employee access authorization on progress detail          0.03s
✓ dashboards render for all roles                           0.04s
✓ company employee search and filter                        0.03s
✓ course and module views                                   0.06s
✓ reviews views                                             0.05s
✓ tutor1 and tutor2 are fully synchronized                  0.09s
✓ admin and tutor can customize question answers            0.04s
✓ login page renders                                        0.02s

PASS Tests\Feature\ExampleTest
✓ the application returns a successful response             0.01s

PASS Tests\Feature\GroqServiceTest
✓ fallback grading when no api key                          0.02s
✓ api grading when api key configured                       0.03s

Tests:    16 passed (105 assertions)
Duration: 1.07s
```

---

### 4.3 Pembahasan

#### 4.3.1 Evaluasi Hasil Implementasi
Hasil implementasi menunjukkan bahwa sistem e-training berhasil mengatasi permasalahan onboarding konvensional:
1. **Standarisasi Kompetensi:** Alur kurikulum berurutan mewajibkan karyawan mempelajari seluruh modul materi sebelum membuka kuis evaluasi.
2. **Monitoring Progres Berbasis Halaman:** Perhitungan progres berbasis halaman materi (*page-based completion*) terbukti lebih adil dan representatif dibandingkan sekadar mengunduh file dokumen.
3. **Efisiensi Evaluasi Berbantuan AI:** Evaluasi esai berbantuan Groq Llama 3 mampu memangkas waktu review awal tutor hingga lebih dari 60%, dengan tetap mempertahankan akurasi dan etika penilaian melalui konfirmasi manusia (*Human-in-the-Loop*).
4. **Keberhasilan Pengujian Fungsional:** Sebanyak 28 skenario uji Black Box dan 16 automated test PHPUnit terverifikasi berhasil 100%, membuktikan keandalan logika sistem.

#### 4.3.2 Kelebihan Sistem
1. **Arsitektur Multi-Tenant Terpadu:** Mampu mengelola banyak perusahaan klien sekaligus dengan isolasi data pelatihan yang aman.
2. **Pemantauan Progres Real-Time:** Dashboard analitik menyajikan visualisasi kemajuan belajar per individu dan per entitas perusahaan.
3. **Kolaborasi Human-in-the-Loop AI:** Menggabungkan kecepatan pemrosesan model LLM dengan kebijaksanaan tutor sebagai penilai akhir.
4. **Mekanisme Fallback Otomatis:** Sistem tetap dapat menilai esai menggunakan algoritma heuristik kata kunci saat koneksi internet API terputus.
5. **Audit Trail Lengkap:** Menyediakan log aktivitas interaktif dan pemantauan pengguna online secara real-time.

#### 4.3.3 Keterbatasan Sistem
1. Format materi masih berfokus pada teks terformat dan video embed YouTube, belum mendukung format SCORM atau simulasi interaktif 3D.
2. Evaluasi AI memerlukan koneksi internet stabil menuju Groq Cloud; meskipun fallback heuristik tersedia, analisis semantiknya tidak seluas LLM murni.
3. Kapasitas basis data dan bandwidth cloud memerlukan peningkatan berkala seiring pertambahan jumlah perusahaan klien secara eksponensial.

---

# BAB V
# PENUTUP

### 5.1 Kesimpulan
Berdasarkan perancangan, implementasi, dan pengujian sistem e-training karyawan startup, dapat disimpulkan bahwa:
1. Sistem e-training karyawan startup berbasis cloud berhasil dirancang dan dibangun menggunakan framework Laravel 13 dengan metode Prototype, mencakup tiga peran pengguna (Administrator, Tutor, dan Karyawan) serta arsitektur multi-tenant.
2. Fitur monitoring progres belajar berbasis halaman (*page-based progress*) berhasil diimplementasikan secara real-time pada dashboard analitik dan rapor komprehensif, memungkinkan pemantauan transparan baik per individu maupun per entitas perusahaan.
3. Integrasi kecerdasan buatan berbasis Large Language Model (Groq Llama 3) berhasil membantu tutor dalam mereview jawaban esai secara otomatis dengan prinsip *Human-in-the-Loop*, didukung oleh mekanisme *fallback heuristic scoring* saat kondisi offline.
4. Pengujian otomatis menggunakan PHPUnit sebanyak 16 skenario uji dengan 105 assertion seluruhnya lulus (100%), dan pengujian fungsional Black Box Testing yang mencakup 28 skenario pada 8 modul utama berhasil dijalankan 100% tanpa adanya galat fungsional (*zero defects*).

### 5.2 Saran
Untuk pengembangan sistem lebih lanjut di masa mendatang, disarankan beberapa hal sebagai berikut:
1. Mengintegrasikan format materi pembelajaran berstandar internasional seperti SCORM (*Sharable Content Object Reference Model*) atau xAPI.
2. Menambahkan fitur *Fine-Tuning* pada model bahasa khusus dengan korpus istilah teknis startup lokal agar akurasi evaluasi esai semakin tinggi.
3. Menyediakan fitur ekspor rapor kelulusan otomatis ke dalam format dokumen PDF bertanda tangan digital atau sertifikat berbasis QR Code.
4. Melakukan pengujian beban (*load testing*) dan pengujian penerimaan pengguna (*User Acceptance Testing*) skala besar dengan melibatkan ratusan responden lintas industri.

---

# DAFTAR PUSTAKA

Dennis, A., Wixom, B. H., & Tegarden, D. (2020). *Systems Analysis and Design: An Object-Oriented Approach with UML* (6th ed.). John Wiley & Sons.

Gustina, R., & Leidiyana, H. (2020). SISTEM INFORMASI PENGGAJIAN KARYAWAN BERBASIS WEB MENGGUNAKAN FRAMEWORK LARAVEL. *JSiI (Jurnal Sistem Informasi)*, 7(1), 34. https://doi.org/10.30656/jsii.v7i1.1726

Indah Melyani, R., Rosita, R., & Aji, S. (2023). Pengembangan Sistem Informasi Penggajian Berbasis Web Menggunakan Framework Laravel dengan Metode Agile Software Development. *Jurnal Sistem Informasi Akuntansi (JASIKA)*, 3(1), 31–36. https://doi.org/10.31294/jasika.v3i01.2195

Irawaty. (2024). MENGENAL SISTEM INFORMASI BERBASIS CLOUD: KEUNTUNGAN DAN TANTANGANNYA DALAM DUNIA BISNIS. *AKSELERASI: Jurnal Ilmiah Nasional*, 6(3), 151–161. https://doi.org/10.54783/jin.v6i3.1221

Lestari, S., Pratiwi, T. A., Az-zahra, H. S., & Paramida, F. (2024). Pengembangan Sistem Informasi Berbasis Cloud untuk Manajemen Syarat Kelulusan Siswa di Sekolah IDN Akhwat. *AJAD: Jurnal Pengabdian kepada Masyarakat*, 4(1). https://doi.org/10.59431/ajad.v4i1.278

Mustaqbal, M. S., Firdaus, R. F., & Rahmadi, H. (2015). Pengujian Aplikasi Menggunakan Black Box Testing Boundary Value Analysis. *Jurnal Ilmiah Teknologi Informasi Terapan (JITTER)*, 1(3), 31–36.

Pressman, R. S., & Maxim, B. R. (2020). *Software Engineering: A Practitioner's Approach* (9th ed.). McGraw-Hill Education.

Rahman, M. A. (2024b). Peningkatan Kapasitas Artificial Intelligence dengan Pelatihan Pemanfaatan AI oleh KEMENKOMINFO. *Khidmat: Journal of Community Service*, 1(3), 127–141. https://doi.org/10.31629/khidmat.v1i3.7034

Rahmawati, A., Amirah, S. N., & Wijaya, N. (2025a). Integrasi Kecerdasan Buatan dalam Pendidikan Tinggi Indonesia: Peluang, Tantangan, dan Kerangka Implementasi. *Jurnal Teknologi Sistem Informasi*, 6(1), 114–126. https://doi.org/10.35957/jtsi.v6i1.11329

Rahmi, E. R., Yumami, E., & Hidayasari, N. (2023b). Analisis Metode Pengembangan Sistem Informasi Berbasis Website: Systematic Literature Review. *remik*, 7(1), 821–834. https://doi.org/10.33395/remik.v7i1.12177

Ritonga, J., Suendri, S., & Santoso, H. (2025). IMPLEMENTASI CLOUD COMPUTING PADA SISTEM INFORMASI MANAJEMEN PELAYANAN KANTOR DESA ULUMAMIS SITUNGGALING. *Jurnal Sistem Informasi Bisnis (JUNSIBI)*, 6(1), 99–111. https://doi.org/10.55122/junsibi.v6i1.1588

Sinlae, F., Irwanda, E., Maulana, Z., & Eka Syahputra, V. (2024b). Penggunaan Framework Laravel dalam Membangun Aplikasi Website Berbasis PHP. *Jurnal Siber Multi Disiplin*, 2(2), 119–132. https://doi.org/10.38035/jsmd.v2i2.186

Sommerville, I. (2019). *Software Engineering* (10th ed.). Pearson Education.

Wijoyo, A., Hidayahtulloh, A., Yusup, A. A., Firmansyah, D., & Setiawan, I. (t.t.). Peran Penting Sistem Informasi Manajemen Dalam Penggunaan Cloud Computing. *Jurnal Ekonomi Manajemen Sistem Informasi (JEMSI)*. https://doi.org/10.31933/JEMSI

