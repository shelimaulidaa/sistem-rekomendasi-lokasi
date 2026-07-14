import re

with open('resources/views/manajer/history/index.blade.php', 'r') as f:
    content = f.read()

# Replace title
content = content.replace("Hasil Keputusan TOPSIS", "Riwayat Penilaian")

# Replace route export
content = content.replace("route('manajer.hasil.export.pdf')", "route('manajer.history.export.pdf', ['batch_id' => $activeBatchId])")

# Inject filter after header, before executive summary cards
filter_ui = """    <!-- Batch Filter -->
    <div class="mb-6 flex flex-col sm:flex-row justify-end items-center">
        <form action="{{ route('manajer.history.index') }}" method="GET" class="w-full sm:w-auto flex items-center gap-2">
            <label for="batch_id" class="text-sm font-medium text-gray-700 whitespace-nowrap">Pilih Batch:</label>
            <select name="batch_id" onchange="this.form.submit()" class="block w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 h-[42px] bg-white">
                <option value="">-- Semua Batch --</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch->id }}" {{ $activeBatchId == $batch->id ? 'selected' : '' }}>
                        {{ $batch->nama_batch }} {{ $batch->is_active ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Executive Summary Cards -->"""

content = content.replace("    <!-- Executive Summary Cards -->", filter_ui)

with open('resources/views/manajer/history/index.blade.php', 'w') as f:
    f.write(content)

print("Updated history index.")
