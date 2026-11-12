4.2. Praktikum CRUD Master Data
1. CRUD Departemen
Create Migration tabel & model

Copy
php artisan make:model Departemen -m
Edit File Migration
Buka file migration table Departemen


Copy
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departemens', function (Blueprint $table) {
            $table->id();
            $table->string('nama_departemen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departemens');
    }
};
Edit file Model
Buka file Model Departemen


Copy
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departemen extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_departemen',
    ];

    /**
     * Departemen memiliki banyak Team.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
Create Resource Departemen

Copy
php artisan make:filament-resource Departemen
Buka file Resource Departemen pada direktori : app/Filament/Resources/DepartemenResource.php


Copy
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartemenResource\Pages;
use App\Filament\Resources\DepartemenResource\RelationManagers;
use App\Models\Departemen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartemenResource extends Resource
{
    protected static ?string $model = Departemen::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Departemen';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_departemen')
                ->required()
                ->label('Nama Departemen')
                ->maxLength(40),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('nama_departemen')->label('Nama Departemen')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat'),
            ])
            ->filters([
                Tables\Filters\Filter::make('nama_departemen')
                    ->form([
                        Forms\Components\TextInput::make('nama_departemen')
                            ->label('Nama Departemen'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['nama_departemen'], function ($query, $nama) {
                                $query->where('nama_departemen', 'like', "%{$nama}%");
                            });
                    }),
            ])
        
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartemens::route('/'),
            // 'create' => Pages\CreateDepartemen::route('/create'),
            // 'edit' => Pages\EditDepartemen::route('/{record}/edit'),
        ];
    }
}
2. Create CRUD Team
Create Migration tabel & model

Copy
php artisan make:model Team -m
Edit File Migration
Buka file migration table Team


Copy
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departemen_id')->constrained()->cascadeOnDelete();
            $table->string('nama_team');
            $table->timestamps();
            $table->unique(['departemen_id', 'nama_team']); // nama team unik dalam 1 departemen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
Edit file Model
Buka file Model Team


Copy
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    // protected $table = 'teams';

    protected $fillable = [
        'departemen_id',
        'nama_team',
    ];

    /**
     * Team milik satu Departemen.
     */
    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    /**
     * Team memiliki banyak Karyawan.
     */
    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class);
    }
}
Create Resource Team

Copy
php artisan make:filament-resource Team
Buka file Resource Departemen pada direktori : app/Filament/Resources/TeamResource.php


Copy
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Teams';
    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('departemen_id')
                ->relationship('departemen', 'nama_departemen')
                ->label('Departemen')
                ->native(false)
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('nama_team')
                ->label('Nama Team')
                ->required()
                ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('departemen.nama_departemen')->label('Departemen')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('nama_team')->label('Team')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Dibuat'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('departemen_id')
                    ->relationship('departemen', 'nama_departemen')
                    ->label('Departemen'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            // 'create' => Pages\CreateTeam::route('/create'),
            // 'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
3. CRUD Karyawan
Create Migration tabel & model

Copy
php artisan make:model Karyawan -m
Edit File Migration
Buka file migration table Karyawan


Copy
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan');
            $table->string('email')->unique();
            $table->string('jabatan');
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
Edit file Model
Buka file Model Karyawan


Copy
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Karyawan extends Model
{
    use HasFactory;

    // protected $table = 'karyawans';

    protected $fillable = [
        'nama_karyawan',
        'email',
        'jabatan',
        'team_id',
    ];

    /**
     * Karyawan milik satu Team.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Akses cepat ke Departemen melalui Team (bukan relasi Eloquent murni).
     * Untuk dipakai di kode biasa (bukan untuk eager load/Filament column chaining).
     */
    public function getDepartemenAttribute()
    {
        return $this->team?->departemen;
    }
}
Create Resource Karyawan

Copy
php artisan make:filament-resource Karyawan
Buka file Resource Departemen pada direktori : app/Filament/Resources/KaryawanResource.php


Copy
<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Team;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Karyawan;
use Filament\Forms\Form;
use App\Models\Departemen;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KaryawanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KaryawanResource\RelationManagers;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Karyawan';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\TextInput::make('nama_karyawan')
                    ->label('Nama Karyawan')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\Select::make('jabatan')
                    ->label('Jabatan')
                    ->options([
                        'Team Member'         => 'Team Member',
                        'Group Leader'        => 'Group Leader',
                        'Section Leader'      => 'Section Leader',
                        'Supervisor'          => 'Supervisor',
                        'Assistant Manager'   => 'Assistant Manager',
                        'Team Manager'        => 'Team Manager',
                        'Deputi Manager'      => 'Deputi Manager',
                        'Departement Manager' => 'Departement Manager',
                        'Presiden Director'   => 'Presiden Director',
                    ])
                    ->native(false)     // dropdown bergaya select2
                    ->searchable()      // bisa ketik untuk cari
                    ->preload()         // load opsi di awal
                    ->required(),

                Forms\Components\Select::make('departemen_selector')
                    ->label('Departemen')
                    ->options(fn () => Departemen::query()->orderBy('nama_departemen')->pluck('nama_departemen', 'id'))
                    ->live()            // v3: ->live() (atau ->reactive())
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('team_id')
                    ->label('Team')
                    ->options(function (Get $get) {
                        $depId = $get('departemen_selector');
                        if (!$depId) return [];
                        return Team::where('departemen_id', $depId)->orderBy('nama_team')->pluck('nama_team', 'id');
                        })
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_karyawan')->label('Nama Karyawan')->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('jabatan')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('team.nama_team')->label('Team')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('departemen.nama_departemen')->label('Departemen'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('team_id')->label('Team')
                    ->relationship('team', 'nama_team'),
                Tables\Filters\SelectFilter::make('departemen_id')->label('Departemen')
                    ->options(Departemen::orderBy('nama_departemen')->pluck('nama_departemen', 'id')->toArray())
                    ->query(function ($query, $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('team', fn($q) => $q->where('departemen_id', $data['value']));
                        }
                    }),   
                Tables\Filters\SelectFilter::make('jabatan')
                    ->label('Jabatan')
                    ->options([
                        'Team Member'         => 'Team Member',
                        'Group Leader'        => 'Group Leader',
                        'Section Leader'      => 'Section Leader',
                        'Supervisor'          => 'Supervisor',
                        'Assistant Manager'   => 'Assistant Manager',
                        'Team Manager'        => 'Team Manager',
                        'Deputi Manager'      => 'Deputi Manager',
                        'Departement Manager' => 'Departement Manager',
                        'Presiden Director'   => 'Presiden Director',
                    ]),
        
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKaryawans::route('/'),
            // 'create' => Pages\CreateKaryawan::route('/create'),
            // 'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}
4. Jalankan Migrate

Copy
php artisan migrate
5. Buat Seeder
Create Seeder

Copy
php artisan make:seeder DepartemenTeamSeeder
Edit File Seeder
Buka file DepartementTeamSeeder


Copy
<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Team;
use Illuminate\Database\Seeder;

class DepartemenTeamSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'MSD' => ['HR Team', 'GA Team'],
            'ITB' => ['P/C Team', 'Infra Team', 'MES Team'],
            'FCD' => ['CCP Team', 'SMP Team', 'Coke Team', 'RMH Team', 'Sinter Team', 'Gas Team', 'Plate Mill Team', 'HRP Team'],
            'CMD' => ['Crane Team', 'Instrument Team', 'Field Repair Team'],
        ];

        foreach ($data as $depName => $teams) {
            $dep = Departemen::firstOrCreate(['nama_departemen' => $depName]);
            foreach ($teams as $t) {
                Team::firstOrCreate([
                    'departemen_id' => $dep->id,
                    'nama_team' => $t,
                ]);
            }
        }
    }
}

Call Database Seeder
Buka file DatabaseSeeder > panggil DepartemenTeamSeeder


Copy
<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DepartemenTeamSeeder::class);
    }
}
Run Seeder

Copy
php artisan db:seed
6. Coba Jalankan dan Akses CRUD
Jalankan server:


Copy
php artisan serve
Login ke dashboard:


Copy
http://127.0.0.1:8000/admin
Coba buka menu:

Departemen → Tambahkan beberapa departemen.

Team → Tambahkan beberapa Team berdasarkan Departemen yang dipilih.

Karyawan → Tambahkan data karyawan dengan memilih departemen dan team sebelumnya..

🎉 CRUD otomatis sudah jadi tanpa menulis controller atau route manual!

🧪 Langkah Praktikum Mahasiswa
Search
Langkah
Deskripsi
Perintah
1

Buat model + migration Departemen

php artisan make:model Departemen -m

2

Buat Filament Resource Departemen

php artisan make:filament-resource Departemen

3

Buat model + migration Karyawan

php artisan make:model Karyawan -m

4

Buat Filament Resource Karyawan

php artisan make:filament-resource Karyawan

5

Tambah relasi di model

belongsTo dan hasMany

6

Jalankan migrasi

php artisan migrate

7

Jalankan server & test di /admin

php artisan serve

🎯 Output yang Diharapkan
Menu Departemen dan Karyawan muncul di sidebar Filament.

CRUD Departemen & Karyawan berfungsi penuh (Create, Edit, Delete).

Data karyawan menampilkan relasi ke nama departemen.

💡 Tugas / Challenge
Tambahkan validasi agar email karyawan unik dan wajib diisi.

Ubah urutan kolom di tabel agar nama tampil pertama.

Tambahkan kolom pencarian (searchable()) di kolom nama dan email.

Commit hasilnya ke GitHub dengan pesan:


Copy
git add .
git commit -m "Add CRUD Karyawan and Departemen using Filament"
git push origin main
📚 Rangkuman
Konsep
Deskripsi
Filament Resource

Fitur utama untuk membuat CRUD otomatis di dashboard

Form Schema

Menentukan input (TextInput, Select, dsb)

Table Columns

Menentukan kolom data yang ditampilkan

Relationship

Menghubungkan antar tabel (One-to-Many)

Migration

Membuat struktur tabel di database

Pertemuan 5 - Relasi Data di Laravel + Filament
Departemen → Team → Karyawan

Di bawah ini penjelasan lengkapnya — baik dari sisi database (migration) maupun kode (model Eloquent) agar kamu benar-benar yakin bahwa relasinya sudah berjalan.

🧩 1. Struktur Relasi Antar Tabel
Tabel
Relasi
Keterangan
departemens

🔹 memiliki banyak teams

