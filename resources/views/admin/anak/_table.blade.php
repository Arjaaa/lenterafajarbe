<div class="table-responsive text-nowrap">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Nama Anak</th>
                <th>Akun Wali (Ortu)</th>
                <th>Asal Sekolah</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            @forelse ($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="avatar avatar-md">
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $student->name }}"
                                    class="rounded-circle" style="object-fit: cover;">
                            @else
                                <span class="avatar-initial rounded-circle bg-label-secondary"><i class="bx bx-user"></i></span>
                            @endif
                        </div>
                    </td>
                    <td><strong>{{ $student->name }}</strong></td>
                    <td>
                        {{ $student->parent_id && \App\Models\User::find($student->parent_id) ? \App\Models\User::find($student->parent_id)->name : 'Belum Ditautkan' }}
                    </td>
                    <td>{{ $student->school_name ?? '-' }}</td>
                    <td><span class="badge bg-label-success me-1">Aktif</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-icon btn-info" data-bs-toggle="modal"
                            data-bs-target="#modalDetailAnak{{ $student->id }}" title="Lihat Detail">
                            <i class="bx bx-show"></i>
                        </button>

                        <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalEditAnak{{ $student->id }}" title="Edit">
                            <i class="bx bx-edit-alt"></i>
                        </button>

                        <form action="{{ route('koor.destroyAnak', $student->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Kamu yakin ingin menghapus data anak {{ $student->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i
                                    class="bx bx-trash"></i></button>
                        </form>

                        {{-- Panggil Potongan Modal Detail & Edit di Sini --}}
                        @include('admin.anak._modal-detail')
                        @include('admin.anak._modal-edit')

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data anak di database.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>