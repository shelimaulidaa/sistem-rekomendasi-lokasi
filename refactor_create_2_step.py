import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# 1. Alpine JS Data
content = content.replace("totalSteps: 4,", "totalSteps: 2,\n                  isCalculatingSpatial: false,\n                  spatialError: null,")

# Alpine JS: validateStep1
new_validate_step1 = """validateStep1() {
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
                      
                      const kompel = document.getElementById('jumlah_kompetitor');
                      if(!kompel || kompel.value === '' || parseInt(kompel.value) < 0) { 
                          alert('Jumlah Kompetitor wajib diisi dan harus bernilai valid (>= 0).'); return false; 
                      }
                      
                      const jr = this.jarakRphDisplay;
                      if(jr === '' || jr === null || isNaN(parseFloat(jr.toString().replace(/,/g, '.'))) || parseFloat(jr.toString().replace(/,/g, '.')) < 0) { 
                          alert('Jarak RPH wajib diisi dan harus bernilai valid (>= 0).'); return false; 
                      }
                      
                      return true;
                  },"""

# Use regex to replace validateStep1
content = re.sub(r"validateStep1\(\) \{.*?(?=\s+validateStep2\(\))", new_validate_step1, content, flags=re.DOTALL)

# Replace validateStep2, 3 logic with merged logic
new_validate_step2 = """validateStep2() {
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
                  },"""

content = re.sub(r"validateStep2\(\) \{.*?(?=\s+nextStep\(\))", new_validate_step2, content, flags=re.DOTALL)

# Add fetchSpatialData method inside Alpine JS
fetch_spatial_code = """
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
                              document.getElementById('jumlah_kompetitor').value = data.competitor_count;
                              this.jarakRphDisplay = data.nearest_rph_distance;
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
"""
content = content.replace("focusTop() {", fetch_spatial_code + "focusTop() {")


# 2. Modify form submit
content = content.replace("if(step !== 4)", "if(step !== 2)")


# 3. Modify Desktop Stepper UI
desktop_stepper = """<!-- Desktop Stepper -->
                <div class="hidden sm:block">
                    <nav aria-label="Progress">
                        <ol role="list" class="flex items-center justify-between">
                            
                            <!-- Step 1 -->
                            <li class="relative flex-1">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true" style="width: 100%;">
                                    <div class="h-1 w-full transition-colors duration-300" :class="step > 1 ? 'bg-primary' : 'bg-gray-200'"></div>
                                </div>
                                <button type="button" @click="goToStep(1)" class="relative flex h-10 w-10 items-center justify-center rounded-full transition-all duration-300 shadow-sm"
                                    :class="step > 1 ? 'bg-primary hover:bg-primary-dark' : (step === 1 ? 'border-4 border-primary bg-white scale-110' : 'border-4 border-gray-300 bg-white hover:border-gray-400')">
                                    <template x-if="step > 1">
                                        <svg class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                    </template>
                                    <template x-if="step === 1">
                                        <span class="h-3 w-3 rounded-full bg-primary"></span>
                                    </template>
                                </button>
                                <span class="absolute -bottom-8 left-0 -translate-x-[20%] text-xs font-bold w-max" :class="step >= 1 ? 'text-primary' : 'text-gray-500'">Informasi Lokasi</span>
                            </li>
                            
                            <!-- Step 2 -->
                            <li class="relative">
                                <button type="button" @click="goToStep(2)" class="relative flex h-10 w-10 items-center justify-center rounded-full transition-all duration-300 shadow-sm"
                                    :class="step === 2 ? 'border-4 border-primary bg-white scale-110' : 'border-4 border-gray-300 bg-white hover:border-gray-400'">
                                    <template x-if="step === 2">
                                        <span class="h-3 w-3 rounded-full bg-primary"></span>
                                    </template>
                                    <template x-if="step < 2">
                                        <span class="h-3 w-3 rounded-full bg-transparent group-hover:bg-gray-300"></span>
                                    </template>
                                </button>
                                <span class="absolute -bottom-8 left-0 -translate-x-[20%] text-xs font-bold w-max" :class="step >= 2 ? 'text-primary' : 'text-gray-500'">Kondisi Bangunan & Dokumentasi</span>
                            </li>

                        </ol>
                    </nav>
                </div>"""
content = re.sub(r"<!-- Desktop Stepper -->.*?<!-- STEP 1 -->", desktop_stepper + "\n\n                <!-- STEP 1 -->", content, flags=re.DOTALL)


