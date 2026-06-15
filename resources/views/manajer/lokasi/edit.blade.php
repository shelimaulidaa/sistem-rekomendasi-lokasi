<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-dark leading-tight">
            {{ __('Edit Lokasi') }} - <span class="text-primary">{{ $lokasi->nama_lokasi }}</span>
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="wilayahDropdown()">
        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
            <div class="p-6 sm:p-8">
                
                <form method="POST" action="{{ route('manajer.lokasi.update', $lokasi) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        
                        <!-- Nama Lokasi -->
                        <div>
                            <label for="nama_lokasi" class="block text-sm font-medium text-base-dark mb-1">Nama Lokasi / Alternatif</label>
                            <input id="nama_lokasi" type="text" name="nama_lokasi" value="{{ old('nama_lokasi', $lokasi->nama_lokasi) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px]">
                            <x-input-error :messages="$errors->get('nama_lokasi')" class="mt-2" />
                        </div>

                        <!-- Alamat Lengkap -->
                        <div>
                            <label for="alamat" class="block text-sm font-medium text-base-dark mb-1">Alamat Lengkap</label>
                            <textarea id="alamat" name="alamat" required rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px]">{{ old('alamat', $lokasi->alamat) }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>

                        <!-- Wilayah Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Provinsi -->
                            <div>
                                <label class="block text-sm font-medium text-base-dark mb-1">Provinsi</label>
                                
                                <template x-if="!selectedProvince && legacyProvinsi">
                                    <div class="text-xs text-yellow-600 mb-1 flex items-center bg-yellow-50 p-1 rounded">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Lama: <span class="font-semibold ml-1" x-text="legacyProvinsi"></span>
                                    </div>
                                </template>
                                
                                <select 
                                    x-model="selectedProvince" 
                                    @change="fetchRegencies; selectedProvinceName = $event.target.options[$event.target.selectedIndex].text; selectedRegency = ''; selectedRegencyName = ''; selectedDistrict = ''; selectedDistrictName = '';" 
                                    :required="!legacyProvinsi"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px] bg-white"
                                >
                                    <option value="">-- Update Provinsi --</option>
                                    <template x-for="prov in provinces" :key="prov.id">
                                        <option :value="prov.id" x-text="prov.name"></option>
                                    </template>
                                </select>
                                <input type="hidden" name="province_id" :value="selectedProvince">
                                <input type="hidden" name="provinsi" :value="selectedProvince ? selectedProvinceName : legacyProvinsi">
                                <x-input-error :messages="$errors->get('provinsi')" class="mt-2" />
                            </div>

                            <!-- Kabupaten -->
                            <div>
                                <label class="block text-sm font-medium text-base-dark mb-1">Kabupaten/Kota</label>
                                
                                <template x-if="!selectedRegency && legacyKabupaten">
                                    <div class="text-xs text-yellow-600 mb-1 flex items-center bg-yellow-50 p-1 rounded">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Lama: <span class="font-semibold ml-1" x-text="legacyKabupaten"></span>
                                    </div>
                                </template>
                                
                                <select 
                                    x-model="selectedRegency" 
                                    @change="fetchDistricts; selectedRegencyName = $event.target.options[$event.target.selectedIndex].text; selectedDistrict = ''; selectedDistrictName = '';" 
                                    :required="!legacyKabupaten || selectedProvince !== ''"
                                    :disabled="regencies.length === 0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px] bg-white disabled:bg-gray-100 disabled:text-gray-400"
                                >
                                    <option value="">-- Update Kabupaten --</option>
                                    <template x-for="reg in regencies" :key="reg.id">
                                        <option :value="reg.id" x-text="reg.name"></option>
                                    </template>
                                </select>
                                <input type="hidden" name="regency_id" :value="selectedRegency">
                                <input type="hidden" name="kabupaten" :value="selectedRegency ? selectedRegencyName : legacyKabupaten">
                                <x-input-error :messages="$errors->get('kabupaten')" class="mt-2" />
                            </div>

                            <!-- Kecamatan -->
                            <div>
                                <label class="block text-sm font-medium text-base-dark mb-1">Kecamatan</label>
                                
                                <template x-if="!selectedDistrict && legacyKecamatan">
                                    <div class="text-xs text-yellow-600 mb-1 flex items-center bg-yellow-50 p-1 rounded">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Lama: <span class="font-semibold ml-1" x-text="legacyKecamatan"></span>
                                    </div>
                                </template>
                                
                                <select 
                                    x-model="selectedDistrict" 
                                    @change="selectedDistrictName = $event.target.options[$event.target.selectedIndex].text" 
                                    :required="!legacyKecamatan || selectedRegency !== ''"
                                    :disabled="districts.length === 0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm transition-colors py-2 px-3 min-h-[44px] bg-white disabled:bg-gray-100 disabled:text-gray-400"
                                >
                                    <option value="">-- Update Kecamatan --</option>
                                    <template x-for="dist in districts" :key="dist.id">
                                        <option :value="dist.id" x-text="dist.name"></option>
                                    </template>
                                </select>
                                <input type="hidden" name="district_id" :value="selectedDistrict">
                                <input type="hidden" name="kecamatan" :value="selectedDistrict ? selectedDistrictName : legacyKecamatan">
                                <x-input-error :messages="$errors->get('kecamatan')" class="mt-2" />
                            </div>
                        </div>



                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end mt-8 pt-5 border-t border-gray-100 gap-3 sm:gap-4">
                        <a href="{{ route('manajer.lokasi.index') }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center text-sm font-medium text-gray-500 hover:text-base-dark transition-colors py-2 min-h-[44px]">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 min-h-[44px] bg-primary border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors shadow-sm">
                            Update Lokasi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Alpine JS Script for Dependent Dropdown & Legacy Fallback -->
    <script>
        function wilayahDropdown() {
            return {
                provinces: [],
                regencies: [],
                districts: [],
                
                // Track IDs
                selectedProvince: '{{ old('province_id', $lokasi->province_id) }}',
                selectedRegency: '{{ old('regency_id', $lokasi->regency_id) }}',
                selectedDistrict: '{{ old('district_id', $lokasi->district_id) }}',
                
                // Track Names
                selectedProvinceName: '{{ old('provinsi', $lokasi->province_id ? $lokasi->provinsi : '') }}',
                selectedRegencyName: '{{ old('kabupaten', $lokasi->regency_id ? $lokasi->kabupaten : '') }}',
                selectedDistrictName: '{{ old('kecamatan', $lokasi->district_id ? $lokasi->kecamatan : '') }}',

                // Legacy fallbacks (when ID is null but string name exists)
                legacyProvinsi: '{{ !$lokasi->province_id ? $lokasi->provinsi : '' }}',
                legacyKabupaten: '{{ !$lokasi->regency_id ? $lokasi->kabupaten : '' }}',
                legacyKecamatan: '{{ !$lokasi->district_id ? $lokasi->kecamatan : '' }}',

                async init() {
                    let res = await fetch('/api/wilayah/provinces');
                    this.provinces = await res.json();
                    
                    if (this.selectedProvince) {
                        await this.fetchRegencies();
                    }
                    if (this.selectedRegency) {
                        await this.fetchDistricts();
                    }
                },

                async fetchRegencies() {
                    this.regencies = [];
                    this.districts = [];
                    if (!this.selectedProvince) return;
                    
                    let res = await fetch(`/api/wilayah/regencies/${this.selectedProvince}`);
                    this.regencies = await res.json();
                },

                async fetchDistricts() {
                    this.districts = [];
                    if (!this.selectedRegency) return;
                    
                    let res = await fetch(`/api/wilayah/districts/${this.selectedRegency}`);
                    this.districts = await res.json();
                }
            }
        }
    </script>


</x-app-layout>
