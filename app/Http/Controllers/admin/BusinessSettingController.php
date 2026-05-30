<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BusinessSettingController extends Controller
{
    /**
     * Authorization helper — avoids repeating the same check everywhere.
     */
    private function authorizeAdmin(): bool
    {
        $user = Auth::user();
        return $user->is_super_admin
            || ($user->role && in_array($user->role->code, ['super_admin', 'owner']));
    }

    /**
     * Display business settings page.
     */
    public function index()
    {
        if (!$this->authorizeAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $settings    = BusinessSetting::getAllGrouped();
        $contactInfo = BusinessSetting::getContactInfo();
        $mailConfig  = BusinessSetting::getMailConfig();
        $locations   = BusinessSetting::getLocations();

        return view('admin.settings.index', compact('settings', 'contactInfo', 'mailConfig', 'locations'));
    }

    /**
     * Update general settings (company name, logo, stamp).
     */
    public function updateGeneral(Request $request)
    {
        if (!$this->authorizeAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'company_name'  => 'nullable|string|max:255',
            'company_logo'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_stamp' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        DB::beginTransaction();

        try {
            if ($request->has('company_name')) {
                BusinessSetting::set('company_name', $request->company_name);
            }

            if ($request->hasFile('company_logo')) {
                $this->deleteExistingFile('company_logo');
                $path = $request->file('company_logo')->store('settings/logos', 'public');
                BusinessSetting::set('company_logo', $path, 'file');
            }

            if ($request->hasFile('company_stamp')) {
                $this->deleteExistingFile('company_stamp');
                $path = $request->file('company_stamp')->store('settings/stamps', 'public');
                BusinessSetting::set('company_stamp', $path, 'file');
            }

            DB::commit();

            Log::info('General settings updated', ['user_id' => Auth::id()]);

            return redirect()->back()->with('success', 'General settings updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update general settings', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Update contact settings.
     */
    public function updateContact(Request $request)
    {
        if (!$this->authorizeAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'phone'             => 'nullable|string|max:50',
            'phone_alternative' => 'nullable|string|max:50',
            'email'             => 'nullable|email|max:255',
            'email_alternative' => 'nullable|email|max:255',
            'address'           => 'nullable|string|max:500',
            'city'              => 'nullable|string|max:100',
            'country'           => 'nullable|string|max:100',
            'postal_code'       => 'nullable|string|max:20',
            'facebook'          => 'nullable|url|max:255',
            'twitter'           => 'nullable|url|max:255',
            'instagram'         => 'nullable|url|max:255',
            'linkedin'          => 'nullable|url|max:255',
        ]);

        DB::beginTransaction();

        try {
            $fields = [
                'phone', 'phone_alternative', 'email', 'email_alternative',
                'address', 'city', 'country', 'postal_code',
                'facebook', 'twitter', 'instagram', 'linkedin',
            ];

            foreach ($fields as $field) {
                BusinessSetting::set($field, $request->$field);
            }

            DB::commit();

            Log::info('Contact settings updated', ['user_id' => Auth::id()]);

            return redirect()->back()->with('success', 'Contact settings updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update contact settings', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to update contact settings: ' . $e->getMessage());
        }
    }

    /**
     * Update email (SMTP) settings.
     */
    public function updateEmail(Request $request)
    {
        if (!$this->authorizeAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'mail_mailer'       => 'nullable|string|max:50',
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|integer|min:1|max:65535',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_encryption'   => 'nullable|string|max:50',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name'    => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $fields = [
                'mail_mailer', 'mail_host', 'mail_port',
                'mail_username', 'mail_encryption',
                'mail_from_address', 'mail_from_name',
            ];

            foreach ($fields as $field) {
                BusinessSetting::set($field, $request->$field);
            }

            // Only update password if a new value was provided
            if ($request->filled('mail_password')) {
                BusinessSetting::set('mail_password', $request->mail_password);
            }

            DB::commit();

            Log::info('Email settings updated', ['user_id' => Auth::id()]);

            return redirect()->back()->with('success', 'Email settings updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update email settings', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to update email settings: ' . $e->getMessage());
        }
    }

    /**
     * Update locations / branches.
     */
    public function updateLocations(Request $request)
    {
        if (!$this->authorizeAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'locations'          => 'nullable|array',
            'locations.*.name'   => 'required|string|max:255',
            'locations.*.address'=> 'nullable|string|max:500',
            'locations.*.phone'  => 'nullable|string|max:50',
            'locations.*.email'  => 'nullable|email|max:255',
            'locations.*.is_main'=> 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $locations = $request->locations ?? [];

            // Ensure only one main location
            $hasMain = false;
            foreach ($locations as &$location) {
                $location['is_main'] = isset($location['is_main']) && $location['is_main'];
                if ($location['is_main']) {
                    if ($hasMain) {
                        $location['is_main'] = false;
                    } else {
                        $hasMain = true;
                    }
                }
            }
            unset($location);

            BusinessSetting::set('locations', $locations, 'json', 'location');

            DB::commit();

            Log::info('Locations updated', [
                'user_id' => Auth::id(),
                'count'   => count($locations),
            ]);

            return redirect()->back()->with('success', 'Locations updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update locations', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to update locations: ' . $e->getMessage());
        }
    }

    /**
     * Send a test email using the currently saved SMTP config.
     */
    public function testEmail(Request $request)
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $mailConfig = BusinessSetting::getMailConfig();

            config([
                'mail.default'                    => $mailConfig['mailer'],
                'mail.mailers.smtp.host'          => $mailConfig['host'],
                'mail.mailers.smtp.port'          => $mailConfig['port'],
                'mail.mailers.smtp.username'      => $mailConfig['username'],
                'mail.mailers.smtp.password'      => $mailConfig['password'],
                'mail.mailers.smtp.encryption'    => $mailConfig['encryption'],
                'mail.from.address'               => $mailConfig['from_address'],
                'mail.from.name'                  => $mailConfig['from_name'],
            ]);

            \Mail::raw(
                'This is a test email from your business management system. '
                . 'If you received this, your email settings are configured correctly.',
                function ($message) use ($request) {
                    $message->to($request->test_email)
                            ->subject('Test Email from ' . BusinessSetting::get('company_name', 'Business System'));
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->test_email,
            ]);

        } catch (\Exception $e) {
            Log::error('Test email failed', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove a stored image (logo or stamp).
     */
    public function removeLogo(Request $request)
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $keyMap = [
            'logo'  => 'company_logo',
            'stamp' => 'company_stamp',
        ];

        $type = $request->type;

        if (!isset($keyMap[$type])) {
            return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
        }

        try {
            $this->deleteExistingFile($keyMap[$type]);
            BusinessSetting::set($keyMap[$type], null, 'file');

            return response()->json(['success' => true, 'message' => 'Image removed successfully.']);

        } catch (\Exception $e) {
            Log::error('Failed to remove image', [
                'user_id' => Auth::id(),
                'type'    => $type,
                'error'   => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove image: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete the physical file for a given setting key.
     * Retrieves the raw path (not the asset URL) directly from the DB.
     */
    private function deleteExistingFile(string $key): void
    {
        $setting = BusinessSetting::where('key', $key)->first();

        if (!$setting) return;

        // Get the raw stored value and json_decode to get the plain path
        $raw  = $setting->getRawOriginal('value');
        $path = $raw ? json_decode($raw) : null;

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