One-to-Many

teams

🔹 milik satu departemen🔹 memiliki banyak karyawans

BelongsTo & HasMany

karyawans

🔹 milik satu team

BelongsTo

🗄️ 2. Skema Tabel (Migration)
departemens

Copy
Schema::create('departemens', function (Blueprint $table) {
    $table->id();
    $table->string('nama_departemen');
    $table->timestamps();
});
teams

Copy
Schema::create('teams', function (Blueprint $table) {
    $table->id();
    $table->foreignId('departemen_id')->constrained()->cascadeOnDelete();
    $table->string('nama_team');
    $table->timestamps();
});
Relasi di atas artinya:

Setiap team punya departemen_id,

dan jika departemen dihapus, semua team-nya ikut terhapus (cascade delete).

karyawans

Copy
Schema::create('karyawans', function (Blueprint $table) {
    $table->id();
    $table->string('nama_karyawan');
    $table->string('email')->unique();
    $table->string('jabatan');
    $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});
Artinya:

Setiap karyawan punya team_id (foreign key ke teams.id)

Kalau sebuah team dihapus, semua karyawannya ikut dihapus.

🧠 3. Relasi di Level Model (Eloquent ORM)
app/Models/Departemen.php

Copy
class Departemen extends Model
{
    use HasFactory;

    protected $fillable = ['nama_departemen'];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
app/Models/Team.php

Copy
class Team extends Model
{
    use HasFactory;

    protected $fillable = ['departemen_id', 'nama_team'];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class);
    }
}
app/Models/Karyawan.php

Copy
class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = ['nama_karyawan', 'email', 'jabatan', 'team_id'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // Akses cepat departemen dari relasi team
    public function getDepartemenAttribute()
    {
        return $this->team?->departemen;
    }
}
🧮 4. Contoh Akses Data
Tujuan
Query
Dapatkan semua team dalam satu departemen

$departemen->teams;

Dapatkan semua karyawan dalam satu team

$team->karyawans;

Dapatkan departemen dari seorang karyawan

$karyawan->team->departemen;

Dapatkan nama departemen langsung dari karyawan

$karyawan->departemen->nama_departemen;

📊 5. Visualisasi Relasi

Copy
Departemen
   │
   ├── hasMany(Team)
   │
   ▼
Team
   │
   ├── belongsTo(Departemen)
   ├── hasMany(Karyawan)
   │
   ▼
Karyawan
   └── belongsTo(Team)
✅ Kesimpulan
kita sudah membuat relasi antar tabel dengan benar:

Setiap departemen bisa punya banyak team.

Setiap team bisa punya banyak karyawan.

Setiap karyawan hanya milik satu team, dan lewat team dia terhubung ke satu departemen.

Relasi ini sudah:

Didefinisikan di migration (foreign key)

Dikonfigurasi di model Eloquent

Bisa digunakan langsung di Filament Resource lewat relasi berantai (team.departemen.nama_departemen)

Pertemuan 5 - Relasi Data di Laravel + Filament
🎯 Tujuan Pembelajaran
Setelah mengikuti pertemuan ini, mahasiswa diharapkan mampu:

Memahami konsep relasi antar tabel dalam database relasional.

Menerapkan relasi One-to-Many (Departemen → Team, Team → Karyawan) dalam Laravel Eloquent.

Menampilkan dan mengelola data berelasi menggunakan Filament Resource.

Membuat relasi Many-to-Many sederhana (opsional) seperti relasi Karyawan ↔ Proyek.

📘 Materi Pembelajaran
1. Konsep Relasi di Database
Relasi menghubungkan dua atau lebih tabel berdasarkan Foreign Key (FK).

Jenis Relasi
Contoh
Deskripsi
One-to-One

User ↔ Profile

Satu user punya satu profil

One-to-Many

Departemen → Team

Satu departemen punya banyak team

Many-to-Many

Karyawan ↔ Proyek

Banyak karyawan bisa mengerjakan banyak proyek

2. Relasi One-to-Many di Eloquent
📍 Contoh 1: Departemen → Team

Model Departemen.php:


Copy
public function teams()
{
    return $this->hasMany(Team::class);
}
Model Team.php:


Copy
public function departemen()
{
    return $this->belongsTo(Departemen::class);
}
📍 Contoh 2: Team → Karyawan

Model Team.php:


Copy
public function karyawans()
{
    return $this->hasMany(Karyawan::class);
}
Model Karyawan.php:


Copy
public function team()
{
    return $this->belongsTo(Team::class);
}
💡 Dengan relasi ini, kamu bisa melakukan:


Copy
$departemen->teams; // semua team dalam departemen
$team->karyawans;   // semua karyawan dalam team
$karyawan->team;    // team tempat karyawan bekerja
3. Menampilkan Relasi di Filament Resource
Kita akan menampilkan relasi Departemen dan Team di dalam tabel KaryawanResource.


Copy
Tables\Columns\TextColumn::make('team.nama_team')->label('Team'),
Tables\Columns\TextColumn::make('team.departemen.nama_departemen')->label('Departemen'),
Relasi berantai (team.departemen.nama_departemen) akan otomatis membaca data antar model tanpa perlu join manual.

4. Membuat Filter Berdasarkan Relasi
Tambahkan filter di KaryawanResource:


Copy
Tables\Filters\SelectFilter::make('departemen')
    ->label('Departemen')
    ->options(\App\Models\Departemen::pluck('nama_departemen', 'id'))
    ->query(function ($query, $data) {
        if (!empty($data['value'])) {
            $query->whereHas('team', fn($q) => $q->where('departemen_id', $data['value']));
        }
    }),
Mahasiswa akan belajar cara menggunakan whereHas() untuk memfilter data berdasarkan relasi.

5. Relasi Many-to-Many (Opsional/Bonus)
Contoh: relasi Karyawan ↔ Proyek

a. Migration


Copy
Schema::create('karyawan_proyek', function (Blueprint $table) {
    $table->id();
    $table->foreignId('karyawan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('proyek_id')->constrained()->cascadeOnDelete();
});
b. Model


Copy
// Karyawan.php
public function proyeks()
{
    return $this->belongsToMany(Proyek::class, 'karyawan_proyek');
}

Copy
// Proyek.php
public function karyawans()
{
    return $this->belongsToMany(Karyawan::class, 'karyawan_proyek');
}
🧪 Praktikum Langkah demi Langkah
Langkah
Deskripsi
Perintah
1

Pastikan migrasi departemens, teams, dan karyawans sudah benar dan saling berelasi

-

2

Tambahkan relasi hasMany dan belongsTo di model

edit model

3

Jalankan ulang migrasi untuk memastikan foreign key valid

php artisan migrate:fresh --seed

4

Tambahkan kolom relasi di KaryawanResource

edit KaryawanResource.php

5

Tambahkan filter berdasarkan Departemen

edit KaryawanResource.php

6

Coba tambah data baru di Filament (/admin) dan perhatikan relasi bekerja otomatis

-

📈 Output yang Diharapkan
Data karyawan dapat ditampilkan bersama Team dan Departemen.

Filter dapat memisahkan karyawan berdasarkan departemen.

Relasi berfungsi di semua arah:

$departemen->teams

$team->karyawans

$karyawan->team->departemen

💡 Tugas / Challenge
Tambahkan field “Jumlah Karyawan” di tabel TeamResource (gunakan count() dari relasi karyawans).

Tambahkan filter “Team” di KaryawanResource.

Buat contoh relasi Many-to-Many sederhana: Karyawan ↔ Proyek (opsional untuk nilai plus).

📚 Rangkuman
Konsep
Fungsi
hasMany()

Relasi satu ke banyak (parent → child)

belongsTo()

Relasi kebalikan (child → parent)

whereHas()

Filter data berdasarkan relasi

Filament SelectFilter

Membuat dropdown filter untuk relasi

Nested relation (team.departemen.nama)

Menampilkan data lintas tabel tanpa join manual

🧠 Kesimpulan
Setelah pertemuan ini, mahasiswa sudah:

Memahami dan menerapkan relasi antar tabel di Laravel Eloquent.

Dapat menampilkan dan memfilter data relasi di Filament Admin.

Siap untuk melangkah ke pertemuan berikutnya: Customisasi Form & Table Filament.

Pertemuan 6 - Customisasi Form & Table di Filament (Absensi Karyawan)
🎯 Tujuan Pembelajaran
Setelah mengikuti pertemuan ini, mahasiswa mampu:

Mengubah tampilan form dan tabel di Filament Admin Panel.

Menggunakan komponen Form (TextInput, DatePicker, FileUpload, Toggle, dsb).

Mengelola data Absensi (Check-in dan Check-out) dengan relasi ke Karyawan.

Menampilkan data absensi lengkap dengan foto masuk & foto pulang.

📘 Materi Pembelajaran
1. Konsep Customisasi Form & Table
Secara default, Filament membuat CRUD otomatis dengan form dan tabel sederhana.
Namun, kita bisa mengubah tampilan dan fungsi dengan menambahkan komponen-komponen berikut:

Search
Komponen
Kegunaan
TextInput

Input teks biasa

DatePicker / DateTimePicker

Pilihan tanggal / waktu

FileUpload

Upload file atau foto

Select

Dropdown pilihan (termasuk relasi)

Toggle

Switch On/Off

Textarea

Input teks panjang

ColorPicker

Pilih warna

RichEditor

Input teks dengan format HTML

2. Menyiapkan Tabel Absensi
Kita akan buat tabel baru bernama absensis untuk menyimpan data kehadiran harian.

Migration


Copy
php artisan make:model Absensi -m
database/migrations/xxxx_xx_xx_create_absensis_table.php


Copy
Schema::create('absensis', function (Blueprint $table) {
    $table->id();
    $table->foreignId('karyawan_id')->constrained()->cascadeOnDelete();
    $table->timestamp('waktu_masuk')->nullable();
    $table->timestamp('waktu_pulang')->nullable();
    $table->string('foto_masuk')->nullable();
    $table->string('foto_pulang')->nullable();
    $table->string('status')->default('Hadir'); // Hadir, Izin, Sakit, dll.
    $table->timestamps();
});
Lalu:


Copy
php artisan migrate
3. Menambahkan Relasi Model
app/Models/Karyawan.php


Copy
public function absensis()
{
    return $this->hasMany(Absensi::class);
}
app/Models/Absensi.php


Copy
public function karyawan()
{
    return $this->belongsTo(Karyawan::class);
}
4. Membuat Filament Resource

Copy
php artisan make:filament-resource Absensi
5. Kustomisasi Form Absensi
Buka file app/Filament/Resources/AbsensiResource.php, lalu ubah bagian form() menjadi seperti ini:


Copy
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;

public static function form(Form $form): Form
{
    return $form->schema([
        Select::make('karyawan_id')
            ->label('Nama Karyawan')
            ->relationship('karyawan', 'nama_karyawan')
            ->searchable()
            ->required(),

        DateTimePicker::make('waktu_masuk')
            ->label('Waktu Masuk')
            ->seconds(false)
            ->default(now()),

        FileUpload::make('foto_masuk')
            ->label('Foto Masuk')
            ->directory('foto_absensi')
            ->image()
            ->imageEditor()
            ->required(),

        DateTimePicker::make('waktu_pulang')
            ->label('Waktu Pulang')
            ->seconds(false)
            ->nullable(),

        FileUpload::make('foto_pulang')
            ->label('Foto Pulang')
            ->directory('foto_absensi')
            ->image()
            ->imageEditor()
            ->nullable(),

        Select::make('status')
            ->label('Status')
            ->options([
                'Hadir' => 'Hadir',
                'Izin' => 'Izin',
                'Sakit' => 'Sakit',
                'Cuti' => 'Cuti',
            ])
            ->default('Hadir')
            ->required(),
    ])->columns(2);
}
6. Menampilkan Data di Table
Masih di AbsensiResource.php, ubah bagian table() menjadi:


Copy
use Filament\Tables;

public static function table(Table $table): Table
{
    return $table->columns([
        Tables\Columns\TextColumn::make('karyawan.nama_karyawan')->label('Nama Karyawan')->searchable(),
        Tables\Columns\TextColumn::make('waktu_masuk')->dateTime('d M Y H:i')->label('Masuk'),
        Tables\Columns\TextColumn::make('waktu_pulang')->dateTime('d M Y H:i')->label('Pulang'),
        Tables\Columns\ImageColumn::make('foto_masuk')->label('Foto Masuk')->height(60),
        Tables\Columns\ImageColumn::make('foto_pulang')->label('Foto Pulang')->height(60),
        Tables\Columns\BadgeColumn::make('status')
            ->colors([
                'success' => 'Hadir',
                'warning' => 'Izin',
                'danger' => 'Sakit',
                'gray' => 'Cuti',
            ]),
    ])
    ->filters([
        Tables\Filters\SelectFilter::make('status')->options([
            'Hadir' => 'Hadir',
            'Izin' => 'Izin',
            'Sakit' => 'Sakit',
            'Cuti' => 'Cuti',
        ]),
    ])
    ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
    ]);
}
7. Menambahkan Fitur Kamera (Opsional)
Jika ingin mahasiswa belajar capture foto langsung dari kamera, gunakan library seperti WebcamJS di form Filament.