# 4. Moving the Numerik section into Step 1
# We extract the whole Step 2 section
step2_match = re.search(r"<!-- STEP 2 -->\s*<div x-show=\"step === 2\" x-cloak>(.*?)<!-- Step 2 Nav -->", content, re.DOTALL)
if step2_match:
    numerik_content = step2_match.group(1)
    
    # We add isCalculatingSpatial loaders to the inputs
    # Jumlah kompetitor
    numerik_content = numerik_content.replace(
        """<input id="jumlah_kompetitor" type="number" name="jumlah_kompetitor" value="{{ old('jumlah_kompetitor') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">""",
        """<input id="jumlah_kompetitor" type="number" name="jumlah_kompetitor" value="{{ old('jumlah_kompetitor') }}" required :readonly="!spatialError" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]" :class="{'bg-gray-100': !spatialError, 'animate-pulse': isCalculatingSpatial}">
                                        <div x-show="isCalculatingSpatial" class="absolute inset-y-0 right-10 pr-3 flex items-center">
                                            <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </div>"""
    )
    
    # Jarak RPH
    numerik_content = numerik_content.replace(
        """<input id="jarak_rph_display" type="text" inputmode="decimal" x-model="jarakRphDisplay" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]">""",
        """<input id="jarak_rph_display" type="text" inputmode="decimal" x-model="jarakRphDisplay" required :readonly="!spatialError" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 min-h-[44px]" :class="{'bg-gray-100': !spatialError, 'animate-pulse': isCalculatingSpatial}">
                                        <div x-show="isCalculatingSpatial" class="absolute inset-y-0 right-10 pr-3 flex items-center">
                                            <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </div>"""
    )
    
    # Add helper text error
    numerik_content += """\n<div x-show="spatialError" class="col-span-full mt-2 text-sm text-red-500 font-medium" x-text="spatialError"></div>\n"""

    # Insert into Step 1 before Step 1 Nav
    content = content.replace("<!-- Step 1 Nav -->", numerik_content + "\n                        <!-- Step 1 Nav -->", 1)

# Now delete Step 2 entirely
content = re.sub(r"<!-- STEP 2 -->.*?<!-- STEP 3 -->", "<!-- STEP 2 -->", content, flags=re.DOTALL)

# Now change Step 3 and 4 to be under Step 2
content = content.replace("<!-- STEP 3 -->", "<!-- STEP 2 CONTENT BEGIN -->")
content = content.replace("""<div x-show="step === 3" x-cloak>""", """<div x-show="step === 2" x-cloak>""")

# Remove Step 3 Nav
content = re.sub(r"<!-- Step 3 Nav -->.*?<!-- STEP 4 -->", "<!-- STEP 4 CONTENT -->", content, flags=re.DOTALL)
content = content.replace("""<div x-show="step === 4" x-cloak>""", "")

# Replace Step 4 submit button logic
content = content.replace("""<!-- Step 4 Nav -->""", """<!-- Step 2 Nav -->""")


# Replace map dragend logic to use updateLocation
content = content.replace("marker.on('dragend', function(e) {", "marker.on('dragend', function(e) {\n                updateLocation(e.target.getLatLng().lat, e.target.getLatLng().lng);")
content = content.replace("document.getElementById('latitude').value = marker.getLatLng().lat;", "")
content = content.replace("document.getElementById('longitude').value = marker.getLatLng().lng;", "")

content = content.replace("map.on('click', function(e) {", "map.on('click', function(e) {\n                updateLocation(e.latlng.lat, e.latlng.lng);")
content = content.replace("document.getElementById('latitude').value = e.latlng.lat;", "")
content = content.replace("document.getElementById('longitude').value = e.latlng.lng;", "")

# Replace "Gunakan Lokasi Saat Ini"
content = content.replace("document.getElementById('latitude').value = lat;", "updateLocation(lat, lng);")
content = content.replace("document.getElementById('longitude').value = lng;", "")

# Add updateLocation helper inside leaflet setup
leaflet_patch = """
        function updateLocation(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Dispatch event to AlpineJS
            let event = new CustomEvent('location-updated', { detail: { lat: lat, lng: lng } });
            window.dispatchEvent(event);
        }
"""
content = content.replace("function initMap() {", leaflet_patch + "\n        function initMap() {")

# In Alpine, attach @location-updated.window
content = content.replace("""x-data="{""", """@location-updated.window="fetchSpatialData($event.detail.lat, $event.detail.lng)"\n              x-data="{""")


with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Refactored create.blade.php")

