import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# Define the Alpine component for batchManager
batch_script = """    <!-- Alpine Batch Manager Logic -->
    <script>
        function batchManager() {
            return {
                batches: @json($batches),
                selectedBatch: '{{ old("batch_id", "") }}',
                showModal: false,
                isEditing: false,
                editId: null,
                batchName: '',
                isSubmitting: false,
                
                init() {
                    if(!this.selectedBatch && this.batches.length > 0) {
                        // Select the active batch, or the first one
                        let active = this.batches.find(b => b.is_active);
                        this.selectedBatch = active ? active.id : this.batches[0].id;
                    }
                },
                
                openAddModal() {
                    this.isEditing = false;
                    this.editId = null;
                    this.batchName = '';
                    this.showModal = true;
                },
                
                openEditModal() {
                    let batch = this.batches.find(b => b.id == this.selectedBatch);
                    if(!batch) return;
                    this.isEditing = true;
                    this.editId = batch.id;
                    this.batchName = batch.nama_batch;
                    this.showModal = true;
                },
                
                async saveBatch() {
                    if(!this.batchName.trim()) { alert('Nama batch tidak boleh kosong'); return; }
                    this.isSubmitting = true;
                    
                    try {
                        let url = this.isEditing ? `/manajer/batches/${this.editId}` : '/manajer/batches';
                        let method = this.isEditing ? 'PUT' : 'POST';
                        
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ nama_batch: this.batchName })
                        });
                        
                        const data = await response.json();
                        
                        if(response.ok) {
                            if(this.isEditing) {
                                let idx = this.batches.findIndex(b => b.id == this.editId);
                                if(idx !== -1) Object.assign(this.batches[idx], data.batch);
                            } else {
                                this.batches.unshift(data.batch);
                                this.selectedBatch = data.batch.id;
                            }
                            this.showModal = false;
                        } else {
                            alert(data.message || data.errors?.nama_batch?.[0] || 'Terjadi kesalahan');
                        }
                    } catch (err) {
                        alert('Gagal menghubungi server');
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                
                async deleteBatch() {
                    if(!this.selectedBatch) return;
                    if(!confirm('Apakah Anda yakin ingin menghapus Batch ini? (Hanya bisa dihapus jika belum ada lokasi yang terikat)')) return;
                    
                    try {
                        const response = await fetch(`/manajer/batches/${this.selectedBatch}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        
                        if(response.ok) {
                            this.batches = this.batches.filter(b => b.id != this.selectedBatch);
                            this.selectedBatch = this.batches.length > 0 ? this.batches[0].id : '';
                            alert(data.message);
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    } catch (err) {
                        alert('Gagal menghubungi server');
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine Image Uploader Logic -->"""

# Inject the script
content = content.replace("    <!-- Alpine Image Uploader Logic -->", batch_script)

# Define the UI section
batch_ui = """                        <!-- Section 0: Pemilihan Batch -->
                        <div x-data="batchManager()" x-init="init()" class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    <h3 class="text-lg font-bold text-base-dark">Periode / Batch Observasi</h3>
                                </div>
                            </div>
                            <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-end gap-4">
                                <div class="flex-grow">
                                    <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Batch <span class="text-red-500">*</span></label>
                                    <select id="batch_id" name="batch_id" x-model="selectedBatch" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm py-2 px-3 h-[42px]">
                                        <option value="" disabled>-- Pilih Batch --</option>
                                        <template x-for="b in batches" :key="b.id">
                                            <option :value="b.id" x-text="b.nama_batch"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 h-[42px]">
                                    <button type="button" @click="openAddModal" class="px-3 py-2 bg-primary text-white text-sm font-medium rounded-md shadow-sm hover:bg-green-700 transition flex items-center h-full">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah
                                    </button>
                                    <button type="button" @click="openEditModal" x-show="selectedBatch" class="px-3 py-2 bg-orange-500 text-white text-sm font-medium rounded-md shadow-sm hover:bg-orange-600 transition flex items-center h-full">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Edit
                                    </button>
                                    <button type="button" @click="deleteBatch" x-show="selectedBatch" class="px-3 py-2 bg-red-500 text-white text-sm font-medium rounded-md shadow-sm hover:bg-red-600 transition flex items-center h-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                
                                <!-- Modal Add/Edit -->
                                <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false" aria-hidden="true"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" x-text="isEditing ? 'Edit Batch' : 'Tambah Batch Baru'"></h3>
                                                        <div class="mt-4">
                                                            <label class="block text-sm font-medium text-gray-700">Nama Batch</label>
                                                            <input type="text" x-model="batchName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Misal: Cabang Antapani 2026">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="button" @click="saveBatch" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                                    <span x-show="!isSubmitting">Simpan</span>
                                                    <span x-show="isSubmitting">Menyimpan...</span>
                                                </button>
                                                <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 1: Koordinat Peta Observasi Aktual -->"""

# Inject UI
content = content.replace("                        <!-- Section 1: Koordinat Peta Observasi Aktual -->", batch_ui)

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Injected Batch UI successfully.")