Konsepnya:

Tambahkan field foto_masuk (string path file).

Tambahkan custom blade view untuk form yang memanggil kamera HTML5 (navigator.mediaDevices.getUserMedia()).

Setelah capture, hasil foto dikirim ke server (base64 → disimpan sebagai file).

Untuk tahap perkuliahan, cukup gunakan FileUpload dulu (upload manual dari kamera/galeri).

🧪 Langkah Praktikum
Search
Langkah
Deskripsi
Perintah
1

Buat model dan migration Absensi

php artisan make:model Absensi -m

2

Tambahkan relasi di model Karyawan dan Absensi

edit file model

3

Jalankan migrasi

php artisan migrate

4

Buat Filament Resource Absensi

php artisan make:filament-resource Absensi

5

Ubah form & table di AbsensiResource.php

edit manual

6

Tambahkan beberapa data absensi via /admin

buka dashboard

7

Pastikan data tampil lengkap (nama karyawan, jam masuk/pulang, foto)

test aplikasi

🎯 Output yang Diharapkan
Tabel Absensi muncul di menu sidebar.

Mahasiswa bisa menambahkan record absensi lengkap dengan:

Karyawan

Waktu masuk & pulang

Foto (upload manual)

Status kehadiran

Dashboard menampilkan tabel absensi dengan gambar & badge warna.

💡 Tugas / Challenge
Tambahkan validasi agar satu karyawan tidak bisa absen dua kali di hari yang sama.

Tambahkan kolom tanggal_absen (DATE) otomatis ($table->date('tanggal')->default(now())).

Tampilkan total absensi per karyawan di KaryawanResource (gunakan count() relasi absensis).

(Bonus) Integrasikan kamera langsung menggunakan JavaScript MediaDevices.

📚 Rangkuman
Komponen
Fungsi
FileUpload

Upload dan preview foto absensi

Select

Dropdown pilihan (relasi & status)

DateTimePicker

Input waktu masuk/pulang

BadgeColumn

Warna otomatis berdasarkan status

relationship()

Menghubungkan tabel relasi di Filament

🧠 Kesimpulan
Setelah pertemuan ini mahasiswa mampu:

Membuat form absensi yang lengkap di Filament.

Mengatur tampilan tabel absensi agar lebih informatif.

Memahami bagaimana relasi Karyawan → Absensi bekerja dalam CRUD Filament.

Siap melanjutkan ke Pertemuan 7: Autentikasi & Manajemen Role (Admin vs User).

Pertemuan 6 - Customisasi Form & Table di Filament: Absensi & Overtime
🎯 Tujuan Pembelajaran
Setelah pertemuan ini, mahasiswa diharapkan mampu:

Membuat Form Filament kustom dengan validasi dinamis.

Menangani absensi harian dan absensi lembur (overtime) secara otomatis.

Menggunakan Laravel Eloquent & Filament Hooks untuk memproses logika sebelum menyimpan data.

Menambahkan field kondisional (misal: alasan overtime muncul hanya jika lembur).

📘 Materi Pembelajaran
1. Konsep Absensi & Overtime
Kondisi
Aksi
Karyawan melakukan absensi pertama di hari itu

Disimpan sebagai absensi normal

Karyawan melakukan absensi kedua di hari yang sama

Disimpan sebagai absensi overtime (lembur)

Jika absensi overtime

Field “Alasan Overtime” wajib diisi

Struktur tabel absensi:


Copy
id | karyawan_id | tanggal | waktu_masuk | waktu_pulang | is_overtime | alasan_overtime | foto_masuk | foto_pulang
2. Menyiapkan Tabel Absensi
Kita akan buat tabel baru bernama absensis untuk menyimpan data kehadiran harian.

- Migration Absensi

Copy
php artisan make:model Absensi -m
- Edit Migration
database/migrations/2025_10_10_000000_create_absensis_table.php


Copy
Schema::create('absensis', function (Blueprint $table) {
    $table->id();
    $table->foreignId('karyawan_id')->constrained()->cascadeOnDelete();
    $table->date('tanggal');
    $table->timestamp('waktu_masuk')->nullable();
    $table->timestamp('waktu_pulang')->nullable();
    $table->boolean('is_overtime')->default(false);
    $table->string('alasan_overtime')->nullable();
    $table->string('foto_masuk')->nullable();
    $table->string('foto_pulang')->nullable();
    $table->timestamps();
});
3. Model Relasi
app/Models/Absensi.php


Copy
class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id', 'tanggal', 'waktu_masuk', 'waktu_pulang',
        'is_overtime', 'alasan_overtime', 'foto_masuk', 'foto_pulang'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
app/Models/Karyawan.php


Copy
public function absensis()
{
    return $this->hasMany(Absensi::class);
}
4. Membuat Resource Filament

Copy
php artisan make:filament-resource Absensi
5. Form Absensi dengan Logika Overtime
Edit app/Filament/Resources/AbsensiResource.php


Copy
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Select::make('karyawan_id')
            ->label('Karyawan')
            ->relationship('karyawan', 'nama_karyawan')
            ->required()
            ->searchable()
            ->preload(),

        Forms\Components\DatePicker::make('tanggal')
            ->label('Tanggal')
            ->default(now())
            ->required(),

        Forms\Components\TimePicker::make('waktu_masuk')
            ->label('Waktu Masuk')
            ->required(),

        Forms\Components\TimePicker::make('waktu_pulang')
            ->label('Waktu Pulang')
            ->required(),

        Forms\Components\FileUpload::make('foto_masuk')
            ->label('Foto Masuk')
            ->directory('absensi/foto_masuk')
            ->image(),

        Forms\Components\FileUpload::make('foto_pulang')
            ->label('Foto Pulang')
            ->directory('absensi/foto_pulang')
            ->image(),

        Forms\Components\Toggle::make('is_overtime')
            ->label('Apakah Overtime?')
            ->reactive(),

        Forms\Components\Textarea::make('alasan_overtime')
            ->label('Alasan Overtime')
            ->visible(fn ($get) => $get('is_overtime'))
            ->required(fn ($get) => $get('is_overtime')),
    ])->columns(2);
}
6. Menentukan Overtime Otomatis (Hooks)
Tambahkan di AbsensiResource → beforeSave() hook di Page CreateAbsensi atau langsung di saving() model event.

app/Filament/Resources/AbsensiResource/Pages/CreateAbsensi.php


Copy
protected function mutateFormDataBeforeCreate(array $data): array
{
    // Cek apakah karyawan sudah absen di tanggal ini
    $exists = \App\Models\Absensi::where('karyawan_id', $data['karyawan_id'])
        ->where('tanggal', $data['tanggal'])
        ->exists();

    // Jika sudah ada absensi hari ini → ini lembur
    if ($exists) {
        $data['is_overtime'] = true;
    } else {
        $data['is_overtime'] = false;
    }

    // Jika overtime tapi tidak isi alasan
    if ($data['is_overtime'] && empty($data['alasan_overtime'])) {
        throw new \Filament\Notifications\NotificationException('Alasan lembur wajib diisi!');
    }

    return $data;
}
💡 Alternatif: bisa juga taruh di model event booted() dalam Absensi.php agar berlaku di semua tempat, tidak hanya Filament.

7. Menampilkan Kolom di Tabel Filament

