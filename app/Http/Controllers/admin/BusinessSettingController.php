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
     * Display business settings page.
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user has admin or super admin role
        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->code, ['super_admin', 'owner']))) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $settings = BusinessSetting::getAllGrouped();
        $contactInfo = BusinessSetting::getContactInfo();
        $mailConfig = BusinessSetting::getMailConfig();
        $locations = BusinessSetting::getLocations();

        return view('admin.settings.index', compact('settings', 'contactInfo', 'mailConfig', 'locations'));
    }

    /**
     * Update general settings (company name, logo, stamp, etc.)
     */
    public function updateGeneral(Request $request)
    {
        $user = Auth::user();

        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->code, ['super_admin', 'owner']))) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_logo_dark' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:512',
            'company_stamp' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        DB::beginTransaction();

        try {
            // Update company name
            if ($request->has('company_name')) {
                BusinessSetting::set('company_name', $request->company_name);
            }

            // Upload and update company logo
            if ($request->hasFile('company_logo')) {
                $oldLogo = BusinessSetting::get('company_logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                $path = $request->file('company_logo')->store('settings/logos', 'public');
                BusinessSetting::set('company_logo', $path, 'file');
            }

            // Upload and update company logo dark
            if ($request->hasFile('company_logo_dark')) {
                $oldLogo = BusinessSetting::get('company_logo_dark');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                $path = $request->file('company_logo_dark')->store('settings/logos', 'public');
                BusinessSetting::set('company_logo_dark', $path, 'file');
            }

            // Upload and update favicon
            if ($request->hasFile('favicon')) {
                $oldFavicon = BusinessSetting::get('favicon');
                if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                    Storage::disk('public')->delete($oldFavicon);
                }
                $path = $request->file('favicon')->store('settings/favicons', 'public');
                BusinessSetting::set('favicon', $path, 'file');
            }

            // Upload and update company stamp
            if ($request->hasFile('company_stamp')) {
                $oldStamp = BusinessSetting::get('company_stamp');
                if ($oldStamp && Storage::disk('public')->exists($oldStamp)) {
                    Storage::disk('public')->delete($oldStamp);
                }
                $path = $request->file('company_stamp')->store('settings/stamps', 'public');
                BusinessSetting::set('company_stamp', $path, 'file');
            }

            DB::commit();

            Log::info('General settings updated', [
                'user_id' => Auth::id(),
                'updates' => $request->except(['_token', 'company_logo', 'company_logo_dark', 'favicon', 'company_stamp'])
            ]);

            return redirect()->back()->with('success', 'General settings updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update general settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Update contact settings
     */
    public function updateContact(Request $request)
    {
        $user = Auth::user();

        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->code, ['super_admin', 'owner']))) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'phone' => 'nullable|string|max:50',
            'phone_alternative' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'email_alternative' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
        ]);

        DB::beginTransaction();

        try {
            BusinessSetting::set('phone', $request->phone);
            BusinessSetting::set('phone_alternative', $request->phone_alternative);
            BusinessSetting::set('email', $request->email);
            BusinessSetting::set('email_alternative', $request->email_alternative);
            BusinessSetting::set('address', $request->address);
            BusinessSetting::set('city', $request->city);
            BusinessSetting::set('country', $request->country);
            BusinessSetting::set('postal_code', $request->postal_code);
            BusinessSetting::set('facebook', $request->facebook);
            BusinessSetting::set('twitter', $request->twitter);
            BusinessSetting::set('instagram', $request->instagram);
            BusinessSetting::set('linkedin', $request->linkedin);

            DB::commit();

            Log::info('Contact settings updated', ['user_id' => Auth::id()]);

            return redirect()->back()->with('success', 'Contact settings updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update contact settings: ' . $e->getMessage());
        }
    }

    /**
     * Update email (SMTP) settings
     */
    public function updateEmail(Request $request)
    {
        $user = Auth::user();

        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->code, ['super_admin', 'owner']))) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'mail_mailer' => 'nullable|string|max:50',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:50',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            BusinessSetting::set('mail_mailer', $request->mail_mailer);
            BusinessSetting::set('mail_host', $request->mail_host);
            BusinessSetting::set('mail_port', $request->mail_port);
            BusinessSetting::set('mail_username', $request->mail_username);

            // Only update password if provided (don't store empty)
            if ($request->filled('mail_password')) {
                BusinessSetting::set('mail_password', $request->mail_password);
            }

            BusinessSetting::set('mail_encryption', $request->mail_encryption);
            BusinessSetting::set('mail_from_address', $request->mail_from_address);
            BusinessSetting::set('mail_from_name', $request->mail_from_name);

            DB::commit();

            Log::info('Email settings updated', ['user_id' => Auth::id()]);

            return redirect()->back()->with('success', 'Email settings updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update email settings: ' . $e->getMessage());
        }
    }

    /**
     * Update locations (branches)
     */
    public function updateLocations(Request $request)
    {
        $user = Auth::user();

        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->code, ['super_admin', 'owner']))) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'locations' => 'nullable|array',
            'locations.*.name' => 'required|string|max:255',
            'locations.*.address' => 'nullable|string|max:500',
            'locations.*.phone' => 'nullable|string|max:50',
            'locations.*.email' => 'nullable|email|max:255',
            'locations.*.is_main' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $locations = $request->locations ?? [];

            // Ensure only one main branch
            $hasMain = false;
            foreach ($locations as &$location) {
                if (isset($location['is_main']) && $location['is_main']) {
                    if ($hasMain) {
                        $location['is_main'] = false;
                    } else {
                        $hasMain = true;
                    }
                }
            }

            BusinessSetting::set('locations', $locations, 'json', 'location');

            DB::commit();

            Log::info('Locations updated', ['user_id' => Auth::id(), 'count' => count($locations)]);

            return redirect()->back()->with('success', 'Locations updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update locations: ' . $e->getMessage());
        }
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        $user = Auth::user();

        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->code, ['super_admin', 'owner']))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            $mailConfig = BusinessSetting::getMailConfig();

            // Configure mail dynamically
            config([
                'mail.default' => $mailConfig['mailer'],
                'mail.mailers.smtp.host' => $mailConfig['host'],
                'mail.mailers.smtp.port' => $mailConfig['port'],
                'mail.mailers.smtp.username' => $mailConfig['username'],
                'mail.mailers.smtp.password' => $mailConfig['password'],
                'mail.mailers.smtp.encryption' => $mailConfig['encryption'],
                'mail.from.address' => $mailConfig['from_address'],
                'mail.from.name' => $mailConfig['from_name'],
            ]);

            \Mail::raw('This is a test email from your business management system. If you received this, your email settings are configured correctly.', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email from ' . BusinessSetting::get('company_name', 'Business System'));
            });

            return response()->json(['success' => true, 'message' => 'Test email sent successfully to ' . $request->test_email]);

        } catch (\Exception $e) {
            Log::error('Test email failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove logo
     */
    public function removeLogo(Request $request)
    {
        $user = Auth::user();

        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->code, ['super_admin', 'owner']))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $type = $request->type; // logo, logo_dark, favicon, stamp

        try {
            $key = '';
            switch ($type) {
                case 'logo':
                    $key = 'company_logo';
                    break;
                case 'logo_dark':
                    $key = 'company_logo_dark';
                    break;
                case 'favicon':
                    $key = 'favicon';
                    break;
                case 'stamp':
                    $key = 'company_stamp';
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
            }

            $oldFile = BusinessSetting::get($key);
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }

            BusinessSetting::set($key, null, 'file');

            return response()->json(['success' => true, 'message' => 'Image removed successfully.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to remove image: ' . $e->getMessage()]);
        }
    }
}
