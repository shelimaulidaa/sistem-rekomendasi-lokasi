import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# 1. Update Alpine State
state_replace = """                  rphName: '',
                  competitorCount: 0,
                  spatialSearchRadius: 0,
                  competitorsList: [],
                  rphList: [],
                  showCompetitorModal: false,
                  showRphModal: false,"""
content = content.replace("rphName: '',\n                  competitorCount: 0,\n                  spatialSearchRadius: 0,", state_replace)

# 2. Update fetchSpatialData
fetch_replace = """                              this.rphName = data.nearest_rph_name;
                              this.competitorCount = data.competitor_count;
                              this.spatialSearchRadius = data.search_radius;
                              this.competitorsList = data.competitors_list || [];
                              this.rphList = data.rph_list || [];
                              this.isCalculatingSpatial = false;"""
content = content.replace("this.rphName = data.nearest_rph_name;\n                              this.competitorCount = data.competitor_count;\n                              this.spatialSearchRadius = data.search_radius;\n                              this.isCalculatingSpatial = false;", fetch_replace)

# 3. Update Anchor links
link1 = """<a href="#" @click.prevent="showRphModal = true" class="text-sm font-semibold text-primary hover:text-primary-dark">Lihat Detail RPH ></a>"""
content = re.sub(r'<a href="#" class="text-sm font-semibold text-primary hover:text-primary-dark">Lihat Detail RPH ></a>', link1, content)

link2 = """<a href="#" @click.prevent="showCompetitorModal = true" class="text-sm font-semibold text-primary hover:text-primary-dark">Lihat Daftar Kompetitor ></a>"""
content = re.sub(r'<a href="#" class="text-sm font-semibold text-primary hover:text-primary-dark">Lihat Daftar Kompetitor ></a>', link2, content)

# 4. Modals HTML
modals = """
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
                                    <button type="button" @click="let name = prompt('Masukkan Nama Kompetitor:'); if(name) { competitorsList.unshift({id: 'manual_'+Date.now(), nama: name, distance: 0, rating: 0}); competitorCount = competitorsList.length; }" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:text-sm">
                                        + Tambah Kompetitor
                                    </button>
                                    <button type="button" @click="showCompetitorModal = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mt-2 overflow-x-auto">
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
                                                    <button type="button" @click="let name = prompt('Edit nama:', item.nama); if(name) item.nama = name;" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-3 py-1 rounded border border-yellow-200">Edit</button>
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
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" @click="showCompetitorModal = false">
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
                            
                            <div class="mt-2 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">NO</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NAMA RPH</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">JARAK (KM)</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="(item, index) in rphList" :key="item.id">
                                            <tr :class="{'bg-green-50': index === 0}">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    <span x-text="index + 1"></span>
                                                    <span x-show="index === 0" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Terdekat</span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                                    <span x-text="item.nama"></span>
                                                    <div x-show="item.alamat" class="text-xs text-gray-500 mt-1" x-text="item.alamat"></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-bold text-green-600"><span x-text="item.distance"></span> KM</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center space-x-2">
                                                    <button type="button" @click="rphName = item.nama; jarakRphDisplay = item.distance; document.getElementById('jarak_rph_display').value = item.distance;" class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded border border-green-200">Gunakan Ini</button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="rphList.length === 0">
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada RPH di sekitar.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" @click="showRphModal = false">
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
"""

content = content.replace("</form>", modals + "\n        </form>")

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Updated create.blade.php")
