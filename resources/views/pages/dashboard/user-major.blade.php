@extends('layouts.dashboard')

@section('title')
  Pilih Jurusan
@endsection

@section('content')
<style>
  .searchable-select {
    position: relative;
  }
  
  .select-wrapper {
    position: relative;
  }
  
  .search-input {
    width: 100%;
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    background-color: #fff;
    font-size: 1rem;
    line-height: 1.5;
  }
  
  .search-input:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  }
  
  .dropdown-arrow {
    position: absolute;
    top: 50%;
    right: 0.75rem;
    transform: translateY(-50%);
    pointer-events: none;
    width: 0;
    height: 0;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 4px solid #6c757d;
  }
  
  .options-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
  }
  
  .option-item {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
  }
  
  .option-item:hover, .option-item.highlighted {
    background-color: #e9ecef;
  }
  
  .option-item.selected {
    background-color: #007bff;
    color: white;
  }
  
  .no-options {
    padding: 0.5rem 0.75rem;
    color: #6c757d;
    font-style: italic;
  }
  
  .form-select.hidden {
    display: none;
  }
</style>

<!-- Notifikasi sukses -->
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

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
                <label class="form-label fw-semibold">Perguruan Tinggi</label>
                <div class="searchable-select">
                  <div class="select-wrapper">
                    <input type="text" class="search-input university-search" placeholder="-- Cari atau Pilih Universitas --" autocomplete="off">
                    <div class="dropdown-arrow"></div>
                    <div class="options-dropdown university-dropdown"></div>
                  </div>
                  <select class="form-select university-select hidden" name="universities[]" data-index="{{ $i }}">
                    <option value="" disabled>-- Pilih Universitas --</option>
                    @foreach($universities as $u)
                      <option value="{{ $u->id }}" {{ $u->id == $selectedMajor->university->id ? 'selected' : '' }}>
                        {{ $u->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Program Studi</label>
                <div class="searchable-select">
                  <div class="select-wrapper">
                    <input type="text" class="search-input major-search" placeholder="-- Cari atau Pilih Program Studi --" autocomplete="off">
                    <div class="dropdown-arrow"></div>
                    <div class="options-dropdown major-dropdown"></div>
                  </div>
                  <select class="form-select major-select hidden" name="majors[]">
                    <option value="" disabled>-- Pilih Program Studi --</option>
                    @foreach($majors->where('university_id', $selectedMajor->university->id) as $m)
                      <option value="{{ $m->id }}" {{ $m->id == $selectedMajor->id ? 'selected' : '' }}>
                        {{ $m->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="row text-center mt-4 info-section">
                <div class="col">
                  <div class="fw-semibold text-muted small">Daya Tampung</div>
                  <div class="fs-5 fw-bold text-primary quota">{{ $selectedMajor->quota }}</div>
                </div>
                <div class="col">
                  <div class="fw-semibold text-muted small">Peminat</div>
                  <div class="fs-5 fw-bold text-warning peminat">{{ $selectedMajor->peminat }}</div>
                </div>
                <div class="col">
                  <div class="fw-semibold text-muted small">Keketatan</div>
                  <div class="fs-5 fw-bold text-danger keketatan">
                    @php
                      $keketatan = $selectedMajor->peminat > 0 ? number_format(($selectedMajor->quota / $selectedMajor->peminat) * 100, 2) . '%' : '0%';
                    @endphp
                    {{ $keketatan }}
                  </div>
                </div>
              </div>
              
              <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-sm btn-outline-danger remove-card">
                  <i class="bi bi-trash"></i> Hapus
                </button>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <div class="card mb-4 shadow-sm border-0 major-card" data-initialized="false">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0 pilihan-title">Pilihan 1</h5>
              <h4 class="badge bg-secondary level-badge">-</h4>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Perguruan Tinggi</label>
              <div class="searchable-select">
                <div class="select-wrapper">
                  <input type="text" class="search-input university-search" placeholder="-- Cari atau Pilih Universitas --" autocomplete="off">
                  <div class="dropdown-arrow"></div>
                  <div class="options-dropdown university-dropdown"></div>
                </div>
                <select class="form-select university-select hidden" name="universities[]" data-index="0">
                  <option value="" disabled selected>-- Pilih Universitas --</option>
                  @foreach($universities as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Program Studi</label>
              <div class="searchable-select">
                <div class="select-wrapper">
                  <input type="text" class="search-input major-search" placeholder="-- Cari atau Pilih Program Studi --" autocomplete="off" disabled>
                  <div class="dropdown-arrow"></div>
                  <div class="options-dropdown major-dropdown"></div>
                </div>
                <select class="form-select major-select hidden" name="majors[]" disabled>
                  <option value="" disabled selected>-- Pilih Program Studi --</option>
                </select>
              </div>
            </div>

            <div class="row text-center mt-4 info-section">
              <div class="col">
                <div class="fw-semibold text-muted small">Daya Tampung</div>
                <div class="fs-5 fw-bold text-muted quota">-</div>
              </div>
              <div class="col">
                <div class="fw-semibold text-muted small">Peminat</div>
                <div class="fs-5 fw-bold text-muted peminat">-</div>
              </div>
              <div class="col">
                <div class="fw-semibold text-muted small">Keketatan</div>
                <div class="fs-5 fw-bold text-muted keketatan">-</div>
              </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-sm btn-outline-danger remove-card">
                <i class="bi bi-trash"></i> Hapus
              </button>
            </div>
          </div>
        </div>
      @endif
    </div>

    <div class="d-grid mb-3">
      <button type="button" class="btn btn-outline-primary" id="add-card">
        <i class="bi bi-plus-circle me-2"></i>Tambah Pilihan
      </button>
    </div>

    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-success btn-lg">
        <i class="bi bi-save me-2"></i> Simpan Semua
      </button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('cards-container');
  const addBtn = document.getElementById('add-card');

  // Data jurusan lengkap dari backend
  const allMajors = @json($majorsArray);
  const universities = @json($universities->toArray());

  // Build majorCache indexed by stringified major id
  const majorCache = {};
  const universityCache = {};
  allMajors.forEach(m => {
    // Pastikan key sebagai string
    const majorIdStr = String(m.id);
    const uniIdStr = String(m.university_id);

    majorCache[majorIdStr] = m;

    if (!universityCache[uniIdStr]) {
      universityCache[uniIdStr] = [];
    }
    universityCache[uniIdStr].push(m);
  });

  class SearchableSelect {
    constructor(container, options, onSelect) {
      this.container = container;
      this.input = container.querySelector('.search-input');
      this.dropdown = container.querySelector('.options-dropdown');
      this.hiddenSelect = container.parentElement.querySelector('select');
      this.options = options;
      this.onSelect = onSelect;
      this.filteredOptions = [...options];
      this.highlightedIndex = -1;
      this.init();
    }

    init() {
      this.renderOptions();
      this.bindEvents();

      // Set initial value jika select sudah ada pilihan
      const selectedOption = this.hiddenSelect.querySelector('option:checked');
      if (selectedOption && selectedOption.value) {
        const option = this.options.find(opt => String(opt.value) === String(selectedOption.value));
        if (option) {
          this.input.value = option.text;
        }
      }
    }

    bindEvents() {
      this.input.addEventListener('focus', () => this.showDropdown());
      this.input.addEventListener('input', (e) => this.filterOptions(e.target.value));
      this.input.addEventListener('keydown', (e) => this.handleKeydown(e));

      document.addEventListener('click', (e) => {
        if (!this.container.contains(e.target)) {
          this.hideDropdown();
        }
      });
    }

    filterOptions(query) {
      this.filteredOptions = this.options.filter(option =>
        option.text.toLowerCase().includes(query.toLowerCase())
      );
      this.highlightedIndex = -1;
      this.renderOptions();
      this.showDropdown();
    }

    renderOptions() {
      this.dropdown.innerHTML = '';
      if (this.filteredOptions.length === 0) {
        this.dropdown.innerHTML = '<div class="no-options">Tidak ada pilihan yang cocok</div>';
        return;
      }

      this.filteredOptions.forEach((option, index) => {
        const div = document.createElement('div');
        div.className = 'option-item';
        div.textContent = option.text;
        div.dataset.value = option.value;

        if (index === this.highlightedIndex) {
          div.classList.add('highlighted');
        }

        if (String(option.value) === String(this.hiddenSelect.value)) {
          div.classList.add('selected');
        }

        div.addEventListener('click', () => this.selectOption(option));
        this.dropdown.appendChild(div);
      });
    }

    selectOption(option) {
      this.input.value = option.text;
      this.hiddenSelect.value = option.value;

      // Set atribut selected pada <option> di hidden select
      const opts = this.hiddenSelect.querySelectorAll('option');
      opts.forEach(optEl => optEl.removeAttribute('selected'));
      const sel = this.hiddenSelect.querySelector(`option[value="${option.value}"]`);
      if (sel) {
        sel.setAttribute('selected', 'selected');
      }

      // Trigger change event
      const event = new Event('change', { bubbles: true });
      this.hiddenSelect.dispatchEvent(event);

      this.hideDropdown();
      if (this.onSelect) {
        this.onSelect(option);
      }
    }

    showDropdown() {
      if (this.input.disabled) return;
      this.dropdown.style.display = 'block';
    }

    hideDropdown() {
      this.dropdown.style.display = 'none';
    }

    handleKeydown(e) {
      if (this.dropdown.style.display === 'none') return;

      switch (e.key) {
        case 'ArrowDown':
          e.preventDefault();
          this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.filteredOptions.length - 1);
          this.renderOptions();
          break;
        case 'ArrowUp':
          e.preventDefault();
          this.highlightedIndex = Math.max(this.highlightedIndex - 1, -1);
          this.renderOptions();
          break;
        case 'Enter':
          e.preventDefault();
          if (this.highlightedIndex >= 0) {
            this.selectOption(this.filteredOptions[this.highlightedIndex]);
          }
          break;
        case 'Escape':
          this.hideDropdown();
          break;
      }
    }

    updateOptions(newOptions) {
      this.options = newOptions;
      this.filteredOptions = [...newOptions];
      this.highlightedIndex = -1;

      // Rebuild <select> hidden dengan opsi baru
      this.hiddenSelect.innerHTML = '<option value="" disabled selected>-- Pilih Program Studi --</option>';
      newOptions.forEach(option => {
        const optionEl = document.createElement('option');
        optionEl.value = option.value;
        optionEl.textContent = option.text;
        this.hiddenSelect.appendChild(optionEl);
      });

      this.renderOptions();
    }

    disable() {
      this.input.disabled = true;
      this.input.style.backgroundColor = '#e9ecef';
      this.hiddenSelect.disabled = true;
      this.hideDropdown();
    }

    enable() {
      this.input.disabled = false;
      this.input.style.backgroundColor = '#fff';
      this.hiddenSelect.disabled = false;
    }

    reset() {
      this.input.value = '';
      this.hiddenSelect.value = '';
      const opts = this.hiddenSelect.querySelectorAll('option');
      opts.forEach(optEl => optEl.removeAttribute('selected'));
      this.hideDropdown();
    }
  }

    function updateInfoSection(card, majorData) {
  console.log('DEBUG updateInfoSection →', majorData);

  const quotaEl      = card.querySelector('.quota');
  const peminatEl    = card.querySelector('.peminat');
  const levelBadgeEl = card.querySelector('.level-badge');
  const keketatanEl  = card.querySelector('.keketatan');

  if (quotaEl) {
    quotaEl.textContent = majorData.quota;
    // Sertakan kembali "quota" agar element tetap bisa ditemukan di pemanggilan berikutnya
    quotaEl.className = 'fs-5 fw-bold text-primary quota';
  }

  if (peminatEl) {
    peminatEl.textContent = majorData.peminat;
    // Sertakan kembali "peminat"
    peminatEl.className = 'fs-5 fw-bold text-warning peminat';
  }

  if (levelBadgeEl) {
    levelBadgeEl.textContent = majorData.level;
    // class lama sudah menyertakan "level-badge", jadi cukup pertahankan
    levelBadgeEl.className = 'badge bg-primary level-badge';
  }

  if (keketatanEl) {
    const persen = majorData.peminat > 0
      ? ((majorData.quota / majorData.peminat) * 100).toFixed(2) + '%'
      : '0%';
    keketatanEl.textContent = persen;
    // Sertakan kembali "keketatan"
    keketatanEl.className = 'fs-5 fw-bold text-danger keketatan';
  }
}


  function updateInfoSection(card, majorData) {
  console.log('DEBUG updateInfoSection →', majorData, card);

  // Ambil elemen‐elemen berdasarkan class
  const quotaEl      = card.querySelector('.quota');
  const peminatEl    = card.querySelector('.peminat');
  const levelBadgeEl = card.querySelector('.level-badge');
  const keketatanEl  = card.querySelector('.keketatan');

  // Jika tidak ketemu, tampilkan warning di console supaya kita tahu
  if (!quotaEl)      console.warn('⚠️ updateInfoSection: elemen .quota TIDAK DITEMUKAN di dalam:', card);
  if (!peminatEl)    console.warn('⚠️ updateInfoSection: elemen .peminat TIDAK DITEMUKAN di dalam:', card);
  if (!levelBadgeEl) console.warn('⚠️ updateInfoSection: elemen .level-badge TIDAK DITEMUKAN di dalam:', card);
  if (!keketatanEl)  console.warn('⚠️ updateInfoSection: elemen .keketatan TIDAK DITEMUKAN di dalam:', card);

  // 1) Update Daya Tampung (quota)
  if (quotaEl) {
    quotaEl.textContent = majorData.quota;
    // PENTING: sertakan kembali kata "quota" agar querySelector('.quota') tetap valid di panggilan selanjutnya
    quotaEl.className = 'fs-5 fw-bold text-primary quota';
  }

  // 2) Update Peminat
  if (peminatEl) {
    peminatEl.textContent = majorData.peminat;
    // Sertakan kembali kata "peminat"
    peminatEl.className = 'fs-5 fw-bold text-warning peminat';
  }

  // 3) Update Level (badge)
  if (levelBadgeEl) {
    levelBadgeEl.textContent = majorData.level;
    // Class "level-badge" harus tetap ada, hanya styling-nya yang kita jaga ulang
    levelBadgeEl.className = 'badge bg-primary level-badge';
  }

  // 4) Update Keketatan
  if (keketatanEl) {
    const persen = majorData.peminat > 0
      ? ((majorData.quota / majorData.peminat) * 100).toFixed(2) + '%'
      : '0%';
    keketatanEl.textContent = persen;
    // Sertakan kembali kata "keketatan"
    keketatanEl.className = 'fs-5 fw-bold text-danger keketatan';
  }
}


  function initCard(card) {
    const [univContainer, majorContainer] = card.querySelectorAll('.searchable-select');

    // Inisialisasi SearchableSelect untuk Universitas
    const universityOptions = universities.map(u => ({ value: String(u.id), text: u.name }));
    const universitySelect = new SearchableSelect(univContainer, universityOptions, (option) => {
      // Saat universitas dipilih, perbarui opsi Program Studi
      const uniKey = String(option.value);
      const majorList = Array.isArray(universityCache[uniKey])
        ? universityCache[uniKey].map(m => ({ value: String(m.id), text: m.name }))
        : [];
      
      majorSelect.updateOptions(majorList);
      majorSelect.reset();
      majorSelect.enable();

      // Reset info-section jika universitas berubah
      resetInfoSection(card);
    });

    // Inisialisasi SearchableSelect untuk Program Studi
    const universitySelectEl = card.querySelector('.university-select');
    const selectedUniId = universitySelectEl.value ? String(universitySelectEl.value) : '';
    const initialMajors = selectedUniId && universityCache[selectedUniId]
      ? universityCache[selectedUniId].map(m => ({ value: String(m.id), text: m.name }))
      : [];
    
    const majorSelect = new SearchableSelect(majorContainer, initialMajors, (option) => {
      const majKey = String(option.value);
      const selectedData = majorCache[majKey];
      if (selectedData) {
        updateInfoSection(card, selectedData);
      }
    });

    // Jika belum pilih universitas, disable major select
    if (!selectedUniId) {
      majorSelect.disable();
    } else {
      majorSelect.enable();
    }

    // Event untuk tombol Hapus
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

  // Inisialisasi kartu yang sudah ada
  document.querySelectorAll('.major-card').forEach(card => {
    const majorSelectEl = card.querySelector('.major-select');
    const selectedId = majorSelectEl.value ? String(majorSelectEl.value) : '';
    if (selectedId && majorCache[selectedId]) {
      updateInfoSection(card, majorCache[selectedId]);
    }
    initCard(card);
  });

  // Tambah kartu baru
  addBtn.addEventListener('click', function () {
    const cardCount = document.querySelectorAll('.major-card').length;
    const newCardHTML = `
      <div class="card mb-4 shadow-sm border-0 major-card" data-initialized="false">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0 pilihan-title">Pilihan ${cardCount + 1}</h5>
            <h4 class="badge bg-secondary level-badge">-</h4>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Perguruan Tinggi</label>
            <div class="searchable-select">
              <div class="select-wrapper">
                <input type="text" class="search-input university-search" placeholder="-- Cari atau Pilih Universitas --" autocomplete="off">
                <div class="dropdown-arrow"></div>
                <div class="options-dropdown university-dropdown"></div>
              </div>
              <select class="form-select university-select hidden" name="universities[]" data-index="${cardCount}">
                <option value="" disabled selected>-- Pilih Universitas --</option>
                ${universities.map(u => `<option value="${u.id}">${u.name}</option>`).join('')}
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Program Studi</label>
            <div class="searchable-select">
              <div class="select-wrapper">
                <input type="text" class="search-input major-search" placeholder="-- Cari atau Pilih Program Studi --" autocomplete="off" disabled>
                <div class="dropdown-arrow"></div>
                <div class="options-dropdown major-dropdown"></div>
              </div>
              <select class="form-select major-select hidden" name="majors[]" disabled>
                <option value="" disabled selected>-- Pilih Program Studi --</option>
              </select>
            </div>
          </div>

          <div class="row text-center mt-4 info-section">
            <div class="col">
              <div class="fw-semibold text-muted small">Daya Tampung</div>
              <div class="fs-5 fw-bold text-muted quota">-</div>
            </div>
            <div class="col">
              <div class="fw-semibold text-muted small">Peminat</div>
              <div class="fs-5 fw-bold text-muted peminat">-</div>
            </div>
            <div class="col">
              <div class="fw-semibold text-muted small">Keketatan</div>
              <div class="fs-5 fw-bold text-muted keketatan">-</div>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-sm btn-outline-danger remove-card">
              <i class="bi bi-trash"></i> Hapus
            </button>
          </div>
        </div>
      </div>
    `;

    container.insertAdjacentHTML('beforeend', newCardHTML);
    // Pastikan elemen sudah ada di DOM sebelum inisialisasi
    requestAnimationFrame(() => {
      const newCard = container.lastElementChild;
      initCard(newCard);
      updatePilihanLabels();
    });
  });

  // Validasi form sebelum submit
  document.getElementById('major-form').addEventListener('submit', function(e) {
    let isValid = true;
    const cards = document.querySelectorAll('.major-card');

    cards.forEach((card, index) => {
      const universitySelect = card.querySelector('.university-select');
      const majorSelect = card.querySelector('.major-select');
      if (!universitySelect.value || !majorSelect.value) {
        isValid = false;
      }
    });

    if (!isValid) {
      e.preventDefault();
      alert('Mohon lengkapi semua pilihan universitas dan program studi.');
    }
  });
});
</script>

@endsection