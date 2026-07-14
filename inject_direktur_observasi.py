import re

with open('resources/views/direktur/observasi/index.blade.php', 'r') as f:
    content = f.read()

filter_ui = """            <form action="{{ route('direktur.observasi.index') }}" method="GET" class="w-full sm:w-auto flex flex-col sm:flex-row gap-3">
                <select name="batch_id" onchange="this.form.submit()" class="block w-full sm:w-48 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 h-[44px] bg-white">
                    <option value="">-- Semua Batch --</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" {{ $batchId == $batch->id ? 'selected' : '' }}>
                            {{ $batch->nama_batch }} {{ $batch->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" 
                        class="block w-full sm:w-64 pl-10 pr-3 py-2 min-h-[44px] border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition duration-150 ease-in-out" 
                        placeholder="Cari nama lokasi...">
                </div>
            </form>"""

content = re.sub(r"            <form action=\"\{\{ route\('direktur\.observasi\.index'\) \}\}\" method=\"GET\" class=\"w-full sm:w-auto\">.*?</form>", filter_ui, content, flags=re.DOTALL)

with open('resources/views/direktur/observasi/index.blade.php', 'w') as f:
    f.write(content)

print("Updated direktur observasi.")
