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

    // Multi-selection for test campaign
    public array $selectedAdCreativeIds = [];

    // Test campaign modal fields
    public ?int $selectedMetaProfileId = null;

    public ?string $selectedCampaignId = null;

    public ?string $selectedAdSetId = null;

    public string $testCampaignName = '';

    public array $metaProfiles = [];

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

        // Load user's meta profiles for test campaign modal
        $this->metaProfiles = Auth::user()
            ->metaProfiles()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
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

    /**
     * Get available campaigns filtered by selected meta profile.
     */
    public function getAvailableCampaignsProperty(): array
    {
        if (!$this->selectedMetaProfileId) {
            return [];
        }

        $profile = collect($this->metaProfiles)->firstWhere('id', $this->selectedMetaProfileId);

        return $profile['campaigns_available_for_duplicate'] ?? [];
    }

    /**
     * Get available ad sets filtered by selected meta profile.
     */
    public function getAvailableAdSetsProperty(): array
    {
        if (!$this->selectedMetaProfileId) {
            return [];
        }

        $profile = collect($this->metaProfiles)->firstWhere('id', $this->selectedMetaProfileId);

        return $profile['ad_sets_available_for_duplicate'] ?? [];
    }

    /**
     * Reset campaign and ad set when meta profile changes.
     */
    public function updatedSelectedMetaProfileId(): void
    {
        $this->selectedCampaignId = null;
        $this->selectedAdSetId = null;
    }

    /**
     * Create test campaign and send to N8N.
     */
    public function createTestCampaign(): void
    {
        // Validate
        $this->validate([
            'selectedAdCreativeIds' => ['required', 'array', 'min:1'],
            'selectedMetaProfileId' => ['required', 'integer'],
            'selectedCampaignId' => ['required', 'string'],
            'selectedAdSetId' => ['required', 'string'],
            'testCampaignName' => ['required', 'string', 'max:255'],
        ]);

        try {
            // Get selected meta profile
            $metaProfile = collect($this->metaProfiles)->firstWhere('id', $this->selectedMetaProfileId);

            if (!$metaProfile) {
                session()->flash('error', 'Meta profile not found.');

                return;
            }

            // Get selected campaign and ad set
            $campaign = collect($metaProfile['campaigns_available_for_duplicate'] ?? [])->firstWhere('id', $this->selectedCampaignId);
            $adSet = collect($metaProfile['ad_sets_available_for_duplicate'] ?? [])->firstWhere('id', $this->selectedAdSetId);

            if (!$campaign || !$adSet) {
                session()->flash('error', 'Campaign or Ad Set not found.');

                return;
            }

            // Get selected ad creatives
            $selectedAdCreatives = AdCreative::whereIn('id', $this->selectedAdCreativeIds)
                ->where('user_id', Auth::id())
                ->get();

            if ($selectedAdCreatives->isEmpty()) {
                session()->flash('error', 'No valid ad creatives selected.');

                return;
            }

            // Prepare ad creatives data
            $adCreativesData = $selectedAdCreatives->map(function ($adCreative) {
                return [
                    'id' => $adCreative->id,
                    'type' => $adCreative->type,
                    'video_url' => $adCreative->processed_video_url ?? $adCreative->original_video_url,
                    'thumbnail_url' => $adCreative->thumbnail_url,
                    'title' => $adCreative->title,
                    'message' => $adCreative->message,
                    'call_to_action_type' => $adCreative->call_to_action_type,
                    'call_to_action_link' => $adCreative->call_to_action_link,
                    'creative_id' => $adCreative->creative_id,
                ];
            })->toArray();

            // Send to N8N webhook
            $response = Http::timeout(60)->post(config('app.n8n_base_url').'create-test-campaign', [
                'meta_profile' => [
                    'id' => $metaProfile['id'],
                    'name' => $metaProfile['name'],
                    'ad_account_id' => $metaProfile['ad_account_id'],
                    'facebook_page_id' => $metaProfile['facebook_page_id'],
                    'instagram_profile_id' => $metaProfile['instagram_profile_id'],
                    'facebook_pixel' => $metaProfile['facebook_pixel'],
                    'access_token' => $metaProfile['access_token'],
                ],
                'campaign_to_duplicate' => [
                    'id' => $campaign['id'],
                    'name' => $campaign['name'],
                ],
                'ad_set_to_duplicate' => [
                    'id' => $adSet['id'],
                    'name' => $adSet['name'],
                ],
                'new_campaign_name' => $this->testCampaignName,
                'ad_creatives' => $adCreativesData,
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
                'user_id' => Auth::id(),
                'app_url' => config('app.url'),
            ]);

            if ($response->successful()) {
                session()->flash('message', 'Test campaign sent to N8N successfully. It will be created shortly.');

                // Reset form and selections
                $this->reset(['selectedAdCreativeIds', 'selectedMetaProfileId', 'selectedCampaignId', 'selectedAdSetId', 'testCampaignName']);
            } else {
                Log::error('Failed to create test campaign via N8N', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'product_id' => $this->product->id,
                ]);
                session()->flash('error', 'Failed to send test campaign to N8N. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error creating test campaign', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'An error occurred while creating test campaign. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.products.ad-creatives');
    }
}
