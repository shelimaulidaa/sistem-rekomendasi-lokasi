import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# 1. Add Alpine variables
content = content.replace("alamatLengkap: '{{ old('alamat_lengkap') }}',", "alamatLengkap: '{{ old('alamat_lengkap') }}',\n                                umk_kota: '',\n                                pdrb_kota: '',\n                                penduduk_muslim_kota: '',")

# 2. Add loadJabarStats function after updateDistName
load_stats_fn = """                                updateDistName() {
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
                                },"""
content = content.replace("                                updateDistName() {\n                                    const dist = this.districts.find(d => d.id == this.selectedDistId);\n                                    if (dist) this.distName = dist.name;\n                                },", load_stats_fn)

# 3. Call loadJabarStats after setting regName in loadDistricts
content = content.replace("if (reg) this.regName = reg.name;", "if (reg) { this.regName = reg.name; this.loadJabarStats(); }")

# 4. Call loadJabarStats in auto select
content = content.replace("this.regName = kabMatch.name;", "this.regName = kabMatch.name;\n                                                    this.loadJabarStats();")

# 5. Add the UI cards before the closing of the section (just after the grids)
ui_cards = """                                </div>
                                
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
                                </div>"""

content = content.replace("                                </div>\n                                \n                                <div class=\"col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4\">", ui_cards + "\n                                \n                                <div class=\"col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4\">")


with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Updated create.blade.php for jabar stats.")
