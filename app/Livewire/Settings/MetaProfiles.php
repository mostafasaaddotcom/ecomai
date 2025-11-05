<?php

namespace App\Livewire\Settings;

use App\Models\UserMetaProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MetaProfiles extends Component
{
    public string $name = '';
    public string $ad_account_id = '';
    public string $facebook_page_id = '';
    public string $instagram_profile_id = '';
    public string $access_token = '';
    public string $facebook_pixel = '';

    public array $userMetaProfiles = [];

    // Properties for adding campaigns and ad sets
    public string $campaign_id = '';
    public string $campaign_name = '';
    public string $ad_set_id = '';
    public string $ad_set_name = '';

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->userMetaProfiles = Auth::user()
            ->metaProfiles()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'ad_account_id' => ['required', 'string', 'max:255'],
            'facebook_page_id' => ['required', 'string', 'max:255'],
            'instagram_profile_id' => ['nullable', 'string', 'max:255'],
            'access_token' => ['required', 'string', 'max:500'],
            'facebook_pixel' => ['required', 'string', 'max:255'],
        ]);

        // Check if ad_account_id already exists for this user
        $existing = Auth::user()
            ->metaProfiles()
            ->where('ad_account_id', $validated['ad_account_id'])
            ->first();

        if ($existing) {
            session()->flash('error', 'A profile with this Ad Account ID already exists.');
            return;
        }

        // Set as default if this is the first profile
        $isDefault = Auth::user()->metaProfiles()->count() === 0;

        UserMetaProfile::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'ad_account_id' => $validated['ad_account_id'],
            'facebook_page_id' => $validated['facebook_page_id'],
            'instagram_profile_id' => $validated['instagram_profile_id'],
            'access_token' => $validated['access_token'],
            'facebook_pixel' => $validated['facebook_pixel'],
            'is_default' => $isDefault,
            'campaigns_available_for_duplicate' => [],
            'ad_sets_available_for_duplicate' => [],
        ]);

        // Reset form
        $this->reset(['name', 'ad_account_id', 'facebook_page_id', 'instagram_profile_id', 'access_token', 'facebook_pixel']);

        $this->loadData();
        session()->flash('message', 'Meta profile added successfully.');
    }

    public function setDefaultProfile(int $profileId): void
    {
        // Unset all defaults first
        Auth::user()->metaProfiles()->update(['is_default' => false]);

        // Set the new default
        $profile = UserMetaProfile::where('id', $profileId)
            ->where('user_id', Auth::id())
            ->first();

        if ($profile) {
            $profile->update(['is_default' => true]);
            $this->loadData();
            session()->flash('message', 'Default profile updated.');
        } else {
            session()->flash('error', 'Profile not found.');
        }
    }

    public function deleteProfile(int $profileId): void
    {
        $profile = UserMetaProfile::where('id', $profileId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$profile) {
            session()->flash('error', 'Profile not found.');
            return;
        }

        $wasDefault = $profile->is_default;
        $profile->delete();

        // If we deleted the default profile, set a new one
        if ($wasDefault) {
            $newDefault = Auth::user()->metaProfiles()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $this->loadData();
        session()->flash('message', 'Meta profile deleted successfully.');
    }

    public function addCampaign(int $profileId): void
    {
        $validated = $this->validate([
            'campaign_id' => ['required', 'string', 'max:255'],
            'campaign_name' => ['required', 'string', 'max:255'],
        ]);

        $profile = UserMetaProfile::where('id', $profileId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$profile) {
            session()->flash('error', 'Profile not found.');
            return;
        }

        $campaigns = $profile->campaigns_available_for_duplicate ?? [];

        // Check if campaign ID already exists
        foreach ($campaigns as $campaign) {
            if ($campaign['id'] === $validated['campaign_id']) {
                session()->flash('error', 'Campaign ID already exists in this profile.');
                return;
            }
        }

        $campaigns[] = [
            'id' => $validated['campaign_id'],
            'name' => $validated['campaign_name'],
        ];

        $profile->update(['campaigns_available_for_duplicate' => $campaigns]);

        $this->reset(['campaign_id', 'campaign_name']);
        $this->loadData();
        session()->flash('message', 'Campaign added successfully.');
    }

    public function removeCampaign(int $profileId, int $index): void
    {
        $profile = UserMetaProfile::where('id', $profileId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$profile) {
            session()->flash('error', 'Profile not found.');
            return;
        }

        $campaigns = $profile->campaigns_available_for_duplicate ?? [];

        if (isset($campaigns[$index])) {
            array_splice($campaigns, $index, 1);
            $profile->update(['campaigns_available_for_duplicate' => $campaigns]);
            $this->loadData();
            session()->flash('message', 'Campaign removed successfully.');
        }
    }

    public function addAdSet(int $profileId): void
    {
        $validated = $this->validate([
            'ad_set_id' => ['required', 'string', 'max:255'],
            'ad_set_name' => ['required', 'string', 'max:255'],
        ]);

        $profile = UserMetaProfile::where('id', $profileId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$profile) {
            session()->flash('error', 'Profile not found.');
            return;
        }

        $adSets = $profile->ad_sets_available_for_duplicate ?? [];

        // Check if ad set ID already exists
        foreach ($adSets as $adSet) {
            if ($adSet['id'] === $validated['ad_set_id']) {
                session()->flash('error', 'Ad Set ID already exists in this profile.');
                return;
            }
        }

        $adSets[] = [
            'id' => $validated['ad_set_id'],
            'name' => $validated['ad_set_name'],
        ];

        $profile->update(['ad_sets_available_for_duplicate' => $adSets]);

        $this->reset(['ad_set_id', 'ad_set_name']);
        $this->loadData();
        session()->flash('message', 'Ad Set added successfully.');
    }

    public function removeAdSet(int $profileId, int $index): void
    {
        $profile = UserMetaProfile::where('id', $profileId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$profile) {
            session()->flash('error', 'Profile not found.');
            return;
        }

        $adSets = $profile->ad_sets_available_for_duplicate ?? [];

        if (isset($adSets[$index])) {
            array_splice($adSets, $index, 1);
            $profile->update(['ad_sets_available_for_duplicate' => $adSets]);
            $this->loadData();
            session()->flash('message', 'Ad Set removed successfully.');
        }
    }

    public function render()
    {
        return view('livewire.settings.meta-profiles');
    }
}
