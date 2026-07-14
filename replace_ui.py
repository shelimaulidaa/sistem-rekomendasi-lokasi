import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# 1. Add Alpine state for manual edit
content = content.replace("spatialError: null,", "spatialError: null,\n                  isManualRph: false,\n                  isManualKompetitor: false,")

# 2. Extract the grid and change it
grid_match = re.search(r'(<div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">)(.*?)(</div>\s*<!-- Step 1 Nav -->)', content, flags=re.DOTALL)
if grid_match:
    grid_inner = grid_match.group(2)
    
    harga_sewa_match = re.search(r'(<div>\s*<label for="harga_sewa".*?</p>\s*</div>)', grid_inner, flags=re.DOTALL)
    harga_sewa_html = harga_sewa_match.group(1).replace('<div>', '', 1)
    harga_sewa_html = harga_sewa_html[::-1].replace('</div>'[::-1], '', 1)[::-1] # remove last div
    
    new_html = """
                            <div class="p-4 sm:p-6 space-y-6">
                                <!-- Harga Sewa stays as normal input -->
                                <div class="max-w-md">
""" + harga_sewa_html + """
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
                                            <a href="#" class="text-sm font-semibold text-primary hover:text-primary-dark">Lihat Detail RPH ></a>
                                            <button type="button" @click="isManualRph = !isManualRph" class="flex items-center text-xs font-medium bg-white border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50 transition-colors">
                                                <svg class="w-3.5 h-3.5 mr-1.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                                Edit Manual
                                            </button>
                                        </div>
                                        
                                        <!-- Manual Edit Section RPH -->
                                        <div x-show="isManualRph" x-transition class="mt-4 pt-4 border-t border-gray-100 bg-gray-50 p-4 rounded-md -mx-4 -mb-4">
                                            <label for="jarak_rph" class="block text-sm font-medium text-base-dark mb-1">Input Jarak RPH Manual (KM)</label>
                                            <div class="flex gap-3">
                                                <div class="relative flex-1">
                                                    <input type="hidden" name="jarak_rph" :value="jarakRphDisplay ? jarakRphDisplay.toString().replace(/,/g, '.') : ''">
                                                    <input id="jarak_rph_display" type="text" inputmode="decimal" x-model="jarakRphDisplay" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3">
                                                </div>
                                                <button type="button" @click="isManualRph = false" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">Simpan</button>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Gunakan titik atau koma untuk angka desimal</p>
                                        </div>
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
                                            <a href="#" class="text-sm font-semibold text-primary hover:text-primary-dark">Lihat Daftar Kompetitor ></a>
                                            <button type="button" @click="isManualKompetitor = !isManualKompetitor" class="flex items-center text-xs font-medium bg-white border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50 transition-colors">
                                                <svg class="w-3.5 h-3.5 mr-1.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                                Edit Manual
                                            </button>
                                        </div>
                                        
                                        <!-- Manual Edit Section Kompetitor -->
                                        <div x-show="isManualKompetitor" x-transition class="mt-4 pt-4 border-t border-gray-100 bg-gray-50 p-4 rounded-md -mx-4 -mb-4">
                                            <label for="jumlah_kompetitor" class="block text-sm font-medium text-base-dark mb-1">Input Jumlah Kompetitor Manual</label>
                                            <div class="flex gap-3">
                                                <div class="relative flex-1">
                                                    <input id="jumlah_kompetitor" type="number" name="jumlah_kompetitor" x-model="competitorCount" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3">
                                                </div>
                                                <button type="button" @click="isManualKompetitor = false" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
"""
    content = content.replace(grid_match.group(0), new_html + "\n                        <!-- Step 1 Nav -->")

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Applied Custom Card UI")
