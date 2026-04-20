@extends('layouts.legal', [
    'title' => 'Terms of Service',
    'description' => 'Read our terms of service for using StagePhoto.ru platform',
    'lastUpdated' => 'April 20, 2026'
])

@section('content')
    <div class="space-y-6">
        <section>
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing and using StagePhoto.ru ("the Platform"), you agree to be bound by these Terms of Service. If you do not agree, please do not use the Platform.</p>
        </section>

        <section>
            <h2>2. User Accounts</h2>
            <h3>2.1 Registration</h3>
            <p>To access certain features, you must register for an account. You agree to provide accurate and complete information and to keep it updated.</p>

            <h3>2.2 Account Security</h3>
            <p>You are responsible for maintaining the confidentiality of your password and for all activities under your account. Notify us immediately of any unauthorized use.</p>
        </section>

        <section>
            <h2>3. Content Guidelines</h2>
            <h3>3.1 Your Content</h3>
            <p>You retain all rights to the photographs you upload. By uploading content, you grant StagePhoto.ru a non-exclusive license to display, distribute, and promote your work on the Platform.</p>

            <h3>3.2 Prohibited Content</h3>
            <p>The following content is prohibited:</p>
            <ul>
                <li>Copyrighted material you don't own or have permission to use</li>
                <li>Offensive, discriminatory, or harassing content</li>
                <li>NSFW or explicit material</li>
                <li>Content promoting violence or illegal activities</li>
            </ul>
        </section>

        <section>
            <h2>4. Intellectual Property</h2>
            <p>All content on StagePhoto.ru, including logos, designs, and code, is owned by StagePhoto.ru and protected by copyright laws. Photographers retain ownership of their uploaded images.</p>
        </section>

        <section>
            <h2>5. Photography Licensing</h2>
            <p>When you upload photos, you retain full copyright. Other users may request licenses for your work through our request system. Any licensing agreements are between you and the requester.</p>
        </section>

        <section>
            <h2>6. Prohibited Conduct</h2>
            <p>You agree not to:</p>
            <ul>
                <li>Use the Platform for any illegal purpose</li>
                <li>Harass, abuse, or harm another person</li>
                <li>Impersonate any person or entity</li>
                <li>Upload viruses or malicious code</li>
                <li>Attempt to gain unauthorized access to our systems</li>
            </ul>
        </section>

        <section>
            <h2>7. Termination</h2>
            <p>We reserve the right to suspend or terminate accounts that violate these terms. You may delete your account at any time through your settings.</p>
        </section>

        <section>
            <h2>8. Disclaimer of Warranties</h2>
            <p>The Platform is provided "as is" without warranties of any kind. We do not guarantee uninterrupted or error-free service.</p>
        </section>

        <section>
            <h2>9. Limitation of Liability</h2>
            <p>StagePhoto.ru shall not be liable for any indirect, incidental, or consequential damages arising from your use of the Platform.</p>
        </section>

        <section>
            <h2>10. Changes to Terms</h2>
            <p>We may modify these terms at any time. Continued use of the Platform constitutes acceptance of the modified terms.</p>
        </section>

        <section>
            <h2>11. Contact Information</h2>
            <p>For questions about these Terms, contact us at: <a href="mailto:legal@stagephoto.ru">legal@stagephoto.ru</a></p>
        </section>
    </div>
@endsection
