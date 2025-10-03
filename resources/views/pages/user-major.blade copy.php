@extends('layouts.dashboard')

@section('title')
  Pilih Jurusan
@endsection
<!-- Notifikasi sukses -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
@section('content')
<div class="container my-5">
  <form action="{{ route('user.majors.store') }}" method="POST" id="major-form">
    @csrf
    <h2 class="mb-4">Pilihan Jurusan</h2>
    {{-- notifikasi --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
  $majorsArray = $majors->map(function($m) {
    return [
      'id' => $m->id,
      'name' => $m->name,
      'university_id' => $m->university_id,
      'quota' => $m->quota,
      'peminat' => $m->peminat,
      'level' => $m->level,
    ];
  })->toArray();
@endphp
    
 <div id="cards-container">
 @if(count($userMajors) > 0)
       <div id="cards-container">
  @foreach($userMajors as $i => $major)
  @php
    $selectedMajor = \App\Models\Major::with('university')->find($major);
  @endphp
  <div class="card mb-4 shadow-sm border-0 major-card" data-initialized="true">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title mb-0 pilihan-title">Pilihan {{ $i + 1 }}</h5>
        <h4 class="badge bg-primary level-badge">{{ $selectedMajor->level }}</h4>
      </div>

      <div class="mb-3">
        <label class="form-label">Perguruan Tinggi</label>
        <select class="form-select university-select" name="universities[]" data-index="{{ $i }}">
          <option disabled selected>-- Pilih Universitas --</option>
          @foreach($universities as $u)
            <option value="{{ $u->id }}" {{ $u->id == $selectedMajor->university->id ? 'selected' : '' }}>
              {{ $u->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Program Studi</label>
        <select class="form-select major-select" name="majors[]">
          @foreach($majors->where('university_id', $selectedMajor->university->id) as $m)
            <option value="{{ $m->id }}" {{ $m->id == $selectedMajor->id ? 'selected' : '' }}>
              {{ $m->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="row text-center mt-4 info-section">
        <div class="col">
          <div class="fw-semibold text-muted">Daya Tampung</div>
          <div class="fs-5 quota">{{ $selectedMajor->quota }}</div>
        </div>
        <div class="col">
          <div class="fw-semibold text-muted">Peminat</div>
          <div class="fs-5 peminat">{{ $selectedMajor->peminat }}</div>
        </div>
        <div class="col">
          <div class="fw-semibold text-muted">Keketatan</div>
          <div class="fs-5 keketatan">
            @php
              $keketatan = $selectedMajor->peminat > 0 ? number_format(($selectedMajor->quota / $selectedMajor->peminat) * 100, 2) . '%' : '0%';
            @endphp
            {{ $keketatan }}
          </div>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-outline-danger mt-3 remove-card">Hapus</button>
    </div>
  </div>
@endforeach
</div>
  @else
    <div class="card mb-4 shadow-sm border-0 major-card" data-initialized="false">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
         <h5 class="card-title mb-0 pilihan-title">Pilihan 1</h5>
          <h4 class="badge bg-primary level-badge">-</h4>
        </div>

        <div class="mb-3">
          <label class="form-label">Perguruan Tinggi</label>
          <select class="form-select university-select" name="universities[]" data-index="0">
            <option disabled selected>-- Pilih Universitas --</option>
            @foreach($universities as $u)
              <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Program Studi</label>
          <select class="form-select major-select" name="majors[]" disabled>
            <option disabled selected>-- Pilih Prodi --</option>
          </select>
        </div>

        <div class="row text-center mt-4 info-section">
          <div class="col">
            <div class="fw-semibold text-muted">Daya Tampung</div>
            <div class="fs-5 quota">-</div>
          </div>
          <div class="col">
            <div class="fw-semibold text-muted">Peminat</div>
            <div class="fs-5 peminat">-</div>
          </div>
          <div class="col">
            <div class="fw-semibold text-muted">Keketatan</div>
            <div class="fs-5 keketatan">-</div>
          </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger mt-3 remove-card">Hapus</button>
      </div>
    </div>
  @endif
</div>
    <button type="button" class="btn btn-sm btn-outline-danger remove-card">×</button>

    <div class="d-grid mb-3">
      <button type="button" class="btn btn-outline-primary" id="add-card">
        <i class="bi bi-plus-circle me-2"></i>Tambah Pilihan
      </button>
    </div>

    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-success">
        <i class="bi bi-save me-1"></i> Simpan Semua
      </button>
    </div>
  </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('cards-container');
  const addBtn = document.getElementById('add-card');

  // 1. Data jurusan lengkap dari backend (blade), bentuk array of object
  const allMajors = @json($majorsArray);

  // 2. Build majorCache indexed by major id
  const majorCache = {};
  allMajors.forEach(m => majorCache[m.id] = m);

  // 3. Build universityCache indexed by university id, for easy lookup majors per university
  const universityCache = {};
  allMajors.forEach(m => {
    if (!universityCache[m.university_id]) universityCache[m.university_id] = [];
    universityCache[m.university_id].push(m);
  });

  function updateInfoSection(card, majorData) {
    card.querySelector('.quota').textContent = majorData.quota;
    card.querySelector('.peminat').textContent = majorData.peminat;
    card.querySelector('.level-badge').textContent = majorData.level;
    const keketatan = majorData.peminat > 0
      ? ((majorData.quota / majorData.peminat) * 100).toFixed(2) + '%'
      : '0%';
    card.querySelector('.keketatan').textContent = keketatan;
  }

  function renderMajorOptions(majorSelect, universityId) {
    majorSelect.innerHTML = '<option disabled selected>-- Pilih Prodi --</option>';
    if(universityCache[universityId]){
      universityCache[universityId].forEach(major => {
        const option = document.createElement('option');
        option.value = major.id;
        option.textContent = major.name;
        majorSelect.appendChild(option);
      });
    }
    majorSelect.disabled = false;
  }

  function initCard(card) {
    const universitySelect = card.querySelector('.university-select');
    const majorSelect = card.querySelector('.major-select');

    universitySelect.addEventListener('change', function () {
      const universityId = this.value;

      majorSelect.innerHTML = '<option>Loading...</option>';
      majorSelect.disabled = true;

      renderMajorOptions(majorSelect, universityId);

      // Reset info section when university changes
      card.querySelector('.quota').textContent = '-';
      card.querySelector('.peminat').textContent = '-';
      card.querySelector('.level-badge').textContent = '-';
      card.querySelector('.keketatan').textContent = '-';
    });

    majorSelect.addEventListener('change', function () {
      const selectedId = this.value;
      const selectedData = majorCache[selectedId];

      if (selectedData) {
        updateInfoSection(card, selectedData);
      } else {
        // fallback kosong
        card.querySelector('.quota').textContent = '-';
        card.querySelector('.peminat').textContent = '-';
        card.querySelector('.level-badge').textContent = '-';
        card.querySelector('.keketatan').textContent = '-';
      }
    });

    card.querySelector('.remove-card').addEventListener('click', function () {
      if (document.querySelectorAll('.major-card').length > 1) {
        card.remove();
         updatePilihanLabels();
      }
    });
  }

  function updatePilihanLabels() {
  const titles = document.querySelectorAll('.pilihan-title');
  titles.forEach((title, index) => {
    title.textContent = `Pilihan ${index + 1}`;
  });
}


  // Init existing cards
  document.querySelectorAll('.major-card').forEach(card => {
    // Karena data awal sudah ada, langsung update info dari major yang sudah dipilih
    const majorSelect = card.querySelector('.major-select');
    const selectedId = majorSelect.value;
    if(selectedId && majorCache[selectedId]){
      updateInfoSection(card, majorCache[selectedId]);
    }
    initCard(card);
  });

  // Clone card
  addBtn.addEventListener('click', function () {
    const firstCard = document.querySelector('.major-card');
    const newCard = firstCard.cloneNode(true);

    newCard.querySelector('.university-select').selectedIndex = 0;
    newCard.querySelector('.major-select').innerHTML = '<option disabled selected>-- Pilih Prodi --</option>';
    newCard.querySelector('.major-select').disabled = true;

    newCard.querySelector('.quota').textContent = '-';
    newCard.querySelector('.peminat').textContent = '-';
    newCard.querySelector('.level-badge').textContent = '-';
    newCard.querySelector('.keketatan').textContent = '-';

     container.appendChild(newCard);

  initCard(newCard); // fungsi init event listener untuk card baru

  updatePilihanLabels(); // update nomor pilihan
  });
});
</script>
