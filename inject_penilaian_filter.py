import re

with open('resources/views/manajer/penilaian/index.blade.php', 'r') as f:
    content = f.read()

filter_ui = """                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto items-center">
                    <!-- Batch Filter -->
                    <form action="{{ route('manajer.penilaian.index') }}" method="GET" class="w-full sm:w-auto flex items-center gap-2">
                        <select name="batch_id" onchange="this.form.submit()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 h-[42px] bg-white">
                            <option value="">-- Pilih Batch --</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ $activeBatchId == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->nama_batch }} {{ $batch->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <form action="{{ route('manajer.perhitungan.calculate') }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <input type="hidden" name="batch_id" value="{{ $activeBatchId }}">
                        <button type="submit\""""

content = content.replace('                </div>\n                \n                <form action="{{ route(\'manajer.perhitungan.calculate\') }}" method="POST" class="w-full sm:w-auto">\n                    @csrf\n                    <button type="submit"', filter_ui)

with open('resources/views/manajer/penilaian/index.blade.php', 'w') as f:
    f.write(content)

print("Injected Penilaian filter successfully.")
