<?php

namespace App\Livewire\Products;

use App\Models\AdCreative;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AdCreatives extends Component
{
    public Product $product;

    public bool $isProcessing = false;

    // URL inputs
    public string $videoUrl = '';

    public string $fileUrl = '';

    // Form fields for mix video and audio
    public array $selectedVoiceIds = [];

    public string $title = '';

    public string $message = '';

    public ?string $callToActionType = null;

    public ?string $callToActionLink = null;

    public ?string $pageId = null;

    public ?string $instagramUserId = null;

    // Available CTA types
    public array $ctaTypes = [
        'SHOP_NOW' => 'Shop Now',
        'LEARN_MORE' => 'Learn More',
        'SIGN_UP' => 'Sign Up',
        'BUY_NOW' => 'Buy Now',
        'CONTACT_US' => 'Contact Us',
        'DOWNLOAD' => 'Download',
        'BOOK_TRAVEL' => 'Book Travel',
        'APPLY_NOW' => 'Apply Now',
        'SUBSCRIBE' => 'Subscribe',
        'GET_QUOTE' => 'Get Quote',
        'WATCH_MORE' => 'Watch More',
        'SEE_MENU' => 'See Menu',
        'CALL_NOW' => 'Call Now',
        'MESSAGE_PAGE' => 'Message Page',
        'SEND_MESSAGE' => 'Send Message',
        'WHATSAPP_MESSAGE' => 'WhatsApp Message',
        'GET_OFFER' => 'Get Offer',
        'GET_SHOWTIMES' => 'Get Showtimes',
    ];

    /**
     * Mount the component.
     */
    public function mount(Product $product): void
    {
        // Ensure the product belongs to the authenticated user
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        $this->product = $product->load(['adCreatives', 'copies']);
    }

    /**
     * Refresh ad creatives after upload.
     */
    public function refreshAdCreatives(): void
    {
        $this->product->load('adCreatives');
    }

    /**
     * Create ad creative and send to N8N webhook.
     */
    public function createAndSend(): void
    {
        // Validate
        $this->validate([
            'videoUrl' => ['required', 'url'],
            'selectedVoiceIds' => ['required', 'array', 'min:1'],
        ]);

        $this->isProcessing = true;

        try {
            // Get all selected voices
            $selectedVoices = $this->product->copies->whereIn('id', $this->selectedVoiceIds);

            if ($selectedVoices->isEmpty()) {
                session()->flash('error', 'Selected voices not found.');
                $this->isProcessing = false;

                return;
            }

            // Create one ad creative per voice
            $adCreatives = [];
            foreach ($selectedVoices as $voice) {
                $adCreative = AdCreative::create([
                    'user_id' => Auth::id(),
                    'product_id' => $this->product->id,
                    'type' => 'video',
                    'original_video_url' => $this->videoUrl,
                    'processed_video_url' => null, // Will be set by N8N after processing
                    'video_id' => null, // Will be set by N8N (Facebook video ID)
                    'title' => $this->title ?: null,
                    'message' => $this->message ?: null,
                    'call_to_action_type' => $this->callToActionType,
                    'call_to_action_link' => $this->callToActionLink ?: null,
                    'page_id' => $this->pageId ?: null,
                    'instagram_user_id' => $this->instagramUserId ?: null,
                ]);

                $adCreatives[] = [
                    'ad_creative_id' => $adCreative->id,
                    'voice' => [
                        'voice_url' => $voice->voice_url_link,
                        'voice_copy_id' => $voice->id,
                        'voice_type' => $voice->type,
                        'angle' => $voice->angle,
                        'content' => $voice->content,
                    ],
                ];
            }

            // Send webhook to N8N with all ad creatives
            $response = Http::timeout(30)->post(config('app.n8n_base_url').'mix-video-audio', [
                'video_url' => $this->videoUrl,
                'ad_creatives' => $adCreatives,
                'user_id' => Auth::id(),
                'product_id' => $this->product->id,
                'product_data' => [
                    'name' => $this->product->name,
                    'description_user' => $this->product->description_user,
                    'description_ai' => $this->product->description_ai,
                    'type' => $this->product->type,
                    'price' => $this->product->price,
                    'main_image_url' => $this->product->main_image_url,
                    'store_link_url' => $this->product->store_link_url,
                ],
                'metadata' => [
                    'title' => $this->title,
                    'message' => $this->message,
                    'call_to_action_type' => $this->callToActionType,
                    'call_to_action_link' => $this->callToActionLink,
                    'page_id' => $this->pageId,
                    'instagram_user_id' => $this->instagramUserId,
                ],
                'app_url' => config('app.url'),
            ]);

            if ($response->successful()) {
                $count = count($adCreatives);
                session()->flash('message', "{$count} ad creative(s) created and sent to N8N successfully. Please refresh in a moment to see updates.");
                // Refresh ad creatives
                $this->product->load('adCreatives');

                // Reset form
                $this->reset(['videoUrl', 'selectedVoiceIds', 'title', 'message', 'callToActionType', 'callToActionLink', 'pageId', 'instagramUserId']);
            } else {
                Log::error('Webhook failed for ad creative creation', [
                    'ad_creative_ids' => array_column($adCreatives, 'ad_creative_id'),
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                session()->flash('error', 'Failed to send to N8N. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error creating ad creative', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while creating ad creative. Please try again.');
        } finally {
            $this->isProcessing = false;
        }
    }

    /**
     * Upload ad creative (for upload tab).
     */
    public function uploadAdCreative(): void
    {
        // Validate
        $this->validate([
            'fileUrl' => ['required', 'url'],
            'title' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'callToActionType' => ['nullable', 'string'],
            'callToActionLink' => ['nullable', 'string'],
            'pageId' => ['nullable', 'string'],
            'instagramUserId' => ['nullable', 'string'],
        ]);

        $this->isProcessing = true;

        try {
            // Determine file type from URL extension
            $extension = strtolower(pathinfo($this->fileUrl, PATHINFO_EXTENSION));
            $videoExtensions = ['mp4', 'mov', 'avi', 'wmv', 'flv', 'mkv', 'webm'];
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            $isVideo = in_array($extension, $videoExtensions);
            $isImage = in_array($extension, $imageExtensions);

            if (!$isVideo && !$isImage) {
                session()->flash('error', 'Invalid file type. URL must point to a video or image.');
                $this->isProcessing = false;

                return;
            }

            // Create ad creative record
            $adCreative = AdCreative::create([
                'user_id' => Auth::id(),
                'product_id' => $this->product->id,
                'type' => $isVideo ? 'video' : 'image',
                'original_video_url' => $isVideo ? $this->fileUrl : null,
                'processed_video_url' => null,
                'video_id' => null,
                'thumbnail_url' => $isImage ? $this->fileUrl : null,
                'title' => $this->title ?: null,
                'message' => $this->message ?: null,
                'call_to_action_type' => $this->callToActionType,
                'call_to_action_link' => $this->callToActionLink ?: null,
                'page_id' => $this->pageId ?: null,
                'instagram_user_id' => $this->instagramUserId ?: null,
            ]);

            // Refresh ad creatives
            $this->product->load('adCreatives');

            // Reset form
            $this->reset(['fileUrl', 'title', 'message', 'callToActionType', 'callToActionLink', 'pageId', 'instagramUserId']);

            session()->flash('message', 'Ad creative created successfully.');
        } catch (\Exception $e) {
            Log::error('Error uploading ad creative', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while uploading ad creative. Please try again.');
        } finally {
            $this->isProcessing = false;
        }
    }

    /**
     * Delete an ad creative.
     */
    public function deleteAdCreative(int $adCreativeId): void
    {
        $adCreative = AdCreative::findOrFail($adCreativeId);

        // Ensure the ad creative belongs to the authenticated user
        if ($adCreative->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete the ad creative record
        $adCreative->delete();

        // Reload ad creatives
        $this->product->load('adCreatives');

        session()->flash('message', 'Ad creative deleted successfully.');
    }

    public function render()
    {
        return view('livewire.products.ad-creatives');
    }
}