Copy
public static function table(Table $table): Table
{
    return $table->columns([
        Tables\Columns\TextColumn::make('karyawan.nama_karyawan')->label('Karyawan')->sortable()->searchable(),
        Tables\Columns\TextColumn::make('tanggal')->label('Tanggal')->date(),
        Tables\Columns\TextColumn::make('waktu_masuk')->label('Masuk')->time(),
        Tables\Columns\TextColumn::make('waktu_pulang')->label('Pulang')->time(),
        Tables\Columns\IconColumn::make('is_overtime')
            ->boolean()
            ->label('Lembur'),
        Tables\Columns\TextColumn::make('alasan_overtime')
            ->label('Alasan Overtime')
            ->toggleable()
            ->limit(30),
    ]);
}
8. Validasi di Model (Opsional)
Tambahkan validasi otomatis di Absensi model:


Copy
protected static function booted()
{
    static::creating(function ($absen) {
        // Jika sudah absen hari ini → ubah jadi overtime
        $exists = Absensi::where('karyawan_id', $absen->karyawan_id)
            ->where('tanggal', $absen->tanggal)
            ->exists();

        $absen->is_overtime = $exists;

        if ($absen->is_overtime && empty($absen->alasan_overtime)) {
            throw new \Exception("Alasan lembur wajib diisi!");
        }
    });
}
🧪 Langkah Praktikum Mahasiswa
Search
Langkah
Deskripsi
Perintah
1

Buat migration & model Absensi

php artisan make:model Absensi -m

2

Tambahkan relasi hasMany di Karyawan

edit Karyawan.php

3

Buat Filament Resource Absensi

php artisan make:filament-resource Absensi

4

Custom form untuk menampilkan field overtime & alasan lembur

edit AbsensiResource.php

5

Tambahkan logika otomatis di CreateAbsensi.php

edit file di Resources/AbsensiResource/Pages/

6

Jalankan seeder dan test absensi ganda di hari sama → pastikan record kedua jadi lembur

php artisan serve

7

Commit hasil ke GitHub

git add . && git commit -m "Add Absensi & Overtime logic" && git push origin main

🎯 Output yang Diharapkan
Form absensi menampilkan semua field (termasuk upload foto).

Absensi pertama hari itu = normal.

Absensi kedua hari itu = lembur (is_overtime = true).

Jika lembur, field alasan overtime wajib diisi.

Data tampil rapi di Filament Table.

💡 Tugas / Challenge
Tambahkan filter di tabel absensi untuk menampilkan hanya data lembur (is_overtime = true).

Tambahkan validasi agar satu karyawan tidak bisa absen dua kali dengan waktu masuk yang sama.

Tambahkan notifikasi Filament yang muncul saat data disimpan:


Copy
Notification::make()
    ->title('Absensi Berhasil Disimpan!')
    ->success()
    ->send();
📚 Rangkuman
Konsep
Penjelasan
hasMany / belongsTo

Menghubungkan Karyawan dengan Absensi

mutateFormDataBeforeCreate()

Hook Filament sebelum data disimpan

Kondisi visible(fn($get))

Menampilkan field dinamis

Validasi kondisional

Field wajib diisi tergantung kondisi

is_overtime

Boolean penanda lembur otomatis

✅ Kesimpulan
Setelah pertemuan ini, mahasiswa sudah:

Menguasai pembuatan form dinamis di Filament.

Memahami logika relasional dinamis (absensi → overtime).

Siap membuat aplikasi absensi yang lebih realistis dan cerdas.

Pertemuan 7 — Autentikasi & Role Management
🎯 Tujuan Pembelajaran
Pada pertemuan ini mahasiswa mampu:

Mengimplementasikan sistem autentikasi menggunakan Laravel Breeze / Jetstream.

Mengelola role & permission menggunakan Filament Shield.

Mengatur akses menu dan halaman berdasarkan role.

Mengimplementasikan skenario multi-role berdasarkan jabatan (position) karyawan.

Memastikan akses sesuai aturan:

Semua role bisa absensi & lihat absensinya sendiri

Level atasan bisa melihat absensi bawahan sesuai struktur organisasi

Administrator (admin) punya akses penuh

📚 Materi Pembelajaran
1️⃣ Autentikasi Menggunakan Laravel Breeze / Jetstream
Laravel Breeze menyediakan autentikasi dasar (login, register, reset password).
Laravel Jetstream menyediakan autentikasi lebih lengkap (teams, 2FA, profile, API Tokens).

Instalasi Laravel Breeze (opsi yang lebih ringan)

Copy
composer require laravel/breeze --dev
php artisan breeze:install
npm install
npm run build
php artisan migrate
Alternatif Jetstream (lebih lengkap)

Copy
composer require laravel/jetstream
php artisan jetstream:install livewire
npm install
npm run build
php artisan migrate
Setelah ini, sistem login sudah bisa digunakan.

2️⃣ Role & Permission Menggunakan Filament Shield
Instalasi

Copy
composer require filament/shield
php artisan shield:install
php artisan migrate
Ini akan mengaktifkan:

Manajemen Permission

Manajemen Role

Middleware proteksi akses

Panel konfigurasi role/permission

3️⃣ Tambahkan Role Baru
Role yang digunakan:
admin

team_member

group_leader

section_leader

supervisor

assistant_manager

team_manager

departement_manager

Perintah membuat role

Copy
php artisan shield:role admin
php artisan shield:role team_member
php artisan shield:role group_leader
php artisan shield:role section_leader
php artisan shield:role supervisor
php artisan shield:role assistant_manager
php artisan shield:role team_manager
php artisan shield:role departement_manager
Menambahkan role ke user
Contoh:


Copy
$user->assignRole('team_member');
Role admin tidak terhubung ke tabel karyawan → user admin dibuat manual.

4️⃣ Konfigurasi Akses Menu Filament berdasarkan Role
Master Data (Departemen, Team, Position, Karyawan)
Hanya admin yang boleh mengakses:

Tambahkan pada setiap Resource:


Copy
public static function canViewAny(): bool
{
    return auth()->user()->hasRole('admin');
}
Atau di Filament Shield panel → centang akses hanya untuk admin.

🧩 5️⃣ Aturan Akses Absensi (Core Logic)
Semua role:
Bisa melakukan absensi

Bisa melihat histori absensi milik sendiri

Hierarki Penglihatan Absensi:
Search
Role
Bisa Lihat Absensi
team_member

sendiri

group_leader

team_member

section_leader

team_member, group_leader

supervisor

team_member, group_leader, section_leader

assistant_manager

team_member, group_leader, section_leader, supervisor

team_manager

team_member, group_leader, section_leader, assistant_manager, supervisor

departement_manager

semua level kecuali admin

admin

semua user & semua absensi

📌 Penerapan di Controller / Query Builder
Ambil posisi user:

Copy
$role = auth()->user()->getRoleNames()->first();
Logic filtering absensi:

Copy
$user = auth()->user();

switch ($user->role) {
    case 'group_leader':
        $allowed = ['team_member'];
        break;

    case 'section_leader':
        $allowed = ['team_member', 'group_leader'];
        break;

    case 'supervisor':
        $allowed = ['team_member', 'group_leader', 'section_leader'];
        break;

    case 'assistant_manager':
        $allowed = ['team_member', 'group_leader', 'section_leader', 'supervisor'];
        break;

    case 'team_manager':
        $allowed = ['team_member', 'group_leader', 'section_leader', 'assistant_manager', 'supervisor'];
        break;

    case 'departement_manager':
        $allowed = ['team_member', 'group_leader', 'section_leader', 'supervisor', 'assistant_manager', 'team_manager'];
        break;

    case 'admin':
        $allowed = ['*'];  // semua
        break;

    default:
        $allowed = []; // user biasa hanya lihat absensi sendiri
}
Query ambil absensi:

Copy
if ($user->hasRole('admin')) {
    $absensi = Absensi::all();
} elseif ($allowed == []) {
    $absensi = Absensi::where('user_id', $user->id)->get();
} else {
    $positions = Position::whereIn('key', $allowed)->pluck('id');
    $absensi = Absensi::whereIn('position_id', $positions)->get();
}
🧪 6️⃣ Praktikum Pertemuan 7
Langkah 1 — Install Autentikasi

Copy
composer require laravel/breeze --dev

Copy
php artisan breeze:install


Copy
npm install

Copy
npm run build


Copy
php artisan migrate
Langkah 2 — Install Filament Shield

Copy
composer require filament/shield
php artisan shield:install
php artisan migrate

Copy
composer require bezhansalleh/filament-shield

Copy
php artisan shield:install


Copy
php artisan migrate
Publish Spatie Permission

Copy
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
Ini akan membuat file migration seperti:


Copy
database/migrations/xxxx_xx_xx_xxxxxx_create_permission_tables.php

Copy
php artisan migrate
Ini akan membuat tabel:

roles

permissions

model_has_roles

model_has_permissions

role_has_permissions

Langkah 3 — Tambahkan Role

Copy
php artisan permission:create-role admin
php artisan permission:create-role team_member
php artisan permission:create-role group_leader
php artisan permission:create-role section_leader
php artisan permission:create-role supervisor
php artisan permission:create-role assistant_manager
php artisan permission:create-role team_manager
php artisan permission:create-role departement_manager

Buat Seeder Role Permission

Copy
php artisan make:seeder RolePermissionSeeder

Copy
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Hapus dulu (opsional) agar idempotent
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions untuk resource Attendance
        $permissions = [
            'attendance.create',
            'attendance.view',            // view own / records you are allowed to see
            'attendance.view_subordinates', // melihat daftar absensi bawahan
            'attendance.update',
            'attendance.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Roles
        $roles = [
            'admin',
            'team_member',
            'group_leader',
            'section_leader',
            'supervisor',
            'assistant_manager',
            'team_manager',
            'departement_manager',
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        // Assign permissions:
        // Admin => semua permission
        $admin = Role::where('name', 'admin')->first();
        $admin->syncPermissions($permissions);

        // Non-admin : hanya boleh create & view own
        $basicPerms = ['attendance.create', 'attendance.view'];
        $nonAdmins = [
            'team_member',
            'group_leader',
            'section_leader',
            'supervisor',
            'assistant_manager',
            'team_manager',
            'departement_manager',
        ];
        foreach ($nonAdmins as $r) {
            Role::where('name', $r)->first()->syncPermissions($basicPerms);
        }

        // Beri beberapa role kemampuan melihat bawahan (sesuaikan)
        // Misal managers & leaders boleh melihat bawahan
        $seeSubordinates = [
            'group_leader',
            'section_leader',
            'supervisor',
            'team_manager',
            'departement_manager',
        ];
        foreach ($seeSubordinates as $r) {
            Role::where('name', $r)->first()->givePermissionTo('attendance.view_subordinates');
        }

        // Opsional: assign roles to existing users berdasarkan field `position`
        // Perhatikan: sesuaikan nama field jika berbeda (misal 'position' di users table)
        foreach (User::cursor() as $user) {
            if ($user->position) {
                $pos = strtolower($user->position); // sesuaikan normalisasi
                if (Role::where('name', $pos)->exists()) {
                    $user->syncRoles([$pos]);
                }
            }
        }
    }
}

