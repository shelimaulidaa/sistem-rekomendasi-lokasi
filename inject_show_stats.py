import re

with open('resources/views/manajer/observasi/show.blade.php', 'r') as f:
    content = f.read()

stats_ui = """                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($observasi->umk || $observasi->pdrb || $observasi->jumlah_penduduk_muslim)
                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-base-dark mb-3">Statistik Wilayah</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 flex flex-col">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">UMK Kabupaten / Kota</span>
                                    <span class="text-lg font-bold text-gray-800">{{ $observasi->umk ? 'Rp ' . number_format($observasi->umk, 2, ',', '.') : '-' }}</span>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 flex flex-col">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">PDRB Per Kapita</span>
                                    <span class="text-lg font-bold text-gray-800">{{ $observasi->pdrb ? 'Rp ' . number_format($observasi->pdrb, 0, ',', '.') : '-' }}</span>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 flex flex-col">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jumlah Penduduk Muslim</span>
                                    <span class="text-lg font-bold text-gray-800">{{ $observasi->jumlah_penduduk_muslim ? number_format($observasi->jumlah_penduduk_muslim, 0, ',', '.') . ' Jiwa' : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        @endif"""

content = content.replace("                                    </div>\n                                </div>\n                            </div>\n                        </div>", stats_ui)

with open('resources/views/manajer/observasi/show.blade.php', 'w') as f:
    f.write(content)

with open('resources/views/direktur/observasi/show.blade.php', 'r') as f:
    dir_content = f.read()

dir_content = dir_content.replace("                                    </div>\n                                </div>\n                            </div>\n                        </div>", stats_ui)

with open('resources/views/direktur/observasi/show.blade.php', 'w') as f:
    f.write(dir_content)

print("Updated show views.")
