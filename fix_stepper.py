import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# We want to replace the Desktop Stepper section
desktop_stepper_pattern = re.compile(r'<!-- Desktop Stepper -->.*?<!-- STEP 1 -->', re.DOTALL)

new_stepper = """<!-- Desktop Stepper -->
                <div class="hidden sm:block px-12 md:px-24">
                    <nav aria-label="Progress">
                        <ol role="list" class="flex items-center justify-between relative">
                            <!-- Line -->
                            <div class="absolute top-1/2 left-0 w-full -translate-y-1/2" aria-hidden="true">
                                <div class="h-1 w-full bg-gray-200">
                                    <div class="h-1 transition-all duration-300 bg-primary" :style="`width: ${progress}%`"></div>
                                </div>
                            </div>
                            
                            <!-- Step 1 -->
                            <li class="relative z-10 flex flex-col items-center">
                                <button type="button" @click="goToStep(1)" class="relative flex h-10 w-10 items-center justify-center rounded-full transition-all duration-300 shadow-sm"
                                    :class="step > 1 ? 'bg-primary hover:bg-primary-dark' : (step === 1 ? 'border-4 border-primary bg-white scale-110' : 'border-4 border-gray-300 bg-white hover:border-gray-400')">
                                    <template x-if="step > 1">
                                        <svg class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                    </template>
                                    <template x-if="step === 1">
                                        <span class="h-3 w-3 rounded-full bg-primary"></span>
                                    </template>
                                </button>
                                <span class="absolute -bottom-8 text-xs font-bold w-max text-center" :class="step >= 1 ? 'text-primary' : 'text-gray-500'">Informasi Lokasi</span>
                            </li>
                            
                            <!-- Step 2 -->
                            <li class="relative z-10 flex flex-col items-center">
                                <button type="button" @click="goToStep(2)" class="relative flex h-10 w-10 items-center justify-center rounded-full transition-all duration-300 shadow-sm"
                                    :class="step === 2 ? 'border-4 border-primary bg-white scale-110' : 'border-4 border-gray-300 bg-white hover:border-gray-400'">
                                    <template x-if="step === 2">
                                        <span class="h-3 w-3 rounded-full bg-primary"></span>
                                    </template>
                                    <template x-if="step < 2">
                                        <span class="h-3 w-3 rounded-full bg-transparent group-hover:bg-gray-300"></span>
                                    </template>
                                </button>
                                <span class="absolute -bottom-8 text-xs font-bold w-max text-center" :class="step >= 2 ? 'text-primary' : 'text-gray-500'">Kondisi Bangunan & Dokumentasi</span>
                            </li>

                        </ol>
                    </nav>
                </div>

                <!-- STEP 1 -->"""

content = desktop_stepper_pattern.sub(new_stepper, content)

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Desktop stepper updated.")