Copy
php artisan db:seed --class=RolePermissionSeeder
Policy AttendancePolicy (aturan akses)
Buat policy: 


Copy
php artisan make:policy AttendancePolicy --model=Attendance
Isi contoh app/Policies/AttendancePolicy.php:


Copy
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        // super admin / admin full access
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        // Rules to view list: if manager sees subordinates list, check permission
        return $user->can('attendance.view') || $user->can('attendance.view_subordinates');
    }

    public function view(User $user, Attendance $attendance)
    {
        // admin already returned true by before()
        // allow if user owns the attendance record
        if ($attendance->user_id === $user->id) {
            return $user->can('attendance.view');
        }

        // a manager can view subordinate's attendance if they have permission and the attendance belongs to subordinate
        if ($user->can('attendance.view_subordinates') && $this->isSubordinate($user, $attendance->user)) {
            return true;
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->can('attendance.create');
    }

    public function update(User $user, Attendance $attendance)
    {
        // only admin allowed (covered by before). non-admin false.
        return false;
    }

    public function delete(User $user, Attendance $attendance)
    {
        // only admin allowed
        return false;
    }

    // helper to determine subordinate relation (sesuaikan relasi)
    protected function isSubordinate(User $manager, User $possibleSubordinate)
    {
        // Asumsi: users table punya manager_id pointing to their manager's id
        // atau ada relasi custom seperti department/position hierarchy
        return $possibleSubordinate->manager_id === $manager->id;
    }
}
AuthServiceProvider

Copy
php artisan make:provider AuthServiceProvider
Laravel akan membuat file:


Copy
app/Providers/AuthServiceProvider.php
✅ Isi AuthServiceProvider untuk register Policy
Setelah file dibuat, isi seperti ini:


Copy
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Attendance;
use App\Policies\AttendancePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Attendance::class => AttendancePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
Jika kamu punya policy lain, tambahkan di array $policies.

🔥 Langkah terakhir (penting)
Tambahkan provider ini ke Laravel (kalau belum otomatis), buka:


Copy
bootstrap/app.php
Cari bagian:


Copy
->withProviders([
    // daftar providers
])
Tambahkan:


Copy
App\Providers\AuthServiceProvider::class,
Contoh:


Copy
->withProviders([
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
])
Setelah itu lakukan:

Copy
php artisan optimize:clear
Pastikan User model benar (file: app/Models/User.php)
Buka file itu dan pastikan persis seperti blok kode ini (sesuaikan use lainnya jika ada):


Copy
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // <--- pastikan ini

class User extends Authenticatable
{
    use Notifiable, HasRoles; // <--- HasRoles dipakai di sini

    // ... atribut dan relasi lain ...
}
Poin penting:

Jangan use App\Models\HasRoles; — harus Spatie\Permission\Traits\HasRoles.

Pastikan tidak ada typo, dan file disimpan.

Refresh autoload & cache
Setelah mengubah User.php atau menginstall package, jalankan:


Copy
composer dump-autoload
php artisan optimize:clear
php artisan permission:cache-reset
php artisan cache:clear
4) Pastikan migration/konfigurasi Spatie sudah ada (opsional tapi umum)
Jika belum pernah publish/migrate Spatie, lakukan:


Copy
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
(Tapi ini hanya untuk membuat tabel roles/permissions — tidak berhubungan langsung dengan method yang undefined.)

5) Cek di Tinker apakah method tersedia
Jalankan tinker:


Copy
php artisan tinker
Kemudian:


Copy
$u = \App\Models\User::where('email','median@posco.net')->first();
method_exists($u, 'getRoleNames');                // harus true
method_exists($u, 'getPermissionsViaRoles');      // harus true
Jika kedua method_exists mengembalikan true, coba lagi:


Copy
$u->getRoleNames();
$u->getPermissionsViaRoles()->pluck('name');
Menyinkronkan position ke role otomatis (opsional)
Install Spatie

Copy
composer require spatie/laravel-permission
Jika kamu ingin agar ketika user dibuat/diupdate field position otomatis sinkron ke role Spatie, tambahkan di User model:


Copy
protected static function booted()
{
    static::created(function ($user) {
        if (!empty($user->position) && \Spatie\Permission\Models\Role::where('name', $user->position)->exists()) {
            $user->assignRole($user->position);
        }
    });

    static::updated(function ($user) {
        if ($user->isDirty('position')) {
            $pos = $user->position;
            if ($pos && \Spatie\Permission\Models\Role::where('name', $pos)->exists()) {
                $user->syncRoles([$pos]);
            } else {
                $user->syncRoles([]); // atau hapus role lama
            }
        }
    });
}

Copy
php artisan tinker
User::find(1)->assignRole('admin');
Langkah 5 — Batasi Menu Filament
Di setiap Resource:


Copy
public static function canViewAny(): bool
{
    return auth()->user()->hasRole('admin');
}
Langkah 6 — Implementasi Aturan Absensi
Semua bisa melihat absensi milik sendiri

Atasan bisa melihat bawahan sesuai hierarki

Admin bisa lihat semuanya

Langkah 7 — Uji Coba Sistem
Login sebagai team_member → hanya lihat milik sendiri

Login sebagai supervisor → lihat bawahan

Login sebagai admin → semua resource muncul

Login sebagai departement_manager → lihat semua user sesuai aturan

🎉 Output Akhir Pertemuan
Sistem berhasil dibangun dengan fitur:

Login / logout bekerja

User memiliki role

Akses menu mengikuti role

Absensi hanya dapat dilihat sesuai otorisasi

Admin mengelola master data & seluruh panel

Pertemuan 7 — Autentikasi & Role Management
Tujuan singkat
Implementasi auth + role/permission sehingga role memiliki akses sesuai ketentuan:

team_member → bisa absen & lihat riwayat sendiri

group_leader → absen, riwayat sendiri, lihat riwayat team_member

section_leader → absen, riwayat sendiri, lihat riwayat team_member & group_leader

supervisor → ... tambah akses section_leader

assistant_manager → ... tambah akses supervisor

team_manager → ... tambah akses assistant_manager

department_manager → ... tambah akses team_manager

admin → semua akses + halaman admin Filament

Kita implementasikan hirarki akses menggunakan Spatie permissions + sebuah helper/hierarchy check untuk memfilter riwayat absen yang dapat diakses.

1) Install paket yang dibutuhkan
Jalankan di proyek Laravel (di root repo):


Copy
# 1) Laravel Breeze (auth scaffolding)
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
php artisan migrate

# 2) Filament (admin panel)
composer require filament/filament

# 3) Filament Shield (meng-setup Spatie + integrasi Filament)
composer require usman/filament-shield
# (nama paket bisa berbeda tergantung versi; jika paket resmi Filament Shield: spatie/laravel-permission + filament-shield
composer require spatie/laravel-permission
composer require filament/shield
Catatan: Jika nama paket filament/shield berbeda di versi terbaru, gunakan dokumentasi resmi. Perintah di atas umum — sesuaikan jika composer menolak (paket name/version).

Setelah install Spatie:


Copy
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
Jika menggunakan Filament Shield (jika tersedia):


Copy
php artisan shield:install
# atau php artisan filament:shield:install tergantung versi
2) Setup Model User & Trait
Di app/Models/User.php tambahkan trait HasRoles:


Copy
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    // ...
}
Pastikan config/auth.php dan config/permission.php publish/config sudah ada.

3) Buat roles & permissions — naming convention
Kita bikin permissions dasar untuk fitur absensi:

attendance.create (bisa absen)

attendance.view.self (lihat riwayat sendiri)

attendance.view.team_member

attendance.view.group_leader

attendance.view.section_leader

attendance.view.supervisor

attendance.view.assistant_manager

attendance.view.team_manager

attendance.view.department_manager

filament.* (akses admin panel / Filament) — Filament biasanya punya gate viewFilament.

Tapi lebih praktis: buat role dan assign permission yang sesuai.

4) Seeder: buat roles & permissions otomatis
Buat seeder database/seeders/RolePermissionSeeder.php:


Copy
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // permissions
        $permissions = [
            'attendance.create',
            'attendance.view.self',
            'attendance.view.team_member',
            'attendance.view.group_leader',
            'attendance.view.section_leader',
            'attendance.view.supervisor',
            'attendance.view.assistant_manager',
            'attendance.view.team_manager',
            'attendance.view.department_manager',
            'filament.access', // akses admin panel
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // create roles and assign permissions according to hierarchy
        $roles = [
            'team_member' => [
                'attendance.create',
                'attendance.view.self',
            ],
            'group_leader' => [
                'attendance.create',
                'attendance.view.self',
                'attendance.view.team_member',
            ],
            'section_leader' => [
                'attendance.create',
                'attendance.view.self',
                'attendance.view.team_member',
                'attendance.view.group_leader',
            ],
            'supervisor' => [
                'attendance.create',
                'attendance.view.self',
                'attendance.view.team_member',
                'attendance.view.group_leader',
                'attendance.view.section_leader',
            ],
            'assistant_manager' => [
                'attendance.create',
                'attendance.view.self',
                'attendance.view.team_member',
                'attendance.view.group_leader',
                'attendance.view.section_leader',
                'attendance.view.supervisor',
            ],
            'team_manager' => [
                'attendance.create',
                'attendance.view.self',
                'attendance.view.team_member',
                'attendance.view.group_leader',
                'attendance.view.section_leader',
                'attendance.view.supervisor',
                'attendance.view.assistant_manager',
            ],
            'department_manager' => [
                'attendance.create',
                'attendance.view.self',
                'attendance.view.team_member',
                'attendance.view.group_leader',
                'attendance.view.section_leader',
                'attendance.view.supervisor',
                'attendance.view.assistant_manager',
                'attendance.view.team_manager',
            ],
            'admin' => [
                'filament.access',
                // beri semua permissions (alternatif: syncAllPermissions())
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            if ($roleName === 'admin') {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($perms);
            }
        }
    }
}
Jalankan:


