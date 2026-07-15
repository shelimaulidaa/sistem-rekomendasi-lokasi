<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-dark leading-tight">
            {{ __('Buat Observasi Lokasi Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Error Summaries -->
        @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg shadow-sm">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold text-sm">Ada kesalahan dalam pengisian form:</span>
            </div>
            <ul class="list-disc list-inside text-sm ml-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('manajer.observasi.store') }}" enctype="multipart/form-data"
              @location-updated.window="fetchSpatialData($event.detail.lat, $event.detail.lng)"
              
              x-data="{
                  step: 1,
                  totalSteps: 2,
                  isCalculatingSpatial: false,
                  spatialError: null,
                  isManualRph: false,
                  isManualKompetitor: false,
                                    rphName: '',
                  competitorCount: 0,
                  spatialSearchRadius: 0,
                  competitorsList: [],
                  pendampingList: [],
                  rphList: [],
                  showCompetitorModal: false,
                  editingCompetitor: null,
                  showRphModal: false,
                  isSubmitting: false,
                  // Persist states here so x-if doesn't destroy them
                  hargaSewaRaw: '{{ old('harga_sewa') }}',
                  jarakRphDisplay: '{{ old('jarak_rph') }}',
                  lat: '{{ old('latitude') }}',
                  lng: '{{ old('longitude') }}',
                  images: [],
                  
                  
                  init() {
                      // Fetch automatically if coordinates exist
                      if (this.lat && this.lng) {
                          this.fetchSpatialData(this.lat, this.lng);
                      }
                      
                      // Also listen for location-updated directly in Alpine
                      window.addEventListener('location-updated', (e) => {
                          this.fetchSpatialData(e.detail.lat, e.detail.lng);
                      });
                  },

                  get progress() {
                      return ((this.step - 1) / (this.totalSteps - 1)) * 100;
                  },
                  
                  validateStep1() {
                      const namaPemilik = document.querySelector('[name=nama_pemilik]');
                      if(!namaPemilik || namaPemilik.value.trim() === '') { alert('Nama Pemilik wajib diisi.'); return false; }
                      
                      const noHp = document.querySelector('[name=nomor_telepon_pemilik]');
                      if(!noHp || noHp.value.trim() === '') { alert('Nomor Telepon Pemilik wajib diisi.'); return false; }

                      const alamatLengkap = document.querySelector('[name=alamat_lengkap]');
                      if(!alamatLengkap || alamatLengkap.value.trim() === '') { alert('Alamat Lengkap wajib diisi.'); return false; }

                      const prov = document.querySelector('[name=province_id]');
                      if(!prov || prov.value === '') { alert('Provinsi wajib dipilih.'); return false; }

                      const kab = document.querySelector('[name=regency_id]');
                      if(!kab || kab.value === '') { alert('Kabupaten/Kota wajib dipilih.'); return false; }

                      const kec = document.querySelector('[name=district_id]');
                      if(!kec || kec.value === '') { alert('Kecamatan wajib dipilih.'); return false; }

                      const tanggal = document.getElementById('tanggal_observasi');
                      if(tanggal && !tanggal.value) { alert('Tanggal Observasi wajib diisi.'); return false; }
                      
                      const hs = this.hargaSewaRaw;
                      if(hs === '' || hs === null || isNaN(parseFloat(hs)) || parseFloat(hs) < 0) { 
                          alert('Harga Sewa wajib diisi dan harus bernilai valid (>= 0).'); return false; 
                      }
                      
                      if(this.isCalculatingSpatial) {
                          alert('Harap tunggu, sistem sedang menghitung Jumlah Kompetitor dan Jarak RPH.'); return false;
                      }
                      
                      if(this.competitorCount === '' || this.competitorCount === null || parseInt(this.competitorCount) < 0) { 
                          alert('Jumlah Kompetitor wajib diisi dan harus bernilai valid (>= 0).'); return false; 
                      }
                      
                      const jr = this.jarakRphDisplay;
                      if(jr === '' || jr === null || isNaN(parseFloat(jr.toString().replace(/,/g, '.'))) || parseFloat(jr.toString().replace(/,/g, '.')) < 0) { 
                          alert('Jarak RPH wajib diisi dan harus bernilai valid (>= 0).'); return false; 
                      }
                      
                      return true;
                  },
                  
                  validateStep2() {
                      const jenis = document.querySelector('[name=jenis_bangunan]');
                      if(!jenis || jenis.value.trim() === '') { alert('Jenis Bangunan wajib diisi.'); return false; }
                      
                      const lt = document.querySelector('[name=luas_tanah]');
                      if(!lt || lt.value === '' || isNaN(parseFloat(lt.value.replace(/,/g, '.'))) || parseFloat(lt.value.replace(/,/g, '.')) < 0) { 
                          alert('Luas Tanah wajib diisi dan harus bernilai valid (>= 0).'); return false; 
                      }
                      
                      const lb = document.querySelector('[name=luas_bangunan]');
                      if(!lb || lb.value === '' || isNaN(parseFloat(lb.value.replace(/,/g, '.'))) || parseFloat(lb.value.replace(/,/g, '.')) < 0) { 
                          alert('Luas Bangunan wajib diisi dan harus bernilai valid (>= 0).'); return false; 
                      }
                      
                      const jr = document.querySelector('[name=jumlah_ruangan]');
                      if(!jr || jr.value === '' || parseInt(jr.value) < 0) { 
                          alert('Jumlah Ruangan wajib diisi dan harus bernilai valid (>= 0).'); return false; 
                      }
                      
                      const wc = document.querySelector('[name=jumlah_wc]');
                      if(!wc || wc.value === '' || parseInt(wc.value) < 0) { 
                          alert('Jumlah WC wajib diisi dan harus bernilai valid (>= 0).'); return false; 
                      }
                      
                      const sa = document.querySelector('[name=sumber_air]');
                      if(!sa || sa.value.trim() === '') { alert('Sumber Air wajib diisi.'); return false; }
                      
                      // For Documentation validation, we can check this.images or let server validate
                      return true;
                  },
                  
                  nextStep() {
                      if(this.step === 1 && !this.validateStep1()) return;
                      if(this.step === 2 && !this.validateStep2()) return;
                      if(this.step === 3 && !this.validateStep3()) return;
                      
                      if(this.step < this.totalSteps) {
                          this.step++;
                          this.focusTop();
                      }
                  },
                  
                  prevStep() {
                      if(this.step > 1) {
                          this.step--;
                          this.focusTop();
                      }
                  },
                  
                  goToStep(targetStep) {
                      if(targetStep < this.step) {
                          this.step = targetStep;
                          this.focusTop();
                      } else if (targetStep > this.step) {
                          let canProceed = true;
                          for(let i = this.step; i < targetStep; i++) {
                              if(i === 1 && !this.validateStep1()) { canProceed = false; break; }
                              if(i === 2 && !this.validateStep2()) { canProceed = false; break; }
                              if(i === 3 && !this.validateStep3()) { canProceed = false; break; }
                          }
                          if(canProceed) {
                              this.step = targetStep;
                              this.focusTop();
                          }
                      }
                  },
                  
                  
                  fetchSpatialData(lat, lng) {
                      if (!lat || !lng) return;
                      this.isCalculatingSpatial = true;
                      this.spatialError = null;
                      
                      fetch(`/api/spatial/analyze-location?latitude=${lat}&longitude=${lng}`)
                          .then(response => {
                              if (!response.ok) throw new Error('API Error');
                              return response.json();
                          })
                          .then(data => {
                              this.jarakRphDisplay = data.nearest_rph_distance;
                                                            this.rphName = data.nearest_rph_name;
                              this.competitorCount = data.competitor_count;
                              this.spatialSearchRadius = data.search_radius;
                              this.competitorsList = data.competitors_list || [];
                              this.rphList = data.rph_list || [];
                              this.isCalculatingSpatial = false;
                          })
                          .catch(error => {
                              console.error(error);
                              this.spatialError = 'Gagal menghitung otomatis. Silakan isi manual.';
                              this.isCalculatingSpatial = false;
                          });
                  },
                  
                  updateLocation(lat, lng) {
                      this.lat = lat;
                      this.lng = lng;
                      this.fetchSpatialData(lat, lng);
                  },
focusTop() {
                      window.scrollTo({ top: 0, behavior: 'smooth' });
                  },
                  
                  formatThousand(val) {
                      if (!val && val !== 0 && val !== '0') return '';
                      let numberString = val.toString().replace(/[^0-9]/g, '');
                      return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                  }
              }"
              @submit="if(step !== 2) { $event.preventDefault(); nextStep(); return; } if(isSubmitting) $event.preventDefault(); else isSubmitting = true;">
            @csrf

            <!-- Mobile & Desktop Stepper UI -->
            <div class="mb-8">
                <!-- Mobile Stepper -->
                <div class="sm:hidden mb-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2 flex justify-between">
                        <span>Langkah <span x-text="step"></span> dari <span x-text="totalSteps"></span></span>
                        <span x-text="Math.round(progress) + '%'"></span>
                    </p>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                    </div>
                </div>

                <div class="hidden sm:block px-12 md:px-24">
                    <nav aria-label="Progress">
                        <ol role="list" class="flex items-center justify-between relative">
                            <!-- Line -->
                            <div class="absolute top-5 left-5 right-5 -translate-y-1/2" aria-hidden="true">
                                <div class="h-1 w-[675px] bg-gray-200">
                                    <div class="h-1 transition-all duration-300 bg-primary" :style="`width: ${progress}%`"></div>
                                </div>
                            </div>
                            
                            <!-- Step 1 -->
                            <li class="relative z-10 flex flex-col items-center">
                                <button type="button" @click="goToStep(1)" class="relative flex h-10 w-10 items-center justify-center rounded-full transition-all duration-300 shadow-sm"
                                    :class="step > 1 ? 'bg-primary hover:bg-primary-dark' : (step === 1 ? 'border-4 border-primary bg-white scale-110' : 'border-4 border-gray-300 bg-white hover:border-gray-400')">
                                    <template x-if="step > 1">
                                        <svg class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                    </template>
                                    <template x-if="step === 1">
                                        <span class="h-3 w-3 rounded-full bg-primary"></span>
                                    </template>
                                </button>
                                <span class="mt-3 text-xs font-bold w-max text-center" :class="step >= 1 ? 'text-primary' : 'text-gray-500'">Informasi Lokasi</span>
                            </li>
                            
                            <!-- Step 2 -->
                            <li class="relative z-10 flex flex-col items-center">
                                <button type="button" @click="goToStep(2)" class="relative flex h-10 w-10 items-center justify-center rounded-full transition-all duration-300 shadow-sm"
                                    :class="step === 2 ? 'border-4 border-primary bg-white scale-110' : 'border-4 border-gray-300 bg-white hover:border-gray-400'">
                                    <template x-if="step === 2">
                                        <span class="h-3 w-3 rounded-full bg-primary"></span>
                                    </template>
                                    <template x-if="step < 2">
                                        <span class="h-3 w-3 rounded-full bg-transparent group-hover:bg-gray-300"></span>
                                    </template>
                                </button>
                                <span class="mt-3 text-xs font-bold w-max text-center" :class="step >= 2 ? 'text-primary' : 'text-gray-500'">Kondisi Bangunan & Dokumentasi</span>
                            </li>

                        </ol>
                    </nav>
                </div>

                <!-- STEP 1 -->
                <div x-show="step === 1" x-cloak>
                    <div class="space-y-6">
                        <!-- Section 0: Pemilihan Batch -->
                        <div x-data="batchManager()" x-init="init()" class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    <h3 class="text-lg font-bold text-base-dark">Periode / Batch Observasi</h3>
                                </div>
                            </div>
                            <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-end gap-4">
                                <div class="flex-grow">
                                    <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Batch <span class="text-red-500">*</span></label>
                                    <select id="batch_id" name="batch_id" x-model="selectedBatch" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 h-[42px]">
                                        <option value="" disabled>-- Pilih Batch --</option>
                                        <template x-for="b in batches" :key="b.id">
                                            <option :value="b.id" x-text="b.nama_batch"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 h-[42px]">
                                    <button type="button" @click="openAddModal" class="px-3 py-2 bg-primary text-white text-sm font-medium rounded-md shadow-sm hover:bg-green-700 transition flex items-center h-full">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah
                                    </button>
                                    <button type="button" @click="openEditModal" x-show="selectedBatch" class="px-3 py-2 bg-orange-500 text-white text-sm font-medium rounded-md shadow-sm hover:bg-orange-600 transition flex items-center h-full">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Edit
                                    </button>
                                    <button type="button" @click="deleteBatch" x-show="selectedBatch" class="px-3 py-2 bg-red-500 text-white text-sm font-medium rounded-md shadow-sm hover:bg-red-600 transition flex items-center h-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                
                                <!-- Modal Add/Edit -->
                                <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false" aria-hidden="true"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" x-text="isEditing ? 'Edit Batch' : 'Tambah Batch Baru'"></h3>
                                                        <div class="mt-4">
                                                            <label class="block text-sm font-medium text-gray-700">Nama Batch</label>
                                                            <input type="text" x-model="batchName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Misal: Cabang Antapani 2026">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="button" @click="saveBatch" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                                    <span x-show="!isSubmitting">Simpan</span>
                                                    <span x-show="isSubmitting">Menyimpan...</span>
                                                </button>
                                                <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 1: Koordinat Peta Observasi Aktual -->
                        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <h3 class="text-lg font-bold text-base-dark">Koordinat Observasi</h3>
                            </div>
                            <div class="p-4 sm:p-6">
                                <template x-if="step === 1">
                                    <div x-data="locationMap()" x-init="initMap()" class="space-y-4">
                                        <div class="flex flex-col md:flex-row items-center justify-between gap-3 md:gap-2">
                                            <div>
                                                <h3 class="text-sm font-bold text-base-dark flex items-center">
                                                    Pilih Titik Lokasi
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                        Disarankan
                                                    </span>
                                                </h3>
                                                <p class="text-xs text-base-medium mt-1">Sistem akan secara otomatis mencari detail alamat jika Anda menggunakan lokasi saat ini atau memilih titik di peta.</p>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <button type="button" @click="getCurrentLocation" :disabled="isFetchingLocation || isGeocoding" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 min-h-[44px] bg-primary border border-transparent rounded-lg text-sm font-bold text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors disabled:opacity-75 disabled:cursor-not-allowed shadow-sm">
                                                    <template x-if="!isFetchingLocation">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    </template>
                                                    <template x-if="isFetchingLocation">
                                                        <svg class="animate-spin w-5 h-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    </template>
                                                    <span x-text="isFetchingLocation ? 'Mengambil lokasi...' : 'Gunakan Lokasi Saat Ini'"></span>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Map Container -->
                                        <div class="relative w-full rounded-lg border border-gray-300 shadow-sm overflow-hidden h-[300px] md:h-[400px]" id="map-container" wire:ignore>
                                            <div id="map" class="w-full h-full z-0 relative"></div>
                                            
                                            <!-- Overlays -->
                                            <div x-show="isFetchingLocation || isGeocoding" x-transition class="absolute inset-0 z-[1000] bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center">
                                                <svg class="animate-spin w-10 h-10 text-primary mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                <p class="font-bold text-gray-700 text-sm" x-text="isFetchingLocation ? '📍 Mengambil koordinat GPS...' : '🛰️ Mencari detail alamat (Geocoding)...'"></p>
                                            </div>
                                        </div>

                                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                                            <p class="text-xs text-amber-600 font-medium">💡 Helper: Anda juga dapat menggeser (drag) pin hijau pada peta untuk memperbarui lokasi.</p>
                                            <template x-if="acquisitionTime">
                                                <div class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                                    Diperoleh: <span x-text="acquisitionTime"></span> <template x-if="accuracy"><span x-text="' (Akurasi: ±' + Math.round(accuracy) + 'm)'"></span></template>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Coordinate Inputs (Always in DOM) -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100">
                                            <div>
                                                <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                                <input id="latitude" type="text"  x-model="lat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px] bg-gray-50 text-gray-500" readonly placeholder="Opsional">
                                            </div>
                                            <div>
                                                <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                                <input id="longitude" type="text"  x-model="lng" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px] bg-gray-50 text-gray-500" readonly placeholder="Opsional">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Section 2: Data Utama -->
                        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl"
              x-data="{
                                provinces: [], regencies: [], districts: [],
                                selectedProvId: '{{ old('province_id') }}',
                                selectedRegId: '{{ old('regency_id') }}',
                                selectedDistId: '{{ old('district_id') }}',
                                provName: '{{ old('provinsi') }}',
                                regName: '{{ old('kabupaten_kota') }}',
                                distName: '{{ old('kecamatan') }}',
                                alamatLengkap: '{{ old('alamat_lengkap') }}',
                                umk_kota: '',
                                pdrb_kota: '',
                                penduduk_muslim_kota: '',
                                
                                async loadProvinces() {
                                    const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                                    this.provinces = await res.json();
                                    if (this.selectedProvId) this.loadRegencies();
                                },
                                async loadRegencies() {
                                    if (!this.selectedProvId) return;
                                    const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.selectedProvId}.json`);
                                    this.regencies = await res.json();
                                    const prov = this.provinces.find(p => p.id == this.selectedProvId);
                                    if (prov) this.provName = prov.name;
                                    if (this.selectedRegId) this.loadDistricts();
                                },
                                async loadDistricts() {
                                    if (!this.selectedRegId) return;
                                    const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.selectedRegId}.json`);
                                    this.districts = await res.json();
                                    const reg = this.regencies.find(r => r.id == this.selectedRegId);
                                    if (reg) { this.regName = reg.name; this.loadJabarStats(); }
                                },
                                updateDistName() {
                                    const dist = this.districts.find(d => d.id == this.selectedDistId);
                                    if (dist) this.distName = dist.name;
                                },
                                async loadJabarStats() {
                                    if (!this.regName) return;
                                    try {
                                        const res = await fetch(`/api/wilayah/jabar-stats?regency_name=${encodeURIComponent(this.regName)}`);
                                        const data = await res.json();
                                        if (data) {
                                            this.umk_kota = data.umk;
                                            this.pdrb_kota = data.pdrb_per_capita;
                                            this.penduduk_muslim_kota = data.jumlah_penduduk_muslim;
                                        } else {
                                            this.umk_kota = '';
                                            this.pdrb_kota = '';
                                            this.penduduk_muslim_kota = '';
                                        }
                                    } catch (e) {
                                        console.error('Error fetching Jabar stats:', e);
                                    }
                                },
                                
                                normalizeRegionName(name) {
                                    if (!name) return '';
                                    let n = name.toUpperCase().trim();
                                    
                                    // English to Indonesian translation mappings
                                    const map = {
                                        'WEST JAVA': 'JAWA BARAT',
                                        'EAST JAVA': 'JAWA TIMUR',
                                        'CENTRAL JAVA': 'JAWA TENGAH',
                                        'JAKARTA': 'DKI JAKARTA',
                                        'YOGYAKARTA': 'DI YOGYAKARTA',
                                        'NORTH SUMATRA': 'SUMATERA UTARA',
                                        'SOUTH SUMATRA': 'SUMATERA SELATAN',
                                        'WEST SUMATRA': 'SUMATERA BARAT',
                                        'BALI': 'BALI',
                                        'BANTEN': 'BANTEN'
                                    };
                                    
                                    if(map[n]) n = map[n];
                                    
                                    // Handle City/Regency suffixes
                                    if(n.endsWith(' CITY')) n = 'KOTA ' + n.replace(' CITY', '');
                                    if(n.endsWith(' REGENCY')) n = 'KABUPATEN ' + n.replace(' REGENCY', '');
                                    
                                    // Sometimes API returns Kota/Kabupaten at the end
                                    if(n.endsWith(' KOTA')) n = 'KOTA ' + n.replace(' KOTA', '');
                                    if(n.endsWith(' KABUPATEN')) n = 'KABUPATEN ' + n.replace(' KABUPATEN', '');
                                    
                                    return n;
                                },
                                
                                async handleAddressResolved(e) {
                                    const data = e.detail;
                                    
                                    // Check if user already inputted manual address/regions and isn't empty
                                    if (this.alamatLengkap.trim() !== '' || this.selectedProvId !== '') {
                                        if (!confirm('Alamat sudah terisi. Apakah Anda ingin memperbaruinya dengan data baru dari GPS/Peta?')) {
                                            return; // user canceled overwrite
                                        }
                                    }

                                    // Update Alamat Lengkap
                                    this.alamatLengkap = data.fullAddress;
                                    
                                    // Auto Select Provinsi
                                    if (data.state) {
                                        const normState = this.normalizeRegionName(data.state);
                                        let provMatch = this.provinces.find(p => p.name.toUpperCase() === normState || p.name.toUpperCase().includes(normState) || normState.includes(p.name.toUpperCase()));
                                        
                                        if (provMatch) {
                                            this.selectedProvId = provMatch.id;
                                            this.provName = provMatch.name;
                                            
                                            // Fetch Regencies
                                            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provMatch.id}.json`);
                                            this.regencies = await res.json();
                                            
                                            // Auto Select Kabupaten
                                            if (data.city || data.county) {
                                                let rawKab = data.city || data.county;
                                                const normKab = this.normalizeRegionName(rawKab);
                                                
                                                const kabMatch = this.regencies.find(r => r.name.toUpperCase() === normKab || r.name.toUpperCase().includes(normKab) || normKab.includes(r.name.toUpperCase()));
                                                if (kabMatch) {
                                                    this.selectedRegId = kabMatch.id;
                                                    this.regName = kabMatch.name;
                                                    this.loadJabarStats();
                                                    
                                                    // Fetch Districts
                                                    const res2 = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${kabMatch.id}.json`);
                                                    this.districts = await res2.json();
                                                    
                                                    // Auto Select Kecamatan
                                                    if (data.district || data.suburb) {
                                                        let rawKec = data.district || data.suburb;
                                                        // Strip 'Kecamatan' or 'District' from the string
                                                        let normKec = rawKec.toUpperCase().replace('KECAMATAN ', '').replace(' DISTRICT', '').trim();
                                                        
                                                        const kecMatch = this.districts.find(d => d.name.toUpperCase() === normKec || d.name.toUpperCase().includes(normKec) || normKec.includes(d.name.toUpperCase()));
                                                        if (kecMatch) {
                                                            this.selectedDistId = kecMatch.id;
                                                            this.distName = kecMatch.name;
                                                            this.updateDistName();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }"
                            x-init="loadProvinces()"
                            @address-resolved.window="handleAddressResolved($event)"
                        >
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <h3 class="text-lg font-bold text-base-dark">Informasi Lokasi</h3>
                            </div>
                            
                            <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-1 md:col-span-1">
                                    <label class="block text-sm font-medium text-base-dark mb-1">Nama Pemilik</label>
                                    <input type="text" name="nama_pemilik" value="{{ old('nama_pemilik') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                </div>
                                <div class="col-span-1 md:col-span-1">
                                    <label class="block text-sm font-medium text-base-dark mb-1">Nomor Telepon Pemilik</label>
                                    <input type="text" name="nomor_telepon_pemilik" value="{{ old('nomor_telepon_pemilik') }}" inputmode="numeric" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-base-dark mb-1">Alamat Lengkap</label>
                                    <textarea name="alamat_lengkap" x-model="alamatLengkap" rows="2" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3"></textarea>
                                </div>
                                
                                <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Hidden Inputs to submit names -->
                                    <input type="hidden" name="provinsi" :value="provName">
                                    <input type="hidden" name="kabupaten_kota" :value="regName">
                                    <input type="hidden" name="kecamatan" :value="distName">
                                    <input type="hidden" name="umk" :value="umk_kota">
                                    <input type="hidden" name="pdrb" :value="pdrb_kota">
                                    <input type="hidden" name="jumlah_penduduk_muslim" :value="penduduk_muslim_kota">

                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Provinsi</label>
                                        <select name="province_id" x-model="selectedProvId" @change="regencies=[]; districts=[]; selectedRegId=''; selectedDistId=''; provName=''; regName=''; distName=''; loadRegencies()" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                            <option value="">Pilih Provinsi</option>
                                            <template x-for="prov in provinces" :key="prov.id">
                                                <option :value="prov.id" x-text="prov.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Kabupaten/Kota</label>
                                        <select name="regency_id" x-model="selectedRegId" @change="districts=[]; selectedDistId=''; regName=''; distName=''; loadDistricts()" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                            <option value="">Pilih Kabupaten/Kota</option>
                                            <template x-for="reg in regencies" :key="reg.id">
                                                <option :value="reg.id" x-text="reg.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Kecamatan</label>
                                        <select name="district_id" x-model="selectedDistId" @change="updateDistName()" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                            <option value="">Pilih Kecamatan</option>
                                            <template x-for="dist in districts" :key="dist.id">
                                                <option :value="dist.id" x-text="dist.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Jabar Stats Cards -->
                                <div class="col-span-1 md:col-span-2 mt-4 grid grid-cols-1 md:grid-cols-3 gap-4" x-show="umk_kota || pdrb_kota || penduduk_muslim_kota">
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 flex flex-col">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">UMK Kabupaten / Kota</span>
                                        <span class="text-lg font-bold text-gray-800" x-text="umk_kota ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(umk_kota) : '-'"></span>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 flex flex-col">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">PDRB Per Kapita</span>
                                        <span class="text-lg font-bold text-gray-800" x-text="pdrb_kota ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(pdrb_kota) : '-'"></span>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 flex flex-col">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jumlah Penduduk Muslim</span>
                                        <span class="text-lg font-bold text-gray-800" x-text="penduduk_muslim_kota ? new Intl.NumberFormat('id-ID').format(penduduk_muslim_kota) + ' Jiwa' : '-'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    <div class="space-y-6">
                        <!-- Informasi Survei -->
                        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <h3 class="text-lg font-bold text-base-dark">2. Informasi Survei</h3>
                            </div>
                            <div class="p-4 sm:p-6 space-y-6">
                                <div class="pt-2">
                                    <label class="block text-sm font-medium text-base-dark mb-2 uppercase tracking-wider text-gray-500 text-xs">Anggota Pendamping Survei</label>
                                    <button type="button" @click="pendampingList.push('')" class="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 hover:bg-green-100 border border-transparent rounded-md font-medium text-sm transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Tambah Anggota Pendamping
                                    </button>
                                    
                                    <div class="mt-4 space-y-3">
                                        <template x-for="(pendamping, index) in pendampingList" :key="index">
                                            <div class="flex items-center gap-2">
                                                <input type="text" x-model="pendampingList[index]" name="anggota_pendamping[]" placeholder="Nama Anggota Pendamping" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2.5 px-3">
                                                <button type="button" @click="pendampingList.splice(index, 1)" class="p-2 text-red-500 hover:bg-red-50 rounded-md transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                                    <div>
                                        <label for="tanggal_observasi" class="block text-sm font-medium text-base-dark mb-1 uppercase tracking-wider text-gray-500 text-xs">Tanggal Survei</label>
                                        <input id="tanggal_observasi" type="date" name="tanggal_observasi" value="{{ old('tanggal_observasi', date('Y-m-d')) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2.5 px-3 min-h-[44px] bg-gray-50 text-gray-700">
                                    </div>
                                    <div>
                                        <label for="jam_observasi" class="block text-sm font-medium text-base-dark mb-1 uppercase tracking-wider text-gray-500 text-xs">Jam Survei</label>
                                        <input id="jam_observasi" type="time" name="jam_observasi" value="{{ old('jam_observasi') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2.5 px-3 min-h-[44px] bg-gray-50 text-gray-700">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <h3 class="text-lg font-bold text-base-dark">Informasi Operasional</h3>
                            </div>
                            
                            <div class="p-4 sm:p-6 space-y-6">
                                <!-- Harga Sewa stays as normal input -->
                                <div class="max-w-md">

                                    <label for="harga_sewa" class="block text-sm font-medium text-base-dark mb-1">Harga Sewa / Tahun</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="hidden" name="harga_sewa" :value="hargaSewaRaw">
                                        <input id="harga_sewa_display" type="text" inputmode="numeric" required 
                                            :value="formatThousand(hargaSewaRaw)"
                                            @input="hargaSewaRaw = $event.target.value.replace(/[^0-9]/g, '')"
                                            class="w-full pl-9 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 font-medium">Contoh: 12.000.000/tahun atau 120.000.000/tahun</p>
                                
                                </div>
                                
                                <div class="space-y-4">
                                    <!-- RPH Card -->
                                    <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm" :class="{'animate-pulse': isCalculatingSpatial}">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Rumah Potong Hewan (RPH) Terdekat</p>
                                                <h4 class="text-base font-bold text-base-dark mt-1" x-text="rphName ? rphName : (isCalculatingSpatial ? 'Menghitung otomatis...' : 'Belum ada data')"></h4>
                                                <div x-show="spatialError" class="text-xs text-red-500 mt-1" x-text="spatialError"></div>
                                            </div>
                                            <div class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-1 rounded-md flex items-center" x-show="jarakRphDisplay && !isCalculatingSpatial">
                                                <span x-text="jarakRphDisplay"></span> &nbsp;KM
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">
                                            <button type="button" @click="showRphModal = true" class="flex items-center text-xs font-medium bg-white border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50 transition-colors ml-auto">
                                                <svg class="w-3.5 h-3.5 mr-1.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                                Edit Data RPH
                                            </button>
                                        </div>
                                        <input type="hidden" name="jarak_rph" :value="jarakRphDisplay ? jarakRphDisplay.toString().replace(/,/g, '.') : ''">
                                    </div>
                                    
                                    <!-- Kompetitor Card -->
                                    <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm" :class="{'animate-pulse': isCalculatingSpatial}">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Jumlah Kompetitor (Radius <span x-text="spatialSearchRadius || 5"></span> KM)</p>
                                                <h4 class="text-base font-bold text-base-dark mt-1" x-text="competitorCount !== null && competitorCount !== '' ? competitorCount + ' Kompetitor' : (isCalculatingSpatial ? 'Menghitung otomatis...' : 'Belum ada data')"></h4>
                                                <div x-show="spatialError" class="text-xs text-red-500 mt-1" x-text="spatialError"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">
                                            <button type="button" @click="showCompetitorModal = true; editingCompetitor = null" class="flex items-center text-xs font-medium bg-white border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50 transition-colors ml-auto">
                                                <svg class="w-3.5 h-3.5 mr-1.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                                Kelola Data Kompetitor
                                            </button>
                                        </div>
                                        <input type="hidden" name="jumlah_kompetitor" :value="competitorCount">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- Step 1 Nav -->
                        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end pt-4 gap-3 sm:gap-4">
                            <a href="{{ route('manajer.observasi.index') }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center text-sm font-medium text-gray-500 hover:text-base-dark transition-colors py-2 min-h-[44px] px-6">
                                Batal
                            </a>
                            <button type="button" @click="nextStep()" class="w-full sm:w-auto justify-center inline-flex items-center px-6 py-2.5 min-h-[44px] bg-primary border border-transparent rounded-lg font-bold text-sm text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all shadow-md">
                                Selanjutnya
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                </div>
                <!-- STEP 2 -->
                <div x-show="step === 2" x-cloak>
                    <div class="space-y-6">
                        <!-- Section 3 (Formerly 4): Detail Fisik Bangunan -->
                        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <h3 class="text-lg font-bold text-base-dark">Informasi Bangunan</h3>
                            </div>
                            <div class="p-4 sm:p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Luas Bangunan (m²) *</label>
                                        <input type="text" inputmode="decimal" name="luas_bangunan" placeholder="e.g. 120" value="{{ old('luas_bangunan') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Luas Tanah (m²) *</label>
                                        <input type="text" inputmode="decimal" name="luas_tanah" placeholder="e.g. 150" value="{{ old('luas_tanah') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Jumlah Lantai *</label>
                                        <input type="number" name="jumlah_lantai" placeholder="e.g. 2" value="{{ old('jumlah_lantai') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                    </div>

                                    <div class="lg:col-span-2">
                                        <label class="block text-sm font-medium text-base-dark mb-1">Jml Ruang Operasional *</label>
                                        <input type="number" name="jumlah_ruangan" placeholder="e.g. 4" value="{{ old('jumlah_ruangan') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Jumlah Kamar Mandi *</label>
                                        <input type="number" name="jumlah_wc" placeholder="e.g. 2" value="{{ old('jumlah_wc') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                    </div>

                                    <div class="lg:col-span-3">
                                        <label class="block text-sm font-medium text-base-dark mb-1">Jenis Bangunan *</label>
                                        <select name="jenis_bangunan" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                            <option value="" disabled {{ old('jenis_bangunan') ? '' : 'selected' }}>Pilih Jenis Bangunan</option>
                                            <option value="Ruko" {{ old('jenis_bangunan') == 'Ruko' ? 'selected' : '' }}>Ruko</option>
                                            <option value="Rukan" {{ old('jenis_bangunan') == 'Rukan' ? 'selected' : '' }}>Rukan</option>
                                            <option value="Kios" {{ old('jenis_bangunan') == 'Kios' ? 'selected' : '' }}>Kios</option>
                                            <option value="Gudang" {{ old('jenis_bangunan') == 'Gudang' ? 'selected' : '' }}>Gudang</option>
                                            <option value="Rumah" {{ old('jenis_bangunan') == 'Rumah' ? 'selected' : '' }}>Rumah</option>
                                            <option value="Tanah Kosong" {{ old('jenis_bangunan') == 'Tanah Kosong' ? 'selected' : '' }}>Tanah Kosong</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Kondisi Bangunan *</label>
                                        <select name="kondisi_bangunan" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                            <option value="" disabled {{ old('kondisi_bangunan') ? '' : 'selected' }}>Pilih Kondisi</option>
                                            <option value="Sangat Baik" {{ old('kondisi_bangunan') == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                            <option value="Baik" {{ old('kondisi_bangunan') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                            <option value="Cukup" {{ old('kondisi_bangunan') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                            <option value="Kurang" {{ old('kondisi_bangunan') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                            <option value="Buruk" {{ old('kondisi_bangunan') == 'Buruk' ? 'selected' : '' }}>Buruk</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Sumber Air Bersih *</label>
                                        <select name="sumber_air" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                            <option value="" disabled {{ old('sumber_air') ? '' : 'selected' }}>Pilih Sumber Air</option>
                                            <option value="Sumur Bor" {{ old('sumber_air') == 'Sumur Bor' ? 'selected' : '' }}>Sumur Bor</option>
                                            <option value="PDAM" {{ old('sumber_air') == 'PDAM' ? 'selected' : '' }}>PDAM</option>
                                            <option value="Sumur Gali" {{ old('sumber_air') == 'Sumur Gali' ? 'selected' : '' }}>Sumur Gali</option>
                                            <option value="Mata Air" {{ old('sumber_air') == 'Mata Air' ? 'selected' : '' }}>Mata Air</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Daya Listrik *</label>
                                        <select name="daya_listrik" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                            <option value="" disabled {{ old('daya_listrik') ? '' : 'selected' }}>Pilih Daya Listrik</option>
                                            <option value="450 VA" {{ old('daya_listrik') == '450 VA' ? 'selected' : '' }}>450 VA</option>
                                            <option value="900 VA" {{ old('daya_listrik') == '900 VA' ? 'selected' : '' }}>900 VA</option>
                                            <option value="1300 VA" {{ old('daya_listrik') == '1300 VA' ? 'selected' : '' }}>1300 VA</option>
                                            <option value="2200 VA" {{ old('daya_listrik') == '2200 VA' ? 'selected' : '' }}>2200 VA</option>
                                            <option value="3500 VA" {{ old('daya_listrik') == '3500 VA' ? 'selected' : '' }}>3500 VA</option>
                                            <option value="> 3500 VA" {{ old('daya_listrik') == '> 3500 VA' ? 'selected' : '' }}>> 3500 VA</option>
                                            <option value="Tidak Ada" {{ old('daya_listrik') == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Area Parkir *</label>
                                        <select name="area_parkir" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                            <option value="" disabled {{ old('area_parkir') ? '' : 'selected' }}>Pilih Area Parkir</option>
                                            <option value="Mobil (1-2 Unit)" {{ old('area_parkir') == 'Mobil (1-2 Unit)' ? 'selected' : '' }}>Mobil (1-2 Unit)</option>
                                            <option value="Mobil (> 2 Unit)" {{ old('area_parkir') == 'Mobil (> 2 Unit)' ? 'selected' : '' }}>Mobil (> 2 Unit)</option>
                                            <option value="Hanya Motor" {{ old('area_parkir') == 'Hanya Motor' ? 'selected' : '' }}>Hanya Motor</option>
                                            <option value="Tidak Ada" {{ old('area_parkir') == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-base-dark mb-1">Lebar Jalan *</label>
                                        <select name="lebar_jalan" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                            <option value="" disabled {{ old('lebar_jalan') ? '' : 'selected' }}>Pilih Lebar Jalan</option>
                                            <option value="< 3 Meter" {{ old('lebar_jalan') == '< 3 Meter' ? 'selected' : '' }}>< 3 Meter</option>
                                            <option value="3-5 Meter" {{ old('lebar_jalan') == '3-5 Meter' ? 'selected' : '' }}>3-5 Meter</option>
                                            <option value="> 5 Meter" {{ old('lebar_jalan') == '> 5 Meter' ? 'selected' : '' }}>> 5 Meter</option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-base-dark mb-1">Ventilasi *</label>
                                            <select name="ventilasi" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                                <option value="" disabled {{ old('ventilasi') ? '' : 'selected' }}>Pilih</option>
                                                <option value="Baik" {{ old('ventilasi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="Cukup" {{ old('ventilasi') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                                <option value="Kurang" {{ old('ventilasi') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-base-dark mb-1">Sirkulasi *</label>
                                            <select name="sirkulasi" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">
                                                <option value="" disabled {{ old('sirkulasi') ? '' : 'selected' }}>Pilih</option>
                                                <option value="Baik" {{ old('sirkulasi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="Cukup" {{ old('sirkulasi') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                                <option value="Kurang" {{ old('sirkulasi') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="border-gray-100 my-6">

                                <div>
                                    <label for="catatan" class="block text-sm font-medium text-base-dark mb-1">Catatan Tambahan (Opsional)</label>
                                    <textarea id="catatan" name="catatan" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px]">{{ old('catatan') }}</textarea>
                                </div>
                            </div>
                        </div>


<!-- Section 3: Indikator Aksesibilitas & Kelayakan -->
                        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <h3 class="text-lg font-bold text-base-dark">Indikator Aksesibilitas & Kelayakan Bangunan</h3>
                            </div>
                            <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                                
                                <!-- Aksesibilitas -->
                                <div>
                                    <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Aksesibilitas</h4>
                                    <div class="space-y-3">
                                        <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                            <input type="checkbox" name="akses_roda4" value="1" {{ old('akses_roda4') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                            <span class="ml-3 text-sm text-base-dark font-medium">Bisa diakses kendaraan roda 4</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                            <input type="checkbox" name="jalan_bagus" value="1" {{ old('jalan_bagus') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                            <span class="ml-3 text-sm text-base-dark font-medium">Kondisi jalan bagus / tidak berlubang</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                            <input type="checkbox" name="dekat_fasilitas" value="1" {{ old('dekat_fasilitas') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                            <span class="ml-3 text-sm text-base-dark font-medium">Dekat dengan fasilitas umum (pasar/jalan utama)</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Kelayakan Bangunan -->
                                <div>
                                    <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Kelayakan Bangunan</h4>
                                    <div class="space-y-3">
                                        <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                            <input type="checkbox" name="bangunan_layak" value="1" {{ old('bangunan_layak') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                            <span class="ml-3 text-sm text-base-dark font-medium">Struktur bangunan kokoh & layak pakai</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                            <input type="checkbox" name="ventilasi_baik" value="1" {{ old('ventilasi_baik') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                            <span class="ml-3 text-sm text-base-dark font-medium">Ventilasi / Sirkulasi udara baik</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                            <input type="checkbox" name="air_listrik_memadai" value="1" {{ old('air_listrik_memadai') ? 'checked' : '' }} class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                            <span class="ml-3 text-sm text-base-dark font-medium">Jaringan air & listrik instalasi memadai</span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        

                
                    <div class="space-y-6">
                        <!-- Section 5: Dokumentasi Foto (Alpine JS Uploader) -->
                        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <h3 class="text-lg font-bold text-base-dark">Dokumentasi Lokasi (Maks. 10 Foto)</h3>
                            </div>
                            
                            <div class="p-4 sm:p-6">
                                <template x-if="step === 2">
                                    <div x-data="imageUploader()" x-init="initUploader()" class="space-y-4">
                                        <div class="flex items-center justify-center w-full"
                                             @dragover.prevent="dragover = true"
                                             @dragleave.prevent="dragover = false"
                                             @drop.prevent="dropFiles">
                                            
                                            <label for="fotos" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-xl cursor-pointer transition-colors"
                                                   :class="dragover ? 'border-primary bg-primary/5' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                    <p class="mb-2 text-sm text-gray-500"><span class="font-bold text-primary">Klik untuk upload</span> atau drag and drop foto</p>
                                                    <p class="text-xs text-gray-400">JPG, JPEG, PNG, WEBP (Maks 10 Foto, @10MB, Max Total: 100MB)</p>
                                                </div>
                                                <input type="file" id="fotos" name="photos[]" x-ref="fileInput" @change="addFiles($event.target.files)" multiple accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" />
                                            </label>
                                        </div>

                                        <!-- Alerts -->
                                        <div x-show="totalSizeWarning" class="bg-red-50 text-red-600 text-sm px-4 py-3 rounded-lg border border-red-200" style="display: none;">
                                            Total ukuran file melebihi 100MB. Silakan hapus beberapa foto.
                                        </div>
                                        <div x-show="maxFilesWarning" class="bg-red-50 text-red-600 text-sm px-4 py-3 rounded-lg border border-red-200" style="display: none;">
                                            Maksimal 10 foto yang diizinkan.
                                        </div>

                                        <!-- Image Preview Grid -->
                                        <template x-if="images.length > 0">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                                                <template x-for="(img, index) in images" :key="index">
                                                    <div class="relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm aspect-square bg-gray-50">
                                                        <img :src="img.url" class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110" />
                                                        <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                            <button type="button" @click="removeImage(index)" class="bg-red-500 hover:bg-red-600 text-white rounded-full p-2 transform scale-0 group-hover:scale-100 transition-transform">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            </button>
                                                        </div>
                                                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white text-[10px] px-2 py-1 truncate" x-text="img.name"></div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Step 2 Nav -->
                        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end pt-4 gap-3 sm:gap-4">
                            <button type="button" @click="prevStep()" :disabled="isSubmitting" class="w-full sm:w-auto inline-flex items-center justify-center text-sm font-medium text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors py-2.5 min-h-[44px] px-6 disabled:opacity-50">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Sebelumnya
                            </button>
                            <button type="submit" :disabled="isSubmitting" class="w-full sm:w-auto justify-center inline-flex items-center px-6 py-2.5 min-h-[44px] bg-primary border border-transparent rounded-lg font-bold text-sm text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Observasi'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Competitor Modal -->
            <div x-show="showCompetitorModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showCompetitorModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCompetitorModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div x-show="showCompetitorModal" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex justify-between items-center mb-5">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Daftar Kompetitor Aqiqah
                                </h3>
                                <div class="flex items-center space-x-4">
                                    <button type="button" x-show="!editingCompetitor" @click="editingCompetitor = { id: 'manual_'+Date.now(), nama: '', distance: 0, rating: 0, isNew: true }" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none sm:text-sm">
                                        + Tambah Kompetitor
                                    </button>
                                    <button type="button" @click="showCompetitorModal = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div x-show="!editingCompetitor" class="mt-2 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">NO</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NAMA KOMPETITOR</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">JARAK (KM)</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">RATING</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="(item, index) in competitorsList" :key="item.id">
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center" x-text="index + 1"></td>
                                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                                    <span x-text="item.nama"></span>
                                                    <div x-show="item.alamat" class="text-xs text-gray-500 mt-1" x-text="item.alamat"></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center"><span x-text="item.distance"></span> KM</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-yellow-600 text-center" x-text="item.rating ? item.rating : '-'"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center space-x-2">
                                                    <button type="button" @click="editingCompetitor = { id: item.id, nama: item.nama, distance: item.distance, rating: item.rating, isNew: false }" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-3 py-1 rounded border border-yellow-200">Edit</button>
                                                    <button type="button" @click="if(confirm('Hapus kompetitor ini?')) { competitorsList = competitorsList.filter(c => c.id !== item.id); competitorCount = competitorsList.length; }" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded border border-red-200">Hapus</button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="competitorsList.length === 0">
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada kompetitor di radius ini.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Edit Form inside Modal -->
                            <div x-show="editingCompetitor" style="display: none;" class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kompetitor</label>
                                    <input type="text" x-model="editingCompetitor?.nama" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jarak (KM)</label>
                                        <input type="text" inputmode="decimal" x-model="editingCompetitor?.distance" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                        <input type="text" inputmode="decimal" x-model="editingCompetitor?.rating" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3">
                                    </div>
                                </div>
                                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                                    <button type="button" @click="editingCompetitor = null" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                                    <button type="button" @click="if(editingCompetitor.isNew) { competitorsList.unshift(editingCompetitor); } else { let idx = competitorsList.findIndex(c => c.id === editingCompetitor.id); if(idx !== -1) competitorsList[idx] = editingCompetitor; } competitorCount = competitorsList.length; editingCompetitor = null;" class="px-4 py-2 bg-primary border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-primary-dark">Simpan</button>
                                </div>
                            </div>

                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" x-show="!editingCompetitor" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" @click="showCompetitorModal = false">
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RPH Modal -->
            <div x-show="showRphModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showRphModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showRphModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div x-show="showRphModal" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex justify-between items-center mb-5">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Detail Rumah Potong Hewan (RPH)
                                </h3>
                                <button type="button" @click="showRphModal = false" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <div class="mt-4 space-y-4">
                                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg flex items-start">
                                    <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="text-sm text-blue-800">
                                        <p class="font-semibold mb-1">Rekomendasi Sistem (Terdekat):</p>
                                        <p x-show="rphList.length > 0">
                                            <span class="font-bold" x-text="rphList[0]?.nama"></span> — Jarak: <span class="font-bold" x-text="rphList[0]?.distance + ' KM'"></span>
                                        </p>
                                        <p x-show="rphList.length === 0">Tidak ada RPH di sekitar.</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama RPH (Manual)</label>
                                    <input type="text" x-model="rphName" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3" placeholder="Masukkan nama RPH...">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jarak RPH (KM)</label>
                                    <input type="text" inputmode="decimal" x-model="jarakRphDisplay" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3" placeholder="Contoh: 1.5">
                                    <p class="text-xs text-gray-500 mt-1">Gunakan titik atau koma untuk angka desimal</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" @click="showRphModal = false">
                                Simpan & Tutup
                            </button>
                            <button type="button" @click="if(rphList.length > 0) { rphName = rphList[0].nama; jarakRphDisplay = rphList[0].distance; }" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Reset ke Sistem
                            </button>
                        </div>
                    </div>
                </div>
            </div>

                    <!-- Hidden inputs to ensure they are submitted regardless of step -->
            <input type="hidden" name="latitude" id="hidden_lat">
            <input type="hidden" name="longitude" id="hidden_lng">
        </form>
    </div>

    <!-- Alpine Batch Manager Logic -->
    <script>
        function batchManager() {
            return {
                batches: @json($batches),
                selectedBatch: '{{ old("batch_id", "") }}',
                showModal: false,
                isEditing: false,
                editId: null,
                batchName: '',
                isSubmitting: false,
                
                init() {
                    if(!this.selectedBatch && this.batches.length > 0) {
                        // Select the active batch, or the first one
                        let active = this.batches.find(b => b.is_active);
                        this.selectedBatch = active ? active.id : this.batches[0].id;
                    }
                },
                
                openAddModal() {
                    this.isEditing = false;
                    this.editId = null;
                    this.batchName = '';
                    this.showModal = true;
                },
                
                openEditModal() {
                    let batch = this.batches.find(b => b.id == this.selectedBatch);
                    if(!batch) return;
                    this.isEditing = true;
                    this.editId = batch.id;
                    this.batchName = batch.nama_batch;
                    this.showModal = true;
                },
                
                async saveBatch() {
                    if(!this.batchName.trim()) { alert('Nama batch tidak boleh kosong'); return; }
                    this.isSubmitting = true;
                    
                    try {
                        let url = this.isEditing ? `/manajer/batches/${this.editId}` : '/manajer/batches';
                        let method = this.isEditing ? 'PUT' : 'POST';
                        
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ nama_batch: this.batchName })
                        });
                        
                        const data = await response.json();
                        
                        if(response.ok) {
                            if(this.isEditing) {
                                let idx = this.batches.findIndex(b => b.id == this.editId);
                                if(idx !== -1) Object.assign(this.batches[idx], data.batch);
                            } else {
                                this.batches.unshift(data.batch);
                                this.selectedBatch = data.batch.id;
                            }
                            this.showModal = false;
                        } else {
                            alert(data.message || data.errors?.nama_batch?.[0] || 'Terjadi kesalahan');
                        }
                    } catch (err) {
                        alert('Gagal menghubungi server');
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                
                async deleteBatch() {
                    if(!this.selectedBatch) return;
                    if(!confirm('Apakah Anda yakin ingin menghapus Batch ini? (Hanya bisa dihapus jika belum ada lokasi yang terikat)')) return;
                    
                    try {
                        const response = await fetch(`/manajer/batches/${this.selectedBatch}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        
                        if(response.ok) {
                            this.batches = this.batches.filter(b => b.id != this.selectedBatch);
                            this.selectedBatch = this.batches.length > 0 ? this.batches[0].id : '';
                            alert(data.message);
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    } catch (err) {
                        alert('Gagal menghubungi server');
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine Image Uploader Logic -->
    <script>
        function imageUploader() {
            return {
                dragover: false,
                totalSizeWarning: false,
                maxFilesWarning: false,
                
                initUploader() {
                    // Pre-populate input files on re-render if user navigated back
                    this.$nextTick(() => {
                        if(this.$refs.fileInput && this.images.length > 0) {
                            this.syncFileInput();
                        }
                    });
                },
                
                dropFiles(e) {
                    this.dragover = false;
                    this.addFiles(e.dataTransfer.files);
                },
                
                addFiles(files) {
                    let totalSize = this.images.reduce((acc, img) => acc + img.file.size, 0);
                    
                    Array.from(files).forEach(file => {
                        // Check if it's an image
                        if (!file.type.match('image.*')) return;
                        
                        // Check individual file size (Max 10MB)
                        if (file.size > (10 * 1024 * 1024)) {
                            alert('File ' + file.name + ' terlalu besar! Maksimal ukuran per foto adalah 10MB.');
                            return;
                        }

                        // Check Max Files (10)
                        if (this.images.length >= 10) {
                            this.maxFilesWarning = true;
                            return;
                        } else {
                            this.maxFilesWarning = false;
                        }

                        // Check Total Size (100MB = 100 * 1024 * 1024)
                        if ((totalSize + file.size) > (100 * 1024 * 1024)) {
                            this.totalSizeWarning = true;
                            return;
                        } else {
                            this.totalSizeWarning = false;
                        }

                        totalSize += file.size;

                        // Create Preview URL
                        let url = URL.createObjectURL(file);
                        this.images.push({
                            file: file,
                            url: url,
                            name: file.name
                        });
                    });

                    this.syncFileInput();
                },

                removeImage(index) {
                    URL.revokeObjectURL(this.images[index].url);
                    this.images.splice(index, 1);
                    this.totalSizeWarning = false;
                    this.maxFilesWarning = false;
                    this.syncFileInput();
                },

                syncFileInput() {
                    const dt = new DataTransfer();
                    this.images.forEach(img => {
                        dt.items.add(img.file);
                    });
                    if(this.$refs.fileInput) {
                        this.$refs.fileInput.files = dt.files;
                    }
                }
            }
        }
    </script>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map { min-height: 300px; }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        function locationMap() {
            return {
                map: null,
                marker: null,
                greenIcon: null,
                isFetchingLocation: false,
                isGeocoding: false,
                accuracy: null,
                acquisitionTime: null,
                geocodeCache: {},
                debounceTimeout: null,

                initMap() {
                    this.$nextTick(() => {
                        let defaultLat = this.lat ? parseFloat(this.lat) : -0.789;
                        let defaultLng = this.lng ? parseFloat(this.lng) : 113.921;
                        let defaultZoom = this.lat ? 13 : 5;

                        this.map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(this.map);

                        this.greenIcon = new L.Icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        });

                        if (this.lat && this.lng) {
                            this.marker = L.marker([this.lat, this.lng], { draggable: true, icon: this.greenIcon }).addTo(this.map);
                            this.setupMarkerEvents();
                        }

                        this.map.on('click', (e) => {
                            const coords = e.latlng;
                            this.updateMarker(coords.lat, coords.lng, true);
                        });
                        
                        setTimeout(() => {
                            this.map.invalidateSize();
                        }, 200);
                    });
                },

                
                updateMarker(lat, lng, doGeocode = false) {
                    this.lat = lat.toFixed(6);
                    this.lng = lng.toFixed(6);
                    
                    // Directly update the hidden inputs to guarantee form submission
                    let latInput = document.querySelector('input[name="latitude"]');
                    let lngInput = document.querySelector('input[name="longitude"]');
                    if(latInput) latInput.value = this.lat;
                    if(lngInput) lngInput.value = this.lng;
                    if (this.marker) {
                        this.marker.setLatLng([lat, lng]);
                    } else {
                        this.marker = L.marker([lat, lng], { draggable: true, icon: this.greenIcon }).addTo(this.map);
                        this.setupMarkerEvents();
                    }
                    if (doGeocode) {
                        this.debouncedGeocode(lat, lng);
                    }
                    
                    // Trigger spatial data fetch
                    window.dispatchEvent(new CustomEvent('location-updated', { detail: { lat: lat, lng: lng } }));
                },

                
                setupMarkerEvents() {
                    this.marker.on('dragend', (e) => {
                        const coords = e.target.getLatLng();
                        this.lat = coords.lat.toFixed(6);
                        this.lng = coords.lng.toFixed(6);
                        
                        let latInput = document.querySelector('input[name="latitude"]');
                        let lngInput = document.querySelector('input[name="longitude"]');
                        if(latInput) latInput.value = this.lat;
                        if(lngInput) lngInput.value = this.lng;
                        this.debouncedGeocode(coords.lat, coords.lng);
                        
                        // Trigger spatial data fetch on drag end
                        window.dispatchEvent(new CustomEvent('location-updated', { detail: { lat: coords.lat, lng: coords.lng } }));
                    });
                },

                getCurrentLocation() {
                    if ("geolocation" in navigator) {
                        this.isFetchingLocation = true;
                        navigator.geolocation.getCurrentPosition((position) => {
                            this.isFetchingLocation = false;
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            this.accuracy = position.coords.accuracy;
                            
                            const date = new Date();
                            this.acquisitionTime = date.getHours().toString().padStart(2, '0') + ':' + 
                                                   date.getMinutes().toString().padStart(2, '0') + ':' + 
                                                   date.getSeconds().toString().padStart(2, '0');
                            
                            this.map.setView([lat, lng], 15);
                            this.updateMarker(lat, lng, true);
                        }, (error) => {
                            this.isFetchingLocation = false;
                            alert("Tidak dapat mengambil lokasi Anda. Pastikan Anda telah memberikan izin pada browser. Jika gagal, silakan cari lokasi di peta secara manual.");
                        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                    } else {
                        alert("Browser Anda tidak mendukung fitur Geolocation.");
                    }
                },
                
                debouncedGeocode(lat, lng) {
                    if (this.debounceTimeout) {
                        clearTimeout(this.debounceTimeout);
                    }
                    this.debounceTimeout = setTimeout(() => {
                        this.reverseGeocode(lat, lng);
                    }, 500); // 500ms debounce
                },
                
                async reverseGeocode(lat, lng) {
                    const cacheKey = `${lat.toFixed(4)},${lng.toFixed(4)}`;
                    
                    if (this.geocodeCache[cacheKey]) {
                        // Use Cache
                        this.dispatchAddress(this.geocodeCache[cacheKey]);
                        return;
                    }
                    
                    this.isGeocoding = true;
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                        if (!response.ok) throw new Error("HTTP error " + response.status);
                        
                        const data = await response.json();
                        
                        if (data && data.address) {
                            const addr = data.address;
                            // Priority: road, house_number, suburb, village, city_district, district, city, state, country
                            let parts = [];
                            if (addr.road) parts.push(addr.road + (addr.house_number ? ' No. ' + addr.house_number : ''));
                            if (addr.suburb) parts.push(addr.suburb);
                            if (addr.village && !addr.suburb) parts.push(addr.village);
                            if (addr.city_district) parts.push(addr.city_district);
                            if (addr.district) parts.push('Kecamatan ' + addr.district.replace('Kecamatan ', ''));
                            if (addr.city) parts.push(addr.city);
                            else if (addr.county) parts.push(addr.county);
                            if (addr.state) parts.push(addr.state);
                            
                            const fullAddress = parts.filter(p => p).join(', ');
                            
                            const geocodeData = {
                                fullAddress: fullAddress,
                                state: addr.state || '',
                                city: addr.city || '',
                                county: addr.county || '',
                                district: addr.district || '',
                                suburb: addr.suburb || addr.village || ''
                            };
                            
                            this.geocodeCache[cacheKey] = geocodeData;
                            this.dispatchAddress(geocodeData);
                        } else {
                            console.warn("No address found for these coordinates.");
                        }
                    } catch (e) {
                        console.error("Reverse geocoding failed", e);
                        // Do not block UI, just fail silently or show a small notice
                    } finally {
                        this.isGeocoding = false;
                    }
                },
                
                dispatchAddress(data) {
                    // Dispatch event to window so the region x-data can catch it
                    window.dispatchEvent(new CustomEvent('address-resolved', {
                        detail: data
                    }));
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
