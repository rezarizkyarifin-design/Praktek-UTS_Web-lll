<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Riwayat Absensi
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (auth()->user()->karyawan)
                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Catat Absensi</h3>
                    <form method="POST" action="{{ route('attendances.store') }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
                        @csrf

                        <div>
                            <x-input-label for="tanggal" value="Tanggal" />
                            <x-text-input id="tanggal" name="tanggal" type="date" class="mt-1 block w-full" :value="old('tanggal', today()->toDateString())" required />
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="waktu_masuk" value="Waktu Masuk" />
                            <x-text-input id="waktu_masuk" name="waktu_masuk" type="time" class="mt-1 block w-full" :value="old('waktu_masuk')" required />
                            <x-input-error :messages="$errors->get('waktu_masuk')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="waktu_pulang" value="Waktu Pulang (opsional)" />
                            <x-text-input id="waktu_pulang" name="waktu_pulang" type="time" class="mt-1 block w-full" :value="old('waktu_pulang')" />
                            <x-input-error :messages="$errors->get('waktu_pulang')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="alasan_overtime" value="Alasan Overtime (diisi untuk absensi kedua)" />
                            <x-text-input id="alasan_overtime" name="alasan_overtime" type="text" class="mt-1 block w-full" maxlength="255" :value="old('alasan_overtime')" />
                            <x-input-error :messages="$errors->get('alasan_overtime')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="foto_masuk" value="Foto Masuk (opsional)" />
                            <input id="foto_masuk" name="foto_masuk" type="file" accept="image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-700">
                            <x-input-error :messages="$errors->get('foto_masuk')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="foto_pulang" value="Foto Pulang (opsional)" />
                            <input id="foto_pulang" name="foto_pulang" type="file" accept="image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-700">
                            <x-input-error :messages="$errors->get('foto_pulang')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-primary-button>Simpan Absensi</x-primary-button>
                        </div>
                    </form>
                </section>
            @else
                <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-800">
                    Akun Anda belum terhubung dengan data karyawan. Hubungi administrator.
                </div>
            @endif

            <section class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Karyawan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Team / Departemen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Masuk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pulang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Overtime</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($attendances as $attendance)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $attendance->tanggal->format('d M Y') }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $attendance->karyawan->nama_karyawan }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        {{ $attendance->karyawan->team->nama_team }} / {{ $attendance->karyawan->team->departemen->nama_departemen }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $attendance->waktu_masuk }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $attendance->waktu_pulang ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $attendance->is_overtime ? 'Ya: '.$attendance->alasan_overtime : 'Tidak' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada data absensi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $attendances->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