Copy
php artisan db:seed --class=RolePermissionSeeder
5) Assignment role pada user — contoh seeder / admin UI
Untuk membuat akun admin cepat:


Copy
// tinker or seeder
$user = \App\Models\User::find(1);
$user->assignRole('admin');
Atau saat registrasi (breeze) set default role team_member:

Di RegisteredUserController@store setelah create user:


Copy
$user->assignRole('team_member');
6) Policy / Gate untuk membatasi akses riwayat absen
Kita buat AttendancePolicy (asumsi model Attendance):


Copy
php artisan make:policy AttendancePolicy --model=Attendance
Di app/Policies/AttendancePolicy.php:


Copy
public function view(User $user, Attendance $attendance)
{
    // owner always can view
    if ($attendance->user_id === $user->id) {
        return $user->can('attendance.view.self');
    }

    // role-based visibility: kita mengecek apakah $user punya permission
    // untuk melihat level dari $attendance->user->role
    // asumsikan setiap user hanya punya 1 role (atau gunakan primary role)
    $targetRole = $attendance->user->getRoleNames()->first(); // e.g. 'team_member'

    // build permission name
    $permission = 'attendance.view.' . $targetRole;

    return $user->can($permission);
}
Catatan: getRoleNames()->first() mengambil role pertama; jika user punya banyak role, ubah sesuai rules perusahaan.

Register policy di AuthServiceProvider.

7) Helper: menentukan hirarki dan query pemfilteran riwayat
Daripada memeriksa satu-per-satu, kita buat helper yang mengembalikan array roles yang bisa diakses oleh role tertentu.

Buat app/Services/RoleHierarchy.php:


Copy
<?php
namespace App\Services;

class RoleHierarchy
{
    public static function accessibleRoles(string $roleName): array
    {
        $order = [
            'team_member',
            'group_leader',
            'section_leader',
            'supervisor',
            'assistant_manager',
            'team_manager',
            'department_manager',
            'admin',
        ];

        $idx = array_search($roleName, $order);
        if ($idx === false) {
            return [$roleName]; // fallback
        }

        // roles below or equal to this role (i.e. this role can see itself and lower in hierarchy)
        return array_slice($order, 0, $idx + 1);
    }
}
Contoh penggunaan di controller (riwayat yang bisa dilihat user):


Copy
use App\Services\RoleHierarchy;
use Spatie\Permission\Models\Role;

public function index(Request $request)
{
    $user = $request->user();
    $userRole = $user->getRoleNames()->first();

    $accessibleRoles = RoleHierarchy::accessibleRoles($userRole);

    // ambil user ids yang memiliki role di $accessibleRoles
    $userIds = \App\Models\User::role($accessibleRoles)->pluck('id')->toArray();

    // jika hanya melihat riwayat sendiri: $userIds akan berisi sendiri dan mungkin tim
    $attendances = \App\Models\Attendance::whereIn('user_id', $userIds)
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    return view('attendances.index', compact('attendances'));
}
Penjelasan: fungsi role($roles) berasal dari Spatie trait (query scope).

8) Route & middleware
Tambahkan middleware untuk route:

routes/web.php:


Copy
Route::middleware(['auth'])->group(function () {
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances', [AttendanceController::class, 'store'])
        ->name('attendances.store')->middleware('permission:attendance.create');
});
Untuk Filament admin, biasanya harus set gate viewFilament agar hanya admin dapat akses:

Di App\Providers\FilamentServiceProvider (atau AuthServiceProvider):


Copy
use Filament\Facades\Filament;

Filament::serving(function () {
    Filament::registerNavigationItems([
        //
    ]);
});

Gate::define('viewFilament', function ($user) {
    return $user->hasRole('admin') || $user->can('filament.access');
});
9) Filament Resources (opsional): User & Attendance
Buat Filament Resource untuk Attendance (hanya admin dan role berizin):


Copy
php artisan make:filament-resource Attendance
Di resource query, gunakan scope untuk memfilter jika bukan admin:


Copy
protected static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();
    if (!$user->hasRole('admin')) {
        $role = $user->getRoleNames()->first();
        $accessibleRoles = \App\Services\RoleHierarchy::accessibleRoles($role);
        $userIds = \App\Models\User::role($accessibleRoles)->pluck('id')->toArray();

        return $query->whereIn('user_id', $userIds);
    }
    return $query;
}
Untuk akses ke resource, di getPages() / pages kamu bisa menambahkan policy or can('attendance.view.*') checks.

10) Frontend: tampilkan tombol absen & riwayat
Contoh blade (simple):

resources/views/attendances/index.blade.php:


Copy
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Riwayat Absensi</h1>

    @can('attendance.create')
    <form action="{{ route('attendances.store') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary">Absen Sekarang</button>
    </form>
    @endcan

    <table class="table mt-4">
        <thead>
            <tr>
                <th>User</th>
                <th>Tanggal</th>
                <th>Tipe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $a)
            <tr>
                <td>{{ $a->user->name }}</td>
                <td>{{ $a->created_at }}</td>
                <td>{{ $a->type ?? 'in' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $attendances->links() }}
</div>
@endsection
11) AttendanceController store (contoh sederhana)

Copy
public function store(Request $request)
{
    $this->authorize('create', Attendance::class); // optional, atau middleware permission

    $attendance = Attendance::create([
        'user_id' => $request->user()->id,
        'type' => 'in',
        // other fields...
    ]);

    return redirect()->route('attendances.index')->with('success', 'Absen berhasil.');
}
Jika menggunakan middleware 'permission:attendance.create' maka authorize line optional.

12) Testing & verifikasi
Jalankan seeder RolePermissionSeeder.

Buat beberapa user dan assign roles berbeda (tinker / seeder).

Login sebagai team_member — coba absen, lihat riwayat: harusnya hanya riwayat sendiri.

Login sebagai group_leader — lihat riwayat: harusnya termasuk team_member (gunakan data test yang memetakan anggota ke role).

Login sebagai admin — cek Filament admin panel bisa diakses.

Tips debug: gunakan dd($accessibleRoles, $userIds) di controller untuk memastikan logika hierarki.

13) Catatan penting & asumsi
Saya mengasumsikan model Attendance sudah ada dan ber-relasi ke User (field user_id). Jika belum, buat migration:


Copy
php artisan make:migration create_attendances_table
Contoh schema:


Copy
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->string('type')->nullable(); // in/out
$table->timestamps();
Saya mengasumsikan tiap user hanya memakai 1 role utama. Jika user punya multi-role, perlu definisikan primary role atau aturan resolusi.

Filament Shield/Shield package name/version bisa berbeda; cek composer require output jika ada perintah alternatif.

Jika ingin granular team membership (mis. group/team relationships), untuk filter riwayat lebih akurat perlu tambahkan relasi users.team_id atau users.manager_id. Saat ini akses diatur hanya berdasarkan role-hierarchy, bukan struktur organisasi/relasi tim.

14) Lampiran: snippet cepat untuk menambahkan primary role pada users table (opsional)
Jika ingin menyimpan primary_role pada users:

Migration:


Copy
Schema::table('users', function (Blueprint $table) {
    $table->string('primary_role')->nullable()->after('email');
});
Set primary_role ketika assign role, dan gunakan itu dalam RoleHierarchy::accessibleRoles($user->primary_role) untuk konsistensi.

Halaman Login

Copy
/admin
Namun setelah login, arah redirect-nya berbeda tergantung role.
Admin diarahkan ke Halaman Admin (Filament Panel)
User lain diarahkan ke Halaman Absensi (absen.index)

✅ Cara membuat redirect berbeda setelah login
Edit file berikut:

app/Http/Controllers/Auth/AuthenticatedSessionController.php

Pada method store() ubah bagian setelah $request->session()->regenerate() seperti ini:


Copy
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    $user = $request->user();

    // ==== REDIRECT BY ROLE ====
    if ($user->hasRole('admin')) {
        return redirect()->intended('/admin');  // Filament admin panel
    }

    // selain admin masuk ke halaman absensi
    return redirect()->intended(route('attendances.index'));
}
Jika Filament admin URL kamu adalah /admin, sudah benar.
Jika berbeda (misalnya /dashboard), sesuaikan redirect-nya.

🔥 Alur yang terjadi
1️⃣ User membuka /login
Semua role memakai halaman yang sama.

2️⃣ User login → cek role
Admin → masuk ke Admin Panel (Filament)

Team_member, group_leader, dll → langsung masuk ke halaman absen

3️⃣ Role-based feature
User tetap punya satu halaman login, tapi dashboard tujuannya beda.

2️⃣ Role & Permission Menggunakan Filament Shield
1. Buat Resource User
Make User Resource
Sebelum melanjutkan membuat Role & Permission menggunakan Filament Shield, Buat User Resource dengan menjalankan perintah berikut 


Copy
php artisan make:filament-resource User --generate
perintah di atas digunakan untuk menampilkan menu Management User pada Admin Panel

Ubah Position Id -> Position Name
Buka UserResource > Ganti Bagian Form


Copy
Forms\Components\Select::make('position_id')
menjadi:


Copy
Forms\Components\Select::make('position_id')
    ->relationship('position', 'position_name') // ambil nama dari tabel positions
    ->label('Position')
    ->searchable()
    ->required(),
Ganti Bagian Table


Copy
Tables\Columns\TextColumn::make('position_id')
menjadi:


Copy
Tables\Columns\TextColumn::make('position.position_name')
    ->label('Position')
    ->sortable()
    ->searchable(),
Buka User Model
Tambahkan kolom position_id


Copy
protected $fillable = [
        'name',
        'email',
        'password',
        'position_id', //Tambahkan position_id 
    ];
Tambahkan juga fungsi untuk relasi ke tabel position


Copy
public function position()
    {
        return $this->belongsTo(\App\Models\Position::class);
    }
Buka Position Model
Tambahkan juga fungsi untuk relasi ke tabel position


Copy
public function users()
    {
        return $this->hasMany(User::class);
    }
2. Install Filament Shield
Jalankan perintah berikut untuk menginstall Filament Shield


Copy
composer require bezhansalleh/filament-shield:"^3.9.10"

Copy
php artisan vendor:publish --tag="filament-shield-config"

Buka file Model User pada direktori : app\Models\User.php > tambahkan HasRoles


Kemudian jalankan perintah berikut


Copy
php artisan shield:setup

Buka AdminPanelProvider pada direktori : app\Providers\Filament\AdminPanelProvider.php


