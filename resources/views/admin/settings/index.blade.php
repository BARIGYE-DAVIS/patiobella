{{-- resources/views/admin/settings/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Business Settings')
@section('page-title', 'Business Settings')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Business Settings</h2>
            <p class="text-sm text-gray-500 mt-1">Manage your company profile, contact details, email configuration, and branches</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-1">
            <button type="button"
                class="tab-btn active group inline-flex items-center gap-2 px-5 py-3 border-b-2 border-orange-500 text-orange-600 font-semibold text-sm transition-all"
                data-tab="general">
                <i class="fas fa-building text-xs"></i> General
            </button>
            <button type="button"
                class="tab-btn group inline-flex items-center gap-2 px-5 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm transition-all"
                data-tab="contact">
                <i class="fas fa-address-card text-xs"></i> Contact
            </button>
            <button type="button"
                class="tab-btn group inline-flex items-center gap-2 px-5 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm transition-all"
                data-tab="email">
                <i class="fas fa-envelope text-xs"></i> Email
            </button>
            <button type="button"
                class="tab-btn group inline-flex items-center gap-2 px-5 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm transition-all"
                data-tab="locations">
                <i class="fas fa-map-marker-alt text-xs"></i> Locations
            </button>
        </nav>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- GENERAL SETTINGS TAB                                    --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div id="tab-general" class="tab-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/60 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-building text-orange-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">General Settings</h3>
                    <p class="text-xs text-gray-500">Company name, logo, and stamp</p>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.settings.general') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Company Name --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Name</label>
                        <input
                            type="text"
                            name="company_name"
                            class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                            value="{{ \App\Models\BusinessSetting::get('company_name') }}"
                            placeholder="Enter your company name">
                    </div>

                    {{-- Logo & Stamp side by side --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Company Logo --}}
                        <div class="rounded-lg border border-gray-200 p-4 bg-gray-50/40">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-image text-gray-400 mr-1.5"></i> Company Logo
                            </label>
                            @if($logo = \App\Models\BusinessSetting::getLogo())
                                <div class="mb-3 flex items-center gap-3 bg-white rounded-lg border border-gray-200 px-3 py-2.5">
                                    <img src="{{ $logo }}" class="h-12 w-auto object-contain" alt="Company Logo">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-700 truncate">Current logo</p>
                                        <button type="button" class="remove-image text-xs text-red-500 hover:text-red-700 transition mt-0.5" data-type="logo">
                                            <i class="fas fa-trash-alt mr-1"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-400 hover:bg-orange-50/40 transition-all group">
                                <i class="fas fa-cloud-upload-alt text-gray-400 group-hover:text-orange-500 text-xl mb-1.5 transition"></i>
                                <span class="text-xs text-gray-500 group-hover:text-orange-600 transition">Click to upload logo</span>
                                <span class="text-[11px] text-gray-400 mt-0.5">PNG, JPG — max 2MB</span>
                                <input type="file" name="company_logo" class="hidden" accept="image/*">
                            </label>
                        </div>

                        {{-- Company Stamp --}}
                        <div class="rounded-lg border border-gray-200 p-4 bg-gray-50/40">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-stamp text-gray-400 mr-1.5"></i> Company Stamp
                            </label>
                            @if($stamp = \App\Models\BusinessSetting::getStamp())
                                <div class="mb-3 flex items-center gap-3 bg-white rounded-lg border border-gray-200 px-3 py-2.5">
                                    <img src="{{ $stamp }}" class="h-12 w-auto object-contain" alt="Company Stamp">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-700 truncate">Current stamp</p>
                                        <button type="button" class="remove-image text-xs text-red-500 hover:text-red-700 transition mt-0.5" data-type="stamp">
                                            <i class="fas fa-trash-alt mr-1"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-400 hover:bg-orange-50/40 transition-all group">
                                <i class="fas fa-cloud-upload-alt text-gray-400 group-hover:text-orange-500 text-xl mb-1.5 transition"></i>
                                <span class="text-xs text-gray-500 group-hover:text-orange-600 transition">Click to upload stamp</span>
                                <span class="text-[11px] text-gray-400 mt-0.5">PNG, JPG — max 1MB</span>
                                <input type="file" name="company_stamp" class="hidden" accept="image/*">
                            </label>
                            <p class="text-[11px] text-gray-400 mt-2"><i class="fas fa-info-circle mr-1"></i>Used on official documents &amp; invoices</p>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            <i class="fas fa-save"></i> Save General Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- CONTACT SETTINGS TAB                                    --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div id="tab-contact" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/60 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-address-card text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Contact Information</h3>
                    <p class="text-xs text-gray-500">Business contact details and social media links</p>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.settings.contact') }}">
                    @csrf

                    {{-- Section: Contact Details --}}
                    <div class="mb-2">
                        <h4 class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Contact Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <i class="fas fa-phone text-gray-400 mr-1"></i> Phone Number
                                </label>
                                <input type="text" name="phone"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                    value="{{ \App\Models\BusinessSetting::get('phone') }}"
                                    placeholder="+1 (555) 000-0000">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <i class="fas fa-phone-alt text-gray-400 mr-1"></i> Alternative Phone
                                </label>
                                <input type="text" name="phone_alternative"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                    value="{{ \App\Models\BusinessSetting::get('phone_alternative') }}"
                                    placeholder="+1 (555) 000-0001">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <i class="fas fa-envelope text-gray-400 mr-1"></i> Email Address
                                </label>
                                <input type="email" name="email"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                    value="{{ \App\Models\BusinessSetting::get('email') }}"
                                    placeholder="info@company.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <i class="fas fa-envelope-open text-gray-400 mr-1"></i> Alternative Email
                                </label>
                                <input type="email" name="email_alternative"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                    value="{{ \App\Models\BusinessSetting::get('email_alternative') }}"
                                    placeholder="support@company.com">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> Physical Address
                                </label>
                                <textarea name="address" rows="2"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition resize-none"
                                    placeholder="123 Business Ave, Suite 100">{{ \App\Models\BusinessSetting::get('address') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                                <input type="text" name="city"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                    value="{{ \App\Models\BusinessSetting::get('city') }}"
                                    placeholder="New York">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Country</label>
                                <input type="text" name="country"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                    value="{{ \App\Models\BusinessSetting::get('country') }}"
                                    placeholder="United States">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Postal Code</label>
                                <input type="text" name="postal_code"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                    value="{{ \App\Models\BusinessSetting::get('postal_code') }}"
                                    placeholder="10001">
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="my-6 border-t border-gray-100"></div>

                    {{-- Section: Social Media --}}
                    <h4 class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Social Media</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <i class="fab fa-facebook text-[#1877F2] mr-1.5"></i> Facebook
                            </label>
                            <input type="url" name="facebook"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                value="{{ \App\Models\BusinessSetting::get('facebook') }}"
                                placeholder="https://facebook.com/yourpage">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <i class="fab fa-twitter text-[#1DA1F2] mr-1.5"></i> Twitter / X
                            </label>
                            <input type="url" name="twitter"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                value="{{ \App\Models\BusinessSetting::get('twitter') }}"
                                placeholder="https://twitter.com/yourhandle">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <i class="fab fa-instagram text-[#E1306C] mr-1.5"></i> Instagram
                            </label>
                            <input type="url" name="instagram"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                value="{{ \App\Models\BusinessSetting::get('instagram') }}"
                                placeholder="https://instagram.com/yourprofile">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <i class="fab fa-linkedin text-[#0A66C2] mr-1.5"></i> LinkedIn
                            </label>
                            <input type="url" name="linkedin"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                value="{{ \App\Models\BusinessSetting::get('linkedin') }}"
                                placeholder="https://linkedin.com/company/yourcompany">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            <i class="fas fa-save"></i> Save Contact Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- EMAIL SETTINGS TAB                                      --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div id="tab-email" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/60 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-envelope text-purple-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">SMTP Email Settings</h3>
                    <p class="text-xs text-gray-500">Configure your email server for sending notifications</p>
                </div>
            </div>
            <div class="p-6">

                {{-- Info Banner --}}
                <div class="mb-6 flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 text-sm text-blue-700">
                    <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                    <span>These settings override your <code class="bg-blue-100 px-1 rounded text-xs">.env</code> mail configuration at runtime. Leave password blank to keep the existing value.</span>
                </div>

                <form method="POST" action="{{ route('admin.settings.email') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Mailer Driver</label>
                            <select name="mail_mailer"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition bg-white">
                                <option value="smtp" {{ \App\Models\BusinessSetting::get('mail_mailer') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ \App\Models\BusinessSetting::get('mail_mailer') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Host</label>
                            <input type="text" name="mail_host"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                value="{{ \App\Models\BusinessSetting::get('mail_host') }}"
                                placeholder="smtp.mailtrap.io">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Port</label>
                            <input type="number" name="mail_port"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                value="{{ \App\Models\BusinessSetting::get('mail_port') }}"
                                placeholder="587">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Encryption</label>
                            <select name="mail_encryption"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition bg-white">
                                <option value="tls" {{ \App\Models\BusinessSetting::get('mail_encryption') == 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                                <option value="ssl" {{ \App\Models\BusinessSetting::get('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="">None</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                            <input type="text" name="mail_username"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                value="{{ \App\Models\BusinessSetting::get('mail_username') }}"
                                placeholder="your@email.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                            <div class="relative">
                                <input type="password" name="mail_password" id="mailPasswordInput"
                                    class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 pr-10 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                    placeholder="Leave blank to keep current">
                                <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition">
                                    <i class="fas fa-eye text-sm" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">From Address</label>
                            <input type="email" name="mail_from_address"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                value="{{ \App\Models\BusinessSetting::get('mail_from_address') }}"
                                placeholder="noreply@company.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">From Name</label>
                            <input type="text" name="mail_from_name"
                                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                value="{{ \App\Models\BusinessSetting::get('mail_from_name') }}"
                                placeholder="Company Name">
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" id="testEmailBtn"
                            class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:border-blue-400 hover:bg-blue-50 text-gray-700 hover:text-blue-700 px-5 py-2.5 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-paper-plane"></i> Send Test Email
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            <i class="fas fa-save"></i> Save Email Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- LOCATIONS TAB                                           --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div id="tab-locations" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/60 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Business Locations</h3>
                        <p class="text-xs text-gray-500">Manage your branches and their contact information</p>
                    </div>
                </div>
                <button type="button" id="addLocationBtn"
                    class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    <i class="fas fa-plus"></i> Add Location
                </button>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.settings.locations') }}" id="locationsForm">
                    @csrf
                    <div id="locationsContainer" class="space-y-4">
                        @php $locations = \App\Models\BusinessSetting::getLocations() ?? []; @endphp
                        @if(is_array($locations) && count($locations) > 0)
                            @foreach($locations as $index => $location)
                            <div class="location-item border border-gray-200 rounded-xl overflow-hidden">
                                {{-- Location Header --}}
                                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-700 text-xs font-bold flex items-center justify-center">{{ $loop->iteration }}</span>
                                        <h4 class="text-sm font-semibold text-gray-700">{{ $location['name'] ?? 'Location #'.$loop->iteration }}</h4>
                                        @if(isset($location['is_main']) && $location['is_main'])
                                            <span class="text-[10px] font-semibold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Main</span>
                                        @endif
                                    </div>
                                    <button type="button" class="remove-location inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </button>
                                </div>
                                {{-- Location Fields --}}
                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Location Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="locations[{{ $index }}][name]"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                            value="{{ $location['name'] ?? '' }}" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                                        <input type="text" name="locations[{{ $index }}][phone]"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                            value="{{ $location['phone'] ?? '' }}" placeholder="+1 (555) 000-0000">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                                        <input type="email" name="locations[{{ $index }}][email]"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                            value="{{ $location['email'] ?? '' }}" placeholder="branch@company.com">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                                        <textarea name="locations[{{ $index }}][address]" rows="2"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition resize-none"
                                            placeholder="123 Branch St, City">{{ $location['address'] ?? '' }}</textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="flex items-center gap-2 cursor-pointer group w-fit">
                                            <input type="checkbox" name="locations[{{ $index }}][is_main]" value="1"
                                                class="rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                                                {{ isset($location['is_main']) && $location['is_main'] ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-600 group-hover:text-gray-800 transition">Set as Main Location</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div id="emptyLocations" class="text-center py-12 text-gray-400">
                                <i class="fas fa-map-marked-alt text-4xl mb-3 block text-gray-300"></i>
                                <p class="text-sm font-medium">No locations added yet</p>
                                <p class="text-xs mt-1">Click "Add Location" above to get started</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            <i class="fas fa-save"></i> Save Locations
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
(function () {
    // ─── Tab Switching ────────────────────────────────────────────────
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const tabId = this.dataset.tab;

            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-orange-500', 'text-orange-600', 'font-semibold');
                b.classList.add('border-transparent', 'text-gray-500', 'font-medium');
            });
            this.classList.add('border-orange-500', 'text-orange-600', 'font-semibold');
            this.classList.remove('border-transparent', 'text-gray-500', 'font-medium');

            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.getElementById(`tab-${tabId}`).classList.remove('hidden');
        });
    });

    // ─── File upload label preview ────────────────────────────────────
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function () {
            const label = this.closest('label');
            if (!label) return;
            const span = label.querySelector('span:first-of-type');
            if (span && this.files[0]) {
                span.textContent = this.files[0].name;
            }
        });
    });

    // ─── Toggle password visibility ───────────────────────────────────
    const toggleBtn = document.getElementById('togglePassword');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const input = document.getElementById('mailPasswordInput');
            const icon  = document.getElementById('togglePasswordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    }

    // ─── Locations: add ──────────────────────────────────────────────
    let locationIndex = {{ count(\App\Models\BusinessSetting::getLocations() ?? []) }};

    const addLocationBtn = document.getElementById('addLocationBtn');
    if (addLocationBtn) {
        addLocationBtn.addEventListener('click', function () {
            const container = document.getElementById('locationsContainer');
            const empty     = document.getElementById('emptyLocations');
            if (empty) empty.remove();

            const idx  = locationIndex++;
            const num  = container.querySelectorAll('.location-item').length + 1;
            const html = `
                <div class="location-item border border-gray-200 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-700 text-xs font-bold flex items-center justify-center">${num}</span>
                            <h4 class="text-sm font-semibold text-gray-700">New Location</h4>
                        </div>
                        <button type="button" class="remove-location inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition">
                            <i class="fas fa-trash-alt"></i> Remove
                        </button>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Location Name <span class="text-red-500">*</span></label>
                            <input type="text" name="locations[${idx}][name]"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                required placeholder="e.g. Head Office">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                            <input type="text" name="locations[${idx}][phone]"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                placeholder="+1 (555) 000-0000">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                            <input type="email" name="locations[${idx}][email]"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition"
                                placeholder="branch@company.com">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                            <textarea name="locations[${idx}][address]" rows="2"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition resize-none"
                                placeholder="123 Branch St, City"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-2 cursor-pointer group w-fit">
                                <input type="checkbox" name="locations[${idx}][is_main]" value="1"
                                    class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                <span class="text-sm text-gray-600 group-hover:text-gray-800 transition">Set as Main Location</span>
                            </label>
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            attachRemoveEvents();
        });
    }

    function attachRemoveEvents() {
        document.querySelectorAll('.remove-location').forEach(btn => {
            btn.replaceWith(btn.cloneNode(true)); // strip old listeners
        });
        document.querySelectorAll('.remove-location').forEach(btn => {
            btn.addEventListener('click', function () {
                if (confirm('Remove this location?')) {
                    this.closest('.location-item').remove();
                }
            });
        });
    }

    attachRemoveEvents();

    // ─── Remove image ─────────────────────────────────────────────────
    document.querySelectorAll('.remove-image').forEach(btn => {
        btn.addEventListener('click', async function () {
            const type = this.dataset.type;
            if (!confirm('Remove this image?')) return;

            try {
                const res    = await fetch('{{ route("admin.settings.remove-image") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ type })
                });
                const result = await res.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.message);
                }
            } catch {
                alert('Failed to remove image. Please try again.');
            }
        });
    });

    // ─── Test Email ───────────────────────────────────────────────────
    const testEmailBtn = document.getElementById('testEmailBtn');
    if (testEmailBtn) {
        testEmailBtn.addEventListener('click', async function () {
            const email = prompt('Enter an email address to send the test to:');
            if (!email) return;

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending…';

            try {
                const res    = await fetch('{{ route("admin.settings.test-email") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ test_email: email })
                });
                const result = await res.json();
                alert(result.success ? result.message : 'Error: ' + result.message);
            } catch {
                alert('Failed to send test email. Please try again.');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Send Test Email';
            }
        });
    }
})();
</script>
@endpush
@endsection
