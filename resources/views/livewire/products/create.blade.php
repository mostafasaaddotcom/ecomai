<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Create Product') }}</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Add a new product to your catalog') }}</p>
        </div>
        <flux:button :href="route('products.index')" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Back to Products') }}
        </flux:button>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <form wire:submit="save" class="space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Product Name')"
                type="text"
                required
                :placeholder="__('Enter product name')"
            />

            <div>
                <flux:label>{{ __('Product Type') }}</flux:label>
                <select
                    wire:model="type"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                >
                    <option value="physical">{{ __('Physical') }}</option>
                    <option value="digital">{{ __('Digital') }}</option>
                </select>
            </div>

            <div>
                <flux:label>{{ __('Store (Optional)') }}</flux:label>
                <select
                    wire:model="user_store_id"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                >
                    <option value="">{{ __('No store selected') }}</option>
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}">
                            {{ $store->name }} ({{ ucfirst($store->platform) }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Select a store to link with this product') }}
                </p>
            </div>

            <flux:input
                wire:model="price"
                :label="__('Price (Optional)')"
                type="number"
                step="0.01"
                min="0"
                :placeholder="__('0.00')"
            />

            <div>
                <flux:label>{{ __('User Description') }}</flux:label>
                <textarea
                    wire:model="description_user"
                    rows="4"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="{{ __('Enter product description from user perspective') }}"
                ></textarea>
            </div>

            <div>
                <flux:label>{{ __('AI Description') }}</flux:label>
                <textarea
                    wire:model="description_ai"
                    rows="4"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="{{ __('Enter AI-generated description') }}"
                ></textarea>
            </div>

            <div x-data="imageUploader()">
                <flux:label>{{ __('Product Image') }}</flux:label>

                <!-- Hidden input for Livewire to store the image path -->
                <input type="hidden" wire:model="main_image">

                <!-- File input (hidden when image is uploaded) -->
                <div x-show="!imageUrl && !uploading">
                    <input
                        type="file"
                        @change="uploadImage($event)"
                        accept="image/*"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    />
                </div>

                <!-- Upload progress -->
                <div x-show="uploading" class="mt-2">
                    <div class="flex items-center gap-2">
                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full bg-blue-500 transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400" x-text="`${uploadProgress}%`"></span>
                    </div>
                </div>

                <!-- Image preview with remove button -->
                <div x-show="imageUrl" class="mt-2">
                    <div class="relative inline-block">
                        <img :src="imageUrl" alt="Preview" class="h-32 w-32 rounded object-cover">
                        <button
                            type="button"
                            @click="removeImage()"
                            class="absolute -right-2 -top-2 rounded-full bg-red-500 p-1 text-white shadow-lg hover:bg-red-600"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Error message -->
                <div x-show="error" class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="error"></div>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Maximum file size: 2MB. Supported formats: JPG, PNG, GIF') }}</p>
            </div>

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">
                    {{ __('Create Product') }}
                </flux:button>
                <flux:button :href="route('products.index')" wire:navigate variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>

<script>
    function imageUploader() {
        return {
            imagePath: '',
            imageUrl: '',
            uploading: false,
            uploadProgress: 0,
            error: '',

            async uploadImage(event) {
                const file = event.target.files[0];
                if (!file) return;

                // Validate file size (2MB)
                if (file.size > 2048 * 1024) {
                    this.error = 'File size must not exceed 2MB';
                    event.target.value = '';
                    return;
                }

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    this.error = 'Please select a valid image file';
                    event.target.value = '';
                    return;
                }

                // Check if CSRF token exists
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    this.error = 'CSRF token not found. Please refresh the page.';
                    console.error('CSRF token meta tag is missing from the page');
                    event.target.value = '';
                    return;
                }

                this.error = '';
                this.uploading = true;
                this.uploadProgress = 0;

                const formData = new FormData();
                formData.append('main_image', file);

                try {
                    const xhr = new XMLHttpRequest();

                    // Track upload progress
                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                        }
                    });

                    // Handle response
                    xhr.addEventListener('load', () => {
                        this.uploading = false;

                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    this.imagePath = response.path;
                                    this.imageUrl = response.url;
                                    this.error = '';

                                    // Sync with Livewire
                                    @this.set('main_image', response.path);
                                } else {
                                    this.error = response.message || 'Upload failed';
                                }
                            } catch (e) {
                                this.error = 'Failed to parse server response';
                                console.error('Parse error:', e, xhr.responseText);
                            }
                        } else {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                // Handle validation errors
                                if (response.errors) {
                                    const errorMessages = Object.values(response.errors).flat();
                                    this.error = errorMessages.join(', ');
                                } else {
                                    this.error = response.message || `Upload failed (${xhr.status})`;
                                }
                            } catch (e) {
                                this.error = `Upload failed (${xhr.status})`;
                                console.error('Error response:', xhr.responseText);
                            }
                        }
                    });

                    // Handle errors
                    xhr.addEventListener('error', () => {
                        this.uploading = false;
                        this.error = 'Network error occurred. Please check your connection.';
                    });

                    // Send request
                    xhr.open('POST', '{{ asset('upload-main-image') }}');
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.content);
                    xhr.send(formData);

                } catch (error) {
                    this.uploading = false;
                    this.error = 'An error occurred: ' + error.message;
                    console.error('Upload error:', error);
                }

                // Reset file input
                event.target.value = '';
            },

            removeImage() {
                this.imagePath = '';
                this.imageUrl = '';
                this.error = '';

                // Sync with Livewire
                @this.set('main_image', '');
            }
        };
    }
</script>