Copy
// Tambahkan baris kode berikut

->plugins([
      \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
  ]);
Jalankan perintah berikut pada Terminal


Copy
php artisan optimize:clear

Jalankan perintah berikut pada Terminal > setelah itu ketik 0 > Enter


Copy
php artisan shield:generate --all

Filament Shield mengenali super_admin yang memiliki kontrol penuh terhadap semua akses, berikut adalah perintah untuk menetapkan user yang ada sebagai super_admin


Copy
php artisan shield:super-admin

jika user lebih dari 1, kita diberikan pilihan untuk memilih 1 user untuk dijadikan super_admin - jika hanya baru memiliki 1 user otomatis akan langsung dijadikan super_admin ketika menjalankan perintah di atas.

Langkah terakhir masuk sebagai user yang memiliki role super_admin


3️⃣
Buat Roles
Buat Seeder Untuk membuat User untuk semua Karyawan

Buat Seeder untuk membuat Roles baru 


Copy
php artisan make:seeder RoleSeeder
Buka File RoleSeeder


Copy
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin',
            'team_member',
            'group_leader',
            'section_leader',
            'supervisor',
            'assistant_manager',
            'team_manager',
            'departement_manager',
        ];

        foreach($roles as $role){
            Role::firstOrCreate(
                ['name' => $role, 'guard_name' => 'web']
            );
        }
    }
}
Jalankan RoleSeeder


Copy
php artisan db:seed --class=RoleSeeder
Tambahkan kolom karyawan_id
Tambahkan kolom karyawan_id pada tabel Users


Copy
php artisan make:migration add_karyawan_id_to_users_table --table=users
Buka file migration add_karyawan_id_to_users_table


Copy
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // kalau pakai tabel 'karyawans'
            $table->foreignId('karyawan_id')
                ->nullable()
                ->constrained('karyawans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['karyawan_id']);
            $table->dropColumn('karyawan_id');
        });
    }
};
Buka file User Model > tambahkah karyawan_id pada fillable


Copy
protected $fillable = [
        'name',
        'email',
        'password',
        'position_id',
        'karyawan_id', //tambahkan baris ini
    ];
Jalankan Migrate


Copy
php artisan migrate
Buat user untuk semua Karyawan
Jalankan perintah berikut untuk membuat daftar User yang diambil dari data Karyawan


Copy
php artisan make:seeder KaryawanToUserSeeder
Buka file KaryawanToUser Seeder


Copy
<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KaryawanToUserSeeder extends Seeder
{
    public function run(): void
    {
        // mapping position_id (angka) ke nama role
        $positionIdToRole = [
            1 => 'super_admin',            
            2 => 'team_member',
            3 => 'group_leader',
            4 => 'section_leader',
            5 => 'supervisor',
            6 => 'assistant_manager',
            7 => 'team_manager',
            8 => 'departement_manager',
        ];

        Karyawan::chunk(100, function ($karyawans) use ($positionIdToRole) {
            foreach ($karyawans as $karyawan) {

                $roleName = $positionIdToRole[$karyawan->position_id] ?? null;

                $user = User::firstOrCreate(
                    ['email' => $karyawan->email],
                    [
                        'name'        => $karyawan->nama_karyawan,
                        'karyawan_id' => $karyawan->id,
                        'position_id' => $karyawan->position_id,
                        'password'    => Hash::make('poscodx'),
                    ],
                );

                if ($roleName) {
                    $user->syncRoles([$roleName]);
                }
            }
        });
    }
}
Jalankan KaryawanToUserSeeder


Copy
php artisan db:seed --class=KaryawanToUserSeeder
Cek Roles
untuk menguji role yang dimiliki oleh user apakah sudah diassign, jalankan perintah berikut pada Terminal : 

Buka Terminal dan Jalankan Tinker:

Di root project Laravel Anda, jalankan:


Copy
php artisan tinker
Ini akan membuka shell interaktif.

Cari User yang Ingin Diperiksa:

Misalnya, untuk user dengan ID 1:


Copy
$user = App\Models\User::find(5);
Ganti 1 dengan ID user yang sesuai. Jika user tidak ditemukan, $user akan null.

Periksa Role yang Dimiliki User:

Untuk melihat semua role:


Copy
$user->roles;
Ini akan menampilkan collection dari model Role yang dimiliki user.

Untuk mendapatkan nama role saja (dalam bentuk array):


Copy
$user->getRoleNames();
Output contoh: ["admin", "editor"]

Periksa Permission yang Dimiliki User:

Untuk melihat semua permission:


Copy
$user->permissions;
Ini akan menampilkan collection dari model Permission.

Untuk nama permission saja:


Copy
$user->getPermissionNames();
Output contoh: ["view admin panel", "edit posts"]

Cek Role atau Permission Spesifik:

Untuk memeriksa apakah user memiliki role tertentu (misalnya, 'admin'):


Copy
$user->hasRole('admin');
Output: true atau false.

Untuk permission:


Copy
$user->hasPermissionTo('view admin panel');
Output: true atau false.

Keluar dari Tinker:

Ketik exit atau tekan Ctrl+D.

4️⃣
Buat Seeder Absensi

Copy
php artisan make:seeder AbsensiSeeder    
Buka file AbsensiSeeder, paste kode berikut : 


Copy
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Absensi;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        // ambil semua karyawan yang sudah di-seed
        $karyawans = Karyawan::all();

        if ($karyawans->isEmpty()) {
            $this->command->warn('Tidak ada data karyawan. Jalankan KaryawanSeeder dulu.');
            return;
        }

        // contoh: generate absensi untuk 5 hari terakhir (hari kerja)
        $jumlahHari = 5;
        $today      = Carbon::today();

        for ($i = $jumlahHari; $i >= 1; $i--) {
            $tanggal = $today->copy()->subDays($i);

            foreach ($karyawans as $karyawan) {

                // jam masuk standar 08:00, jam pulang standar 17:00
                $waktuMasuk  = $tanggal->copy()->setTime(8, 0, 0);
                $waktuPulang = $tanggal->copy()->setTime(17, 0, 0);

                // random overtime (true/false)
                $isOvertime = (bool) random_int(0, 1);

                $alasanOvertime = null;

                if ($isOvertime) {
                    // kalau lembur, pulang lebih lambat 2 jam
                    $waktuPulang = $waktuPulang->copy()->addHours(2);
                    $alasanOvertime = 'Pekerjaan belum selesai';
                }

                Absensi::create([
                    'karyawan_id'     => $karyawan->id,
                    'tanggal'         => $tanggal->toDateString(),
                    'waktu_masuk'     => $waktuMasuk->toDateTimeString(),
                    'waktu_pulang'    => $waktuPulang->toDateTimeString(),
                    'is_overtime'     => $isOvertime,
                    'alasan_overtime' => $alasanOvertime,
                    'foto_masuk'      => null, // bisa diganti dummy path kalau mau
                    'foto_pulang'     => null, // bisa diganti dummy path kalau mau
                ]);
            }
        }
    }
}
Jalankan Absensi Seeder


Copy
php artisan db:seed --class=AbsensiSeeder
Untuk menampilkan Position Name pada table absensi, buka Absensi Resource


Copy
Tables\Columns\TextColumn::make('karyawan.position.position_name')
                    ->label('Position')
                    ->sortable()
                    ->searchable(),


                    5️⃣
Filter Role Absensi
Buka Absensi Resource, tambahkan kode berikut.


Copy
 protected static array $positionRankById = [
        2 => 1, // team_member
        3 => 2, // group_leader
        4 => 3, // section_leader
        5 => 4, // supervisor
        6 => 5, // assistant_manager
        7 => 6, // team_manager
        8 => 7, // departement_manager
    ];

