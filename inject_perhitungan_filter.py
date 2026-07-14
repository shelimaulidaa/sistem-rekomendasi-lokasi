import re

with open('resources/views/manajer/perhitungan/index.blade.php', 'r') as f:
    content = f.read()

filter_ui = """            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto mt-4 sm:mt-0">
                <!-- Batch Filter -->
                <form action="{{ route('manajer.perhitungan.index') }}" method="GET" class="w-full sm:w-auto flex items-center gap-2">
                    <select name="batch_id" onchange="this.form.submit()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 h-[42px] bg-white">
                        <option value="">-- Pilih Batch --</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ $activeBatchId == $batch->id ? 'selected' : '' }}>
                                {{ $batch->nama_batch }} {{ $batch->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>"""

content = content.replace("            </div>\n", filter_ui, 1)

with open('resources/views/manajer/perhitungan/index.blade.php', 'w') as f:
    f.write(content)

print("Injected Perhitungan filter successfully.")
