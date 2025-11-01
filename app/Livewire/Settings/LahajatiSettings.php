<?php

namespace App\Livewire\Settings;

use App\Models\LahajatiDialect;
use App\Models\LahajatiPerformance;
use App\Models\LahajatiVoice;
use App\Models\UserLahajatiDialect;
use App\Models\UserLahajatiPerformance;
use App\Models\UserLahajatiVoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class LahajatiSettings extends Component
{
    public $voices = [];
    public $performances = [];
    public $dialects = [];

    public $userVoices = [];
    public $userPerformances = [];
    public $userDialects = [];

    public $isSyncingVoices = false;
    public $isSyncingPerformances = false;
    public $isSyncingDialects = false;

    public string $activeTab = 'voices';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadData();
    }

    /**
     * Load all data from database.
     */
    public function loadData(): void
    {
        $this->voices = LahajatiVoice::all();
        $this->performances = LahajatiPerformance::all();
        $this->dialects = LahajatiDialect::all();

        $this->userVoices = Auth::user()->lahajatiVoices()->with('lahajatiVoice')->get();
        $this->userPerformances = Auth::user()->lahajatiPerformances()->with('lahajatiPerformance')->get();
        $this->userDialects = Auth::user()->lahajatiDialects()->with('lahajatiDialect')->get();
    }

    /**
     * Sync voices from Lahajati API.
     */
    public function syncVoices(): void
    {
        $this->isSyncingVoices = true;

        try {
            $apiKey = Auth::user()->apiServiceKeys?->lahajati_key;

            if (!$apiKey) {
                session()->flash('error', 'Please add your Lahajati API key in Service Keys settings first.');
                $this->isSyncingVoices = false;
                return;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->get('https://lahajati.ai/api/v1/voices-absolute-control?per_page=100');

            if ($response->successful()) {
                $voicesData = $response->json('data', []);
                foreach ($voicesData as $voiceData) {
                    LahajatiVoice::updateOrCreate(
                        ['lahajati_id' => $voiceData['id_voice']],
                        [
                            'name' => $voiceData['display_name'],
                            'gender' => $voiceData['gender'] ?? null,
                            'metadata' => $voiceData,
                        ]
                    );
                }

                $this->loadData();
                session()->flash('message', 'Voices synced successfully! Total: ' . count($voicesData));
            } else {
                Log::error('Failed to sync Lahajati voices', ['response' => $response->body()]);
                session()->flash('error', 'Failed to sync voices. Please check your API key.');
            }
        } catch (\Exception $e) {
            Log::error('Error syncing Lahajati voices', ['error' => $e->getMessage()]);
            session()->flash('error', 'An error occurred while syncing voices.');
        }

        $this->isSyncingVoices = false;
    }

    /**
     * Sync performances from Lahajati API.
     */
    public function syncPerformances(): void
    {
        $this->isSyncingPerformances = true;

        try {
            $apiKey = Auth::user()->apiServiceKeys?->lahajati_key;

            if (!$apiKey) {
                session()->flash('error', 'Please add your Lahajati API key in Service Keys settings first.');
                $this->isSyncingPerformances = false;
                return;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->get('https://lahajati.ai/api/v1/performance-absolute-control?per_page=100');

            if ($response->successful()) {
                $performancesData = $response->json('data', []);

                foreach ($performancesData as $performanceData) {
                    LahajatiPerformance::updateOrCreate(
                        ['lahajati_id' => $performanceData['performance_id']],
                        ['name' => $performanceData['display_name']]
                    );
                }

                $this->loadData();
                session()->flash('message', 'Performances synced successfully! Total: ' . count($performancesData));
            } else {
                Log::error('Failed to sync Lahajati performances', ['response' => $response->body()]);
                session()->flash('error', 'Failed to sync performances. Please check your API key.');
            }
        } catch (\Exception $e) {
            Log::error('Error syncing Lahajati performances', ['error' => $e->getMessage()]);
            session()->flash('error', 'An error occurred while syncing performances.');
        }

        $this->isSyncingPerformances = false;
    }

    /**
     * Sync dialects from Lahajati API.
     */
    public function syncDialects(): void
    {
        $this->isSyncingDialects = true;

        try {
            $apiKey = Auth::user()->apiServiceKeys?->lahajati_key;

            if (!$apiKey) {
                session()->flash('error', 'Please add your Lahajati API key in Service Keys settings first.');
                $this->isSyncingDialects = false;
                return;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->get('https://lahajati.ai/api/v1/dialect-absolute-control?per_page=100');

            if ($response->successful()) {
                $dialectsData = $response->json('data', []);

                foreach ($dialectsData as $dialectData) {
                    LahajatiDialect::updateOrCreate(
                        ['lahajati_id' => $dialectData['dialect_id']],
                        ['name' => $dialectData['display_name']]
                    );
                }

                $this->loadData();
                session()->flash('message', 'Dialects synced successfully! Total: ' . count($dialectsData));
            } else {
                Log::error('Failed to sync Lahajati dialects', ['response' => $response->body()]);
                session()->flash('error', 'Failed to sync dialects. Please check your API key.');
            }
        } catch (\Exception $e) {
            Log::error('Error syncing Lahajati dialects', ['error' => $e->getMessage()]);
            session()->flash('error', 'An error occurred while syncing dialects.');
        }

        $this->isSyncingDialects = false;
    }

    /**
     * Add a voice to user preferences.
     */
    public function addVoice(int $voiceId): void
    {
        $existing = Auth::user()->lahajatiVoices()->where('lahajati_voice_id', $voiceId)->first();

        if ($existing) {
            session()->flash('error', 'Voice already added to your preferences.');
            return;
        }

        UserLahajatiVoice::create([
            'user_id' => Auth::id(),
            'lahajati_voice_id' => $voiceId,
            'is_default' => Auth::user()->lahajatiVoices()->count() === 0,
        ]);

        $this->loadData();
        session()->flash('message', 'Voice added to your preferences.');
    }

    /**
     * Remove a voice from user preferences.
     */
    public function removeVoice(int $userVoiceId): void
    {
        $userVoice = UserLahajatiVoice::where('id', $userVoiceId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$userVoice) {
            session()->flash('error', 'Voice not found in your preferences.');
            return;
        }

        $wasDefault = $userVoice->is_default;
        $userVoice->delete();

        // If this was the default, set another one as default
        if ($wasDefault) {
            $newDefault = Auth::user()->lahajatiVoices()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $this->loadData();
        session()->flash('message', 'Voice removed from your preferences.');
    }

    /**
     * Set a voice as default.
     */
    public function setDefaultVoice(int $userVoiceId): void
    {
        // Unset all defaults
        Auth::user()->lahajatiVoices()->update(['is_default' => false]);

        // Set the new default
        $userVoice = UserLahajatiVoice::where('id', $userVoiceId)
            ->where('user_id', Auth::id())
            ->first();

        if ($userVoice) {
            $userVoice->update(['is_default' => true]);
            $this->loadData();
            session()->flash('message', 'Default voice updated.');
        }
    }

    /**
     * Add a performance to user preferences.
     */
    public function addPerformance(int $performanceId): void
    {
        $existing = Auth::user()->lahajatiPerformances()->where('lahajati_performance_id', $performanceId)->first();

        if ($existing) {
            session()->flash('error', 'Performance already added to your preferences.');
            return;
        }

        UserLahajatiPerformance::create([
            'user_id' => Auth::id(),
            'lahajati_performance_id' => $performanceId,
            'is_default' => Auth::user()->lahajatiPerformances()->count() === 0,
        ]);

        $this->loadData();
        session()->flash('message', 'Performance added to your preferences.');
    }

    /**
     * Remove a performance from user preferences.
     */
    public function removePerformance(int $userPerformanceId): void
    {
        $userPerformance = UserLahajatiPerformance::where('id', $userPerformanceId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$userPerformance) {
            session()->flash('error', 'Performance not found in your preferences.');
            return;
        }

        $wasDefault = $userPerformance->is_default;
        $userPerformance->delete();

        // If this was the default, set another one as default
        if ($wasDefault) {
            $newDefault = Auth::user()->lahajatiPerformances()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $this->loadData();
        session()->flash('message', 'Performance removed from your preferences.');
    }

    /**
     * Set a performance as default.
     */
    public function setDefaultPerformance(int $userPerformanceId): void
    {
        // Unset all defaults
        Auth::user()->lahajatiPerformances()->update(['is_default' => false]);

        // Set the new default
        $userPerformance = UserLahajatiPerformance::where('id', $userPerformanceId)
            ->where('user_id', Auth::id())
            ->first();

        if ($userPerformance) {
            $userPerformance->update(['is_default' => true]);
            $this->loadData();
            session()->flash('message', 'Default performance updated.');
        }
    }

    /**
     * Add a dialect to user preferences.
     */
    public function addDialect(int $dialectId): void
    {
        $existing = Auth::user()->lahajatiDialects()->where('lahajati_dialect_id', $dialectId)->first();

        if ($existing) {
            session()->flash('error', 'Dialect already added to your preferences.');
            return;
        }

        UserLahajatiDialect::create([
            'user_id' => Auth::id(),
            'lahajati_dialect_id' => $dialectId,
            'is_default' => Auth::user()->lahajatiDialects()->count() === 0,
        ]);

        $this->loadData();
        session()->flash('message', 'Dialect added to your preferences.');
    }

    /**
     * Remove a dialect from user preferences.
     */
    public function removeDialect(int $userDialectId): void
    {
        $userDialect = UserLahajatiDialect::where('id', $userDialectId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$userDialect) {
            session()->flash('error', 'Dialect not found in your preferences.');
            return;
        }

        $wasDefault = $userDialect->is_default;
        $userDialect->delete();

        // If this was the default, set another one as default
        if ($wasDefault) {
            $newDefault = Auth::user()->lahajatiDialects()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $this->loadData();
        session()->flash('message', 'Dialect removed from your preferences.');
    }

    /**
     * Set a dialect as default.
     */
    public function setDefaultDialect(int $userDialectId): void
    {
        // Unset all defaults
        Auth::user()->lahajatiDialects()->update(['is_default' => false]);

        // Set the new default
        $userDialect = UserLahajatiDialect::where('id', $userDialectId)
            ->where('user_id', Auth::id())
            ->first();

        if ($userDialect) {
            $userDialect->update(['is_default' => true]);
            $this->loadData();
            session()->flash('message', 'Default dialect updated.');
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.settings.lahajati-settings');
    }
}