Copy
    /**
     * Batasi data absensi yang bisa dilihat user sesuai jabatan & team.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = Auth::user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // SUPER ADMIN (role Spatie) → boleh lihat semua absensi
        if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
            return $query;
        }

        // Ambil karyawan yang terkait dengan user
        $karyawan = $user->karyawan ?? null;
        if (! $karyawan) {
            // User tidak punya karyawan terkait → jangan tampilkan apa-apa
            return $query->whereRaw('1 = 0');
        }

        $positionId = $karyawan->position_id;

        // Kalau position_id tidak dikenal di hirarki → hanya lihat absensi sendiri
        if (! array_key_exists($positionId, self::$positionRankById)) {
            return $query->where('karyawan_id', $karyawan->id);
        }

        $currentRank = self::$positionRankById[$positionId];

        // TEAM MEMBER (rank paling rendah) → hanya boleh lihat absensi sendiri
        if ($currentRank === 1) {
            return $query->where('karyawan_id', $karyawan->id);
        }

        // Untuk atasan:
        // - boleh lihat dirinya sendiri
        // - boleh lihat bawahan DI TEAM YANG SAMA
        //   dengan position_id yang rank-nya <= rank dia (lebih rendah atau sama)

        $allowedPositionIds = collect(self::$positionRankById)
            ->filter(fn ($rank) => $rank <= $currentRank)
            ->keys(); // ini berisi daftar position_id yang boleh dia lihat

        return $query->where(function (Builder $outer) use ($karyawan, $allowedPositionIds) {
            $outer
                // selalu boleh lihat absensi sendiri
                ->where('karyawan_id', $karyawan->id)
                // + bawahan di team yang sama
                ->orWhereHas('karyawan', function (Builder $q) use ($karyawan, $allowedPositionIds) {
                    $q->where('team_id', $karyawan->team_id)
                      ->whereIn('position_id', $allowedPositionIds);
                });
        });
    }

    ✅ Absensi Custom
🎯Super Admin bisa absen semua karyawan 
Buka Absensi Resource, ganti bagian Select Karyawan


Copy
Forms\Components\Select::make('karyawan_id')
    ->label('Karyawan')
    ->relationship('karyawan', 'nama_karyawan')
    ->searchable()
    ->preload()
    ->required()
    ->visible(fn () =>
        Auth::user()?->hasRole('super_admin')
    )
    ->disabled(fn () =>
        ! Auth::user()?->hasRole('super_admin')
    )
    ->default(fn () =>
        Auth::user()?->karyawan?->id
    ),
🔐 Tambahkan Hidden field fallback (WAJIB)
Karena kalau field Select disembunyikan, nilainya tidak ikut submit, maka kita tambahkan:


Copy
Forms\Components\Hidden::make('karyawan_id')
    ->default(fn () =>
        Auth::user()?->karyawan?->id
    )
    ->required()
    ->visible(fn () =>
        ! Auth::user()?->hasRole('super_admin')
    ),
📌 Ini penting supaya data tetap tersimpan untuk user biasa.

✅ HASIL AKHIR (sesuai requirement)
🔹 super_admin
Dropdown muncul

Bisa pilih semua karyawan

🔹 team_member / group_leader / dst
Tidak ada dropdown

Otomatis tersimpan sebagai dirinya sendiri

Tidak bisa curang pilih orang lain

🎯Create Absensi

Copy
<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AbsensiResource;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\NotificationException;
use App\Models\Absensi;
use App\Helpers\ImageCompressor;


class CreateAbsensi extends CreateRecord
{
    protected static string $resource = AbsensiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        // ================================
        // 1️⃣ Paksa karyawan_id (anti inject)
        // ================================
        if (! $user->hasRole('super_admin')) {
            $data['karyawan_id'] = $user->karyawan->id;
        }

        // ================================
        // 2️⃣ Cek absensi di tanggal yang sama
        // ================================
        $exists = Absensi::where('karyawan_id', $data['karyawan_id'])
            ->where('tanggal', $data['tanggal'])
            ->exists();

        $data['is_overtime'] = $exists;

        // ================================
        // 3️⃣ Validasi alasan lembur
        // ================================
        if ($data['is_overtime'] && empty($data['alasan_overtime'])) {
            throw new NotificationException('Alasan lembur wajib diisi!');
        }

        // ================================
        // 4️⃣ Gabungkan tanggal + jam → TIMESTAMP
        // ================================
        $tanggal = Carbon::parse($data['tanggal']);

        $data['waktu_masuk'] = ! empty($data['waktu_masuk'])
            ? $tanggal->copy()->setTimeFromTimeString($data['waktu_masuk'])
            : null;

        $data['waktu_pulang'] = ! empty($data['waktu_pulang'])
            ? $tanggal->copy()->setTimeFromTimeString($data['waktu_pulang'])
            : null;



        if (! empty($data['foto_masuk'])) {
            ImageCompressor::compress($data['foto_masuk'], 100);
        }

        if (! empty($data['foto_pulang'])) {
            ImageCompressor::compress($data['foto_pulang'], 100);
        }
        return $data;
    }

    // ================================
    // 5️⃣ Kompres foto (max 100 KB)
    // ================================


}
🎯Edit Absensi

Copy
<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\AbsensiResource;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\NotificationException;
use App\Models\Absensi;

use App\Helpers\ImageCompressor;


class EditAbsensi extends EditRecord
{
    protected static string $resource = AbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * ===============================
     * 1️⃣ Saat form dibuka
     * timestamp → TimePicker
     * ===============================
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['waktu_masuk'])) {
            $data['waktu_masuk'] = Carbon::parse($data['waktu_masuk'])->format('H:i');
        }

        if (! empty($data['waktu_pulang'])) {
            $data['waktu_pulang'] = Carbon::parse($data['waktu_pulang'])->format('H:i');
        }

        return $data;
    }

    /**
     * ===============================
     * 2️⃣ Saat simpan data
     * TimePicker → timestamp
     * ===============================
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();

        // 🔐 Paksa karyawan_id (anti inject)
        if (! $user->hasRole('super_admin')) {
            $data['karyawan_id'] = $this->record->karyawan_id;
        }

        // ===============================
        // Cek apakah ini lembur
        // ===============================
        $exists = Absensi::where('karyawan_id', $data['karyawan_id'])
            ->where('tanggal', $data['tanggal'])
            ->where('id', '!=', $this->record->id)
            ->exists();

        $data['is_overtime'] = $exists;

        if ($data['is_overtime'] && empty($data['alasan_overtime'])) {
            throw new NotificationException('Alasan lembur wajib diisi!');
        }

        // ===============================
        // Gabungkan tanggal + jam
        // ===============================
        $tanggal = Carbon::parse($data['tanggal']);

        $data['waktu_masuk'] = ! empty($data['waktu_masuk'])
            ? $tanggal->copy()->setTimeFromTimeString($data['waktu_masuk'])
            : null;

        $data['waktu_pulang'] = ! empty($data['waktu_pulang'])
            ? $tanggal->copy()->setTimeFromTimeString($data['waktu_pulang'])
            : null;

        // ===============================
        // Validasi jam
        // ===============================
        if (
            $data['waktu_masuk'] &&
            $data['waktu_pulang'] &&
            Carbon::parse($data['waktu_pulang'])->lessThanOrEqualTo(
                Carbon::parse($data['waktu_masuk'])
            )
        ) {
            throw new NotificationException('Waktu pulang harus lebih besar dari waktu masuk.');
        }

        if (! empty($data['foto_masuk']) && $data['foto_masuk'] !== $this->record->foto_masuk) {
    ImageCompressor::compress($data['foto_masuk'], 100);
}

if (! empty($data['foto_pulang']) && $data['foto_pulang'] !== $this->record->foto_pulang) {
    ImageCompressor::compress($data['foto_pulang'], 100);
}


        return $data;
    }
}
🎯Absensi Resource

Copy
<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Absensi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AbsensiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AbsensiResource\RelationManagers;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static array $positionRankById = [
        2 => 1, // team_member
        3 => 2, // group_leader
        4 => 3, // section_leader
        5 => 4, // supervisor
        6 => 5, // assistant_manager
        7 => 6, // team_manager
        8 => 7, // departement_manager
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('karyawan_id')
                ->label('Karyawan')
                ->relationship('karyawan', 'nama_karyawan')
                ->searchable()
                ->preload()
                ->required()
                ->visible(
                    fn() =>
                    Auth::user()?->hasRole('super_admin')
                )
                ->disabled(
                    fn() =>
                    ! Auth::user()?->hasRole('super_admin')
                )
                ->default(
                    fn() =>
                    Auth::user()?->karyawan?->id
                ),
            Forms\Components\Hidden::make('karyawan_id')
                ->default(
                    fn() =>
                    Auth::user()?->karyawan?->id
                )
                ->required()
                ->visible(
                    fn() =>
                    ! Auth::user()?->hasRole('super_admin')
                ),


            Forms\Components\DatePicker::make('tanggal')
                ->label('Tanggal')
                ->default(now())
                ->required(),

            Forms\Components\TimePicker::make('waktu_masuk')
                ->label('Waktu Masuk')
                ->required(),

            Forms\Components\TimePicker::make('waktu_pulang')
                ->label('Waktu Pulang')
                ->required(),

            Forms\Components\FileUpload::make('foto_masuk')
                ->label('Foto Masuk')
                ->disk('public')
                ->directory('absensi/foto_masuk')
                ->image()
                ->imagePreviewHeight('150')
                ->maxSize(6120) // 5MB upload awal
                ->acceptedFileTypes(['image/jpeg', 'image/png']),

            Forms\Components\FileUpload::make('foto_pulang')
                ->label('Foto Pulang')
                ->disk('public')
                ->directory('absensi/foto_pulang')
                ->image()
                ->imagePreviewHeight('150')
                ->maxSize(6120)
                ->acceptedFileTypes(['image/jpeg', 'image/png']),

            Forms\Components\Toggle::make('is_overtime')
                ->label('Apakah Overtime?')
                ->reactive(),

            Forms\Components\Textarea::make('alasan_overtime')
                ->label('Alasan Overtime')
                ->visible(fn($get) => $get('is_overtime'))
                ->required(fn($get) => $get('is_overtime')),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('karyawan.nama_karyawan')->label('Karyawan')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('karyawan.position.position_name')
                ->label('Position')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('tanggal')->label('Tanggal')->date()->sortable(),
            Tables\Columns\TextColumn::make('waktu_masuk')->label('Masuk')->time(),
             // =====================
            // FOTO MASUK
            // =====================
            Tables\Columns\ImageColumn::make('foto_masuk')
                ->label('Foto Masuk')
                ->disk('public')
                ->height(50)
                ->width(50)
                ->circular()
                ->toggleable(),

           
            Tables\Columns\TextColumn::make('waktu_pulang')->label('Pulang')->time(),
             // =====================
            // FOTO PULANG
            // =====================
            Tables\Columns\ImageColumn::make('foto_pulang')
                ->label('Foto Pulang')
                ->disk('public')
                ->height(50)
                ->width(50)
                ->circular()
                ->toggleable(),

            Tables\Columns\IconColumn::make('is_overtime')
                ->boolean()
                ->label('Lembur'),
            Tables\Columns\TextColumn::make('alasan_overtime')
                ->label('Alasan Overtime')
                ->toggleable()
                ->limit(30),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Batasi data absensi yang bisa dilihat user sesuai jabatan & team.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = Auth::user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // SUPER ADMIN (role Spatie) → boleh lihat semua absensi
        if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
            return $query;
        }

        // Ambil karyawan yang terkait dengan user
        $karyawan = $user->karyawan ?? null;
        if (! $karyawan) {
            // User tidak punya karyawan terkait → jangan tampilkan apa-apa
            return $query->whereRaw('1 = 0');
        }

        $positionId = $karyawan->position_id;

        // Kalau position_id tidak dikenal di hirarki → hanya lihat absensi sendiri
        if (! array_key_exists($positionId, self::$positionRankById)) {
            return $query->where('karyawan_id', $karyawan->id);
        }

        $currentRank = self::$positionRankById[$positionId];

        // TEAM MEMBER (rank paling rendah) → hanya boleh lihat absensi sendiri
        if ($currentRank === 1) {
            return $query->where('karyawan_id', $karyawan->id);
        }

        // Untuk atasan:
        // - boleh lihat dirinya sendiri
        // - boleh lihat bawahan DI TEAM YANG SAMA
        //   dengan position_id yang rank-nya <= rank dia (lebih rendah atau sama)

        $allowedPositionIds = collect(self::$positionRankById)
            ->filter(fn($rank) => $rank <= $currentRank)
            ->keys(); // ini berisi daftar position_id yang boleh dia lihat

        return $query->where(function (Builder $outer) use ($karyawan, $allowedPositionIds) {
            $outer
                // selalu boleh lihat absensi sendiri
                ->where('karyawan_id', $karyawan->id)
                // + bawahan di team yang sama
                ->orWhereHas('karyawan', function (Builder $q) use ($karyawan, $allowedPositionIds) {
                    $q->where('team_id', $karyawan->team_id)
                        ->whereIn('position_id', $allowedPositionIds);
                });
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}