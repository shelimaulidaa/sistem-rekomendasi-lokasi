import re

with open('resources/views/manajer/observasi/index.blade.php', 'r') as f:
    content = f.read()

filter_ui = """            <!-- Search & Filter -->
            <div class="mb-6 flex flex-col sm:flex-row justify-end gap-3">
                <form method="GET" action="{{ route('manajer.observasi.index') }}" class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
                    <select name="batch_id" onchange="this.form.submit()" class="block w-full sm:w-48 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 h-[44px] bg-white">
                        <option value="">-- Semua Batch --</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ $batchId == $batch->id ? 'selected' : '' }}>
                                {{ $batch->nama_batch }} {{ $batch->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    
                    <div class="flex w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama lokasi..." class="w-full pl-10 pr-3 py-2 min-h-[44px] border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-colors">
                        </div>
                        <button type="submit" class="px-4 py-2 min-h-[44px] bg-gray-50 border border-l-0 border-gray-300 text-base-dark rounded-r-md hover:bg-gray-100 transition-colors text-sm font-medium">
                            Cari
                        </button>
                    </div>
                </form>
            </div>"""

search_pattern = r"            <!-- Search Bar -->\n            <div class=\"mb-6 flex justify-end\">\n                <form method=\"GET\" action=\"\{\{ route\('manajer\.observasi\.index'\) \}\}\" class=\"flex w-full sm:w-1/2 lg:w-1/3\">\n                    <div class=\"relative w-full\">\n                        <div class=\"absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none\">\n                            <svg class=\"h-5 w-5 text-gray-400\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z\"></path></svg>\n                        </div>\n                        <input type=\"text\" name=\"search\" value=\"\{\{ \$search \}\}\" placeholder=\"Cari nama lokasi\.\.\.\" class=\"w-full pl-10 pr-3 py-2 min-h-\[44px\] border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-colors\">\n                    </div>\n                    <button type=\"submit\" class=\"px-4 py-2 min-h-\[44px\] bg-gray-50 border border-l-0 border-gray-300 text-base-dark rounded-r-md hover:bg-gray-100 transition-colors text-sm font-medium\">\n                        Cari\n                    </button>\n                </form>\n            </div>"

content = content.replace("            <!-- Search Bar -->\n            <div class=\"mb-6 flex justify-end\">\n                <form method=\"GET\" action=\"{{ route('manajer.observasi.index') }}\" class=\"flex w-full sm:w-1/2 lg:w-1/3\">\n                    <div class=\"relative w-full\">\n                        <div class=\"absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none\">\n                            <svg class=\"h-5 w-5 text-gray-400\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z\"></path></svg>\n                        </div>\n                        <input type=\"text\" name=\"search\" value=\"{{ $search }}\" placeholder=\"Cari nama lokasi...\" class=\"w-full pl-10 pr-3 py-2 min-h-[44px] border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-colors\">\n                    </div>\n                    <button type=\"submit\" class=\"px-4 py-2 min-h-[44px] bg-gray-50 border border-l-0 border-gray-300 text-base-dark rounded-r-md hover:bg-gray-100 transition-colors text-sm font-medium\">\n                        Cari\n                    </button>\n                </form>\n            </div>", filter_ui)

with open('resources/views/manajer/observasi/index.blade.php', 'w') as f:
    f.write(content)

print("Updated observasi index.")
